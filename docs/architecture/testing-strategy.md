# Testing Strategy

<!--docs/architecture/testing-strategy.md-->

This document defines the comprehensive testing approach for the Laravel Pharmacy Management System and Flutter mobile application. The testing strategy aligns with the simplified architectural mandates, emphasizing practical testing patterns over complex frameworks while ensuring system reliability and maintainability.

## Testing Pyramid

```
                    E2E Tests
                   /          \
              Integration Tests
             /                  \
        Frontend Unit     Backend Unit Tests
       Tests (Flutter)      (Laravel PHPUnit)
      /              \    /                    \
 Widget Tests    Unit Tests   Feature Tests   Unit Tests
```

The testing pyramid follows a balanced approach prioritizing:
- **High Unit Test Coverage** (70%): Fast, isolated tests for business logic
- **Moderate Integration Coverage** (20%): API endpoints and provider integration
- **Focused E2E Coverage** (10%): Critical user workflows and role-based access

## Test Organization

### Frontend Tests (Flutter)

```
test/
├── unit/
│   ├── models/
│   │   ├── user_test.dart                    # Data model validation
│   │   ├── order_test.dart                   # Order model business logic
│   │   └── prescription_test.dart            # Prescription model tests
│   ├── services/
│   │   ├── api_client_test.dart              # HTTP client logic
│   │   ├── auth_api_service_test.dart        # Authentication API calls
│   │   └── storage_service_test.dart         # Local storage operations
│   └── utils/
│       ├── validators_test.dart              # Form validation logic
│       └── formatters_test.dart              # Text formatting utilities
├── widget/
│   ├── components/
│   │   ├── order_card_test.dart              # Order card widget display
│   │   ├── prescription_card_test.dart       # Prescription card widget
│   │   └── primary_button_test.dart          # Reusable button component
│   └── screens/
│       ├── login_screen_test.dart            # Login screen interactions
│       ├── prescription_list_test.dart       # Prescription list rendering
│       └── order_detail_test.dart            # Order detail screen
├── integration/
│   ├── providers/
│   │   ├── auth_provider_test.dart           # Auth state management flow
│   │   ├── orders_provider_test.dart         # Order provider with mock API
│   │   └── prescriptions_provider_test.dart  # Prescription provider flow
│   └── flows/
│       ├── login_flow_test.dart              # Complete login process
│       ├── prescription_upload_test.dart     # Upload prescription flow
│       └── order_tracking_test.dart          # Order status tracking
└── test_helpers/
    ├── mock_services.dart                    # Mock service implementations
    ├── test_data.dart                        # Test data generators
    └── widget_test_helpers.dart              # Widget testing utilities
```

### Backend Tests (Laravel PHPUnit)

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── UserTest.php                      # User model relationships
│   │   ├── OrderTest.php                     # Order model business logic
│   │   └── PrescriptionTest.php              # Prescription model validation
│   ├── Services/
│   │   ├── AuthServiceTest.php               # Authentication business logic
│   │   ├── OrderServiceTest.php              # Order creation and management
│   │   ├── PrescriptionServiceTest.php       # Prescription processing logic
│   │   └── NotificationServiceTest.php       # Notification dispatch logic
│   └── Utils/
│       ├── ValidatorTest.php                 # Custom validation rules
│       └── HelperTest.php                    # Utility functions
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php                     # Login API endpoint
│   │   ├── RegisterTest.php                  # Registration endpoint
│   │   └── LogoutTest.php                    # Logout functionality
│   ├── Prescriptions/
│   │   ├── UploadPrescriptionTest.php        # Prescription upload API
│   │   ├── ProcessPrescriptionTest.php       # Pharmacist processing
│   │   └── PrescriptionListTest.php          # Prescription listing
│   ├── Orders/
│   │   ├── CreateOrderTest.php               # Order creation API
│   │   ├── UpdateOrderStatusTest.php         # Status update API
│   │   └── DeliveryOrdersTest.php            # Delivery assignment
│   ├── Medications/
│   │   ├── MedicationCrudTest.php            # CRUD operations
│   │   └── InventoryManagementTest.php       # Stock management
│   └── Users/
│       ├── UserManagementTest.php            # User CRUD for managers
│       └── RoleBasedAccessTest.php           # Authorization testing
└── TestHelpers/
    ├── DatabaseSeeder.php                    # Test data seeding
    ├── MockHelpers.php                       # Service mocking utilities
    └── ApiTestCase.php                       # Base class for API tests
