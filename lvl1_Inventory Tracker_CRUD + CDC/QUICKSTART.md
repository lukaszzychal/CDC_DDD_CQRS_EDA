🌐 **Language / Język:** 🇬🇧 **English** | 🇵🇱 [Polski](QUICKSTART.pl.md)

---

# 🚀 Quick Start & Manual Testing Guide

This guide will walk you step-by-step through setting up the complete architectural environment (Symfony, PostgreSQL, Debezium CDC, Kafka, Meilisearch), running manual API tests, and verifying CDC data streaming.

---

## 📋 Prerequisites

* **Docker Desktop** (or Docker Engine + Docker Compose)
* HTTP client tool for sending requests: **cURL**, **Postman**, or **Insomnia**
* (Optional) `jq` installed in terminal for JSON formatting

---

## ⚙️ 1. Environment Setup (Docker Compose)

Navigate to the Symfony project directory and start all infrastructure containers:

```bash
cd "lvl1_Inventory Tracker_CRUD + CDC/Inventory_Tracker_Symfony"

# Start all containers in background
docker compose up -d --build
```

### Check running containers status:
```bash
docker compose ps
```

Ensure the following services have `Up` / `running` status:
* `inventory_symfony_app` (PHP 8.4 FPM + `rdkafka`)
* `inventory_symfony_web` (Nginx on port `8080`)
* `inventory_postgres` (PostgreSQL on port `5433` with `wal_level=logical`)
* `inventory_kafka` (Kafka on port `9092` / Kafka UI on `8081`)
* `inventory_debezium` (Debezium REST API on port `8083`)
* `inventory_meilisearch` (Meilisearch on port `7700`)
* `inventory_symfony_worker` (Symfony Messenger Worker)

---

## 🗄️ 2. Database Preparation (Migrations / Schema)

Execute database structure update in PostgreSQL inside the application container:

```bash
docker compose exec app php bin/console doctrine:schema:update --force
```

---

## 🔌 3. CDC Connector Verification (Debezium)

The `debezium-init` container automatically registers the PostgreSQL connector on startup. To verify that CDC is running properly, check the connector status:

```bash
curl -s http://localhost:8083/connectors/inventory-postgres-connector/status | jq .
```

**Expected result:**
`"state": "RUNNING"` for both connector and its tasks (`tasks`).

---

## 📡 4. How to Test PostgreSQL WAL Logging & Kafka Ingestion?

Before sending HTTP requests, it is recommended to monitor data streaming from PostgreSQL WAL logs into Kafka. There are 3 verification methods available:

### 🏆 Method 1: Kafka UI Graphical Interface (**RECOMMENDED**)

