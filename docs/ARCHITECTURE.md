# Solarya Travel - Sistema Prenotazione Catamarani

## Documentazione Architetturale Completa

**Versione:** 1.0  
**Data:** Aprile 2026  
**Autore:** Software Architect  
**Progetto:** Sistema Premium Booking Catamarani

---

## Indice

1. [Architettura Consigliata](#1-architettura-consigliata)
2. [Motivazione della Scelta](#2-motivazione-della-scelta)
3. [Stack Tecnologico](#3-stack-tecnologico)
4. [Moduli Applicativi](#4-moduli-applicativi)
5. [Schema Database](#5-schema-database)
6. [Flusso Prenotazione](#6-flusso-prenotazione)
7. [Flusso Admin](#7-flusso-admin)
8. [Gestione Pagamenti](#8-gestione-pagamenti)
9. [Gestione QR Check-in](#9-gestione-qr-check-in)
10. [Sistema Email](#10-sistema-email)
11. [Requisiti Sicurezza/Privacy](#11-requisiti-sicurezzaprivacy)
12. [Preparazione Fase 2](#12-preparazione-fase-2)
13. [Struttura Cartelle](#13-struttura-cartelle)
14. [Configurazione MAMP](#14-configurazione-mamp)
15. [File .env Esempio](#15-file-env-esempio)
16. [MVP Consigliato](#16-mvp-consigliato)
17. [Funzionalità Future](#17-funzionalità-future)
18. [Rischi Tecnici](#18-rischi-tecnici)

---

## 1. Architettura Consigliata

### Architettura Modulare Monolitica con Laravel

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND LAYER                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │   Public Site   │  │   Admin Panel   │  │  API Endpoints  │  │
│  │   (Livewire)    │  │   (Livewire)    │  │  (REST/JSON)    │  │
│  └────────┬────────┘  └────────┬────────┘  └────────┬────────┘  │
└───────────┼─────────────────────┼─────────────────────┼──────────┘
            │                     │                     │
┌───────────┼─────────────────────┼─────────────────────┼──────────┐
│           ▼                     ▼                     ▼          │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                    APPLICATION LAYER                         │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────────┐ │ │
│  │  │ Services │ │  Actions │ │   DTOs   │ │ Form Requests    │ │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────────────┘ │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                     DOMAIN LAYER                             │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────────┐  │ │
│  │  │  Models  │ │  Events  │ │ Policies │ │  Enums/States  │  │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └────────────────┘  │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                  INFRASTRUCTURE LAYER                        │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────────┐  │ │
│  │  │  Queue   │ │   Mail   │ │ Storage  │ │   Payments     │  │ │
│  │  │  Jobs    │ │ Drivers  │ │ S3/Local │ │ Stripe/PayPal  │  │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └────────────────┘  │ │
│  └─────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
              ┌──────────────────────────────┐
              │          DATABASE            │
              │           MySQL              │
              └──────────────────────────────┘
```

### Pattern Architetturali Adottati

- **Service Layer Pattern**: Logica di business isolata in Services dedicati
- **Action Pattern**: Operazioni atomiche (CreateBookingAction, ProcessPaymentAction)
- **Repository Pattern** (leggero): Per query complesse sul database
- **Event-Driven**: Eventi Laravel per decoupling (es. BookingCreated → SendConfirmationEmail)
- **State Machine**: Per gestione stati prenotazione e pagamento

---

## 2. Motivazione della Scelta

### Perché Laravel + Livewire (e non Laravel + Vue/Inertia)?

| Criterio | Laravel + Livewire | Laravel + Vue/Inertia |
|----------|-------------------|----------------------|
| **Curva apprendimento** | ✅ Bassa, tutto PHP | ⚠️ Richiede JS moderno |
| **Time to market** | ✅ Più veloce | ⚠️ Più setup |
| **SEO friendly** | ✅ SSR nativo | ⚠️ Richiede SSR config |
| **Real-time updates** | ✅ Wire polling/events | ✅ Eccellente |
| **UX premium** | ✅ Con Alpine.js | ✅ Più flessibile |
| **Manutenibilità** | ✅ Unico linguaggio | ⚠️ Due ecosistemi |
| **Calendario interattivo** | ✅ Livewire + libraries | ✅ Vue components |
| **Mobile first** | ✅ Sì | ✅ Sì |

**Raccomandazione: Laravel + Livewire 3 + Alpine.js + TailwindCSS**

**Motivazioni:**

1. **Coerenza stack**: Tutto in PHP, manutenzione semplificata
2. **Livewire 3**: Prestazioni eccellenti, SPA-like experience senza JS framework
3. **Team piccolo**: Meno competenze richieste, più velocità
4. **Luxury UX**: Tailwind + Alpine.js garantiscono animazioni fluide
5. **Funzionalità admin**: Livewire perfetto per dashboard interattive
6. **SEO**: Rendering server-side nativo, cruciale per booking
7. **Future API**: Laravel supporta API REST nativamente per integrazioni

### Alternativa Valida

Se in futuro servisse un'app mobile nativa o SPA molto complessa, si potrebbe:
- Mantenere Livewire per admin
- Aggiungere API JSON + Vue/React per frontend mobile

---

## 3. Stack Tecnologico

### Backend
| Tecnologia | Versione | Scopo |
|------------|----------|-------|
| PHP | 8.3+ | Runtime |
| Laravel | 11.x | Framework |
| MySQL | 8.0+ | Database |
| Redis | 7.x | Cache, Queue, Sessions |
| Composer | 2.x | Dependency Manager |

### Frontend
| Tecnologia | Versione | Scopo |
|------------|----------|-------|
| Livewire | 3.x | Reactive Components |
| Alpine.js | 3.x | Micro-interactions |
| TailwindCSS | 3.4+ | Styling |
| Vite | 5.x | Asset bundling |

### Servizi Esterni
| Servizio | Scopo |
|----------|-------|
| Stripe | Pagamenti carte |
| PayPal | Pagamenti alternativi |
| Mailgun/SES | Email transazionali |
| Cloudflare | CDN, SSL, DDoS protection |
| S3/Spaces | Storage immagini |

### Dev Tools
| Tool | Scopo |
|------|-------|
| Laravel Debugbar | Debug locale |
| Laravel Telescope | Monitoring |
| PHPUnit/Pest | Testing |
| Laravel Pint | Code style |
| Larastan | Static analysis |

---

## 4. Moduli Applicativi

### Struttura Moduli (Domain-Driven)

```
app/
├── Modules/
│   ├── Auth/                    # Autenticazione e registrazione
│   │   ├── Controllers/
│   │   ├── Livewire/
│   │   ├── Services/
│   │   └── Notifications/
│   │
│   ├── Fleet/                   # Gestione flotta catamarani
│   │   ├── Models/              # Catamaran, CatamaranImage
│   │   ├── Services/
│   │   └── Livewire/
│   │
│   ├── Booking/                 # Core prenotazioni
│   │   ├── Models/              # Booking, BookingSlot, BookingSeat
│   │   ├── Services/
│   │   │   ├── BookingService.php
│   │   │   ├── AvailabilityService.php
│   │   │   └── PricingService.php
│   │   ├── Actions/
│   │   │   ├── CreateBookingAction.php
│   │   │   ├── ConfirmBookingAction.php
│   │   │   └── CancelBookingAction.php
│   │   ├── States/              # State machine
│   │   ├── Events/
│   │   └── Livewire/
│   │
│   ├── Availability/            # Gestione disponibilità
│   │   ├── Models/              # TimeSlot, BlockedDate
│   │   ├── Services/
│   │   └── Livewire/
│   │
│   ├── Payment/                 # Pagamenti
│   │   ├── Services/
│   │   │   ├── PaymentService.php
│   │   │   ├── StripeGateway.php
│   │   │   └── PayPalGateway.php
│   │   ├── Controllers/         # Webhooks
│   │   ├── Events/
│   │   └── Models/              # Payment, PaymentAttempt
│   │
│   ├── Addon/                   # Servizi aggiuntivi
│   │   ├── Models/              # Addon, BookingAddon
│   │   └── Services/
│   │
│   ├── CheckIn/                 # QR e check-in
│   │   ├── Services/
│   │   │   ├── QRCodeService.php
│   │   │   └── CheckInService.php
│   │   └── Livewire/
│   │
│   ├── Notification/            # Email e notifiche
│   │   ├── Mail/
│   │   ├── Services/
│   │   └── Jobs/
│   │
│   ├── Review/                  # Recensioni (Fase 2)
│   │   └── ...
│   │
│   └── Admin/                   # Dashboard amministrativa
│       ├── Livewire/
│       │   ├── Dashboard.php
│       │   ├── BookingCalendar.php
│       │   ├── BookingManager.php
│       │   └── FleetManager.php
│       └── Services/
```

### Responsabilità Moduli

| Modulo | Responsabilità Principali |
|--------|--------------------------|
| **Auth** | Login, registrazione, password reset, profilo utente |
| **Fleet** | CRUD catamarani, immagini, specifiche tecniche |
| **Booking** | Creazione, conferma, cancellazione prenotazioni |
| **Availability** | Slot orari, disponibilità, blocchi date |
| **Payment** | Gateway pagamenti, webhook, riconciliazione |
| **Addon** | Servizi extra (pranzo, snorkeling, etc.) |
| **CheckIn** | Generazione QR, validazione, registro imbarchi |
| **Notification** | Template email, code notifiche, scheduling |
| **Admin** | Dashboard, calendario, gestione completa |

---

## 5. Schema Database

### Diagramma ER Semplificato

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────┐
│    users     │       │    bookings      │       │  catamarans  │
├──────────────┤       ├──────────────────┤       ├──────────────┤
│ id           │◄──────│ user_id          │       │ id           │
│ name         │       │ id               │──────►│ name         │
│ email        │       │ catamaran_id     │       │ slug         │
│ password     │       │ booking_type     │       │ description  │
│ phone        │       │ status           │       │ capacity     │
│ role         │       │ start_date       │       │ base_price   │
│ ...          │       │ end_date         │       │ ...          │
└──────────────┘       │ time_slot_id     │       └──────────────┘
                       │ seats_booked     │              │
                       │ is_exclusive     │              │
                       │ total_amount     │       ┌──────┴───────┐
                       │ qr_code          │       │              │
                       │ checked_in_at    │       ▼              ▼
                       │ ...              │  ┌─────────┐  ┌────────────┐
                       └──────────────────┘  │ images  │  │ time_slots │
                              │              └─────────┘  └────────────┘
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
       ┌────────────┐  ┌────────────┐  ┌─────────────┐
       │  payments  │  │booking_addons│ │booking_seats│
       └────────────┘  └────────────┘  └─────────────┘
```

### Schema SQL Dettagliato

```sql
-- =====================================================
-- TABELLA: users
-- Utenti del sistema (clienti e admin)
-- =====================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    role ENUM('customer', 'admin', 'super_admin') DEFAULT 'customer',
    locale VARCHAR(10) DEFAULT 'it',
    avatar_url VARCHAR(500) NULL,
    marketing_consent BOOLEAN DEFAULT FALSE,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: catamarans
-- Flotta di catamarani
-- =====================================================
CREATE TABLE catamarans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    description_short VARCHAR(500) NULL,
    capacity INT UNSIGNED NOT NULL DEFAULT 12,
    length_meters DECIMAL(5,2) NULL,
    features JSON NULL, -- ["wifi", "bar", "sundeck", ...]
    base_price_half_day DECIMAL(10,2) NOT NULL,
    base_price_full_day DECIMAL(10,2) NOT NULL,
    exclusive_price_half_day DECIMAL(10,2) NOT NULL,
    exclusive_price_full_day DECIMAL(10,2) NOT NULL,
    price_per_person_half_day DECIMAL(10,2) NOT NULL,
    price_per_person_full_day DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_catamarans_slug (slug),
    INDEX idx_catamarans_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: catamaran_images
-- Immagini dei catamarani
-- =====================================================
CREATE TABLE catamaran_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    catamaran_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    image_alt VARCHAR(255) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (catamaran_id) REFERENCES catamarans(id) ON DELETE CASCADE,
    INDEX idx_catamaran_images_catamaran (catamaran_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: time_slots
-- Fasce orarie predefinite
-- =====================================================
CREATE TABLE time_slots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- "Mattina", "Pomeriggio"
    slug VARCHAR(100) NOT NULL UNIQUE,
    start_time TIME NOT NULL, -- "09:00:00"
    end_time TIME NOT NULL, -- "13:00:00"
    slot_type ENUM('half_day', 'full_day') NOT NULL,
    price_modifier DECIMAL(5,2) DEFAULT 1.00, -- moltiplicatore prezzo
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_time_slots_type (slot_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: availability
-- Disponibilità per catamarano/giorno/slot
-- =====================================================
CREATE TABLE availability (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    catamaran_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    time_slot_id BIGINT UNSIGNED NULL, -- NULL = giornata intera
    status ENUM('available', 'partially_booked', 'fully_booked', 'blocked') DEFAULT 'available',
    seats_available INT UNSIGNED NOT NULL,
    seats_booked INT UNSIGNED DEFAULT 0,
    is_exclusive_booked BOOLEAN DEFAULT FALSE,
    block_reason VARCHAR(255) NULL,
    custom_price DECIMAL(10,2) NULL, -- override prezzo per questa data
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (catamaran_id) REFERENCES catamarans(id) ON DELETE CASCADE,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE SET NULL,
    
    UNIQUE KEY uk_availability (catamaran_id, date, time_slot_id),
    INDEX idx_availability_date (date),
    INDEX idx_availability_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: blocked_dates
-- Date bloccate (manutenzione, eventi, etc.)
-- =====================================================
CREATE TABLE blocked_dates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    catamaran_id BIGINT UNSIGNED NULL, -- NULL = tutte le barche
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    time_slot_id BIGINT UNSIGNED NULL, -- NULL = tutto il giorno
    reason VARCHAR(255) NULL,
    blocked_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (catamaran_id) REFERENCES catamarans(id) ON DELETE CASCADE,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE SET NULL,
    FOREIGN KEY (blocked_by) REFERENCES users(id),
    
    INDEX idx_blocked_dates_range (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: addons
-- Servizi aggiuntivi
-- =====================================================
CREATE TABLE addons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    price_type ENUM('per_booking', 'per_person', 'per_day') NOT NULL DEFAULT 'per_booking',
    max_quantity INT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    requires_advance_booking BOOLEAN DEFAULT FALSE,
    advance_hours INT UNSIGNED DEFAULT 24,
    image_path VARCHAR(500) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_addons_slug (slug),
    INDEX idx_addons_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: bookings
-- Prenotazioni principali
-- =====================================================
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    booking_number VARCHAR(20) NOT NULL UNIQUE, -- "SLY-2026-00001"
    user_id BIGINT UNSIGNED NOT NULL,
    catamaran_id BIGINT UNSIGNED NOT NULL,
    
    -- Tipologia prenotazione
    booking_type ENUM('seats', 'exclusive') NOT NULL DEFAULT 'seats',
    duration_type ENUM('half_day', 'full_day', 'multi_day') NOT NULL,
    
    -- Date e orari
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    time_slot_id BIGINT UNSIGNED NULL, -- per half_day
    
    -- Posti
    seats_booked INT UNSIGNED NOT NULL DEFAULT 1,
    
    -- Stato prenotazione
    status ENUM(
        'pending',           -- in attesa pagamento
        'confirmed',         -- pagamento ok
        'checked_in',        -- imbarcato
        'completed',         -- viaggio concluso
        'cancelled',         -- cancellato
        'refunded',          -- rimborsato
        'no_show'            -- non presentato
    ) NOT NULL DEFAULT 'pending',
    
    -- Prezzi
    base_amount DECIMAL(10,2) NOT NULL,
    addons_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    
    -- Codice sconto
    discount_code_id BIGINT UNSIGNED NULL,
    
    -- QR Code per check-in
    qr_code VARCHAR(100) NOT NULL UNIQUE,
    qr_code_url VARCHAR(500) NULL,
    
    -- Check-in
    checked_in_at TIMESTAMP NULL,
    checked_in_by BIGINT UNSIGNED NULL,
    
    -- Note
    customer_notes TEXT NULL,
    admin_notes TEXT NULL,
    
    -- Metadati
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    source VARCHAR(50) DEFAULT 'website', -- website, api, admin
    
    -- Timestamps
    confirmed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL, -- scadenza pending
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (catamaran_id) REFERENCES catamarans(id),
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE SET NULL,
    FOREIGN KEY (checked_in_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_bookings_number (booking_number),
    INDEX idx_bookings_user (user_id),
    INDEX idx_bookings_catamaran (catamaran_id),
    INDEX idx_bookings_dates (start_date, end_date),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_qr (qr_code),
    INDEX idx_bookings_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: booking_seats
-- Dettaglio posti per prenotazione
-- =====================================================
CREATE TABLE booking_seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    guest_name VARCHAR(255) NULL,
    guest_email VARCHAR(255) NULL,
    is_primary BOOLEAN DEFAULT FALSE, -- intestatario
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_seats_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: booking_addons
-- Addon associati alla prenotazione
-- =====================================================
CREATE TABLE booking_addons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    addon_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(id),
    INDEX idx_booking_addons_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: payments
-- Pagamenti
-- =====================================================
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    booking_id BIGINT UNSIGNED NOT NULL,
    
    -- Gateway
    gateway ENUM('stripe', 'paypal') NOT NULL,
    gateway_payment_id VARCHAR(255) NULL, -- Stripe PaymentIntent ID
    gateway_transaction_id VARCHAR(255) NULL,
    gateway_customer_id VARCHAR(255) NULL,
    
    -- Importi
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    fee_amount DECIMAL(10,2) DEFAULT 0.00, -- commissioni gateway
    net_amount DECIMAL(10,2) NULL,
    
    -- Stato
    status ENUM(
        'pending',           -- in attesa
        'processing',        -- in elaborazione
        'succeeded',         -- completato
        'failed',            -- fallito
        'cancelled',         -- annullato
        'refunded',          -- rimborsato
        'partially_refunded' -- parzialmente rimborsato
    ) NOT NULL DEFAULT 'pending',
    
    -- Dettagli carta (mascherati)
    card_brand VARCHAR(20) NULL,
    card_last_four VARCHAR(4) NULL,
    
    -- Metadati
    gateway_response JSON NULL,
    failure_reason VARCHAR(500) NULL,
    refund_reason VARCHAR(500) NULL,
    refunded_amount DECIMAL(10,2) DEFAULT 0.00,
    refunded_at TIMESTAMP NULL,
    
    -- Idempotency
    idempotency_key VARCHAR(100) NULL UNIQUE,
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    
    INDEX idx_payments_booking (booking_id),
    INDEX idx_payments_gateway (gateway_payment_id),
    INDEX idx_payments_status (status),
    INDEX idx_payments_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: payment_webhooks
-- Log webhook pagamenti
-- =====================================================
CREATE TABLE payment_webhooks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gateway ENUM('stripe', 'paypal') NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    event_id VARCHAR(255) NOT NULL,
    payload JSON NOT NULL,
    processed BOOLEAN DEFAULT FALSE,
    processed_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_webhook_event (gateway, event_id),
    INDEX idx_webhooks_processed (processed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: discount_codes
-- Codici sconto
-- =====================================================
CREATE TABLE discount_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) NULL,
    max_discount DECIMAL(10,2) NULL,
    usage_limit INT UNSIGNED NULL,
    usage_count INT UNSIGNED DEFAULT 0,
    user_limit INT UNSIGNED DEFAULT 1, -- usi per utente
    valid_from TIMESTAMP NULL,
    valid_until TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_discount_codes_code (code),
    INDEX idx_discount_codes_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: check_ins
-- Registro check-in
-- =====================================================
CREATE TABLE check_ins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    checked_in_by BIGINT UNSIGNED NOT NULL,
    check_in_method ENUM('qr_scan', 'manual', 'app') DEFAULT 'qr_scan',
    device_info VARCHAR(255) NULL,
    location_lat DECIMAL(10,8) NULL,
    location_lng DECIMAL(11,8) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (checked_in_by) REFERENCES users(id),
    INDEX idx_check_ins_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: reviews (Fase 2)
-- =====================================================
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255) NULL,
    comment TEXT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    admin_response TEXT NULL,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY uk_review_booking (booking_id),
    INDEX idx_reviews_user (user_id),
    INDEX idx_reviews_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: email_logs
-- Log email inviate
-- =====================================================
CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    booking_id BIGINT UNSIGNED NULL,
    email_type VARCHAR(100) NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('queued', 'sent', 'delivered', 'bounced', 'failed') DEFAULT 'queued',
    provider_message_id VARCHAR(255) NULL,
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_email_logs_type (email_type),
    INDEX idx_email_logs_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: settings
-- Configurazioni sistema
-- =====================================================
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `group` VARCHAR(100) NOT NULL,
    `key` VARCHAR(100) NOT NULL,
    value TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string',
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_setting (group, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: activity_log
-- Audit trail
-- =====================================================
CREATE TABLE activity_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_name VARCHAR(100) DEFAULT 'default',
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    batch_uuid CHAR(36) NULL,
    event VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_activity_subject (subject_type, subject_id),
    INDEX idx_activity_causer (causer_type, causer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATI INIZIALI
-- =====================================================

-- Time Slots predefiniti
INSERT INTO time_slots (name, slug, start_time, end_time, slot_type, sort_order) VALUES
('Mattina', 'morning', '09:00:00', '13:00:00', 'half_day', 1),
('Pomeriggio', 'afternoon', '14:30:00', '18:30:00', 'half_day', 2),
('Giornata Intera', 'full-day', '09:00:00', '18:30:00', 'full_day', 3);

-- Admin user (password: password)
INSERT INTO users (uuid, name, email, password, role, email_verified_at) VALUES
(UUID(), 'Admin', 'admin@solaryatravel.com', '$2y$12$hashed_password_here', 'super_admin', NOW());

-- Impostazioni base
INSERT INTO settings (`group`, `key`, value, type, is_public) VALUES
('booking', 'advance_booking_hours', '24', 'integer', TRUE),
('booking', 'max_booking_days_ahead', '180', 'integer', TRUE),
('booking', 'pending_expiry_minutes', '30', 'integer', FALSE),
('booking', 'allow_same_day_booking', 'true', 'boolean', TRUE),
('payment', 'tax_rate', '0.22', 'string', FALSE),
('payment', 'default_currency', 'EUR', 'string', TRUE),
('notification', 'review_request_delay_hours', '24', 'integer', FALSE),
('notification', 'reminder_hours_before', '24', 'integer', FALSE);
```

---

## 6. Flusso Prenotazione

### Funnel di Prenotazione (5 step)

```
┌─────────────────────────────────────────────────────────────┐
│  STEP 1: SCOPERTA                                           │
│  ┌─────────────────────────────────────────────────────────┐│
│  │ • Homepage con hero image catamarano                    ││
│  │ • CTA "Prenota la tua esperienza"                       ││
│  │ • Widget ricerca: Data + Persone + Tipo (mezza/intera) ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  STEP 2: SELEZIONE BARCA                                    │
│  ┌─────────────────────────────────────────────────────────┐│
│  │ • Lista catamarani disponibili per data selezionata    ││
│  │ • Filtri: prezzo, capienza, caratteristiche            ││
│  │ • Card con: foto, nome, capienza, prezzo, disponibilità││
│  │ • Badge: "Ultimi X posti", "Più richiesto"             ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  STEP 3: CONFIGURAZIONE PRENOTAZIONE                        │
│  ┌─────────────────────────────────────────────────────────┐│
│  │ • Dettaglio catamarano (gallery, descrizione, features)││
│  │ • Calendario disponibilità                              ││
│  │ • Selezione: posti singoli VS intera barca             ││
│  │ • Se multi-day: selettore date range                   ││
│  │ • Selezione addon con quantità                          ││
│  │ • Riepilogo prezzo dinamico                             ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  STEP 4: DATI E CHECKOUT                                    │
│  ┌─────────────────────────────────────────────────────────┐│
│  │ • Login/Registrazione (se non loggato)                 ││
│  │ • Form dati partecipanti (per ogni posto)              ││
│  │ • Campo note cliente                                    ││
│  │ • Inserimento codice sconto                             ││
│  │ • Riepilogo ordine completo                             ││
│  │ • Selezione metodo pagamento: Stripe / PayPal           ││
│  │ • Checkbox accettazione termini                         ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  STEP 5: CONFERMA                                           │
│  ┌─────────────────────────────────────────────────────────┐│
│  │ • Pagina conferma con numero prenotazione              ││
│  │ • QR Code visibile                                      ││
│  │ • Riepilogo completo                                    ││
│  │ • CTA: "Scarica PDF", "Aggiungi a calendario"          ││
│  │ • Email conferma inviata automaticamente                ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
```

### Diagramma di Sequenza - Prenotazione

```
┌──────┐     ┌──────────┐     ┌────────────┐     ┌─────────┐     ┌────────┐
│Client│     │BookingCtrl│     │BookingSvc  │     │PaymentSvc│     │Database│
└──┬───┘     └────┬─────┘     └─────┬──────┘     └────┬────┘     └───┬────┘
   │              │                  │                 │              │
   │ selectDate() │                  │                 │              │
   │─────────────>│                  │                 │              │
   │              │ checkAvailability│                 │              │
   │              │─────────────────>│                 │              │
   │              │                  │ query availability             │
   │              │                  │────────────────────────────────>│
   │              │                  │<────────────────────────────────│
   │              │<─────────────────│                 │              │
   │<─────────────│                  │                 │              │
   │              │                  │                 │              │
   │ submitBooking│                  │                 │              │
   │─────────────>│                  │                 │              │
   │              │ createBooking()  │                 │              │
   │              │─────────────────>│                 │              │
   │              │                  │ validateAvailability()         │
   │              │                  │────────────────────────────────>│
   │              │                  │ lockSeats()     │              │
   │              │                  │────────────────────────────────>│
   │              │                  │ generateQRCode()│              │
   │              │                  │─────┐           │              │
   │              │                  │<────┘           │              │
   │              │                  │ createPending() │              │
   │              │                  │────────────────────────────────>│
   │              │<─────────────────│                 │              │
   │              │                  │                 │              │
   │              │ initiatePayment()│                 │              │
   │              │─────────────────────────────────────>              │
   │              │                  │                 │ createIntent │
   │              │                  │                 │──────────────>│
   │              │<─────────────────────────────────────              │
   │<─────────────│ (payment form)   │                 │              │
   │              │                  │                 │              │
   │ confirmPayment                  │                 │              │
   │─────────────>│ (via Stripe/PayPal)                │              │
   │              │                  │                 │              │
   │              │                  │                 │ webhook      │
   │              │                  │                 │<─────────────│
   │              │                  │ confirmBooking()│              │
   │              │                  │<────────────────│              │
   │              │                  │ update status   │              │
   │              │                  │────────────────────────────────>│
   │              │                  │ dispatch events │              │
   │              │                  │─────┐           │              │
   │              │                  │<────┘ SendConfirmationEmail    │
   │              │                  │       UpdateAvailability       │
   │<─────────────│ (confirmation page)                │              │
```

### Logica Prenotazione - Pseudocodice

```php
// BookingService.php

public function createBooking(CreateBookingDTO $dto): Booking
{
    return DB::transaction(function () use ($dto) {
        // 1. Verifica disponibilità con lock pessimistico
        $availability = $this->availabilityService
            ->checkAndLock($dto->catamaranId, $dto->dates, $dto->timeSlotId, $dto->seats);
        
        if (!$availability->isAvailable) {
            throw new BookingNotAvailableException($availability->reason);
        }
        
        // 2. Calcola prezzo totale
        $pricing = $this->pricingService->calculate(
            catamaran: $dto->catamaran,
            dates: $dto->dates,
            timeSlot: $dto->timeSlot,
            seats: $dto->seats,
            isExclusive: $dto->isExclusive,
            addons: $dto->addons,
            discountCode: $dto->discountCode
        );
        
        // 3. Crea prenotazione in stato pending
        $booking = Booking::create([
            'booking_number' => $this->generateBookingNumber(),
            'user_id' => $dto->userId,
            'catamaran_id' => $dto->catamaranId,
            'booking_type' => $dto->isExclusive ? 'exclusive' : 'seats',
            'duration_type' => $dto->durationType,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'time_slot_id' => $dto->timeSlotId,
            'seats_booked' => $dto->seats,
            'status' => BookingStatus::PENDING,
            'base_amount' => $pricing->baseAmount,
            'addons_amount' => $pricing->addonsAmount,
            'discount_amount' => $pricing->discountAmount,
            'tax_amount' => $pricing->taxAmount,
            'total_amount' => $pricing->totalAmount,
            'qr_code' => $this->qrCodeService->generateUniqueCode(),
            'expires_at' => now()->addMinutes(config('booking.pending_expiry_minutes')),
        ]);
        
        // 4. Crea record posti
        foreach ($dto->guests as $guest) {
            $booking->seats()->create([
                'guest_name' => $guest->name,
                'guest_email' => $guest->email,
                'is_primary' => $guest->isPrimary,
            ]);
        }
        
        // 5. Collega addon
        foreach ($dto->addons as $addon) {
            $booking->addons()->create([
                'addon_id' => $addon->id,
                'quantity' => $addon->quantity,
                'unit_price' => $addon->price,
                'total_price' => $addon->price * $addon->quantity,
            ]);
        }
        
        // 6. Aggiorna disponibilità (prenotazione temporanea)
        $this->availabilityService->reserveSeats(
            $dto->catamaranId,
            $dto->dates,
            $dto->timeSlotId,
            $dto->seats,
            $dto->isExclusive
        );
        
        // 7. Genera QR Code immagine
        $booking->update([
            'qr_code_url' => $this->qrCodeService->generateImage($booking->qr_code)
        ]);
        
        // 8. Event per pulizia se non completato
        BookingCreated::dispatch($booking);
        
        return $booking;
    });
}
```

---

## 7. Flusso Admin

### Dashboard Admin - Sezioni

```
┌─────────────────────────────────────────────────────────────────────┐
│  SIDEBAR                          │  CONTENT AREA                    │
│  ┌─────────────────────────────┐  │  ┌─────────────────────────────┐ │
│  │ 📊 Dashboard               │  │  │                             │ │
│  │ 📅 Calendario              │  │  │                             │ │
│  │ 📋 Prenotazioni            │  │  │                             │ │
│  │ ⚓ Flotta                  │  │  │                             │ │
│  │ 👥 Utenti                  │  │  │                             │ │
│  │ 🎁 Addon                   │  │  │                             │ │
│  │ 💰 Pagamenti               │  │  │                             │ │
│  │ 📱 Check-in                │  │  │                             │ │
│  │ 📧 Email                   │  │  │                             │ │
│  │ ⚙️ Impostazioni            │  │  │                             │ │
│  │ 📈 Report                  │  │  │                             │ │
│  └─────────────────────────────┘  │  │                             │ │
│                                   │  └─────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### Funzionalità Admin Dettagliate

#### 1. Dashboard
- KPI principali: prenotazioni oggi, settimana, mese
- Revenue totale e trend
- Occupancy rate per barca
- Prossimi imbarchi
- Ultimi pagamenti
- Alert: pagamenti falliti, overbooking

#### 2. Calendario Prenotazioni (Livewire Component)
```php
// Livewire/Admin/BookingCalendar.php

// Vista: Settimana / Mese / Timeline
// Colonne: Catamarani
// Righe: Giorni + Slot orari
// Celle: Prenotazioni con stato (colori)
// Drag & drop: Riallocazione
// Click: Dettaglio prenotazione
```

#### 3. Gestione Prenotazioni
- Lista filtrata per stato, data, barca
- Dettaglio prenotazione completo
- Cambio stato manuale
- Riallocazione su altra barca
- Invio email manuale
- Rimborso parziale/totale
- Note admin

#### 4. Scanner QR (PWA-ready)
```javascript
// Utilizza libreria html5-qrcode
// Scansione camera dispositivo
// Validazione real-time via API
// Feedback sonoro/visivo
// Registro imbarchi
```

---

## 8. Gestione Pagamenti

### Architettura Payment Gateway

```
┌──────────────────────────────────────────────────────────────┐
│                     PaymentService                            │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                    PaymentGateway                        │ │
│  │                    (Interface)                           │ │
│  └────────────────────────┬────────────────────────────────┘ │
│                           │                                   │
│           ┌───────────────┼───────────────┐                  │
│           ▼               ▼               ▼                  │
│  ┌────────────────┐ ┌────────────────┐ ┌────────────────┐   │
│  │ StripeGateway  │ │ PayPalGateway  │ │ MockGateway    │   │
│  │                │ │                │ │ (testing)      │   │
│  └────────────────┘ └────────────────┘ └────────────────┘   │
└──────────────────────────────────────────────────────────────┘
```

### Flusso Pagamento Stripe

```php
// 1. Creazione PaymentIntent
public function createPaymentIntent(Booking $booking): PaymentIntent
{
    // Genera chiave idempotenza
    $idempotencyKey = 'booking_' . $booking->uuid . '_' . time();
    
    $intent = $this->stripe->paymentIntents->create([
        'amount' => $booking->total_amount * 100, // centesimi
        'currency' => 'eur',
        'customer' => $this->getOrCreateCustomer($booking->user),
        'metadata' => [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
        ],
        'description' => "Prenotazione {$booking->booking_number}",
        'receipt_email' => $booking->user->email,
    ], [
        'idempotency_key' => $idempotencyKey,
    ]);
    
    // Salva payment record
    Payment::create([
        'booking_id' => $booking->id,
        'gateway' => 'stripe',
        'gateway_payment_id' => $intent->id,
        'amount' => $booking->total_amount,
        'status' => PaymentStatus::PENDING,
        'idempotency_key' => $idempotencyKey,
    ]);
    
    return $intent;
}

// 2. Webhook Handler
public function handleWebhook(Request $request): Response
{
    $payload = $request->getContent();
    $signature = $request->header('Stripe-Signature');
    
    try {
        $event = Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    } catch (\Exception $e) {
        return response('Invalid signature', 400);
    }
    
    // Log webhook
    PaymentWebhook::create([
        'gateway' => 'stripe',
        'event_type' => $event->type,
        'event_id' => $event->id,
        'payload' => $event->data,
    ]);
    
    // Processa evento
    match ($event->type) {
        'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
        'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
        'charge.refunded' => $this->handleRefund($event->data->object),
        default => null,
    };
    
    return response('OK', 200);
}
```

### Stati Pagamento e Prenotazione

```
BOOKING STATUS           PAYMENT STATUS
──────────────           ──────────────
pending          ◄────── pending
    │                        │
    │                        ▼
    │                    processing
    │                        │
    ▼                        ▼
confirmed        ◄────── succeeded
    │
    ├───► checked_in
    │
    ├───► completed
    │
    ├───► cancelled ◄──── cancelled
    │
    └───► refunded   ◄──── refunded
```

### Protezioni Implementate

1. **Idempotenza**: Ogni richiesta pagamento ha chiave unica
2. **Webhook verification**: Firma verificata Stripe/PayPal
3. **Lock pessimistico**: Previene double-booking
4. **Timeout pending**: Prenotazioni pending scadono dopo 30 min
5. **Reconciliation job**: Verifica giornaliera stato pagamenti
6. **Audit log**: Ogni transazione tracciata

---

## 9. Gestione QR Check-in

### Generazione QR Code

```php
// QRCodeService.php

use SimpleSoftwareIO\QrCode\Facades\QrCode;

public function generateUniqueCode(): string
{
    do {
        $code = strtoupper(Str::random(12));
    } while (Booking::where('qr_code', $code)->exists());
    
    return $code;
}

public function generateImage(string $code): string
{
    $checkInUrl = route('checkin.verify', ['code' => $code]);
    
    $qrImage = QrCode::format('png')
        ->size(300)
        ->errorCorrection('H')
        ->generate($checkInUrl);
    
    $filename = "qrcodes/{$code}.png";
    Storage::disk('public')->put($filename, $qrImage);
    
    return Storage::disk('public')->url($filename);
}
```

### Validazione Check-in

```php
// CheckInService.php

public function validateAndCheckIn(string $qrCode, User $operator): CheckInResult
{
    $booking = Booking::where('qr_code', $qrCode)
        ->with(['catamaran', 'user', 'seats'])
        ->first();
    
    if (!$booking) {
        return new CheckInResult(
            success: false,
            message: 'QR Code non valido'
        );
    }
    
    if ($booking->status === BookingStatus::CHECKED_IN) {
        return new CheckInResult(
            success: false,
            message: 'Check-in già effettuato',
            booking: $booking
        );
    }
    
    if ($booking->status !== BookingStatus::CONFIRMED) {
        return new CheckInResult(
            success: false,
            message: "Prenotazione non confermata (stato: {$booking->status->label()})",
            booking: $booking
        );
    }
    
    if (!$booking->start_date->isToday()) {
        return new CheckInResult(
            success: false,
            message: "Prenotazione per il {$booking->start_date->format('d/m/Y')}",
            booking: $booking
        );
    }
    
    // Esegui check-in
    $booking->update([
        'status' => BookingStatus::CHECKED_IN,
        'checked_in_at' => now(),
        'checked_in_by' => $operator->id,
    ]);
    
    // Log check-in
    CheckIn::create([
        'booking_id' => $booking->id,
        'checked_in_by' => $operator->id,
        'check_in_method' => 'qr_scan',
    ]);
    
    // Event
    BookingCheckedIn::dispatch($booking);
    
    return new CheckInResult(
        success: true,
        message: 'Check-in completato!',
        booking: $booking
    );
}
```

### UI Scanner (Livewire Component)

```php
// Livewire/Admin/QRScanner.php

class QRScanner extends Component
{
    public ?string $scannedCode = null;
    public ?Booking $booking = null;
    public ?string $message = null;
    public string $messageType = 'info';
    
    #[On('qr-scanned')]
    public function handleScan(string $code): void
    {
        $result = app(CheckInService::class)
            ->validateAndCheckIn($code, auth()->user());
        
        $this->booking = $result->booking;
        $this->message = $result->message;
        $this->messageType = $result->success ? 'success' : 'error';
        
        // Feedback sonoro
        $this->dispatch($result->success ? 'play-success' : 'play-error');
    }
    
    public function render()
    {
        return view('livewire.admin.qr-scanner');
    }
}
```

---

## 10. Sistema Email

### Template Email Previsti

| Template | Trigger | Contenuto |
|----------|---------|-----------|
| `welcome` | Registrazione | Benvenuto, verifica email |
| `booking-pending` | Creazione prenotazione | Riepilogo, link pagamento |
| `booking-confirmed` | Pagamento ok | Conferma, QR code, dettagli |
| `payment-receipt` | Pagamento ok | Ricevuta dettagliata |
| `reminder` | 24h prima | Promemoria, indicazioni |
| `review-request` | 24h dopo viaggio | Link recensione |
| `booking-cancelled` | Cancellazione | Conferma cancellazione |
| `refund-processed` | Rimborso | Dettagli rimborso |

### Struttura Email Laravel

```php
// Mail/BookingConfirmed.php

class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public function __construct(
        public Booking $booking
    ) {}
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Prenotazione Confermata - {$this->booking->booking_number}",
        );
    }
    
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.confirmed',
            with: [
                'booking' => $this->booking,
                'catamaran' => $this->booking->catamaran,
                'user' => $this->booking->user,
                'qrCodeUrl' => $this->booking->qr_code_url,
            ],
        );
    }
    
    public function attachments(): array
    {
        return [
            Attachment::fromStorage("qrcodes/{$this->booking->qr_code}.png")
                ->as('qrcode.png')
                ->withMime('image/png'),
        ];
    }
}
```

### Scheduler Jobs

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule): void
{
    // Pulizia prenotazioni pending scadute
    $schedule->command('bookings:cleanup-expired')
        ->everyFiveMinutes();
    
    // Invio promemoria 24h prima
    $schedule->command('bookings:send-reminders')
        ->dailyAt('08:00');
    
    // Richiesta recensioni (24h dopo viaggio)
    $schedule->command('reviews:request')
        ->dailyAt('10:00');
    
    // Riconciliazione pagamenti
    $schedule->command('payments:reconcile')
        ->dailyAt('02:00');
    
    // Aggiorna stato prenotazioni completate
    $schedule->command('bookings:mark-completed')
        ->dailyAt('23:00');
}
```

---

## 11. Requisiti Sicurezza/Privacy

### Sicurezza Implementata

| Area | Misura | Implementazione |
|------|--------|-----------------|
| **Autenticazione** | Password hashing | bcrypt, 12 rounds |
| | Rate limiting | 5 tentativi/minuto |
| | 2FA | (Fase 2) |
| **Autorizzazione** | Policies Laravel | Per ogni risorsa |
| | Role-based | customer, admin, super_admin |
| **Dati** | Encryption at rest | encrypted cast |
| | Sanitization | Form Requests |
| **Pagamenti** | PCI DSS | Tokenizzazione Stripe |
| | Webhook verification | Signature check |
| **API** | CSRF protection | Token middleware |
| | CORS | Configurato |
| **Sessioni** | Secure cookies | HttpOnly, SameSite |
| | Session fixation | Regenerate on login |
| **Headers** | Security headers | CSP, X-Frame-Options |
| **Logging** | Audit trail | Spatie Activity Log |
| **Backup** | Database backup | Daily encrypted |

### GDPR Compliance

```php
// Privacy features

// 1. Consenso esplicito
'marketing_consent' => 'required|boolean',

// 2. Diritto all'oblio
public function deleteAccount(User $user): void
{
    // Anonimizza prenotazioni storiche
    $user->bookings()->update([
        'customer_notes' => null,
    ]);
    
    // Anonimizza dati utente
    $user->update([
        'name' => 'Utente Eliminato',
        'email' => "deleted_{$user->id}@example.com",
        'phone' => null,
    ]);
    
    $user->delete(); // soft delete
}

// 3. Export dati
public function exportUserData(User $user): array
{
    return [
        'profile' => $user->toArray(),
        'bookings' => $user->bookings->toArray(),
        'payments' => $user->payments->toArray(),
    ];
}

// 4. Cookie consent
// Integrazione con cookie banner (es. CookieYes)
```

---

## 12. Preparazione Fase 2

### Architettura Multilingua (Predisposta)

```php
// config/app.php
'locale' => 'it',
'fallback_locale' => 'en',
'available_locales' => ['it', 'en', 'de'],

// Middleware SetLocale
// Salvataggio preferenza utente
// URL prefixing opzionale (/en/booking)

// Struttura traduzioni
resources/
└── lang/
    ├── it/
    │   ├── messages.php
    │   ├── booking.php
    │   └── emails.php
    ├── en/
    │   └── ...
    └── de/
        └── ...

// Database: JSON fields per contenuti tradotti
'description' => ['it' => '...', 'en' => '...']
// O tabella translations separata
```

### Integrazione GetYourGuide (Predisposta)

```php
// Interfaces/ExternalBookingProvider.php
interface ExternalBookingProvider
{
    public function syncProducts(): Collection;
    public function createBooking(BookingDTO $dto): ExternalBooking;
    public function cancelBooking(string $externalId): bool;
    public function getAvailability(string $productId, Carbon $date): Availability;
}

// Services/GetYourGuideService.php (stub)
class GetYourGuideService implements ExternalBookingProvider
{
    // Implementazione API GetYourGuide
    // Webhook per prenotazioni esterne
    // Sync bidirezionale disponibilità
}

// Database: external_bookings table
// - booking_id (nullable, se prenotazione interna)
// - external_provider
// - external_booking_id
// - external_product_id
// - synced_at
```

### Feature Flags

```php
// Utilizzo Laravel Pennant o config-based
'features' => [
    'multi_language' => false,
    'getyourguide_integration' => false,
    'reviews' => false,
    'mobile_app_api' => false,
];

// Nel codice
if (Feature::active('multi_language')) {
    // mostra language switcher
}
```

---

## 13. Struttura Cartelle

```
solaryatravel/
├── app/
│   ├── Actions/                    # Single-action classes
│   │   ├── Booking/
│   │   │   ├── CreateBookingAction.php
│   │   │   ├── ConfirmBookingAction.php
│   │   │   └── CancelBookingAction.php
│   │   └── Payment/
│   │       └── ProcessPaymentAction.php
│   │
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── CleanupExpiredBookings.php
│   │   │   ├── SendBookingReminders.php
│   │   │   └── ReconcilePayments.php
│   │   └── Kernel.php
│   │
│   ├── DTOs/                       # Data Transfer Objects
│   │   ├── CreateBookingDTO.php
│   │   ├── PricingResultDTO.php
│   │   └── CheckInResultDTO.php
│   │
│   ├── Enums/
│   │   ├── BookingStatus.php
│   │   ├── BookingType.php
│   │   ├── DurationType.php
│   │   ├── PaymentStatus.php
│   │   └── UserRole.php
│   │
│   ├── Events/
│   │   ├── BookingCreated.php
│   │   ├── BookingConfirmed.php
│   │   ├── BookingCancelled.php
│   │   ├── BookingCheckedIn.php
│   │   └── PaymentProcessed.php
│   │
│   ├── Exceptions/
│   │   ├── BookingNotAvailableException.php
│   │   ├── PaymentFailedException.php
│   │   └── InvalidQRCodeException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                # API Controllers (future)
│   │   │   ├── Auth/
│   │   │   ├── BookingController.php
│   │   │   ├── CatamaranController.php
│   │   │   ├── WebhookController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       └── ...
│   │   │
│   │   ├── Middleware/
│   │   │   ├── SetLocale.php
│   │   │   └── EnsureUserIsAdmin.php
│   │   │
│   │   └── Requests/
│   │       ├── StoreBookingRequest.php
│   │       └── ...
│   │
│   ├── Livewire/
│   │   ├── Public/
│   │   │   ├── BookingWizard.php
│   │   │   ├── CatamaranList.php
│   │   │   ├── AvailabilityCalendar.php
│   │   │   └── CheckoutForm.php
│   │   │
│   │   └── Admin/
│   │       ├── Dashboard.php
│   │       ├── BookingCalendar.php
│   │       ├── BookingManager.php
│   │       ├── QRScanner.php
│   │       ├── FleetManager.php
│   │       └── ...
│   │
│   ├── Listeners/
│   │   ├── SendBookingConfirmation.php
│   │   ├── UpdateAvailability.php
│   │   └── LogActivity.php
│   │
│   ├── Mail/
│   │   ├── WelcomeMail.php
│   │   ├── BookingConfirmed.php
│   │   ├── BookingReminder.php
│   │   └── ReviewRequest.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Catamaran.php
│   │   ├── CatamaranImage.php
│   │   ├── Booking.php
│   │   ├── BookingSeat.php
│   │   ├── BookingAddon.php
│   │   ├── TimeSlot.php
│   │   ├── Availability.php
│   │   ├── BlockedDate.php
│   │   ├── Addon.php
│   │   ├── Payment.php
│   │   ├── PaymentWebhook.php
│   │   ├── DiscountCode.php
│   │   ├── CheckIn.php
│   │   ├── Review.php
│   │   ├── Setting.php
│   │   └── EmailLog.php
│   │
│   ├── Notifications/
│   │   └── ...
│   │
│   ├── Policies/
│   │   ├── BookingPolicy.php
│   │   ├── CatamaranPolicy.php
│   │   └── UserPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── PaymentServiceProvider.php
│   │
│   └── Services/
│       ├── BookingService.php
│       ├── AvailabilityService.php
│       ├── PricingService.php
│       ├── QRCodeService.php
│       ├── CheckInService.php
│       └── Payment/
│           ├── PaymentService.php
│           ├── PaymentGateway.php (interface)
│           ├── StripeGateway.php
│           └── PayPalGateway.php
│
├── bootstrap/
│
├── config/
│   ├── booking.php              # Config prenotazioni
│   ├── payment.php              # Config pagamenti
│   └── ...
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── CatamaranFactory.php
│   │   └── BookingFactory.php
│   │
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_catamarans_table.php
│   │   ├── 0003_create_time_slots_table.php
│   │   ├── 0004_create_availability_table.php
│   │   ├── 0005_create_addons_table.php
│   │   ├── 0006_create_bookings_table.php
│   │   ├── 0007_create_payments_table.php
│   │   └── ...
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── TimeSlotsSeeder.php
│       ├── CatamaranSeeder.php
│       └── SettingsSeeder.php
│
├── docs/
│   ├── ARCHITECTURE.md          # Questo documento
│   ├── API.md
│   └── DEPLOYMENT.md
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── build/                   # Vite output
│
├── resources/
│   ├── css/
│   │   └── app.css              # Tailwind
│   │
│   ├── js/
│   │   ├── app.js
│   │   └── alpine-components/
│   │
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── guest.blade.php
│   │   │
│   │   ├── components/
│   │   │   ├── booking/
│   │   │   └── ui/
│   │   │
│   │   ├── livewire/
│   │   │   ├── public/
│   │   │   └── admin/
│   │   │
│   │   ├── pages/               # Pagine statiche
│   │   │   ├── home.blade.php
│   │   │   └── ...
│   │   │
│   │   ├── emails/
│   │   │   ├── booking/
│   │   │   │   ├── confirmed.blade.php
│   │   │   │   └── reminder.blade.php
│   │   │   └── ...
│   │   │
│   │   └── admin/
│   │       └── ...
│   │
│   └── lang/
│       ├── it/
│       └── en/
│
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── admin.php
│   └── webhooks.php
│
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── qrcodes/         # QR codes generati
│   └── ...
│
├── tests/
│   ├── Feature/
│   │   ├── BookingTest.php
│   │   ├── PaymentTest.php
│   │   └── CheckInTest.php
│   │
│   └── Unit/
│       ├── PricingServiceTest.php
│       └── AvailabilityServiceTest.php
│
├── .env.example
├── .gitignore
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## 14. Configurazione MAMP

### Setup MAMP su macOS

#### 1. Configurazione Host Virtuale

**File:** `/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf`

```apache
<VirtualHost *:8890>
    ServerName solaryatravel
    DocumentRoot "/Users/claudio/DEVELOPMENT/solaryatravel/public"
    
    <Directory "/Users/claudio/DEVELOPMENT/solaryatravel/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "/Applications/MAMP/logs/solaryatravel-error.log"
    CustomLog "/Applications/MAMP/logs/solaryatravel-access.log" combined
</VirtualHost>
```

#### 2. Modifica file hosts

**File:** `/etc/hosts`

```
127.0.0.1       solaryatravel
```

#### 3. Abilita Virtual Hosts

**File:** `/Applications/MAMP/conf/apache/httpd.conf`

Decommenta la riga:
```apache
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```

#### 4. Configurazione PHP (MAMP)

**File:** `/Applications/MAMP/bin/php/php8.3.x/conf/php.ini`

```ini
; Aumenta limiti per upload immagini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M

; Estensioni necessarie
extension=pdo_mysql
extension=mbstring
extension=gd
extension=zip
extension=intl
```

#### 5. Creazione Database

```sql
-- Esegui in phpMyAdmin o MySQL CLI
CREATE DATABASE solaryatravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'genericUsr'@'localhost' IDENTIFIED BY 'Password08$';
GRANT ALL PRIVILEGES ON solaryatravel.* TO 'genericUsr'@'localhost';
FLUSH PRIVILEGES;
```

#### 6. SSL Locale (opzionale, per HTTPS)

Per testing HTTPS locale con MAMP PRO:
- Vai in MAMP PRO → Host → SSL
- Abilita SSL e usa certificato self-signed
- Aggiungi certificato al Keychain macOS come "trusted"

---

## 15. File .env Esempio

```env
#######################################
# SOLARYA TRAVEL - CONFIGURAZIONE LOCALE
# Ambiente: MAMP su macOS
#######################################

APP_NAME="Solarya Travel"
APP_ENV=local
APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
APP_DEBUG=true
APP_TIMEZONE=Europe/Rome
APP_URL=https://solaryatravel:8890

APP_LOCALE=it
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=it_IT

#######################################
# DATABASE - MAMP MySQL
#######################################
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=solaryatravel
DB_USERNAME=genericUsr
DB_PASSWORD=Password08$
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

#######################################
# CACHE & SESSION
#######################################
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Per sviluppo, file driver
# In produzione: redis
QUEUE_CONNECTION=sync
# QUEUE_CONNECTION=database

#######################################
# MAIL - Locale con Mailpit/MailHog
#######################################
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@solaryatravel.com"
MAIL_FROM_NAME="${APP_NAME}"

# Per testing email reali (Mailtrap)
# MAIL_HOST=sandbox.smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=your_mailtrap_user
# MAIL_PASSWORD=your_mailtrap_pass

#######################################
# STRIPE - TEST MODE
#######################################
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxx

#######################################
# PAYPAL - SANDBOX
#######################################
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_sandbox_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_sandbox_client_secret
PAYPAL_LIVE_CLIENT_ID=
PAYPAL_LIVE_CLIENT_SECRET=

#######################################
# FILE STORAGE
#######################################
FILESYSTEM_DISK=public

# Per produzione con S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=eu-south-1
# AWS_BUCKET=solaryatravel-assets
# AWS_URL=

#######################################
# LOGGING
#######################################
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

#######################################
# TELESCOPE (Dev only)
#######################################
TELESCOPE_ENABLED=true

#######################################
# BOOKING SETTINGS
#######################################
BOOKING_PENDING_EXPIRY_MINUTES=30
BOOKING_ADVANCE_HOURS=24
BOOKING_MAX_DAYS_AHEAD=180
BOOKING_TAX_RATE=0.22

#######################################
# QR CODE
#######################################
QR_CODE_SIZE=300
QR_CODE_STORAGE=public
```

---

## 16. MVP Consigliato

### Fase 1 - MVP (8-10 settimane)

#### Sprint 1-2: Foundation (2 settimane)
- [ ] Setup progetto Laravel
- [ ] Autenticazione (login, registrazione, password reset)
- [ ] Modelli base: User, Catamaran, TimeSlot
- [ ] Admin: gestione catamarani (CRUD)
- [ ] Database migrations e seeders
- [ ] Layout base Tailwind (public + admin)

#### Sprint 3-4: Booking Core (2 settimane)
- [ ] Visualizzazione flotta pubblica
- [ ] Dettaglio catamarano con gallery
- [ ] Calendario disponibilità (Livewire)
- [ ] Logica slot orari e disponibilità
- [ ] Form prenotazione base

#### Sprint 5-6: Booking Complete (2 settimane)
- [ ] Prenotazione posti singoli
- [ ] Prenotazione esclusiva
- [ ] Prenotazione multi-day
- [ ] Gestione addon
- [ ] Calcolo prezzi dinamico
- [ ] Carrello/riepilogo

#### Sprint 7-8: Payments (2 settimane)
- [ ] Integrazione Stripe
- [ ] Webhook handling
- [ ] Stati pagamento/prenotazione
- [ ] Email conferma prenotazione
- [ ] Generazione QR code

#### Sprint 9-10: Admin & Polish (2 settimane)
- [ ] Dashboard admin
- [ ] Calendario prenotazioni admin
- [ ] Scanner QR check-in
- [ ] Email templates completi
- [ ] Testing e bug fixing
- [ ] Deploy staging

### Esclusi da MVP (Fase 2)
- PayPal (solo Stripe in MVP)
- Multilingua
- Recensioni
- GetYourGuide
- Codici sconto
- App mobile

---

## 17. Funzionalità Future

### Roadmap Fase 2 (3-6 mesi dopo lancio)

| Priorità | Feature | Effort | Valore |
|----------|---------|--------|--------|
| **Alta** | PayPal integration | 1 sett | Più conversioni |
| **Alta** | Multilingua (EN, DE) | 2 sett | Mercato estero |
| **Alta** | Sistema recensioni | 1 sett | Social proof |
| **Media** | Codici sconto | 3 giorni | Marketing |
| **Media** | GetYourGuide sync | 2 sett | Visibilità |
| **Media** | PWA per check-in | 1 sett | UX staff |
| **Bassa** | App mobile | 6+ sett | Canale diretto |
| **Bassa** | Dynamic pricing | 2 sett | Revenue optimization |
| **Bassa** | Analytics dashboard | 1 sett | Business intelligence |

### Integrazioni Future

1. **Channel Manager** - Sync con OTA (Viator, etc.)
2. **CRM** - HubSpot/Salesforce per marketing
3. **Contabilità** - Export fatture per commercialista
4. **Meteo** - Alert condizioni meteo avverse
5. **Calendario Google** - Sync prenotazioni

---

## 18. Rischi Tecnici

### Rischi Identificati e Mitigazioni

| Rischio | Probabilità | Impatto | Mitigazione |
|---------|-------------|---------|-------------|
| **Overbooking** | Media | Alto | Lock pessimistico DB, validazione real-time |
| **Double payment** | Bassa | Alto | Idempotency keys, webhook dedup |
| **Webhook failure** | Media | Medio | Retry logic, reconciliation job, alerting |
| **Performance calendario** | Media | Medio | Eager loading, cache, indici DB |
| **Email deliverability** | Media | Medio | SPF/DKIM/DMARC, provider affidabile |
| **Sicurezza pagamenti** | Bassa | Critico | Tokenizzazione, no card storage, PCI compliance |
| **Data loss** | Bassa | Critico | Backup giornalieri, point-in-time recovery |
| **DDoS/Bot** | Media | Medio | Rate limiting, Cloudflare, captcha |
| **Scalabilità** | Bassa | Medio | Architettura cache-first, queue jobs |

### Checklist Pre-Lancio

- [ ] Penetration testing base
- [ ] Load testing (100 utenti concorrenti)
- [ ] Backup automatici verificati
- [ ] Monitoring setup (Sentry, uptime)
- [ ] SSL certificato produzione
- [ ] GDPR compliance review
- [ ] Test pagamenti reali (piccoli importi)
- [ ] Procedure disaster recovery documentate

---

## Route Principali

### Web Routes

```php
// routes/web.php

Route::get('/', [HomeController::class, 'index'])->name('home');

// Flotta pubblica
Route::get('/flotta', [CatamaranController::class, 'index'])->name('fleet');
Route::get('/flotta/{catamaran:slug}', [CatamaranController::class, 'show'])->name('fleet.show');

// Prenotazione
Route::middleware('auth')->group(function () {
    Route::get('/prenota', BookingWizard::class)->name('booking.create');
    Route::get('/prenota/conferma/{booking:uuid}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/prenotazioni', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/prenotazioni/{booking:uuid}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/prenotazioni/{booking:uuid}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Pagine statiche
Route::view('/chi-siamo', 'pages.about')->name('about');
Route::view('/contatti', 'pages.contact')->name('contact');
Route::view('/termini', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
```

### Admin Routes

```php
// routes/admin.php

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    
    // Prenotazioni
    Route::get('/prenotazioni', BookingManager::class)->name('bookings');
    Route::get('/calendario', BookingCalendar::class)->name('calendar');
    Route::get('/check-in', QRScanner::class)->name('checkin');
    
    // Flotta
    Route::resource('catamarani', CatamaranController::class);
    Route::post('catamarani/{catamaran}/images', [CatamaranImageController::class, 'store']);
    
    // Disponibilità
    Route::get('/disponibilita', AvailabilityManager::class)->name('availability');
    Route::post('/blocca-date', [BlockedDateController::class, 'store'])->name('block-dates');
    
    // Addon
    Route::resource('addon', AddonController::class);
    
    // Utenti
    Route::get('/utenti', UserManager::class)->name('users');
    
    // Pagamenti
    Route::get('/pagamenti', PaymentList::class)->name('payments');
    
    // Impostazioni
    Route::get('/impostazioni', Settings::class)->name('settings');
});
```

### API Routes

```php
// routes/api.php (per future integrazioni)

Route::prefix('v1')->group(function () {
    // Disponibilità pubblica
    Route::get('/availability/{catamaran}', [Api\AvailabilityController::class, 'index']);
    Route::get('/catamarans', [Api\CatamaranController::class, 'index']);
    
    // Autenticato
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/bookings', [Api\BookingController::class, 'index']);
        Route::post('/bookings', [Api\BookingController::class, 'store']);
    });
});
```

### Webhook Routes

```php
// routes/webhooks.php

Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->name('webhooks.stripe');

Route::post('/webhooks/paypal', [PayPalWebhookController::class, 'handle'])
    ->name('webhooks.paypal');
```

---

## Best Practice UX per Conversione

### Homepage
- Hero full-width con video/immagine catamarano al tramonto
- CTA prominente "Prenota ora" above the fold
- Trust badges: recensioni, sicurezza pagamenti, garanzie
- Sezione "Come funziona" in 3 step visuali
- Testimonianze con foto reali

### Funnel Prenotazione
- Progress indicator sempre visibile
- Prezzo totale sempre aggiornato in sidebar sticky
- Urgency: "Ultimi 3 posti disponibili"
- Possibilità di salvare e continuare dopo
- Form minimal, solo campi essenziali
- Autofill intelligente
- Validazione real-time inline

### Mobile First
- Touch targets ≥ 44px
- Swipe gallery immagini
- Bottom sheet per selezioni
- Sticky CTA button
- Checkout ottimizzato per thumb zone

### Elementi Luxury
- Typography: font serif per titoli (es. Playfair Display)
- Colori: blu navy, oro, bianco
- Spacing generoso
- Micro-animazioni eleganti
- Fotografia di alta qualità
- Iconografia custom

---

**Documento redatto per: Solarya Travel**  
**Versione: 1.0**  
**Prossimo aggiornamento: dopo review cliente**