```

### E2E Tests (Flutter Integration Tests)

```
integration_test/
├── auth/
│   ├── login_flow_test.dart                  # Complete login workflow
│   └── role_based_navigation_test.dart       # Role-based access verification
├── client_workflows/
│   ├── prescription_upload_test.dart         # Upload to order completion
│   ├── order_tracking_test.dart              # Order status monitoring
│   └── advice_request_test.dart              # Request and response flow
├── staff_workflows/
│   ├── pharmacist_workflow_test.dart         # Prescription processing
│   ├── delivery_workflow_test.dart           # Order delivery management
│   └── manager_dashboard_test.dart           # Management operations
└── test_helpers/
    ├── e2e_test_helpers.dart                 # Common E2E utilities
    ├── mock_backend.dart                     # Backend simulation
    └── test_data_factory.dart                # E2E test data generation
```

## Test Examples

### Frontend Component Test

```dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pharmacy_app/features/orders/widgets/order_card.dart';
import 'package:pharmacy_app/shared/models/order.dart';
import 'package:pharmacy_app/shared/enums/order_status.dart';

import '../../test_helpers/test_data.dart';

/// Test suite for OrderCard widget functionality and display.
/// 
/// Verifies that order information is correctly displayed and user
/// interactions work as expected across different order states.
void main() {
  group('OrderCard Widget Tests', () {
    late Order testOrder;
    
    setUp(() {
      testOrder = TestData.createOrder(
        id: 123,
        totalAmount: 45.99,
        status: OrderStatus.inPreparation,
        createdAt: DateTime(2024, 1, 15, 10, 30),
      );
    });

    testWidgets('displays order information correctly', (tester) async {
      // Arrange
      bool onTapCalled = false;
      
      // Act
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: OrderCard(
              order: testOrder,
              onTap: () => onTapCalled = true,
            ),
          ),
        ),
      );
      
      // Assert
      expect(find.text('Order #123'), findsOneWidget);
      expect(find.text('Total: \$45.99'), findsOneWidget);
      expect(find.text('15/1/2024'), findsOneWidget);
      expect(find.text('In Preparation'), findsOneWidget);
      
      // Verify status chip color
      final chip = tester.widget<Chip>(find.byType(Chip));
      expect(chip.backgroundColor, Colors.orange[100]);
    });

    testWidgets('handles tap interaction', (tester) async {
      // Arrange
      bool onTapCalled = false;
      
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: OrderCard(
              order: testOrder,
              onTap: () => onTapCalled = true,
            ),
          ),
        ),
      );
      
      // Act
      await tester.tap(find.byType(OrderCard));
      await tester.pumpAndSettle();
      
      // Assert
      expect(onTapCalled, isTrue);
    });

    testWidgets('displays different status colors correctly', (tester) async {
      final statusTestCases = [
        (OrderStatus.inPreparation, Colors.orange[100]),
        (OrderStatus.readyForDelivery, Colors.blue[100]),
        (OrderStatus.completed, Colors.green[100]),
        (OrderStatus.cancelled, Colors.red[100]),
        (OrderStatus.failedDelivery, Colors.red[200]),
      ];

      for (final (status, expectedColor) in statusTestCases) {
        // Arrange
        final order = testOrder.copyWith(status: status);
        
        // Act
        await tester.pumpWidget(
          MaterialApp(
            home: Scaffold(
              body: OrderCard(order: order),
            ),
          ),
        );
        
        // Assert
        final chip = tester.widget<Chip>(find.byType(Chip));
        expect(chip.backgroundColor, expectedColor,
            reason: 'Status $status should have color $expectedColor');
      }
    });

    testWidgets('works without onTap callback', (tester) async {
      // Act & Assert - Should not throw
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: OrderCard(order: testOrder),
          ),
        ),
      );
      
      expect(find.byType(OrderCard), findsOneWidget);
    });
  });

  group('OrderCard Edge Cases', () {
    testWidgets('handles very long order amounts', (tester) async {
      // Arrange
      final order = TestData.createOrder(
        totalAmount: 1234567.89,
        status: OrderStatus.completed,
      );
      
      // Act
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: OrderCard(order: order),
          ),
        ),
      );
      
      // Assert
      expect(find.text('Total: \$1234567.89'), findsOneWidget);
    });
  });
}
```

### Backend API Test

```php
<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Test suite for Order API endpoints.
 * 
 * Covers order creation, status updates, and role-based access control
 * for all order-related operations in the pharmacy management system.
 */
