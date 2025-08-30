# Database Schema

This section provides the definitive physical data model for the Pharmacy Management System. The following Data Definition Language (DDL) is specifically written for **SQLite**, adhering to the non-negotiable project constraint for a portable, file-based database. This schema transforms the conceptual data models defined earlier into a concrete, relational structure with enforced integrity constraints, serving as the foundational layer for the Laravel backend.

```sql
--
-- Users Table
-- Stores all user accounts, for both clients and internal staff.
--
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    phone_number TEXT,
    address TEXT,
    role TEXT NOT NULL DEFAULT 'client' CHECK(role IN ('client', 'pharmacist', 'salesperson', 'delivery', 'manager')),
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active', 'disabled')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Medications Table
-- The master catalog for all medications available in the pharmacy.
--
CREATE TABLE medications (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    strength_form TEXT NOT NULL,
    description TEXT NOT NULL,
    price REAL NOT NULL,
    current_quantity INTEGER NOT NULL DEFAULT 0,
    minimum_threshold INTEGER NOT NULL DEFAULT 10,
    category TEXT NOT NULL CHECK(category IN ('Pain Relief', 'Antibiotics', 'Vitamins', 'Cold & Flu', 'Skincare')),
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active', 'disabled')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Prescriptions Table
-- Tracks client-submitted prescription images and their processing status.
--
CREATE TABLE prescriptions (
    id INTEGER PRIMARY KEY,
    client_id INTEGER NOT NULL,
    image_path TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'processed', 'rejected')),
    processed_by INTEGER,
    rejection_reason TEXT,
    reference_number TEXT NOT NULL UNIQUE,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

--
-- Orders Table
-- Represents a customer's order, created from a processed prescription.
--
CREATE TABLE orders (
    id INTEGER PRIMARY KEY,
    client_id INTEGER NOT NULL,
    prescription_id INTEGER UNIQUE,
    total_amount REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'in_preparation' CHECK(status IN ('in_preparation', 'ready_for_delivery', 'completed', 'cancelled', 'failed_delivery')),
    assigned_delivery_user_id INTEGER,
    cancellation_reason TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_delivery_user_id) REFERENCES users(id) ON DELETE SET NULL
);

--
-- Order Items Table
-- A pivot table linking medications to orders, storing quantity and price at time of sale.
--
CREATE TABLE order_items (
    id INTEGER PRIMARY KEY,
    order_id INTEGER NOT NULL,
    medication_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price REAL NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(order_id, medication_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (medication_id) REFERENCES medications(id) ON DELETE RESTRICT
);

--
-- Advice Requests Table
-- Stores client questions and pharmacist responses.
--
CREATE TABLE advice_requests (
    id INTEGER PRIMARY KEY,
    client_id INTEGER NOT NULL,
    question TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'responded', 'rejected')),
    response TEXT,
    responder_id INTEGER,
    rejection_reason TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (responder_id) REFERENCES users(id) ON DELETE SET NULL
);

--
-- Notifications Table
-- The backbone of the in-app asynchronous communication system.
--
CREATE TABLE notifications (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    type TEXT NOT NULL CHECK(type IN ('order_status', 'prescription_update', 'advice_response', 'system_alert')),
    read_at TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--
-- Indexes for Performance
--
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_medications_name ON medications(name);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
```

## Design Rationale

This schema is designed for robustness and data integrity, even within the constraints of SQLite. Key decisions include:

*   **Data Integrity:** Foreign key constraints are used extensively to maintain relational integrity. The `ON DELETE` clauses are chosen carefully:
    *   `RESTRICT`: Prevents the deletion of a user or medication if they are linked to essential records like orders, ensuring historical data is not accidentally orphaned.
    *   `CASCADE`: Used on `notifications` and `advice_requests` so that deleting a user automatically cleans up their associated, non-critical data.
    *   `SET NULL`: Used for optional relationships, like `processed_by` or `assigned_delivery_user_id`, so that deleting a staff member nullifies their assignments without deleting the core order/prescription record.
*   **ENUM Emulation:** SQLite does not have a native `ENUM` type. We enforce valid values for fields like `role` and `status` using `CHECK` constraints, which provides the same level of data validation at the database layer.
*   **Performance:** While this is a small-scale project, basic indexes are created on columns that will be frequently used in `WHERE` clauses (e.g., `users.email` for login, `medications.name` for search). This is a proactive measure to ensure core operations remain performant.
*   **Transactional Support:** The schema is explicitly designed to support atomic business transactions. As validated in our query pattern analysis, the structure allows for a safe "check-and-decrement" pattern on medication stock within a single transaction, which is critical for preventing race conditions and ensuring the reliability of the inventory system.

---
