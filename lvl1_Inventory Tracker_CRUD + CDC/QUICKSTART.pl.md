🌐 **Language / Język:** 🇬🇧 [English](QUICKSTART.md) | 🇵🇱 **Polski**

---

# 🚀 Quick Start & Manual Testing Guide (Instrukcja Szybkiego Uruchomienia i Testowania)

Niniejsza instrukcja przeprowadzi Cię krok po kroku przez uruchomienie całego środowiska architektonicznego (Symfony, PostgreSQL, Debezium CDC, Kafka, Meilisearch) oraz wykonanie ręcznych testów API i weryfikacji strumieniowania danych CDC.

---

## 📋 Wymagania Wstępne (Prerequisites)

* **Docker Desktop** (lub Docker Engine + Docker Compose)
* Narzędzie HTTP do wysyłania zapytań: **cURL**, **Postman** lub **Insomnia**
* (Opcjonalnie) `jq` w terminalu do formatowania odpowiedzi JSON

---

## ⚙️ 1. Uruchomienie Środowiska (Docker Compose)

Przejdź do katalogu projektu Symfony i uruchom wszystkie kontenery infrastruktury:

```bash
cd "lvl1_Inventory Tracker_CRUD + CDC/Inventory_Tracker_Symfony"

# Uruchomienie wszystkich kontenerów w tle
docker compose up -d --build
```

### Sprawdzenie statusu działających kontenerów:
```bash
docker compose ps
```

Upewnij się, że następujące usługi mają status `Up` / `running`:
* `inventory_symfony_app` (PHP 8.4 FPM + `rdkafka`)
* `inventory_symfony_web` (Nginx na porcie `8080`)
* `inventory_postgres` (PostgreSQL na porcie `5433` z `wal_level=logical`)
* `inventory_kafka` (Kafka na porcie `9092` / Kafka UI na `8081`)
* `inventory_debezium` (Debezium REST API na porcie `8083`)
* `inventory_meilisearch` (Meilisearch na porcie `7700`)
* `inventory_symfony_worker` (Symfony Messenger Worker)

---

## 🗄️ 2. Przygotowanie Bazy Danych (Migracje / Schemat)

Wykonaj aktualizację struktury bazy danych PostgreSQL wewnątrz kontenera aplikacji:

```bash
docker compose exec app php bin/console doctrine:schema:update --force
```

---

## 🔌 3. Weryfikacja Statusu Konektora CDC (Debezium)

Kontener `debezium-init` automatycznie rejestruje konektor PostgreSQL przy starcie. Aby upewnić się, że CDC działa poprawnie, sprawdź status konektora:

```bash
curl -s http://localhost:8083/connectors/inventory-postgres-connector/status | jq .
```

**Oczekiwany wynik:**
`"state": "RUNNING"` dla konektora i jego zadań (`tasks`).

---

## 📡 4. Jak przetestować czy baza loguje WAL i dane wpadają do Kafki?

Przed wykonaniem zapytań HTTP warto przygotować podgląd strumieniowania danych z logów PostgreSQL WAL do Kafki. Dostępne są 3 metody weryfikacji:

### 🏆 Metoda 1: Graficzny interfejs Kafka UI (**REKOMENDOWANA**)