class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $pharmacist;
    protected User $client;
    protected Prescription $processedPrescription;
    protected Medication $medication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->pharmacist = User::factory()->create([
            'role' => 'pharmacist',
            'status' => 'active'
        ]);
        
        $this->client = User::factory()->create([
            'role' => 'client',
            'status' => 'active'
        ]);
        
        // Create processed prescription
        $this->processedPrescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'processed',
            'processed_by' => $this->pharmacist->id,
        ]);
        
        // Create medication with stock
        $this->medication = Medication::factory()->create([
            'name' => 'Paracetamol',
            'price' => 10.50,
            'current_quantity' => 100,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function authenticated_pharmacist_can_create_order_from_processed_prescription(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);
        
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 2
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'client_id',
                'prescription_id',
                'total_amount',
                'status',
                'items' => [
                    '*' => [
                        'id',
                        'medication_id',
                        'quantity',
                        'unit_price',
                        'medication' => [
                            'id',
                            'name',
                        ]
                    ]
                ]
            ]
        ]);

        // Verify order creation in database
        $this->assertDatabaseHas('orders', [
            'client_id' => $this->client->id,
            'prescription_id' => $this->processedPrescription->id,
            'total_amount' => 21.00, // 2 * 10.50
            'status' => 'in_preparation',
        ]);

        // Verify order items creation
        $this->assertDatabaseHas('order_items', [
            'medication_id' => $this->medication->id,
            'quantity' => 2,
            'unit_price' => 10.50,
        ]);

        // Verify stock reduction
        $this->medication->refresh();
        $this->assertEquals(98, $this->medication->current_quantity);
    }

    /** @test */
    public function cannot_create_order_with_insufficient_stock(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);
        
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 150 // More than available stock of 100
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => "Insufficient stock for {$this->medication->name}"
        ]);

        // Verify no order was created
        $this->assertDatabaseMissing('orders', [
            'prescription_id' => $this->processedPrescription->id,
        ]);

        // Verify stock was not affected
        $this->medication->refresh();
        $this->assertEquals(100, $this->medication->current_quantity);
    }

    /** @test */
    public function cannot_create_order_from_unprocessed_prescription(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);
        
        $unprocessedPrescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);
        
        $orderData = [
            'prescription_id' => $unprocessedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'Invalid or unprocessed prescription'
        ]);
    }

    /** @test */
    public function cannot_create_duplicate_order_from_same_prescription(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);
        
        // Create existing order for prescription
        Order::factory()->create([
            'client_id' => $this->client->id,
            'prescription_id' => $this->processedPrescription->id,
        ]);
        
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'Prescription already has an associated order'
        ]);
    }

    /** @test */
    public function client_cannot_create_orders(): void
    {
        // Arrange
        Sanctum::actingAs($this->client);
        
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Access denied. Insufficient permissions.'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_orders(): void
    {
        // Arrange - No authentication
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function validates_required_fields(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);

        // Act
        $response = $this->postJson('/api/orders', []);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'prescription_id',
            'items'
        ]);
    }

    /** @test */
    public function order_creation_is_transactional(): void
    {
        // Arrange
        Sanctum::actingAs($this->pharmacist);
        
        // Create medication with very low stock to test transaction rollback
        $lowStockMedication = Medication::factory()->create([
            'current_quantity' => 1,
            'status' => 'active',
        ]);
        
        $orderData = [
            'prescription_id' => $this->processedPrescription->id,
            'items' => [
                [
                    'medication_id' => $this->medication->id,
                    'quantity' => 1 // This will succeed
                ],
                [
                    'medication_id' => $lowStockMedication->id,
                    'quantity' => 2 // This will fail due to insufficient stock
                ]
            ]
        ];

        // Act
        $response = $this->postJson('/api/orders', $orderData);

        // Assert
        $response->assertStatus(400);
        
        // Verify no order was created (transaction rolled back)
        $this->assertDatabaseMissing('orders', [
            'prescription_id' => $this->processedPrescription->id,
        ]);
        
        // Verify stock quantities unchanged
        $this->medication->refresh();
        $lowStockMedication->refresh();
        $this->assertEquals(100, $this->medication->current_quantity);
        $this->assertEquals(1, $lowStockMedication->current_quantity);
    }
}
```

### E2E Test

```dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:integration_test/integration_test.dart';
import 'package:pharmacy_app/main.dart' as app;

