# Frontend Architecture

This section defines the specific architectural patterns, structure, and standards for the Flutter mobile application. The primary goal is to create a codebase that is clean, scalable, testable, and easy for developers (or AI agents) to navigate. The architecture is built upon the decisions made in the **Tech Stack** section (Flutter with Provider for state management) and is designed to directly implement the user flows and components detailed in the **UI/UX Specification**.

## Component Architecture

The frontend will be built using a component-based approach, composing the UI from small, reusable widgets. This promotes consistency and accelerates development.

### Component Organization

To maintain a clean and scalable project, the Flutter application's `lib` directory will be organized using a feature-based structure. This approach groups all files related to a specific feature (e.g., authentication, orders) together, making the codebase highly modular and easy to understand.

```plaintext
lib/
├── main.dart                 # App entry point and root configuration
│
├── core/                     # Core application logic, not tied to any feature
│   ├── api/                  # API client service (Dio setup, interceptors)
│   ├── navigation/           # Centralized routing configuration (Go-Router)
│   ├── security/             # Secure storage wrapper (flutter_secure_storage)
│   ├── db/                   # Local SQLite database helper (sqflite)
│   └── models/               # Data models shared across the app
│
├── features/                 # Each feature of the app is a self-contained module
│   ├── auth/                 # Authentication feature (login, register)
│   │   ├── providers/        # State management (ViewModels) for auth
│   │   ├── screens/          # UI screens (login_screen.dart)
│   │   └── widgets/          # Feature-specific widgets
│   │
│   ├── orders/               # Order management feature
│   │   ├── providers/
│   │   ├── screens/
│   │   └── widgets/
│   │
│   └── ...                   # Other features (prescriptions, admin, etc.)
│
└── shared/                   # Widgets and utilities shared across multiple features
    ├── widgets/              # Reusable UI components (e.g., PrimaryButton, OrderCard)
    ├── theme/                # App theme, colors, typography
    └── utils/                # Utility functions (e.g., formatters, validators)
```

### Component Template

To ensure consistency and enforce best practices, all new UI components should follow a standard template. The following `PrimaryButton` is an example of a reusable, stateless widget that encapsulates styling and behavior, as defined in the UI/UX Specification's component library.

```dart
import 'package:flutter/material.dart';

/// A primary call-to-action button used for the most important action on a screen.
///
/// This button adheres to the application's style guide for primary actions.
/// It handles its own disabled state based on the [onPressed] callback.
class PrimaryButton extends StatelessWidget {
  /// The text to display inside the button.
  final String text;

  /// The callback that is called when the button is tapped.
  /// If null, the button will be displayed in a disabled state.
  final VoidCallback? onPressed;

  const PrimaryButton({
    super.key,
    required this.text,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      style: ElevatedButton.styleFrom(
        // Colors and styles would be sourced from the shared theme.
        // e.g., Theme.of(context).colorScheme.primary
        backgroundColor: const Color(0xFF7B8FF7), 
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
        textStyle: const TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
      onPressed: onPressed,
      child: Text(text),
    );
  }
}
```

## State Management Architecture

This section details the strategy for managing state within the Flutter application. As defined in the **Tech Stack**, we will use the **Provider** package. Our approach is based on the **Model-View-ViewModel (MVVM)** pattern, where the `Provider` acts as the ViewModel, creating a clear separation between the UI (View) and the business logic/state.

### State Structure

We will categorize state into two types to ensure a clean and efficient architecture:

1.  **Feature State:** This is the most common type of state, scoped to a specific feature or screen. It holds data and UI state that is only relevant within that feature's context (e.g., the list of orders on the `OrderManagementScreen`). Each feature module in `lib/features/` will have its own providers.
2.  **Global State:** This is state that needs to be accessed across multiple, unrelated features. It should be used sparingly to avoid tight coupling. Examples include the current authentication status and the authenticated user's profile. Global providers will be initialized at the top of the widget tree in `main.dart`.

### Provider Implementation Pattern

