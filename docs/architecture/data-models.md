# Data Models

This section defines the core data entities that form the backbone of the Pharmacy Management System. These models are a direct translation of the schema specified in the database schema and serve as the conceptual blueprint for the SQLite database, Laravel Eloquent models, and the corresponding Dart classes for the Flutter frontend. The creation of corresponding Dart models is a key architectural decision to enforce type safety across the stack and ensure seamless data serialization between the Laravel API and Flutter client.

## User

**Purpose:** Represents any actor who can authenticate with the system. The `role` attribute is critical for the Role-Based Access Control (RBAC) system, determining the user's permissions and the interface they are presented with.

**Key Attributes:**
- `id`: int - Unique identifier for the user.
- `name`: String - The user's full name.
- `email`: String - The user's unique email address, used for login.
- `phone_number`: String? - The user's contact phone number.
- `address`: String? - The user's physical address for deliveries.
- `role`: UserRole enum - The user's assigned role.
- `status`: UserStatus enum - The current status of the user's account.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'client_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function adviceRequests()
    {
        return $this->hasMany(AdviceRequest::class, 'client_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(Order::class, 'assigned_delivery_user_id');
    }
}
```

**Flutter Dart Model:**
```dart
enum UserRole { client, pharmacist, salesperson, delivery, manager }

enum UserStatus { active, disabled }

class User {
  final int id;
  final String name;
  final String email;
  final String? phoneNumber;
  final String? address;
  final UserRole role;
  final UserStatus status;
  final DateTime createdAt;
  final DateTime updatedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phoneNumber,
    this.address,
    required this.role,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phoneNumber: json['phone_number'],
      address: json['address'],
      role: UserRole.values.byName(json['role']),
      status: UserStatus.values.byName(json['status']),
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone_number': phoneNumber,
      'address': address,
      'role': role.name,
      'status': status.name,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
  bool get isActive => status == UserStatus.active;
  bool get hasValidEmail => email.contains('@');
  bool get hasCompleteProfile => phoneNumber != null && address != null;
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
- `id`: int - Unique identifier for the medication.
- `name`: String - The commercial name of the medication.
- `strength_form`: String - The strength and form (e.g., "500mg Tablet").
- `description`: String - A detailed description of the medication.
- `price`: double - The price per unit.
- `current_quantity`: int - The current stock level.
- `minimum_threshold`: int - The stock level at which a low-stock warning is triggered.
- `category`: MedicationCategory enum - The medication's category.
- `status`: MedicationStatus enum - Whether the medication is available for ordering.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'strength_form',
        'description',
        'price',
        'current_quantity',
        'minimum_threshold',
        'category',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'current_quantity' => 'integer',
        'minimum_threshold' => 'integer',
    ];

    // Relationships
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_quantity', '<=', 'minimum_threshold');
    }
}
```

**Flutter Dart Model:**
```dart
enum MedicationCategory { painRelief, antibiotics, vitamins, coldFlu, skincare }

enum MedicationStatus { active, disabled }

class Medication {
  final int id;
  final String name;
  final String strengthForm;
  final String description;
  final double price;
  final int currentQuantity;
  final int minimumThreshold;
  final MedicationCategory category;
  final MedicationStatus status;
  final DateTime createdAt;
  final DateTime updatedAt;

