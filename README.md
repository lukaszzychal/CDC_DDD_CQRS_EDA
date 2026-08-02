# 🏛️ Architectural Playground: CDC, DDD, CQRS & EDA Showcase

Kompletne repozytorium wzorcowe pokazujące ewolucję i praktyczne zastosowanie architektury napędzanej zdarzeniami (**Event-Driven Architecture**), **CQRS**, **Taktycznego DDD (Domain-Driven Design)** oraz **CDC (Change Data Capture)** przy użyciu nowoczesnego stacku w środowisku PHP 8.4 i Symfony 7.

---

## 🏛️ Wzorce Architektoniczne i Projektowe

### 🔄 CQRS (Command Query Responsibility Segregation)
Ścisłe rozdzielenie ścieżki zapisu (**Write Model** w PostgreSQL 16 poprzez Komendy i Agregaty) od ścieżki odczytu (**Read Models** zoptymalizowane pod szybkie zapytania: Meilisearch oraz MongoDB).

### 🎯 Taktyczne DDD (Domain-Driven Design)
* **Aggregate Root (Korzeń Agregatu) (`Product`, `Category`):** Pilnuje niezmienników biznesowych i spójności danych.
* **Value Objects (Obiekty Wartości) (`ProductId`, `ProductSku`, `Price`, `StockQuantity`):** Niemodyfikowalne obiekty z wbudowaną walidacją (np. cena nie może być ujemna).
* **Domain Events (Zdarzenia Domenowe) (`ProductCreated`, `ProductWasPriced`, `CategoryCreated`):** Odzwierciedlają bezpowrotny fakt biznesowy w domenie.
* **Domain Repositories (Interfejsy Repozytoriów):** Zdefiniowane w warstwie domeny, zaimplementowane w warstwie infrastruktury.

### 📡 CDC (Change Data Capture)
* Bezpośredni odczyt zmian z dziennika bazy danych (**PostgreSQL WAL - Write-Ahead Log**) za pomocą **Debezium Connect**.
* Wysyłanie zdarzeń do **Apache Kafka** bez obciążania aplikacji synchronicznym zapisem.

### ⚡ Event-Driven Architecture (EDA) & Eventual Consistency
* Asynchroniczne zasilanie modeli odczytu poprzez komunikaty w Kafce i **Symfony Messenger**.
* Modele odczytu osiągają spójność ostateczną (*Eventual Consistency*) po przetworzeniu zdarzeń.

---

## 🛡️ Wzorce Obsługi Błędów i Wyjątków

* **Domain Exception Pattern (Dedykowane Wyjątki Domenowe):** Jawne wyjątki biznesowe (`InvalidPriceException`, `InvalidStockQuantityException`, `ProductNotFoundException`).
* **Exception Listener / Interceptor:** Centralne przechwytywanie wyjątków na poziomie HTTP i zamiana ich na odpowiedź JSON zgodną ze standardem **RFC 7807 (Problem Details)**.
* **Fail-Fast:** Wczesna walidacja w obiektach wartości (Value Objects) oraz żądaniach HTTP, zanim dane trafią do bazy lub logiki biznesowej.
* **Dead Letter Queue (DLQ) & Retry Strategy:** Mechanizm obsługi uszkodzonych komunikatów w kolejce (Symfony Messenger / Kafka), izolujący błędne wiadomości do osobnego transportu `failed`.

---

## 💻 Stack Technologiczny

* **Język & Framework:** PHP 8.4, Symfony 7 (Messenger, Routing, Dependency Injection)
* **Relacyjna Baza Danych (Write Model):** PostgreSQL 16
* **Przetwarzanie CDC:** Debezium Connect
* **Broker Wiadomości:** Apache Kafka
* **Read Model #1 (Wyszukiwarka FTS):** Meilisearch
* **Read Model #2 (NoSQL Document Store):** MongoDB 7.0 + Doctrine MongoDB ODM (`doctrine/mongodb-odm-bundle`)
* **Środowisko & Konteneryzacja:** Docker & Docker Compose

---

## 📁 Poziomy Projektu (Project Levels)

1. 📂 **[Poziom 1: Inventory Tracker — CRUD + CDC](file:///Users/lukaszzychal/PhpstormProjects/CDC_DDD_CQRS_EDA/lvl1_Inventory%20Tracker_CRUD%20+%20CDC/README.md)**
   - Bazowy moduł zarządzania produktami i kategoriami.
   - Odczyt WAL z PostgreSQL przez Debezium -> Kafka -> Symfony Messenger -> Meilisearch & MongoDB.
   - Szczegółowa instrukcja uruchomienia: [QUICKSTART.md](file:///Users/lukaszzychal/PhpstormProjects/CDC_DDD_CQRS_EDA/lvl1_Inventory%20Tracker_CRUD%20+%20CDC/QUICKSTART.md).

---

## 💡 Refleksja Architektoniczna: CDC vs Zdarzenia Domenowe

W nowoczesnych projektach warto pamiętać o przemyślanym doborze narzędzi do problemu:

* **W projekcie zielonego pola (Greenfield):** Stosowanie CDC tylko do komunikacji wewnątrz nowo tworzonej aplikacji może okazać się **przerostem formy nad treścią (overkill)**. CDC wymaga sporo boilerplate'u (obsługa surowego payloadu zmian bazy). Zdecydowanie czystszym podejściem jest używanie natywnych **Zdarzeń Domenowych (Domain Events)**.
* **W integracji z Legacy Code:** **CDC to absolutny "game changer"**. Pozwala podpiąć nowoczesne mikroserwisy i architekturę EDA do starych baz danych bez wprowadzania jakichkolwiek zmian w legacy kodzie monolithu!