We will primarily use `ChangeNotifierProvider` combined with a class that extends `ChangeNotifier` for our ViewModels. This pattern provides a simple yet powerful way to manage and expose two types of state: **persistent UI state** (like loading indicators or data) and **one-time events** (like snackbar messages or navigation triggers).

**Provider (ViewModel) Template with Event Handling:**

All providers will follow a consistent structure. They will encapsulate business logic, manage their own state, and notify listeners of changes. To handle one-time events robustly, we will use a nullable `event` property that is consumed and cleared by the UI.

```dart
import 'package:flutter/foundation.dart';
import 'package:pharmacy_management/core/api/api_client_service.dart';
import 'package:pharmacy_management/core/models/user_model.dart';
// Assumes the existence of a feature-specific ApiService
import 'package:pharmacy_management/features/account/api/account_api_service.dart';

// --- 1. Define a shared, extensible base class for all view events. ---
abstract class ViewEvent {}

class ShowSuccessMessage extends ViewEvent {
  final String message;
  ShowSuccessMessage(this.message);
}

class NavigateTo extends ViewEvent {
  final String routeName;
  NavigateTo(this.routeName);
}

/// Manages the state and events for the MyAccountScreen.
class MyAccountProvider extends ChangeNotifier {
  final AccountApiService _accountApiService;

  MyAccountProvider(this._accountApiService);
  // ...
  Future<void> saveUserProfile(String newName) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // The provider now correctly delegates the API call to its specific service.
      final updatedUser = await _accountApiService.updateUser(newName);
      _user = updatedUser;
      _event = ShowSuccessMessage("Profile saved successfully!");
    } on ApiException catch (e) { // Assumes ApiException is handled by the ApiService
      _error = e.message;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
```

**Consuming State and Events in the UI (View):**

The UI will use a combination of `Consumer` (or `context.watch`) for rebuilding based on persistent state, and a listener pattern to handle one-time events without rebuilding.

```dart
// At the top of the MyAccountScreen's build method.
// This listener handles side effects without rebuilding the UI.
ProviderListener<MyAccountProvider>(
  listener: (context, provider) {
    if (provider.event != null) {
      final event = provider.event!;
      // Handle the specific event type
      if (event is ShowSuccessMessage) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(event.message)),
        );
      }
      // CRITICAL: Clear the event immediately after handling it
      // to prevent it from being triggered again on the next rebuild.
      provider.clearEvent();
    }
  },
  // The Consumer widget handles rebuilding the UI based on persistent state.
  child: Consumer<MyAccountProvider>(
    builder: (context, provider, child) {
      if (provider.isLoading) {
        return const Center(child: CircularProgressIndicator());
      }
      if (provider.error != null) {
        return Center(child: Text(provider.error!));
      }
      if (provider.user != null) {
        return UserProfileCard(user: provider.user!);
      }
      return const Center(child: Text("No user data available."));
    },
  ),
)
```

## Routing Architecture

This section defines the application's navigation structure and strategy. A clear and centralized routing architecture is essential for creating a predictable user experience and a maintainable codebase. It governs how users move between different screens, handles authentication guards, and manages the navigation stack.

### Routing Strategy

We will implement a **declarative, type-safe routing** strategy using the official **`go_router`** package. This approach is chosen for its centralization, security features, and robust developer experience.

*   **Centralized Configuration:** All possible navigation paths are defined in a single location, creating a clear map of the application.
*   **Type-Safe Navigation:** We will create a static API for navigation, eliminating the use of raw, error-prone strings in UI code.
*   **Robust Auth Handling:** A centralized `redirect` mechanism will serve as an authentication guard, ensuring all session-related routing logic is handled in one place.

### Route Configuration and Type-Safe API

All route definitions and their corresponding type-safe navigation methods will be consolidated within `lib/core/navigation/app_router.dart`. This file serves as the single source of truth for all navigation logic.

