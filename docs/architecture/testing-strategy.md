# Testing Strategy

This section defines the comprehensive testing approach for the Pharmacy Management System. The strategy is designed to be pragmatic, focusing on verifying the correctness of critical business logic and ensuring the stability of core user journeys, which aligns with the project's scope as an educational capstone. The goal is not to achieve 100% code coverage but to build a robust safety net that validates the most important and highest-risk areas of the application.

## Testing Pyramid

Our testing strategy will be balanced across different levels of the testing pyramid. This ensures we have a fast, reliable feedback loop from unit tests while still validating the full system integration with a smaller number of end-to-end tests.

```plaintext
      /|\
     / | \
    / E2E \
   / Tests \
  /-----------\
 / Integration \
/     Tests     \
-----------------
/  Frontend | Backend  \
/ Unit Tests| Unit Tests \
--------------------------
```

## Test Organization

Tests will be co-located with the code they are testing within their respective `frontend` and `backend` directories.

### Frontend Tests

All frontend tests will reside in the `frontend/test/` directory, organized by feature.

*   **Unit Tests:** These will test individual widgets or state management providers in isolation. They are fast and focus on a single unit of logic.
*   **Widget Tests:** These will test a single screen or a small group of related widgets, verifying UI rendering and basic user interactions.
*   **Integration Tests:** These will test complete user flows within the Flutter app, mocking the API backend to ensure the frontend logic and state management work correctly together.

```plaintext
frontend/
└── test/
    ├── features/
    │   ├── auth/
    │   │   ├── login_screen_test.dart      # Widget Test
    │   │   └── auth_provider_test.dart     # Unit Test
    │   └── orders/
    │       └── order_list_provider_test.dart # Unit Test
    └── shared/
        └── widgets/
            └── primary_button_test.dart    # Widget Test
```

### Backend Tests

Backend tests will reside in the `backend/tests/` directory, separated into `Unit` and `Feature` tests as per Laravel conventions.

*   **Unit Tests:** These will test individual classes (e.g., Service classes) in isolation, with their dependencies mocked. This is critical for testing complex business logic without the overhead of the full framework or database.
*   **Feature Tests:** These will test the full request/response cycle of an API endpoint, including middleware, validation, and database interactions. They are essential for validating the RBAC rules and the transactional integrity of operations like order creation.

```plaintext
backend/
└── tests/
    ├── Unit/
    │   └── Services/
    │       └── OrderServiceTest.php
    └── Feature/
        ├── Auth/
        │   └── LoginTest.php
        └── Orders/
            └── OrderCreationTest.php
```

### End-to-End (E2E) Tests

E2E tests will validate complete user journeys across the entire stack (Flutter app -> Laravel API -> SQLite DB). These tests will be written using Flutter's `integration_test` package and will reside in `frontend/integration_test/`.

## Test Examples

The following examples provide concrete templates for each type of test, establishing a consistent pattern for developers to follow.

### Frontend Component Test (Widget Test)

This test validates the `PrimaryButton` component defined in the Frontend Architecture, ensuring it renders correctly and respects its disabled state.

File: `frontend/test/shared/widgets/primary_button_test.dart`
```dart
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:pharmacy_management/shared/widgets/primary_button.dart';

void main() {
  testWidgets('PrimaryButton displays text and is tappable when enabled', (WidgetTester tester) async {
    bool tapped = false;
    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: PrimaryButton(
            text: 'Submit',
            onPressed: () {
              tapped = true;
            },
          ),
        ),
      ),
    );

    expect(find.text('Submit'), findsOneWidget);
    await tester.tap(find.byType(ElevatedButton));
    expect(tapped, isTrue);
  });

  testWidgets('PrimaryButton is disabled when onPressed is null', (WidgetTester tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: PrimaryButton(
            text: 'Submit',
            onPressed: null, // Disabled state
          ),
        ),
      ),
    );

    final button = tester.widget<ElevatedButton>(find.byType(ElevatedButton));
    expect(button.enabled, isFalse);
  });
}
```

### Backend API Test (Feature Test)

This test validates the manager-only `/api/medications/low-stock` endpoint, ensuring it is correctly protected by the RBAC middleware and returns the expected data.

