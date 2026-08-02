# 📦 Level 1: Inventory Tracker — CQRS, DDD, CDC & EDA

Projekt demonstracyjny przedstawiający nowoczesną, zaawansowaną architekturę aplikacji internetowej opartej o **CQRS**, **Taktyczne DDD (Domain-Driven Design)**, **CDC (Change Data Capture)** oraz **Event-Driven Architecture (EDA)** z pełną obsługą spójności ostatecznej (**Eventual Consistency**).

---

## 🏛️ Wzorce Architektoniczne i Projektowe

### 1. CQRS (Command Query Responsibility Segregation)
Ścisłe rozdzielenie ścieżki zapisu od ścieżki odczytu w celu optymalizacji wydajności i skalowalności:
* **Write Model (Ścieżka Zapisu):** Relacyjna baza danych **PostgreSQL 16**. Zapis realizowany jest wyłącznie przez Komendy (Commands) i Agregaty domenowe dbające o spójność danych.
* **Read Models (Ścieżka Odczytu):** Modele zoptymalizowane pod konkretny przypadek użycia:
  * **Meilisearch:** Pełnotekstowa wyszukiwarka (Full-Text Search) błyskawicznie zwracająca zapytania wyszukiwania produktów.
  * **MongoDB 7.0:** Zdekompresowany sklep dokumentowy (NoSQL Document Store) zaimplementowany przy użyciu Doctrine MongoDB ODM.

### 2. Taktyczne DDD (Domain-Driven Design)
* **Aggregate Root (Korzeń Agregatu) (`Product`, `Category`):** Pilnuje niezmienników biznesowych i spójności danych w domenie.
* **Value Objects (Obiekty Wartości) (`ProductId`, `ProductSku`, `Price`, `StockQuantity`):** Niemodyfikowalne obiekty z wbudowaną walidacją (np. cena nie może być ujemna, zapas nie może spadać poniżej zera).
* **Domain Events (Zdarzenia Domenowe) (`ProductCreated`, `ProductWasPriced`, `CategoryCreated`):** Odzwierciedlają fakt biznesowy, który bezpowrotnie zaszedł w domenie.
* **Domain Repositories (Interfejsy Repozytoriów):** Zdefiniowane jako abstrakcje w warstwie domeny, zaimplementowane w warstwie infrastruktury.

### 3. CDC (Change Data Capture)
* Bezpośredni, nieinwazyjny odczyt zmian z dziennika bazy danych (**PostgreSQL WAL - Write-Ahead Log**) za pomocą **Debezium Connect**.
* Zmiany są automatycznie przechwytywane na poziomie rejestru bazy i wysyłane do **Apache Kafka** bez obciążania aplikacji synchronicznym zapisem czy dodawaniem dodatkowych wywołań w kodzie aplikacji.

### 4. Event-Driven Architecture (EDA) & Eventual Consistency
* Asynchroniczne zasilanie modeli odczytu (MongoDB & Meilisearch) poprzez komunikaty w Kafce przetwarzone przez **Symfony Messenger**.
* Modele odczytu osiągają **spójność ostateczną (Eventual Consistency)** po przetworzeniu zdarzeń, gwarantując niezrównaną szybkość odpowiedzi po stronie zapisu HTTP.

---

## 🛡️ Wzorce Obsługi Błędów i Wyjątków

### 1. Domain Exception Pattern (Dedykowane Wyjątki Domenowe)
Zamiast generycznych błędów runtime stosowane są jawne i semantyczne wyjątki biznesowe, np. `InvalidPriceException`, `InvalidStockQuantityException`, `ProductNotFoundException`.

### 2. Exception Listener / Interceptor (Centralne Przechwytywanie Błędów)
Wzorzec przechwytywania wyjątków na poziomie warstwy HTTP (Symfony Kernel Listener), zamieniający wyjątki domenowe na spójne i standaryzowane odpowiedzi JSON zgodne ze standardem **RFC 7807 (Problem Details for HTTP APIs)**.