```dart
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:pharmacy_management/features/auth/screens/login_screen.dart';
import 'package:pharmacy_management/features/dashboard/screens/dashboard_screen.dart';
import 'package.dart';
import 'package:pharmacy_management/features/orders/screens/order_list_screen.dart';
import 'package:pharmacy_management/features/orders/screens/order_details_screen.dart';
import 'package:pharmacy_management/shared/widgets/scaffold_with_nav_bar.dart';
import 'package:pharmacy_management/features/auth/providers/auth_provider.dart';

class AppRouter {
  final AuthProvider authProvider;

  AppRouter(this.authProvider);

  late final GoRouter router = GoRouter(
    refreshListenable: authProvider,
    initialLocation: '/login',
    routes: [
      // --- Public Routes ---
      GoRoute(
        name: 'login', // 1. Add a unique name for type-safe access
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),

      // --- Authenticated Routes with a Shared UI Shell ---
      ShellRoute(
        builder: (context, state, child) {
          return ScaffoldWithNavBar(child: child);
        },
        routes: [
          GoRoute(
            name: 'dashboard',
            path: '/dashboard',
            builder: (context, state) => const DashboardScreen(),
          ),
          GoRoute(
            name: 'orders',
            path: '/orders',
            builder: (context, state) => const OrderListScreen(),
            routes: [
              // 2. Define named, parameterized sub-routes
              GoRoute(
                name: 'orderDetails',
                path: ':orderId', // e.g., matches '/orders/123'
                builder: (context, state) {
                  final orderId = state.pathParameters['orderId']!;
                  return OrderDetailsScreen(orderId: orderId);
                },
              ),
            ],
          ),
          // ... other main app routes
        ],
      ),
    ],
    
    redirect: (BuildContext context, GoRouterState state) {
      final bool loggedIn = authProvider.isAuthenticated;
      final bool isLoggingIn = state.matchedLocation == '/login';

      if (!loggedIn && !isLoggingIn) return '/login';
      if (loggedIn && isLoggingIn) return '/dashboard';

      return null;
    },
  );

  // --- 3. Create the Type-Safe Navigation API ---
  static void goToLogin(BuildContext context) {
    context.goNamed('login');
  }

  static void goToDashboard(BuildContext context) {
    context.goNamed('dashboard');
  }

  static void goToOrders(BuildContext context) {
    context.goNamed('orders');
  }

  static void goToOrderDetails(BuildContext context, {required String orderId}) {
    context.goNamed('orderDetails', pathParameters: {'orderId': orderId});
  }
}
```

### Protected Route Pattern

The `redirect` function serves as our single, authoritative **authentication guard**. It is executed before every navigation event, ensuring that an unauthenticated user attempting to access any protected route will be immediately and automatically redirected to the `/login` screen. This centralized approach is far more secure and maintainable than placing authentication checks within individual screens.

## API Integration

This section defines the standardized pattern for how the Flutter application will communicate with the Laravel backend.
**Architectural constraint:** **All HTTP requests MUST be channeled through a single, centralized `ApiClientService`.** Feature-specific code must use feature `ApiService`s (e.g., `AuthApiService`, `OrderApiService`) which themselves use the shared `ApiClientService`. **Feature providers MUST NOT use `ApiClientService` directly.**

This gives a single point for managing authentication, structured error handling, logging, timeouts, and any cross-cutting HTTP concerns.

### Structured Error Handling (why this exists — put this early)

To provide specific, actionable feedback to the user and avoid leaking generic network exceptions into UI/state layers, we use a custom `ApiException` to carry structured error information (status code, human message, validation errors) from backend → API layer → state/UI layer. Providers and state managers should handle `ApiException` instances and map them to friendly UI states/messages.

File: `lib/core/api/api_exception.dart`

```dart
/// A custom exception class to represent structured API errors.
class ApiException implements Exception {
  final int? statusCode;
  final String message;
  final Map<String, dynamic>? errors; // For validation errors (e.g., 422)

  ApiException({
    this.statusCode,
    required this.message,
    this.errors,
  });

  @override
  String toString() {
    return 'ApiException: [Status Code: $statusCode] $message';
  }
}
```

### API Client Configuration

