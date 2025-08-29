# Data Models

This section defines the core data entities that form the backbone of the Pharmacy Management System. These models are a direct translation of the schema specified in the `technical-requirements.md` and serve as the conceptual blueprint for the SQLite database, Laravel Eloquent models, and the shared TypeScript interfaces. The creation of TypeScript interfaces is a key architectural decision to enforce type safety across the stack, which is a best practice for monorepo development that prevents entire classes of bugs at the integration boundary between the frontend and backend.

## User

**Purpose:** Represents any actor who can authenticate with the system. The `role` attribute is critical for the Role-Based Access Control (RBAC) system, determining the user's permissions and the interface they are presented with.

**Key Attributes:**
- `id`: number - Unique identifier for the user.
- `name`: string - The user's full name.
- `email`: string - The user's unique email address, used for login.
- `phone_number`: string | null - The user's contact phone number.
- `address`: string | null - The user's physical address for deliveries.
- `role`: 'client' | 'pharmacist' | 'salesperson' | 'delivery' | 'manager' - The user's assigned role.
- `status`: 'active' | 'disabled' - The current status of the user's account.

**TypeScript Interface:**
```typescript
export interface User {
  id: number;
  name: string;
  email: string;
  phone_number?: string | null;
  address?: string | null;
  role: 'client' | 'pharmacist' | 'salesperson' | 'delivery' | 'manager';
  status: 'active' | 'disabled';
  created_at: string;
  updated_at: string;
}
```

**Relationships:**
- A **User** can have many **Prescriptions**.
- A **User** can have many **Orders**.
- A **User** can have many **Advice Requests**.
- A **User** can have many **Notifications**.

## Medication

**Purpose:** Represents a single product in the pharmacy's inventory. It contains all details necessary for public display, order processing, and stock management.

**Key Attributes:**
- `id`: number - Unique identifier for the medication.
- `name`: string - The commercial name of the medication.
- `strength_form`: string - The strength and form (e.g., "500mg Tablet").
- `description`: string - A detailed description of the medication.
- `price`: number - The price per unit.
- `current_quantity`: number - The current stock level.
- `minimum_threshold`: number - The stock level at which a low-stock warning is triggered.
- `category`: 'Pain Relief' | 'Antibiotics' | 'Vitamins' | 'Cold & Flu' | 'Skincare' - The medication's category.
- `status`: 'active' | 'disabled' - Whether the medication is available for ordering.

**TypeScript Interface:**
```typescript
export interface Medication {
  id: number;
  name: string;
  strength_form: string;
  description: string;
  price: number;
  current_quantity: number;
  minimum_threshold: number;
  category: 'Pain Relief' | 'Antibiotics' | 'Vitamins' | 'Cold & Flu' | 'Skincare';
  status: 'active' | 'disabled';
  created_at: string;
  updated_at: string;
}
```

**Relationships:**
- A **Medication** can be in many **Orders** (through the `order_items` join table).

## Prescription

**Purpose:** Represents a client's uploaded prescription image and its state within the processing workflow. It is the initial artifact that triggers the order fulfillment process.

**Key Attributes:**
- `id`: number - Unique identifier for the prescription submission.
- `client_id`: number - Foreign key linking to the `User` who submitted it.
- `image_path`: string - The server-side path to the stored image file.
- `status`: 'pending' | 'processed' | 'rejected' - The current status of the prescription.
- `processed_by`: number | null - Foreign key linking to the staff `User` who processed it.
- `rejection_reason`: string | null - The reason provided if the status is 'rejected'.
- `reference_number`: string - A unique, user-facing reference number.

**TypeScript Interface:**
```typescript
export interface Prescription {
  id: number;
  client_id: number;
  image_path: string;
  status: 'pending' | 'processed' | 'rejected';
  processed_by?: number | null;
  rejection_reason?: string | null;
  reference_number: string;
  created_at: string;
  updated_at: string;
}
```

**Relationships:**
- A **Prescription** belongs to one **User** (the client).
- A **Prescription** can have one **Order** (after being processed).

## Order

**Purpose:** Represents a formal order for medications, created from a processed prescription. It tracks the order's contents, total cost, and fulfillment status.

