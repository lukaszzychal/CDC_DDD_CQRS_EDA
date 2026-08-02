🌐 **Language / Język:** 🇬🇧 [English](README.md) | 🇵🇱 **Polski**

---

# 🏛️ Architectural Playground: CDC, DDD, CQRS & EDA Showcase

Wielopoziomowe repozytorium wzorcowe pokazujące ewolucję, praktyczne wzorce oraz kompromisy inżynieryjne w architekturze napędzanej zdarzeniami (**Event-Driven Architecture**), **CQRS**, **Taktycznym DDD (Domain-Driven Design)** oraz **CDC (Change Data Capture)** przy użyciu nowoczesnego stacku w środowisku PHP 8.4 i Symfony 7.

---

## 🗺️ Mapa Projektu i Poziomy (Showcase Roadmap)

Repozytorium jest ustrukturyzowane jako ścieżka edukacyjna podzielona na moduły/poziomy:

| Poziom | Status | Nazwa Modułu | Główny Koncept & Architektura | Link |
|---|---|---|---|---|
| **Poziom 1** | 🟢 **Aktywny** | **Inventory Tracker (CRUD + CDC)** | CQRS, Taktyczne DDD, Strumieniowanie WAL PostgreSQL przez Debezium + Kafka do Meilisearch (CDC) oraz MongoDB (Zdarzenia Domenowe). | 📂 **[Zobacz README Modułu](lvl1_Inventory%20Tracker_CRUD%20+%20CDC/README.md)** |
| **Poziom 2** | 🟡 *Planowany* | **Real-time Notification System** | Powiadomienia w czasie rzeczywistym, WebSockety, Kafka Consumer Groups oraz Idempotentność konsumentów. | 📂 *[lvl2_Real-time Notification System_Event-Driven]* |
| **Poziom 3** | 🟡 *Planowany* | **Distributed Transaction Manager** | Wzorzec Saga (Orkiestracja vs. Choreografia) do obsługi transakcji rozproszonych i wycofywania zmian. | 📂 *[lvl3_Distributed Transaction_Saga Implementation]* |
| **Poziom 4** | 🟡 *Planowany* | **Custom CDC Middleware** | Middleware do bezinwazyjnej integracji z systemami legacy, własna transformacja schematów i wzbogacanie zdarzeń. | 📂 *[lvl4_Custom CDC Middleware for Legacy Systems]* |

---

## 🏛️ Ogólne Koncepty Architektoniczne w Repozytorium

### 🔄 CQRS (Command Query Responsibility Segregation)
Ścisłe rozdzielenie ścieżki zapisu (**Write Model** w PostgreSQL 16 poprzez Agregaty domenowe) od ścieżek odczytu (**Read Models** zoptymalizowanych pod wyszukiwanie i przechowywanie dokumentów).

### 🎯 Taktyczne DDD (Domain-Driven Design)
Bogate modelowanie domeny przy użyciu **Korzeni Agregatów**, **Niemodyfikowalnych Obiektów Wartości**, **Zdarzeń Domenowych** oraz abstrakcyjnych **Repozytoriów Domenowych**.

### 📡 CDC (Change Data Capture)
Bezinwazyjne przechwytywanie zmian w bazie danych za pomocą **Debezium Connect** czytającego **PostgreSQL Write-Ahead Log (WAL)** i strumieniującego zdarzenia do **Apache Kafka**.

### ⚡ Event-Driven Architecture (EDA) & Eventual Consistency
Asynchroniczne przetwarzanie komunikatów przez **Symfony Messenger** i **Kafkę**, pozwalające osiągnąć spójność ostateczną (*Eventual Consistency*) w odseparowanych mikroserwisach i bazach odczytu.

---

## 💡 Refleksje Architektoniczne: CDC vs. Zdarzenia Domenowe

* **W projektach zielonego pola (Greenfield):** Stosowanie CDC tylko do komunikacji wewnątrz nowo tworzonej aplikacji bywa **przerostem formy nad treścią (overkill)**. CDC wymaga sporo boilerplate'u (obsługa surowych zmian bazy `before`/`after`). Zdecydowanie czystszym podejściem jest używanie natywnych **Zdarzeń Domenowych (Domain Events)** bezpośrednio z kodu.
* **W integracji z Legacy Code:** **CDC to absolutny "game changer"**. Pozwala podpiąć nowoczesne mikroserwisy i architekturę EDA do starych baz danych bez wprowadzania jakichkolwiek zmian w legacy kodzie monolithu!

---

## 💻 Stack Technologiczny

* **Język & Framework:** PHP 8.4, Symfony 7 (Messenger, Routing, Dependency Injection)
* **Relational Database (Write Model):** PostgreSQL 16
* **CDC Engine:** Debezium Connect
* **Message Broker:** Apache Kafka
* **Read Models:** Meilisearch (Full-Text Search) oraz MongoDB 7.0 (NoSQL Document Store + Doctrine ODM)
* **Konteneryzacja:** Docker & Docker Compose
