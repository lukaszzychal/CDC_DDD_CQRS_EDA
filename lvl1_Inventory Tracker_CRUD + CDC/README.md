🌐 **Language / Język:** 🇬🇧 **English** | 🇵🇱 [Polski](README.pl.md)

---

# 📦 Level 1: Inventory Tracker — CQRS, DDD, CDC & EDA

A reference showcase project demonstrating a modern, advanced web application architecture built on **CQRS**, **Tactical Domain-Driven Design (DDD)**, **Change Data Capture (CDC)**, and **Event-Driven Architecture (EDA)** with full support for **Eventual Consistency**.

---

## 🏛️ Architectural & Design Patterns

### 1. CQRS (Command Query Responsibility Segregation)
Strict separation between write path and read path for performance and scalability optimization:
* **Write Model:** Relational database **PostgreSQL 16**. Writes are processed exclusively via Commands and Domain Aggregates enforcing data invariants.
* **Read Models:** Purpose-built models optimized for specific read use cases:
  * **Meilisearch:** Full-Text Search Engine delivering sub-millisecond product query responses.
  * **MongoDB 7.0:** De-normalized Document Store (NoSQL) implemented using Doctrine MongoDB ODM.

### 2. Tactical DDD (Domain-Driven Design)
* **Aggregate Root (`Product`, `Category`):** Guards business invariants and enforces consistency boundaries.
* **Value Objects (`ProductId`, `ProductSku`, `Price`, `StockQuantity`):** Immutable value objects with self-contained validation logic.
* **Domain Events (`ProductCreated`, `ProductWasPriced`, `CategoryCreated`):** Express domain facts that have occurred within the system.
* **Domain Repositories:** Defined as interfaces in the domain layer and implemented in the infrastructure layer.

### 3. CDC (Change Data Capture)
* Non-invasive DB mutation capturing directly from the **PostgreSQL Write-Ahead Log (WAL)** using **Debezium Connect**.
* Changes are automatically published to **Apache Kafka** without placing synchronous write overhead on application processes.

### 4. Event-Driven Architecture (EDA) & Eventual Consistency
* Asynchronous read model ingestion (MongoDB & Meilisearch) via Kafka messages processed by **Symfony Messenger**.
* Read models achieve **Eventual Consistency** post-event processing, providing high HTTP write response speeds.

---

## 🛡️ Exception Handling & Fault Tolerance Patterns

### 1. Domain Exception Pattern
Custom semantic business exceptions (`InvalidPriceException`, `InvalidStockQuantityException`, `ProductNotFoundException`) instead of generic runtime errors.

### 2. Exception Interceptor (Centralized HTTP Exception Listener)
HTTP layer exception listener converting domain exceptions into standardized JSON responses complying with **RFC 7807 (Problem Details for HTTP APIs)**.

### 3. Fail-Fast Pattern
Early validation inside Value Object constructors and DTOs before hitting business logic or DB transactions.

### 4. Dead Letter Queue (DLQ) & Retry Strategy
Failing message isolation mechanism in **Symfony Messenger / Kafka**. Failed messages undergo configured retries before being routed to a dedicated `failed` Dead Letter Queue.

---

## 💻 Tech Stack Overview

| Category | Technology / Tool | Description |
|---|---|---|
| **Language & Framework** | **PHP 8.4**, **Symfony 7** | Messenger Component, Routing, DI Container, Serializer |
| **Write Model** | **PostgreSQL 16** | Relational database with `wal_level=logical` enabled |
| **CDC Engine** | **Debezium Connect** | Streaming WAL log changes from PostgreSQL |
| **Message Broker** | **Apache Kafka** | Event streaming broker for CDC & domain messages |
| **Read Model #1 (FTS)** | **Meilisearch** | High-performance search engine |
| **Read Model #2 (NoSQL)** | **MongoDB 7.0** | Document store + `doctrine/mongodb-odm-bundle` |
| **Ops & Containers** | **Docker & Docker Compose** | Isolated container environment and network topology |

---

## ⚖️ Engineering Trade-Off: CDC vs. Domain Events

> 💡 **Architectural Trade-Off & Conclusion:**
> 
> In greenfield applications or simple CRUD systems, using **CDC (Change Data Capture)** with Debezium and Kafka to update Read Models inside the same domain can be an **overkill**. 
> 
> **Why?**
> * **Boilerplate & Parsing Complexity:** CDC events deal with raw table row states (`before`, `after`, `op`). Instead of clean domain objects like `ProductWasPriced`, developers must write raw payload parsers.
> * **Clean Code & Domain Events:** When you own the codebase, publishing native **Domain Events** directly from Aggregates is significantly cleaner and more maintainable.
>
> **When does CDC shine?**
> * **Legacy System Integration:** CDC is an irreplaceable tool when integrating legacy monoliths (where changing core code is risky/impossible) with new EDA microservices.
> * **Zero-Invasive Integration:** You capture DB changes with **zero code modifications** in the legacy application!

---

## 🚀 Quick Start & Manual Testing

For step-by-step instructions on Docker setup, Debezium connector verification, and cURL requests, see:

👉 **[QUICKSTART.md](QUICKSTART.md)**