We use the `dio` package as our HTTP client. `ApiClientService` is a singleton responsible for creating/configuring the `Dio` instance (base URL, timeouts), attaching interceptors for authentication, logging (in debug), and parsing backend responses into `ApiException`.

Place this file at: `lib/core/api/api_client_service.dart`

```dart
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:pharmacy_management/core/api/api_exception.dart';
import 'package:pharmacy_management/core/security/secure_storage_service.dart';

/// A centralized service for all API communication.
///
/// This service configures a Dio instance with a base URL, interceptors for
/// logging, and automatic handling of authentication tokens and structured errors.
class ApiClientService {
  final Dio _dio;
  final SecureStorageService _secureStorage;

  ApiClientService(this._secureStorage)
      : _dio = Dio(BaseOptions(
          baseUrl: 'http://localhost:8000/api',
          connectTimeout: const Duration(seconds: 5),
          receiveTimeout: const Duration(seconds: 3),
        )) {
    _dio.interceptors.add(_createAppInterceptor());
    if (kDebugMode) {
      _dio.interceptors.add(LogInterceptor(responseBody: true));
    }
  }

  Dio get dio => _dio;

  Interceptor _createAppInterceptor() {
    return InterceptorsWrapper(
      onRequest: (options, handler) async {
        // Attach token to all routes except auth endpoints
        if (options.path != '/login' && options.path != '/register') {
          final token = await _secureStorage.readToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
        }
        return handler.next(options);
      },
      onError: (DioException e, handler) {
        // Parse Laravel (or general JSON) error responses into ApiException.
        if (e.response != null) {
          final responseData = e.response!.data;
          String message = "An unknown error occurred.";
          Map<String, dynamic>? validationErrors;

          if (responseData is Map<String, dynamic>) {
            message = responseData['message'] ?? message;
            validationErrors = (responseData['errors'] is Map)
                ? Map<String, dynamic>.from(responseData['errors'])
                : null;
          }

          final apiException = ApiException(
            statusCode: e.response!.statusCode,
            message: message,
            errors: validationErrors,
          );

          // Re-embed the structured ApiException inside a DioException so callers
          // can detect and re-throw the structured error.
          final customError = DioException(
            requestOptions: e.requestOptions,
            response: e.response,
            error: apiException,
          );

          return handler.next(customError);
        }

        // Non-HTTP errors (e.g., timeouts, connectivity) — pass through.
        return handler.next(e);
      },
    );
  }
}
```

**Notes**

* The interceptor converts JSON error payloads into `ApiException` (message, status, validation errors). If your backend is Laravel, the error format typically includes `message` and `errors` — this is already handled; if your backend differs, adapt parsing logic.
* Logging is enabled only in `kDebugMode`.
* Timeouts are configured centrally so you can tune them in one place.

### API Service Template (how feature code should use the client)

Feature-specific services encapsulate *what* the request is (endpoints, payload shapes) while the `ApiClientService` encapsulates *how* the request is made (auth, retries, parsing errors). **Feature providers / state managers MUST call the feature `ApiService` methods and handle `ApiException`.** `ApiService`s should catch Dio exceptions, extract `ApiException` where present, and re-throw it to the state layer.

File: `lib/features/auth/api/auth_api_service.dart`

```dart
import 'package:dio/dio.dart';
import 'package:pharmacy_management/core/api/api_client_service.dart';
import 'package:pharmacy_management/core/api/api_exception.dart';

class AuthApiService {
  final Dio _dio;

  AuthApiService(ApiClientService apiClient) : _dio = apiClient.dio;

  /// Attempts to log in the user and returns the auth token.
  /// Re-throws [ApiException] for the state management layer to consume.
  Future<String> login(String email, String password) async {
    try {
      final response = await _dio.post(
        '/login',
        data: {'email': email, 'password': password},
      );
      return response.data['token'] as String;
    } on DioException catch (e) {
      // If our interceptor created a structured ApiException, re-throw it.
      if (e.error is ApiException) {
        throw e.error as ApiException;
      }
      // Fallback for network / timeout / other Dio errors.
      throw ApiException(message: "Network error. Please try again.");
    }
  }
}
```

---
