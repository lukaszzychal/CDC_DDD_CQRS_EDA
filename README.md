🌐 **Language / Język:** 🇬🇧 **English** | 🇵🇱 [Polski](README.pl.md)

---

# 🏛️ Architectural Playground: CDC, DDD, CQRS & EDA Showcase

A reference implementation showcase demonstrating the evolution and practical application of **Event-Driven Architecture (EDA)**, **CQRS**, **Tactical Domain-Driven Design (DDD)**, and **Change Data Capture (CDC)** using a modern PHP 8.4 and Symfony 7 tech stack.

---

## 🏛️ Architectural & Design Patterns

### 🔄 CQRS (Command Query Responsibility Segregation)
Strict decoupling of the **Write Path** (Write Model in PostgreSQL 16 via Commands and Aggregates) from the **Read Path** (Read Models optimized for fast query retrieval: Meilisearch & MongoDB).

### 🎯 Tactical DDD (Domain-Driven Design)
* **Aggregate Root (`Product`, `Category`):** Enforces business invariants and transactional consistency boundaries.
* **Value Objects (`ProductId`, `ProductSku`, `Price`, `StockQuantity`):** Immutable value objects with encapsulated self-validation (e.g. price cannot be negative).
* **Domain Events (`ProductCreated`, `ProductWasPriced`, `CategoryCreated`):** Capture irreversible business facts within the domain.
* **Domain Repositories:** Abstract interfaces defined in the domain layer and implemented in the infrastructure layer.

### 📡 CDC (Change Data Capture)
* Non-invasive, real-time database change capture directly from the **PostgreSQL Write-Ahead Log (WAL)** via **Debezium Connect**.
* Streams row-level mutation events to **Apache Kafka** without overhead on application write throughput.

### ⚡ Event-Driven Architecture (EDA) & Eventual Consistency
* Asynchronous feeding of read models via Kafka topics processed by **Symfony Messenger**.
* Read models achieve **Eventual Consistency** after consuming CDC and domain events.

---

## 🛡️ Error Handling & Exception Resilience

* **Domain Exception Pattern:** Explicit, semantic domain exceptions (`InvalidPriceException`, `InvalidStockQuantityException`, `ProductNotFoundException`).
* **Exception Listener / Interceptor:** Centralized HTTP-layer exception interceptor transforming domain exceptions into standard JSON responses compliant with **RFC 7807 (Problem Details)**.
* **Fail-Fast:** Early validation in Value Object constructors and HTTP DTOs before entering business logic or persistence.
* **Dead Letter Queue (DLQ) & Retry Strategy:** Poisoned message handling in queues (**Symfony Messenger / Kafka**), routing failing messages to a dedicated `failed` transport.

---

## 💻 Tech Stack Overview

* **Language & Framework:** PHP 8.4, Symfony 7 (Messenger, Routing, Dependency Injection)
* **Relational Database (Write Model):** PostgreSQL 16
* **CDC Processing Engine:** Debezium Connect
* **Message Broker:** Apache Kafka
* **Read Model #1 (FTS Engine):** Meilisearch
* **Read Model #2 (NoSQL Document Store):** MongoDB 7.0 + Doctrine MongoDB ODM (`doctrine/mongodb-odm-bundle`)
* **Containerization:** Docker & Docker Compose

---

## 📁 Project Showcase Levels

1. 📂 **[Level 1: Inventory Tracker — CRUD + CDC](lvl1_Inventory%20Tracker_CRUD%20+%20CDC/README.md)**
   - Core domain for inventory and category management.
   - Streaming PostgreSQL WAL via Debezium -> Kafka -> Symfony Messenger -> Meilisearch & MongoDB.
   - Complete execution and testing guide: [QUICKSTART.md](lvl1_Inventory%20Tracker_CRUD%20+%20CDC/QUICKSTART.md).

---

## 💡 Engineering Reflection: CDC vs. Domain Events

When architecting modern event-driven applications, tool selection requires trade-off evaluation:

* **In Greenfield Projects:** Using CDC purely for internal application read-model sync can be an **overkill**. CDC introduces boilerplate for parsing raw DB mutation payloads (`before`, `after`, `op`). Publishing native **Domain Events** directly from aggregates is cleaner and more expressible.
* **In Legacy System Integration:** **CDC is an absolute game-changer**. It empowers engineering teams to attach modern EDA microservices to legacy databases with **zero-code intrusion** into legacy monolithic codebases!