File: `backend/tests/Feature/Medication/LowStockReportTest.php`
```php
<?php

namespace Tests\Feature\Medication;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_low_stock_report(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        Medication::factory()->create(['name' => 'Low Stock Med', 'current_quantity' => 5, 'minimum_threshold' => 10]);
        Medication::factory()->create(['name' => 'OK Stock Med', 'current_quantity' => 50, 'minimum_threshold' => 10]);

        $response = $this->actingAs($manager)->getJson('/api/medications/low-stock');

        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment(['name' => 'Low Stock Med']);
    }

    public function test_non_manager_cannot_view_low_stock_report(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->getJson('/api/medications/low-stock');

        $response->assertStatus(403); // Forbidden
    }
}
```

### Critical Test Case Prototype: Transactional Order Processing

This test serves as the definitive, executable specification for the system's most critical business workflow: `UC-17: Process Prescription into Order`. It validates the success path, the transactional rollback on failure, and the RBAC security guard, providing a complete blueprint for implementing high-stakes business logic.

File: `backend/tests/Feature/Prescription/ProcessPrescriptionTest.php`
```php
<?php

namespace Tests\Feature\Prescription;

use App\Models\Medication;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessPrescriptionTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $salesperson;
    private Prescription $prescription;
    private Medication $medicationA;
    private Medication $medicationB;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange: Create the necessary users and data for our tests.
        $this->client = User::factory()->create(['role' => 'client']);
        $this->salesperson = User::factory()->create(['role' => 'salesperson']);

        $this->prescription = Prescription::factory()->create([
            'client_id' => $this->client->id,
            'status' => 'pending',
        ]);

        $this->medicationA = Medication::factory()->create(['current_quantity' => 100]);
        $this->medicationB = Medication::factory()->create(['current_quantity' => 50]);
    }

    /**
     * @test
     * Validates the "happy path" where stock is sufficient.
     */
    public function it_successfully_processes_a_prescription_into_an_order_with_sufficient_stock(): void
    {
        // Act: Make the API call as the salesperson to process the prescription.
        $response = $this->actingAs($this->salesperson)->putJson(
            "/api/prescriptions/{$this->prescription->id}/process",
            [
                'items' => [
                    ['medication_id' => $this->medicationA->id, 'quantity' => 10],
                    ['medication_id' => $this->medicationB->id, 'quantity' => 5],
                ],
            ]
        );

        // Assert: Verify the outcome.
        $response->assertStatus(201) // 201 Created for a new resource (the order)
                 ->assertJsonStructure(['message', 'order_id', 'order_status']);

        // Assert: The prescription status was updated.
        $this->assertDatabaseHas('prescriptions', [
            'id' => $this->prescription->id,
            'status' => 'processed',
            'processed_by' => $this->salesperson->id,
        ]);

        // Assert: A new order was created.
        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        $this->assertEquals($this->client->id, $order->client_id);
        $this->assertEquals('in_preparation', $order->status);

        // Assert: The stock quantities were correctly decremented.
        $this->assertDatabaseHas('medications', [
            'id' => $this->medicationA->id,
            'current_quantity' => 90, // 100 - 10
        ]);
        $this->assertDatabaseHas('medications', [
            'id' => $this->medicationB->id,
            'current_quantity' => 45, // 50 - 5
        ]);

        // Assert: A notification was created for the client.
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->client->id,
            'type' => 'order_status',
        ]);
    }

    /**
     * @test
     * Validates the critical failure case where stock is insufficient.
     * This test proves that the entire operation is rolled back.
     */
    public function it_fails_to_process_and_rolls_back_the_transaction_if_stock_is_insufficient(): void
    {
        // Arrange: Store the initial stock levels before the operation.
        $initialStockA = $this->medicationA->current_quantity; // 100
        $initialStockB = $this->medicationB->current_quantity; // 50

        // Act: Attempt to process an order where one item has insufficient stock.
        $response = $this->actingAs($this->salesperson)->putJson(
            "/api/prescriptions/{$this->prescription->id}/process",
            [
                'items' => [
                    ['medication_id' => $this->medicationA->id, 'quantity' => 10],
                    ['medication_id' => $this->medicationB->id, 'quantity' => 51], // Not enough stock
                ],
            ]
        );

        // Assert: Verify the API response.
        $response->assertStatus(409); // 409 Conflict is appropriate for a business rule violation.

        // Assert: The prescription status REMAINS 'pending'.
        $this->assertDatabaseHas('prescriptions', [
            'id' => $this->prescription->id,
            'status' => 'pending',
        ]);

        // Assert: NO order was created.
        $this->assertDatabaseCount('orders', 0);

        // CRITICAL ASSERTION: Verify that the stock for BOTH medications is unchanged,
        // proving that the entire database transaction was rolled back.
        $this->assertDatabaseHas('medications', [
            'id' => $this->medicationA->id,
            'current_quantity' => $initialStockA, // Should still be 100
        ]);
        $this->assertDatabaseHas('medications', [
            'id' => $this->medicationB->id,
            'current_quantity' => $initialStockB, // Should still be 50
        ]);

        // Assert: NO notification was sent.
        $this->assertDatabaseCount('notifications', 0);
    }

    /**
     * @test
     * Validates the RBAC security, ensuring only authorized roles can process prescriptions.
     */
    public function it_prevents_a_user_without_the_salesperson_role_from_processing_a_prescription(): void
    {
        // Arrange: Create a user with a non-salesperson role (e.g., client).
        $unauthorizedUser = User::factory()->create(['role' => 'client']);

        // Act: Attempt to process the prescription as the unauthorized user.
        $response = $this->actingAs($unauthorizedUser)->putJson(
            "/api/prescriptions/{$this->prescription->id}/process",
            [
                'items' => [
                    ['medication_id' => $this->medicationA->id, 'quantity' => 1],
                ],
            ]
        );

        // Assert: The request is forbidden.
        $response->assertStatus(403);

        // Assert: The prescription status remains unchanged.
        $this->assertDatabaseHas('prescriptions', [
            'id' => $this->prescription->id,
            'status' => 'pending',
        ]);

        // Assert: No order was created.
        $this->assertDatabaseCount('orders', 0);
    }
}
```