import 'test_helpers/e2e_test_helpers.dart';
import 'test_helpers/mock_backend.dart';

/// End-to-end test for the complete prescription upload workflow.
/// 
/// Tests the entire user journey from login through prescription upload
/// to order completion, verifying all UI interactions and backend
/// communication work correctly together.
void main() {
  IntegrationTestWidgetsFlutterBinding.ensureInitialized();

  group('Prescription Upload to Order Completion E2E', () {
    late MockBackend mockBackend;
    
    setUpAll(() async {
      mockBackend = MockBackend();
      await mockBackend.start();
    });
    
    tearDownAll(() async {
      await mockBackend.stop();
    });

    testWidgets('client can upload prescription and receive completed order', 
        (tester) async {
      // Arrange - Set up mock responses
      mockBackend.setupClientLoginResponse();
      mockBackend.setupPrescriptionUploadResponse();
      mockBackend.setupOrderNotificationResponse();
      
      // Launch app
      app.main();
      await tester.pumpAndSettle(const Duration(seconds: 2));

      // Step 1: Login as client
      await E2ETestHelpers.loginAsClient(tester);
      
      // Verify navigation to prescriptions screen
      expect(find.text('My Prescriptions'), findsOneWidget);
      
      // Step 2: Navigate to upload screen
      await tester.tap(find.byIcon(Icons.add));
      await tester.pumpAndSettle();
      
      expect(find.text('Upload Prescription'), findsOneWidget);
      
      // Step 3: Select and upload prescription image
      await tester.tap(find.text('Select Image'));
      await tester.pumpAndSettle();
      
      // Simulate image selection (in real test, would use image_picker)
      await E2ETestHelpers.selectMockImage(tester);
      
      // Upload prescription
      await tester.tap(find.text('Upload Prescription'));
      await tester.pumpAndSettle(const Duration(seconds: 3));
      
      // Verify upload success message
      expect(find.text('Prescription uploaded successfully'), findsOneWidget);
      
      // Step 4: Verify prescription appears in list
      await tester.tap(find.byIcon(Icons.arrow_back));
      await tester.pumpAndSettle();
      
      expect(find.byKey(const Key('prescription_card_1')), findsOneWidget);
      expect(find.text('Pending'), findsOneWidget);
      
      // Step 5: Simulate pharmacist processing prescription
      // (In real scenario, this would be done by pharmacist user)
      mockBackend.processPrescription(prescriptionId: 1);
      
      // Step 6: Simulate order creation by pharmacist
      mockBackend.createOrderFromPrescription(
        prescriptionId: 1,
        items: [
          {'medication_id': 1, 'quantity': 2, 'name': 'Paracetamol 500mg'}
        ],
        totalAmount: 21.00,
      );
      
      // Step 7: Check order notification
      await tester.tap(find.byIcon(Icons.notifications));
      await tester.pumpAndSettle();
      
      expect(find.text('New Order Created'), findsOneWidget);
      expect(find.text('Your order #1 has been created'), findsOneWidget);
      
      // Step 8: Navigate to orders list
      await tester.tap(find.text('Orders'));
      await tester.pumpAndSettle();
      
      // Verify order appears
      expect(find.text('Order #1'), findsOneWidget);
      expect(find.text('Total: \$21.00'), findsOneWidget);
      expect(find.text('In Preparation'), findsOneWidget);
      
      // Step 9: View order details
      await tester.tap(find.text('Order #1'));
      await tester.pumpAndSettle();
      
      // Verify order details
      expect(find.text('Order Details'), findsOneWidget);
      expect(find.text('Paracetamol 500mg'), findsOneWidget);
      expect(find.text('Quantity: 2'), findsOneWidget);
      expect(find.text('Status: In Preparation'), findsOneWidget);
      
      // Step 10: Simulate order status updates
      mockBackend.updateOrderStatus(orderId: 1, status: 'ready_for_delivery');
      
      // Pull to refresh to get status update
      await tester.drag(
        find.byType(RefreshIndicator),
        const Offset(0, 300),
      );
      await tester.pumpAndSettle();
      
      expect(find.text('Status: Ready for Delivery'), findsOneWidget);
      
      // Step 11: Simulate delivery completion
      mockBackend.updateOrderStatus(orderId: 1, status: 'completed');
      
      await tester.drag(
        find.byType(RefreshIndicator),
        const Offset(0, 300),
      );
      await tester.pumpAndSettle();
      
      expect(find.text('Status: Completed'), findsOneWidget);
      
      // Step 12: Verify final state
      await tester.tap(find.byIcon(Icons.arrow_back));
      await tester.pumpAndSettle();
      
      // Order should show as completed in list
      expect(find.text('Completed'), findsOneWidget);
      
      // Prescription should show as processed
      await tester.tap(find.text('Prescriptions'));
      await tester.pumpAndSettle();
      
      expect(find.text('Processed'), findsOneWidget);
    });

    testWidgets('handles network errors gracefully during upload', 
        (tester) async {
      // Arrange - Set up network failure scenario
      mockBackend.setupNetworkFailure();
      
      app.main();
      await tester.pumpAndSettle();
      
      await E2ETestHelpers.loginAsClient(tester);
      
      // Navigate to upload screen
      await tester.tap(find.byIcon(Icons.add));
      await tester.pumpAndSettle();
      
      // Attempt upload with network failure
      await tester.tap(find.text('Select Image'));
      await tester.pumpAndSettle();
      
      await E2ETestHelpers.selectMockImage(tester);
      
      await tester.tap(find.text('Upload Prescription'));
      await tester.pumpAndSettle(const Duration(seconds: 3));
      
      // Verify error handling
      expect(find.text('Network error. Please try again.'), findsOneWidget);
      expect(find.text('Retry'), findsOneWidget);
      
      // Test retry functionality
      mockBackend.clearNetworkFailure();
      mockBackend.setupPrescriptionUploadResponse();
      
      await tester.tap(find.text('Retry'));
      await tester.pumpAndSettle(const Duration(seconds: 3));
      
      expect(find.text('Prescription uploaded successfully'), findsOneWidget);
    });

    testWidgets('role-based access prevents unauthorized actions', 
        (tester) async {
      // Test that client cannot access pharmacist features
      mockBackend.setupClientLoginResponse();
      
      app.main();
      await tester.pumpAndSettle();
      
      await E2ETestHelpers.loginAsClient(tester);
      
      // Attempt to navigate to restricted routes should fail
      // This would typically be tested through deep linking or direct navigation
      // For brevity, we'll verify the bottom navigation doesn't show admin options
      expect(find.text('Manage Users'), findsNothing);
      expect(find.text('Reports'), findsNothing);
      expect(find.text('Process Prescriptions'), findsNothing);
      
      // Verify only client features are available
      expect(find.text('Prescriptions'), findsOneWidget);
      expect(find.text('Orders'), findsOneWidget);
      expect(find.text('Advice'), findsOneWidget);
    });
  });
}
```

## Testing Tools and Configuration

### Flutter Testing Dependencies

```yaml
# pubspec.yaml - Testing dependencies
dev_dependencies:
  flutter_test:
    sdk: flutter
  integration_test:
    sdk: flutter
  mockito: ^5.4.0
  build_runner: ^2.3.3
  shared_preferences_test: ^2.0.0
```

### Laravel Testing Configuration

```php
// phpunit.xml - PHPUnit configuration
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
        <exclude>
            <directory>app/Console/Commands</directory>
        </exclude>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="SANCTUM_STATEFUL_DOMAINS" value=""/>
    </php>
</phpunit>
```

## Continuous Integration Testing Pipeline

The testing strategy integrates with CI/CD pipelines to ensure code quality:

### GitHub Actions Workflow

```yaml
# .github/workflows/test.yml
name: Test Suite

on: [push, pull_request]

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: sqlite3, pdo_sqlite
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
      - name: Run PHPUnit tests
        run: vendor/bin/phpunit --coverage-clover coverage.xml
      - name: Upload coverage
        uses: codecov/codecov-action@v3

  frontend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Flutter
        uses: subosito/flutter
```

---