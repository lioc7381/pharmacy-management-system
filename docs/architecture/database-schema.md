# Database Schema

This section provides the definitive physical data model for the Pharmacy Management System, defined using **Laravel's Schema Builder**. This approach abstracts the underlying SQL, leverages the framework's powerful migration system for database versioning, and generates the appropriate DDL for the project's required **SQLite** driver. This schema transforms the conceptual data models into a concrete, relational structure with enforced integrity constraints, serving as the foundational layer for the Laravel backend.

**Note:** The following code snippets are presented sequentially for readability. In a standard Laravel project, each `Migration` class resides in its own timestamped file within the `database/migrations` directory (e.g., `2024_01_01_000001_create_users_table.php`).

```php
<?php

// database/migrations/2024_01_01_000001_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('role', ['client', 'pharmacist', 'salesperson', 'delivery', 'manager'])
                  ->default('client');
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->timestamps();
            
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

// database/migrations/2024_01_01_000002_create_medications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('strength_form');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('current_quantity')->default(0);
            $table->integer('minimum_threshold')->default(10);
            $table->enum('category', ['Pain Relief', 'Antibiotics', 'Vitamins', 'Cold & Flu', 'Skincare']);
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->timestamps();
            $table->index('name');
            $table->index(['category', 'status']); // For filtered searches
            $table->index('current_quantity'); // For stock checks
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};

// database/migrations/2024_01_01_000003_create_prescriptions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->string('image_path');
            $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->string('reference_number')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};

// database/migrations/2024_01_01_000004_create_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('prescription_id')->nullable()->unique()->constrained('prescriptions')->restrictOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['in_preparation', 'ready_for_delivery', 'completed', 'cancelled', 'failed_delivery'])
                  ->default('in_preparation');
            $table->foreignId('assigned_delivery_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index(['client_id', 'status']); // For user order history
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

// database/migrations/2024_01_01_000005_create_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['order_id', 'medication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

// database/migrations/2024_01_01_000006_create_advice_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advice_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->text('question');
            $table->enum('status', ['pending', 'responded', 'rejected'])->default('pending');
            $table->text('response')->nullable();
            $table->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advice_requests');
    }
};

// database/migrations/2024_01_01_000007_create_notifications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['order_status', 'prescription_update', 'advice_response', 'system_alert']);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

## Design Rationale

This schema is designed for robustness and data integrity, defined via Laravel Migrations to ensure portability while targeting the project's SQLite database. Key decisions include:

*   **Data Integrity:** Foreign key constraints are used extensively to maintain relational integrity. The `onDelete()` clauses are chosen carefully:
    *   `restrictOnDelete()`: Prevents the deletion of a user or medication if they are linked to essential records like orders, ensuring historical data is not accidentally orphaned.
    *   `cascadeOnDelete()`: Used on `notifications` and `advice_requests` so that deleting a user automatically cleans up their associated, non-critical data.
    *   `nullOnDelete()`: Used for optional relationships, like `processed_by` or `assigned_delivery_user_id`, so that deleting a staff member nullifies their assignments without deleting the core order/prescription record.
*   **Type Safety with ENUMs:** Laravel's Schema Builder provides a native `enum()` column type. We use this to enforce a specific set of allowed values for fields like `role` and `status`, providing robust data validation directly at the database layer. This is a more expressive and framework-integrated approach than using raw SQL `CHECK` constraints.
*   **Performance:** While this is a small-scale project, basic indexes are created on columns that will be frequently used in `WHERE` clauses (e.g., `users.email` for login, `medications.name` for search) using the `$table->index()` method. This is a proactive measure to ensure core operations remain performant.
*   **Transactional Support:** The schema is explicitly designed to support atomic business transactions. As validated in our query pattern analysis, the structure allows for a safe "check-and-decrement" pattern on medication stock within a single transaction, which is critical for preventing race conditions and ensuring the reliability of the inventory system.

---