This test serves as a powerful architectural artifact for several key reasons:

1.  **Executable Specification:** It is a precise, machine-verifiable definition of the business logic for `UC-17`, covering success, business rule failure, and now, security.
2.  **Validation of Transactional Integrity:** The second test case definitively proves that our database transaction and rollback mechanism works as designed, providing immense confidence in the system's ability to prevent data corruption.
3.  **Validation of Security:** The newly added third test case validates the RBAC security layer, ensuring that the endpoint is correctly protected and that unauthorized attempts have no side effects on the system's state.
4.  **Definitive Template:** This file now establishes the gold standard for testing all future complex, transactional, and secure endpoints. It provides a complete template covering the three most critical aspects of any API endpoint.
5.  **Drives Robust Implementation:** This comprehensive test suite forces the developer to build the logic correctly, incorporating database transactions, proper exception handling, and secure authorization middleware.

### End-to-End (E2E) Test

This test validates the complete login user journey, from entering credentials in the Flutter app to receiving a successful response from the live backend.

File: `frontend/integration_test/login_flow_test.dart`
```dart
import 'package:flutter_test/flutter_test.dart';
import 'package:integration_test/integration_test.dart';
import 'package:pharmacy_management/main.dart' as app;

void main() {
  IntegrationTestWidgetsFlutterBinding.ensureInitialized();

  testWidgets('Login Flow E2E Test', (WidgetTester tester) async {
    // Start the app.
    app.main();
    await tester.pumpAndSettle();

    // Find the email and password fields.
    final emailField = find.byKey(const Key('login_email_field'));
    final passwordField = find.byKey(const Key('login_password_field'));
    final loginButton = find.byKey(const Key('login_button'));

    // Enter credentials.
    // Assumes a user has been seeded in the backend database.
    await tester.enterText(emailField, 'client@example.com');
    await tester.enterText(passwordField, 'password');

    // Tap the login button and wait for the app to settle.
    await tester.tap(loginButton);
    await tester.pumpAndSettle();

    // Verify that we have navigated to the dashboard.
    expect(find.byKey(const Key('dashboard_screen')), findsOneWidget);
  });
}
```

---