### 3. Fail-Fast (Szybkie Przerwanie przy Błędzie)
Wczesna walidacja danych wewnątrz Konstruktorów Obiektów Wartości (Value Objects) oraz Kontrolerach/DTO przed wykonaniem logiki biznesowej czy zapisem do bazy danych.

### 4. Dead Letter Queue (DLQ) & Retry Strategy
Mechanizm obsługi uszkodzonych lub niezrealizowanych komunikatów w kolejce (**Symfony Messenger / Kafka**). Komunikaty kończące się błędami są poddawane ponownym próbom (Retry), a po przekroczeniu limitu trafiają do dedykowanego transportu awaryjnego `failed` (Dead Letter Queue).

---

## 💻 Stack Technologiczny

| Kategoria | Technologia / Narzędzie | opis |
|---|---|---|
| **Język & Framework** | **PHP 8.4**, **Symfony 7** | Component Messenger, Routing, Dependency Injection, Serializer |
| **Write Model (PostgreSQL)** | **PostgreSQL 16** | Relacyjna baza danych z włączonym `wal_level=logical` |
| **CDC Processing** | **Debezium Connect** | Przechwytywanie zmian z WAL bazy PostgreSQL |
| **Message Broker** | **Apache Kafka** | Kolejkowanie i strumieniowanie komunikatów CDC i zdarzeń |
| **Read Model #1 (FTS)** | **Meilisearch** | Błyskawiczny silnik wyszukiwania pełnotekstowego |
| **Read Model #2 (NoSQL)** | **MongoDB 7.0** | Sklep dokumentowy + `doctrine/mongodb-odm-bundle` |
| **Konteneryzacja & Ops** | **Docker & Docker Compose** | Środowisko uruchomieniowe ze skonfigurowaną siecią i kontenerami |

---

## ⚖️ Refleksja Architektoniczna: CDC vs. Zdarzenia Domenowe

> 💡 **Wniosek Inżynierski & Architektoniczny:**
> 
> W kontekście prostego projektu lub systemu zielonego pola (Greenfield), użycie **CDC (Change Data Capture)** z Debezium i Kafką do aktualizacji Read Modeli w tej samej aplikacji bywa **przerostem formy nad treścią (overkill)**. 
> 
> **Dlaczego?**
> * **Boilerplate i zawiłość parsowania:** Zdarzenia CDC operują na surowym stanie tabel bazy danych (`before`, `after`, `op`). Zamiast czytelnego obiektu domenowego `ProductWasPriced`, trzeba pisać parser i obsługiwać strukturę payloadu Debezium.
> * **Czysty Kod i Zdarzenia Domenowe:** Jeśli masz pełną kontrolę nad kodem aplikacji, znacznie czystszym i prostszym podejściem jest publikowanie **Zdarzeń Domenowych (Domain Events)** bezpośrednio z Agregatów DDD lub szyny aplikacji.
>
> **Kiedy CDC bije konkurencję na głowę?**
> * **Integracja z Legacy Code:** CDC to absolutnie potężne narzędzie, gdy musisz połączyć istniejący, stary system (*Legacy System* - np. monolith bez zdarzeń domenowych, do którego nie chcesz lub nie możesz dopisywać nowego kodu) z nową architekturą mikrousług opartą o EDA.
> * **Zero-Invasive Integration:** Przechwytujesz zmiany bezpośrednio z bazy danych bez dotykania ani jednej linijki legacy kodu!

---

## 🚀 Szybkie Uruchomienie i Testowanie

Szczegółową instrukcję krok po kroku dotyczącą uruchamiania kontenerów Docker, weryfikacji konektorów Debezium oraz gotowych poleceń `curl` / HTTP znajdziesz w pliku:

👉 **[QUICKSTART.md](file:///Users/lukaszzychal/PhpstormProjects/CDC_DDD_CQRS_EDA/lvl1_Inventory%20Tracker_CRUD%20+%20CDC/QUICKSTART.md)**