**Key Attributes:**
- `id`: number - Unique identifier for the order.
- `client_id`: number - Foreign key linking to the `User` who owns the order.
- `prescription_id`: number | null - Foreign key linking to the source `Prescription`.
- `total_amount`: number - The calculated total cost of the order.
- `status`: 'in_preparation' | 'ready_for_delivery' | 'completed' | 'cancelled' | 'failed_delivery' - The current status in the fulfillment workflow.
- `assigned_delivery_user_id`: number | null - Foreign key linking to the `User` assigned to deliver the order.
- `cancellation_reason`: string | null - The reason provided if the status is 'cancelled'.

**TypeScript Interface:**
```typescript
import { OrderItem } from './order-item';

export interface Order {
  id: number;
  client_id: number;
  prescription_id?: number | null;
  total_amount: number;
  status: 'in_preparation' | 'ready_for_delivery' | 'completed' | 'cancelled' | 'failed_delivery';
  assigned_delivery_user_id?: number | null;
  cancellation_reason?: string | null;
  items?: OrderItem[]; // Eager loaded in some API responses
  created_at: string;
  updated_at: string;
}
```

**Relationships:**
- An **Order** belongs to one **User** (the client).
- An **Order** belongs to one **Prescription**.
- An **Order** has many **Order Items**.

## Order Item

**Purpose:** This is a pivot model that links a specific `Medication` to an `Order`. It stores the quantity and the price of the medication at the time the order was placed, which is crucial for accurate historical records.

**Key Attributes:**
- `id`: number - Unique identifier for the order line item.
- `order_id`: number - Foreign key linking to the `Order`.
- `medication_id`: number - Foreign key linking to the `Medication`.
- `quantity`: number - The quantity of the medication ordered.
- `unit_price`: number - The price of a single unit of the medication at the time of purchase.

**TypeScript Interface:**
```typescript
export interface OrderItem {
  id: number;
  order_id: number;
  medication_id: number;
  quantity: number;
  unit_price: number;
  created_at: string;
}
```

**Relationships:**
- An **Order Item** belongs to one **Order**.
- An **Order Item** belongs to one **Medication**.

## Advice Request

**Purpose:** Captures a client's health-related question and the subsequent interaction with a pharmacist. This model is central to the asynchronous advice workflow.

**Key Attributes:**
- `id`: number - Unique identifier for the advice request.
- `client_id`: number - Foreign key linking to the `User` who asked the question.
- `question`: string - The text of the client's question.
- `status`: 'pending' | 'responded' | 'rejected' - The current status of the request.
- `response`: string | null - The pharmacist's response text.
- `responder_id`: number | null - Foreign key linking to the staff `User` who responded.
- `rejection_reason`: string | null - The reason provided if the request was rejected.

**TypeScript Interface:**
```typescript
export interface AdviceRequest {
  id: number;
  client_id: number;
  question: string;
  status: 'pending' | 'responded' | 'rejected';
  response?: string | null;
  responder_id?: number | null;
  rejection_reason?: string | null;
  created_at: string;
  updated_at: string;
}
```

**Relationships:**
- An **Advice Request** belongs to one **User** (the client).
- An **Advice Request** can be handled by one **User** (the pharmacist).

## Notification

**Purpose:** Represents a single, asynchronous message delivered to a user within the application. This model is the foundation of the in-app notification system for communicating order status updates, advice responses, and other alerts.

**Key Attributes:**
- `id`: number - Unique identifier for the notification.
- `user_id`: number - Foreign key linking to the `User` who will receive the notification.
- `title`: string - The title of the notification.
- `message`: string - The body content of the notification.
- `type`: 'order_status' | 'prescription_update' | 'advice_response' | 'system_alert' - The type of notification, for potential UI differentiation (e.g., icons).
- `read_at`: string | null - A timestamp indicating when the user read the notification. Null if unread.

**TypeScript Interface:**
```typescript
export interface Notification {
  id: number;
  user_id: number;
  title: string;
  message: string;
  type: 'order_status' | 'prescription_update' | 'advice_response' | 'system_alert';
  read_at?: string | null;
  created_at: string;
}
```

**Relationships:**
- A **Notification** belongs to one **User**.

---