  Medication({
    required this.id,
    required this.name,
    required this.strengthForm,
    required this.description,
    required this.price,
    required this.currentQuantity,
    required this.minimumThreshold,
    required this.category,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Medication.fromJson(Map<String, dynamic> json) {
    return Medication(
      id: json['id'],
      name: json['name'],
      strengthForm: json['strength_form'],
      description: json['description'],
      price: double.parse(json['price'].toString()),
      currentQuantity: json['current_quantity'],
      minimumThreshold: json['minimum_threshold'],
      category: _categoryFromString(json['category']),
      status: MedicationStatus.values.byName(json['status']),
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  static MedicationCategory _categoryFromString(String category) {
    switch (category) {
      case 'Pain Relief':
        return MedicationCategory.painRelief;
      case 'Antibiotics':
        return MedicationCategory.antibiotics;
      case 'Vitamins':
        return MedicationCategory.vitamins;
      case 'Cold & Flu':
        return MedicationCategory.coldFlu;
      case 'Skincare':
        return MedicationCategory.skincare;
      default:
        throw ArgumentError('Invalid medication category: $category');
    }
  }
  bool get isActive => status == MedicationStatus.active;
  bool get isInStock => currentQuantity > 0;
  bool get isLowStock => currentQuantity <= minimumThreshold;
}
```

**Relationships:**
- A **Medication** can be in many **Orders** (through the `order_items` join table).

## Prescription

**Purpose:** Represents a client's uploaded prescription image and its state within the processing workflow. It is the initial artifact that triggers the order fulfillment process.

**Key Attributes:**
- `id`: int - Unique identifier for the prescription submission.
- `client_id`: int - Foreign key linking to the `User` who submitted it.
- `image_path`: String - The server-side path to the stored image file.
- `status`: PrescriptionStatus enum - The current status of the prescription.
- `processed_by`: int? - Foreign key linking to the staff `User` who processed it.
- `rejection_reason`: String? - The reason provided if the status is 'rejected'.
- `reference_number`: String - A unique, user-facing reference number.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'image_path',
        'status',
        'processed_by',
        'rejection_reason',
        'reference_number',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($prescription) {
            if (empty($prescription->reference_number)) {
                $prescription->reference_number = 'RX-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
```

**Flutter Dart Model:**
```dart
enum PrescriptionStatus { pending, processed, rejected }

class Prescription {
  final int id;
  final int clientId;
  final String imagePath;
  final PrescriptionStatus status;
  final int? processedBy;
  final String? rejectionReason;
  final String referenceNumber;
  final DateTime createdAt;
  final DateTime updatedAt;

  Prescription({
    required this.id,
    required this.clientId,
    required this.imagePath,
    required this.status,
    this.processedBy,
    this.rejectionReason,
    required this.referenceNumber,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Prescription.fromJson(Map<String, dynamic> json) {
    return Prescription(
      id: json['id'],
      clientId: json['client_id'],
      imagePath: json['image_path'],
      status: PrescriptionStatus.values.byName(json['status']),
      processedBy: json['processed_by'],
      rejectionReason: json['rejection_reason'],
      referenceNumber: json['reference_number'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
  bool get isPending => status == PrescriptionStatus.pending;
  bool get isProcessed => status == PrescriptionStatus.processed;
  bool get isRejected => status == PrescriptionStatus.rejected;
}
```

**Relationships:**
- A **Prescription** belongs to one **User** (the client).
- A **Prescription** can have one **Order** (after being processed).

## Order

**Purpose:** Represents a formal order for medications, created from a processed prescription. It tracks the order's contents, total cost, and fulfillment status.

**Key Attributes:**
- `id`: int - Unique identifier for the order.
- `client_id`: int - Foreign key linking to the `User` who owns the order.
- `prescription_id`: int? - Foreign key linking to the source `Prescription`.
- `total_amount`: double - The calculated total cost of the order.
- `status`: OrderStatus enum - The current status in the fulfillment workflow.
- `assigned_delivery_user_id`: int? - Foreign key linking to the `User` assigned to deliver the order.
- `cancellation_reason`: String? - The reason provided if the status is 'cancelled'.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'prescription_id',
        'total_amount',
        'status',
        'assigned_delivery_user_id',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'assigned_delivery_user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function medications()
    {
        return $this->belongsToMany(Medication::class, 'order_items')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeForDelivery($query)
    {
        return $query->where('status', 'ready_for_delivery');
    }
}
```

**Flutter Dart Model:**
```dart
enum OrderStatus { 
  inPreparation, 
  readyForDelivery, 
  completed, 
  cancelled, 
  failedDelivery 
}

class Order {
  final int id;
  final int clientId;
  final int? prescriptionId;
  final double totalAmount;
  final OrderStatus status;
  final int? assignedDeliveryUserId;
  final String? cancellationReason;
  final List<OrderItem>? items;
  final DateTime createdAt;
  final DateTime updatedAt;

  Order({
    required this.id,
    required this.clientId,
    this.prescriptionId,
    required this.totalAmount,
    required this.status,
    this.assignedDeliveryUserId,
    this.cancellationReason,
    this.items,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      clientId: json['client_id'],
      prescriptionId: json['prescription_id'],
      totalAmount: double.parse(json['total_amount'].toString()),
      status: _statusFromString(json['status']),
      assignedDeliveryUserId: json['assigned_delivery_user_id'],
      cancellationReason: json['cancellation_reason'],
      items: json['items'] != null 
          ? (json['items'] as List).map((item) => OrderItem.fromJson(item)).toList()
          : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  static OrderStatus _statusFromString(String status) {
    switch (status) {
      case 'in_preparation':
        return OrderStatus.inPreparation;
      case 'ready_for_delivery':
        return OrderStatus.readyForDelivery;
      case 'completed':
        return OrderStatus.completed;
      case 'cancelled':
        return OrderStatus.cancelled;
      case 'failed_delivery':
        return OrderStatus.failedDelivery;
      default:
        throw ArgumentError('Unknown status: $status');
    }
  }
  bool get isCompleted => status == OrderStatus.completed;
  bool get canBeCancelled => status == OrderStatus.inPreparation || status == OrderStatus.readyForDelivery;
  bool get hasDeliveryAssigned => assignedDeliveryUserId != null;
}
 
```

**Relationships:**
- An **Order** belongs to one **User** (the client).
- An **Order** belongs to one **Prescription**.
- An **Order** has many **Order Items**.

## Order Item

**Purpose:** This is a pivot model that links a specific `Medication` to an `Order`. It stores the quantity and the price of the medication at the time the order was placed, which is crucial for accurate historical records.

**Key Attributes:**
- `id`: int - Unique identifier for the order line item.
- `order_id`: int - Foreign key linking to the `Order`.
- `medication_id`: int - Foreign key linking to the `Medication`.
- `quantity`: int - The quantity of the medication ordered.
- `unit_price`: double - The price of a single unit of the medication at the time of purchase.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'medication_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    // Accessor for line total
    public function getLineTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
```

**Flutter Dart Model:**
```dart
class OrderItem {
  final int id;
  final int orderId;
  final int medicationId;
  final int quantity;
  final double unitPrice;
  final DateTime createdAt;
  final Medication? medication; // Optional for eager loading

  OrderItem({
    required this.id,
    required this.orderId,
    required this.medicationId,
    required this.quantity,
    required this.unitPrice,
    required this.createdAt,
    this.medication,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'],
      orderId: json['order_id'],
      medicationId: json['medication_id'],
      quantity: json['quantity'],
      unitPrice: double.parse(json['unit_price'].toString()),
      createdAt: DateTime.parse(json['created_at']),
      medication: json['medication'] != null 
          ? Medication.fromJson(json['medication']) 
          : null,
    );
  }

  double get lineTotal => quantity * unitPrice;
}
```

**Relationships:**
- An **Order Item** belongs to one **Order**.
- An **Order Item** belongs to one **Medication**.

## Advice Request

**Purpose:** Captures a client's health-related question and the subsequent interaction with a pharmacist. This model is central to the asynchronous advice workflow.

**Key Attributes:**
- `id`: int - Unique identifier for the advice request.
- `client_id`: int - Foreign key linking to the `User` who asked the question.
- `question`: String - The text of the client's question.
- `status`: AdviceRequestStatus enum - The current status of the request.
- `response`: String? - The pharmacist's response text.
- `responder_id`: int? - Foreign key linking to the staff `User` who responded.
- `rejection_reason`: String? - The reason provided if the request was rejected.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdviceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'question',
        'status',
        'response',
        'responder_id',
        'rejection_reason',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
```

**Flutter Dart Model:**
```dart
enum AdviceRequestStatus { pending, responded, rejected }

class AdviceRequest {
  final int id;
  final int clientId;
  final String question;
  final AdviceRequestStatus status;
  final String? response;
  final int? responderId;
  final String? rejectionReason;
  final DateTime createdAt;
  final DateTime updatedAt;

  AdviceRequest({
    required this.id,
    required this.clientId,
    required this.question,
    required this.status,
    this.response,
    this.responderId,
    this.rejectionReason,
    required this.createdAt,
    required this.updatedAt,
  });

  factory AdviceRequest.fromJson(Map<String, dynamic> json) {
    return AdviceRequest(
      id: json['id'],
      clientId: json['client_id'],
      question: json['question'],
      status: AdviceRequestStatus.values.byName(json['status']),
      response: json['response'],
      responderId: json['responder_id'],
      rejectionReason: json['rejection_reason'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }
  bool get isPending => status == AdviceRequestStatus.pending;
  bool get isResponded => status == AdviceRequestStatus.responded;
  bool get hasResponse => response != null && response!.isNotEmpty;
}
```

**Relationships:**
- An **Advice Request** belongs to one **User** (the client).
- An **Advice Request** can be handled by one **User** (the pharmacist).

## Notification

**Purpose:** Represents a single, asynchronous message delivered to a user within the application. This model is the foundation of the in-app notification system for communicating order status updates, advice responses, and other alerts.

**Key Attributes:**
- `id`: int - Unique identifier for the notification.
- `user_id`: int - Foreign key linking to the `User` who will receive the notification.
- `title`: String - The title of the notification.
- `message`: String - The body content of the notification.
- `type`: NotificationType enum - The type of notification, for potential UI differentiation (e.g., icons).
- `read_at`: DateTime? - A timestamp indicating when the user read the notification. Null if unread.

**Laravel Eloquent Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }
}
```

**Flutter Dart Model:**
```dart
enum NotificationType { 
  orderStatus, 
  prescriptionUpdate, 
  adviceResponse, 
  systemAlert 
}

class Notification {
  final int id;
  final int userId;
  final String title;
  final String message;
  final NotificationType type;
  final DateTime? readAt;
  final DateTime createdAt;

  Notification({
    required this.id,
    required this.userId,
    required this.title,
    required this.message,
    required this.type,
    this.readAt,
    required this.createdAt,
  });

  factory Notification.fromJson(Map<String, dynamic> json) {
    return Notification(
      id: json['id'],
      userId: json['user_id'],
      title: json['title'],
      message: json['message'],
      type: _typeFromString(json['type']),
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  static NotificationType _typeFromString(String type) {
    switch (type) {
      case 'order_status':
        return NotificationType.orderStatus;
      case 'prescription_update':
        return NotificationType.prescriptionUpdate;
      case 'advice_response':
        return NotificationType.adviceResponse;
      case 'system_alert':
        return NotificationType.systemAlert;
      default:
        throw ArgumentError('Unknown type: $type');
    }
  }

  bool get isRead => readAt != null;
  bool get isUnread => readAt == null;
}
```

**Relationships:**
- A **Notification** belongs to one **User**.

## Architecture Notes

**Laravel-Flutter Integration:**
- Laravel Eloquent models provide the server-side data layer with built-in relationships, scopes, and business logic methods.
- Flutter Dart models serve as data transfer objects (DTOs) with JSON serialization capabilities for seamless API communication.
- Enum mappings between Laravel database values and Dart enums ensure type safety across the stack.
- The models are designed to support Laravel's resource transformations and Flutter's state management patterns.

**Data Consistency:**
- All models include proper DateTime handling for timestamps, converting between Laravel's Carbon instances and Dart's DateTime objects.
- Decimal precision is maintained for monetary values using Laravel's decimal casting and Dart's double type.
- Foreign key relationships are properly defined in both Laravel (for database integrity) and documented for Flutter (for understanding data flow).

This architecture ensures robust data handling across the full stack while maintaining the flexibility needed for both Laravel's powerful ORM features and Flutter's reactive UI patterns.

---