1. Open Kafka UI in your browser: [http://localhost:8081](http://localhost:8081)
2. Navigate to the **Topics** section in the left menu.
3. Select topic: `cdc_inventory.public.products` (or `cdc_inventory.public.categories`).
4. Switch to the **Messages** tab.
5. You will see real-time streaming events with a JSON payload containing the `after` object with changes captured from PostgreSQL WAL!

### 📺 Method 2: Terminal Kafka Message Stream (`kafka-console-consumer`)

Run the Kafka consumer in your terminal before executing cURL commands:

```bash
docker compose exec kafka kafka-console-consumer \
  --bootstrap-server localhost:9092 \
  --topic cdc_inventory.public.products \
  --from-beginning
```

After sending any POST/PATCH request, you will see Debezium-generated CDC events in JSON format (`op: "c"` or `op: "u"`) in the console window.

### 🔌 Method 3: Query Debezium Connect REST API

Check connector status and processed task count:

```bash
curl -s http://localhost:8083/connectors/inventory-postgres-connector/status | jq .
```

---

## 🧪 5. Testing Use Case Scenarios (Manual HTTP Testing)

### 🔹 Scenario A: Create New Category (`POST /api/category`)

Send a request to create a category (e.g., "Electronics"):

```bash
curl -i -X POST http://localhost:8080/api/category \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Elektronika"
  }'
```

**Expected result (HTTP 201 Created):**
```json
{
  "status": "success",
  "message": "Category created successfully",
  "data": {
    "categoryId": "01918888-9999-7abc-8def-0123456789ab"
  }
}
```

---

### 🔹 Scenario B: Create New Product with Category (`POST /api/product`)

Send a request to create a product using the created Category ID:

```bash
curl -i -X POST http://localhost:8080/api/product \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Klawiatura Mechaniczna RGB",
    "sku": "KB9999",
    "price": 349.99,
    "currency": "PLN",
    "categoryId": "01918888-9999-7abc-8def-0123456789ab",
    "stock": 25
  }'
```

**Expected result (HTTP 201 Created):**
```json
{
  "status": "success",
  "message": "Product created successfully",
  "data": {
    "productId": "01911234-5678-7abc-8def-0123456789ab"
  }
}
```

---

### 🔹 Scenario C: Update Product Price (`PATCH /api/product/{productId}/price`)

> 💡 **Note:** Replace `019fc304-df32-71bc-9101-c569ed23bc9c` with the actual `productId` returned from Scenario B response!

Update the price of the created product:

```bash
curl -i -X PATCH http://localhost:8080/api/product/019fc304-df32-71bc-9101-c569ed23bc9c/price \
  -H "Content-Type: application/json" \
  -d '{
    "price": 299.99,
    "currency": "PLN"
  }'
```

**Expected result (HTTP 200 OK):**
```json
{
  "status": "success",
  "message": "Product price changed successfully",
  "data": {
    "productId": "01911234-5678-7abc-8def-0123456789ab"
  }
}
```

---

### 🔹 Scenario E: Search in Meilisearch Read Model (`GET /api/products/search/meilisearch`)

Perform a read from the **Meilisearch** search engine read model populated via **Change Data Capture (Debezium + Kafka)**:

```bash
# Execute with formatted JSON payload (using python3):
curl -s -X GET "http://localhost:8080/api/products/search/meilisearch?q=Klawiatura" | python3 -m json.tool
```

**Expected result (HTTP 200 OK):**
```json
{
  "status": "success",
  "read_model": "Meilisearch (Out-of-Process CDC via Debezium + Kafka)",
  "message": "Products retrieved successfully from Meilisearch",
  "data": [
    {
      "id": "019fc2cc-c251-745a-bd80-ccbbd6d6cf87",
      "name": "Klawiatura Mechaniczna RGB",
      "sku": "KB9999",
      "price": 299.99,
      "currency": "PLN",
      "stock": 25,
      "category_id": "019fc2cc-2c94-777c-bd13-bbacd27a344b",
      "category_name": "Elektronika"
    }
  ]
}
```

---

### 🔹 Scenario F: Search in MongoDB Read Model (`GET /api/products/search/mongodb`)

Perform a read from the denormalized **MongoDB** document read model populated in-process via **Domain Events**:

```bash
# Execute with formatted JSON payload (using python3):
curl -s -X GET "http://localhost:8080/api/products/search/mongodb?q=Wiedźmin" | python3 -m json.tool
```

**Expected result (HTTP 200 OK):**
```json
{
  "status": "success",
  "read_model": "MongoDB (In-Process Domain Events Projection)",
  "message": "Products retrieved successfully from MongoDB Read Model",
  "data": [
    {
      "_id": "019fc3c4-8fcb-7d53-8e75-3c6749711e10",
      "name": "Wiedźmin - Krew Elfów",
      "sku": "BOOK003",
      "price": 69.99,
      "currency": "PLN",
      "stock": 75,
      "category_id": "019fc3c3-8816-78e0-9ae4-81d801d77348",
      "category_name": "Książki i Komiksy",
      "updated_at": "2026-08-02T18:37:52+00:00"
    }
  ]
}
```

---

## ⚡ 6. Note on Event Processing (Sync vs. Async in Symfony Messenger)

By default in `config/packages/messenger.yaml`, domain events are routed to synchronous transport (`sync`):

```yaml
routing:
    'App\Backend\Domain\Event\ProductCreated': sync
    'App\Backend\Domain\Event\ProductWasPriced': sync
    'App\Backend\Domain\Event\CategoryCreated': sync
    'App\Backend\Domain\Event\CategoryNameChanged': sync
```

**Why?** This enables instant handler execution and immediate result verification during manual tests without waiting for queues.

**Asynchronous testing (`async`):**
If you want to test asynchronous background event handling via worker (`inventory_symfony_worker`), simply replace `sync` with `async` in `messenger.yaml` and clear cache (`cache:clear`). Events will then be sent to the DB/Kafka queue and consumed by the background worker.

---

## 🛠️ Useful Developer Commands

* **Run Kafka Consumer (Symfony Messenger CDC Worker):**
  ```bash
  docker compose exec app php bin/console messenger:consume kafka_cdc -vv
  ```

* **View logs for any container:**
  ```bash
  docker compose logs -f app
  docker compose logs -f debezium
  ```

* **Clear Symfony cache:**
  ```bash
  docker compose exec app php bin/console cache:clear
  ```

* **Stop complete environment:**
  ```bash
  docker compose down
  ```
