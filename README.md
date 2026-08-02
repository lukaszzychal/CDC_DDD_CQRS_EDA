🌐 **Language / Język:** 🇬🇧 **English** | 🇵🇱 [Polski](README.pl.md)

---

# 🏛️ Architectural Playground: CDC, DDD, CQRS & EDA Showcase

A multi-level reference repository demonstrating the evolution, practical patterns, and engineering trade-offs of **Event-Driven Architecture (EDA)**, **CQRS**, **Tactical Domain-Driven Design (DDD)**, and **Change Data Capture (CDC)** using modern PHP 8.4 and Symfony 7.

---

## 🗺️ Showcase Roadmap & Project Levels

This repository is structured as a progressive learning path across multiple application levels:

| Level | Status | Module Name | Focus & Architectural Highlights | Link |
|---|---|---|---|---|
| **Level 1** | 🟢 **Active** | **Inventory Tracker (CRUD + CDC)** | CQRS, Tactical DDD, PostgreSQL WAL streaming via Debezium + Kafka to Meilisearch (CDC) & MongoDB (Domain Events). | 📂 **[View Module README](lvl1_Inventory%20Tracker_CRUD%20+%20CDC/README.md)** |
| **Level 2** | 🟡 *Planned* | **Real-time Notification System** | Event-Driven notifications, WebSocket streaming, Kafka Consumer Groups & Idempotent consumers. | 📂 *[lvl2_Real-time Notification System_Event-Driven]* |
| **Level 3** | 🟡 *Planned* | **Distributed Transaction Manager** | Saga Pattern implementation (Orchestration vs. Choreography) for eventual consistency failure recovery. | 📂 *[lvl3_Distributed Transaction_Saga Implementation]* |
| **Level 4** | 🟡 *Planned* | **Custom CDC Middleware** | Non-invasive legacy system integration middleware with custom schema transformation and event enrichment. | 📂 *[lvl4_Custom CDC Middleware for Legacy Systems]* |

---

## 🏛️ General Architectural Concepts Across the Playground

### 🔄 CQRS (Command Query Responsibility Segregation)
Decoupling application write path (**PostgreSQL Write Model** via Domain Aggregates) from query read paths (**Read Models** optimized for search and document storage).

### 🎯 Tactical Domain-Driven Design (DDD)
Rich domain modeling using **Aggregate Roots**, **Immutable Value Objects**, **Domain Events**, and abstract **Domain Repositories**.

### 📡 Change Data Capture (CDC)
Non-invasive database mutation extraction using **Debezium Connect** reading **PostgreSQL Write-Ahead Logs (WAL)** and streaming events to **Apache Kafka**.

### ⚡ Event-Driven Architecture (EDA) & Eventual Consistency
Asynchronous message processing via **Symfony Messenger** and **Kafka**, achieving eventual consistency across decoupled services and read stores.

---

## 💡 Engineering Reflections: CDC vs. Domain Events

* **In Greenfield Projects:** Using CDC purely for internal application read-model synchronization can be an **overkill**. CDC introduces boilerplate for parsing raw DB mutation payloads (`before`, `after`, `op`). Publishing native **Domain Events** directly from aggregates is cleaner and more expressible.
* **In Legacy System Integration:** **CDC is an absolute game-changer**. It empowers engineering teams to attach modern EDA microservices to legacy databases with **zero-code intrusion** into legacy monolithic codebases!

---

## 💻 Tech Stack Overview

* **Language & Framework:** PHP 8.4, Symfony 7 (Messenger, Routing, Dependency Injection)
* **Relational Database (Write Model):** PostgreSQL 16
* **CDC Processing Engine:** Debezium Connect
* **Message Broker:** Apache Kafka
* **Read Models:** Meilisearch (Full-Text Search) & MongoDB 7.0 (Document Store + Doctrine ODM)
* **Containerization:** Docker & Docker Compose