1. Otwórz w przeglądarce interfejs Kafka UI: [http://localhost:8081](http://localhost:8081)
2. Przejdź do sekcji **Topics** w lewym menu.
3. Wybierz temat: `cdc_inventory.public.products` (lub `cdc_inventory.public.categories`).
4. Przejdź do zakładki **Messages**.
5. Zobaczysz wpadające w czasie rzeczywistym zdarzenia z ładunkiem JSON zawierającym obiekt `after` ze zmianami z PostgreSQL WAL!

### 📺 Metoda 2: Podgląd wiadomości Kafki w terminalu (`kafka-console-consumer`)

Uruchom konsumenta Kafki w terminalu przed wywołaniem cURL-i:

```bash
docker compose exec kafka kafka-console-consumer \
  --bootstrap-server localhost:9092 \
  --topic cdc_inventory.public.products \
  --from-beginning
```

Po wysłaniu dowolnego żądania POST/PATCH w drugim oknie konsoli zobaczysz wygenerowane przez Debezium zdarzenie CDC w formacie JSON (`op: "c"` lub `op: "u"`).

### 🔌 Metoda 3: Odpytanie REST API Debezium Connect

Sprawdź stan konektora oraz liczbę przetworzonych zadań:

```bash
curl -s http://localhost:8083/connectors/inventory-postgres-connector/status | jq .
```

---

## 🧪 5. Przetestowanie Scenariuszy Użycia (Manual HTTP Testing)

### 🔹 Scenariusz A: Utworzenie nowej kategorii (`POST /api/category`)

Wyślij żądanie utworzenia kategorii (np. "Elektronika"):

```bash
curl -i -X POST http://localhost:8080/api/category \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Elektronika"
  }'
```

**Oczekiwany wynik (HTTP 201 Created):**
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

### 🔹 Scenariusz B: Utworzenie nowego produktu z przypisaną kategorią (`POST /api/product`)

Wyślij żądanie utworzenia nowego produktu, podając ID utworzonej kategorii:

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

**Oczekiwany wynik (HTTP 201 Created):**
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

### 🔹 Scenariusz C: Zmiana ceny produktu (`PATCH /api/product/{productId}/price`)

> 💡 **Uwaga:** Zastąp `019fc304-df32-71bc-9101-c569ed23bc9c` rzeczywistym identyfikatorem `productId` zwróconym w odpowiedzi w Scenariuszu B!

Zmień cenę utworzonego produktu:

```bash
curl -i -X PATCH http://localhost:8080/api/product/019fc304-df32-71bc-9101-c569ed23bc9c/price \
  -H "Content-Type: application/json" \
  -d '{
    "price": 299.99,
    "currency": "PLN"
  }'
```

**Oczekiwany wynik (HTTP 200 OK):**
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

### 🔹 Scenariusz E: Wyszukiwanie w Modelu Odczytu Meilisearch (`GET /api/products/search/meilisearch`)

Wykonaj odczyt z modelu wyszukiwarki **Meilisearch** zasilanego przez **Change Data Capture (Debezium + Kafka)**:

```bash
# Wywołanie z sformatowanym ładunkiem JSON (przy użyciu python3):
curl -s -X GET "http://localhost:8080/api/products/search/meilisearch?q=Klawiatura" | python3 -m json.tool
```

**Oczekiwany wynik (HTTP 200 OK):**
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

### 🔹 Scenariusz F: Wyszukiwanie w Modelu Odczytu MongoDB (`GET /api/products/search/mongodb`)

Wykonaj odczyt z zdenormalizowanego dokumentowego modelu odczytu **MongoDB** zasilanego w procesie przez **Zdarzenia Domenowe (Domain Events)**:

```bash
# Wywołanie z sformatowanym ładunkiem JSON (przy użyciu python3):
curl -s -X GET "http://localhost:8080/api/products/search/mongodb?q=Wiedźmin" | python3 -m json.tool
```

**Oczekiwany wynik (HTTP 200 OK):**
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

## ⚡ 6. Uwaga dotycząca przetwarzania Zdarzeń (Sync vs Async w Messengerze)

Domyślnie w pliku `config/packages/messenger.yaml` zdarzenia domenowe skierowane są do transportu synchronicznego (`sync`):

```yaml
routing:
    'App\Backend\Domain\Event\ProductCreated': sync
    'App\Backend\Domain\Event\ProductWasPriced': sync
    'App\Backend\Domain\Event\CategoryCreated': sync
    'App\Backend\Domain\Event\CategoryNameChanged': sync
```

**Dlaczego?** Pozwala to na natychmiastowe wykonanie handlerów i weryfikację wyników podczas testów ręcznych bez konieczności oczekiwania na kolejkę.

**Testowanie asynchroniczne (`async`):**
Jeśli chcesz przetestować asynchroniczną obsługę zdarzeń w tle przez worker (`inventory_symfony_worker`), po prostu zamień w `messenger.yaml` wartość `sync` na `async` i przeładuj pamięć podręczną (`cache:clear`). Zdarzenia będą wówczas trafiać do kolejki bazy/Kafki i być konsumowane przez workera w tle.

---

## 🛠️ Przydatne Polecenia Deweloperskie

* **Uruchomienie konsumenta Kafki (Symfony Messenger CDC Worker):**
  ```bash
  docker compose exec app php bin/console messenger:consume kafka_cdc -vv
  ```

* **Podgląd logów dowolnego kontenera:**
  ```bash
  docker compose logs -f app
  docker compose logs -f debezium
  ```

* **Wyczyszczenie pamięci podręcznej Symfony:**
  ```bash
  docker compose exec app php bin/console cache:clear
  ```

* **Zatrzymanie całego środowiska:**
  ```bash
  docker compose down
  ```
