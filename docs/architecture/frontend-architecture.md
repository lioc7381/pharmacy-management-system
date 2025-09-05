# Frontend Architecture

<!--docs/architecture/[title].md-->

This document defines the frontend-specific architecture for the Flutter Pharmacy Management System, establishing the patterns, structure, and conventions that will guide all frontend development. This architecture is built on the foundation of the simplified Flutter stack defined in the tech stack document, prioritizing maintainability and clarity over complexity.

## Component Architecture

### Component Organization

The Flutter frontend follows a feature-based organization with clear separation of concerns and strict adherence to the architectural mandates defined in the coding standards.

```
lib/
├── main.dart                           # App entry point
├── app/
│   ├── app.dart                        # Main app widget with theme and routing
│   └── routes.dart                     # Centralized go_router configuration
├── core/
│   ├── constants/
│   │   ├── api_constants.dart          # API endpoints and base URLs
│   │   └── app_constants.dart          # App-wide constants
│   ├── services/
│   │   ├── api_client.dart             # Central HTTP client wrapper
│   │   └── storage_service.dart        # SharedPreferences wrapper
│   ├── widgets/
│   │   ├── primary_button.dart         # Reusable UI components
│   │   ├── loading_indicator.dart
│   │   └── error_widget.dart
│   └── utils/
│       ├── validators.dart             # Input validation utilities
│       └── formatters.dart             # Text/date formatting utilities
├── features/
│   ├── auth/
│   │   ├── providers/
│   │   │   └── auth_provider.dart      # Authentication state management
│   │   ├── services/
│   │   │   └── auth_api_service.dart   # Auth-specific API calls
│   │   ├── screens/
│   │   │   ├── login_screen.dart
│   │   │   └── register_screen.dart
│   │   └── widgets/
│   │       └── login_form.dart
│   ├── prescriptions/
│   │   ├── providers/
│   │   │   └── prescriptions_provider.dart
│   │   ├── services/
│   │   │   └── prescriptions_api_service.dart
│   │   ├── screens/
│   │   │   ├── prescription_list_screen.dart
│   │   │   └── prescription_upload_screen.dart
│   │   └── widgets/
│   │       ├── prescription_card.dart
│   │       └── image_picker_widget.dart
│   ├── orders/
│   │   ├── providers/
│   │   │   └── orders_provider.dart
│   │   ├── services/
│   │   │   └── orders_api_service.dart
│   │   ├── screens/
│   │   │   ├── order_list_screen.dart
│   │   │   └── order_detail_screen.dart
│   │   └── widgets/
│   │       └── order_card.dart
│   └── notifications/
│       ├── providers/
│       │   └── notification_provider.dart    # Global provider
│       ├── services/
│       │   └── notification_api_service.dart
│       └── widgets/
│           └── notification_badge.dart
└── shared/
    ├── models/
    │   ├── user.dart                   # Dart data models matching Laravel
    │   ├── medication.dart
    │   ├── order.dart
    │   └── prescription.dart
    └── enums/
        ├── user_role.dart
        └── order_status.dart
```

### Component Template

All Flutter widgets must follow this standardized template with mandatory documentation:

```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

/// A reusable widget that displays order information in card format.
/// 
/// This widget handles the display of order details including status,
/// total amount, and action buttons based on the current order state.
/// 
/// Example usage:
/// ```dart
/// OrderCard(
///   order: orderInstance,
///   onTap: () => _navigateToOrderDetail(orderInstance.id),
/// )
/// ```
class OrderCard extends StatelessWidget {
  /// The order data to display in the card
  final Order order;
  
  /// Callback function executed when the card is tapped
  final VoidCallback? onTap;
  
  /// Creates an [OrderCard] widget.
  /// 
  /// The [order] parameter is required and contains the order information
  /// to display. The [onTap] parameter is optional and defines the action
  /// when the user taps on the card.
  const OrderCard({
    super.key,
    required this.order,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(),
              const SizedBox(height: 12),
              _buildOrderDetails(),
              const SizedBox(height: 8),
              _buildStatusChip(),
            ],
          ),
        ),
      ),
    );
  }

  /// Builds the header section with order ID and date
  Widget _buildHeader() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          'Order #${order.id}',
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
        Text(
          _formatDate(order.createdAt),
          style: TextStyle(
            color: Colors.grey[600],
            fontSize: 12,
          ),
        ),
      ],
    );
  }

  /// Builds the order details section
  Widget _buildOrderDetails() {
    return Text(
      'Total: \$${order.totalAmount.toStringAsFixed(2)}',
      style: const TextStyle(
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
    );
  }

  /// Builds the status indicator chip
  Widget _buildStatusChip() {
    return Chip(
      label: Text(
        _getStatusDisplayText(order.status),
        style: const TextStyle(fontSize: 12),
      ),
      backgroundColor: _getStatusColor(order.status),
    );
  }

  /// Formats the date for display
  String _formatDate(DateTime date) {
    return '${date.day}/${date.month}/${date.year}';
  }

  /// Gets display text for order status
  String _getStatusDisplayText(OrderStatus status) {
    switch (status) {
      case OrderStatus.inPreparation:
        return 'In Preparation';
      case OrderStatus.readyForDelivery:
        return 'Ready for Delivery';
      case OrderStatus.completed:
        return 'Completed';
      case OrderStatus.cancelled:
        return 'Cancelled';
      case OrderStatus.failedDelivery:
        return 'Failed Delivery';
    }
  }

  /// Gets color for order status
  Color _getStatusColor(OrderStatus status) {
    switch (status) {
      case OrderStatus.inPreparation:
        return Colors.orange[100]!;
      case OrderStatus.readyForDelivery:
        return Colors.blue[100]!;
      case OrderStatus.completed:
        return Colors.green[100]!;
      case OrderStatus.cancelled:
        return Colors.red[100]!;
      case OrderStatus.failedDelivery:
        return Colors.red[200]!;
    }
  }
}
```

## State Management Architecture

### State Structure

The state management follows the Provider pattern with clear distinction between global and feature-scoped providers:

```dart
import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/auth_api_service.dart';

/// Global provider that manages authentication state across the entire app.
/// 
/// This provider handles login, logout, and user session management.
/// It must be registered at the app level to enable role-based routing
/// and global access control.
class AuthProvider extends ChangeNotifier {
  final AuthApiService _authService;
  
  User? _currentUser;
  bool _isLoading = false;
  String? _errorMessage;
  
  /// Creates an [AuthProvider] with the required [AuthApiService].
  AuthProvider(this._authService);
  
  // Getters
  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _currentUser != null;
  String? get errorMessage => _errorMessage;
  
  /// Attempts to log in with the provided credentials.
  /// 
  /// Returns true if login is successful, false otherwise.
  /// Sets [errorMessage] if login fails.
  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _clearError();
    
    try {
      final user = await _authService.login(email, password);
      _currentUser = user;
      notifyListeners();
      return true;
    } catch (e) {
      _setError(e.toString());
      return false;
    } finally {
      _setLoading(false);
    }
  }
  
  /// Logs out the current user and clears all session data.
  Future<void> logout() async {
    _setLoading(true);
    
    try {
      await _authService.logout();
    } catch (e) {
      // Log error but don't prevent logout
      debugPrint('Logout error: $e');
    } finally {
      _currentUser = null;
      _clearError();
      _setLoading(false);
      notifyListeners();
    }
  }
  
  /// Attempts to restore user session from stored token.
  Future<void> tryAutoLogin() async {
    _setLoading(true);
    
    try {
      final user = await _authService.getCurrentUser();
      _currentUser = user;
    } catch (e) {
      // Silent fail for auto-login
      _currentUser = null;
    } finally {
      _setLoading(false);
      notifyListeners();
    }
  }
  
  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void _setError(String error) {
    _errorMessage = error;
    notifyListeners();
  }
  
  void _clearError() {
    _errorMessage = null;
  }
}

/// Feature-scoped provider for managing prescription-related state.
/// 
/// This provider handles prescription list, upload operations, and
/// local UI state for prescription screens.
class PrescriptionsProvider extends ChangeNotifier {
  final PrescriptionsApiService _prescriptionsService;
  
  List<Prescription> _prescriptions = [];
  bool _isLoading = false;
  String? _errorMessage;
  String? _successMessage;
  
  /// Creates a [PrescriptionsProvider] with the required service.
  PrescriptionsProvider(this._prescriptionsService);
  
  // Getters
  List<Prescription> get prescriptions => _prescriptions;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String? get successMessage => _successMessage;
  
  /// Fetches the list of prescriptions for the current user.
  Future<void> loadPrescriptions() async {
    _setLoading(true);
    _clearMessages();
    
    try {
      _prescriptions = await _prescriptionsService.getPrescriptions();
      notifyListeners();
    } catch (e) {
      _setError('Failed to load prescriptions: $e');
    } finally {
      _setLoading(false);
    }
  }
  
  /// Uploads a new prescription image.
  /// 
  /// Returns true if upload is successful, false otherwise.
  Future<bool> uploadPrescription(String imagePath) async {
    _setLoading(true);
    _clearMessages();
    
    try {
      final prescription = await _prescriptionsService.uploadPrescription(imagePath);
      _prescriptions.insert(0, prescription);
      _setSuccess('Prescription uploaded successfully');
      notifyListeners();
      return true;
    } catch (e) {
      _setError('Failed to upload prescription: $e');
      return false;
    } finally {
      _setLoading(false);
    }
  }
  
  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void _setError(String error) {
    _errorMessage = error;
    notifyListeners();
  }
  
  void _setSuccess(String message) {
    _successMessage = message;
    notifyListeners();
  }
  
  void _clearMessages() {
    _errorMessage = null;
    _successMessage = null;
  }
  
  /// Clears success message (typically called after showing snackbar).
  void clearSuccessMessage() {
    _successMessage = null;
    notifyListeners();
  }
}
```

### State Management Patterns

- **Global Providers**: Used for cross-cutting concerns that affect multiple features (AuthProvider, NotificationProvider)
- **Feature-Scoped Providers**: Used for business logic specific to a single feature area (PrescriptionsProvider, OrdersProvider)
- **Local StatefulWidget State**: Used for purely UI concerns (form field controllers, animation states, local loading indicators)
- **One-Time Events**: Handled through simple state changes in ChangeNotifier (string? successMessage, bool navigationRequested)
- **No ViewEvent Pattern**: Explicitly prohibited to maintain simplicity
- **Consumer/Selector Usage**: Use Consumer<T> for widgets that need to rebuild on state changes, Selector<T,R> for performance optimization when only specific properties matter
- **State Initialization**: Providers must initialize their state in the constructor or through explicit load methods called from initState()

## Routing Architecture

### Route Organization

The routing structure uses go_router with centralized configuration and role-based access control:

```
Routes Structure:
/
├── /login                              # Public route
├── /register                          # Public route
├── /client/                           # Client role routes
│   ├── /prescriptions                 # Prescription list
│   ├── /prescriptions/upload          # Upload prescription
│   ├── /orders                        # Order history
│   ├── /orders/:id                    # Order details
│   └── /advice                        # Advice requests
├── /pharmacist/                       # Pharmacist role routes
│   ├── /prescriptions                 # Prescriptions to review
│   ├── /prescriptions/:id/process     # Process prescription
│   ├── /orders                        # Order management
│   └── /advice                        # Advice requests to answer
├── /salesperson/                      # Salesperson role routes
│   ├── /medications                   # Medication catalog
│   ├── /medications/add               # Add medication
│   ├── /medications/:id/edit          # Edit medication
│   └── /inventory                     # Inventory management
├── /delivery/                         # Delivery role routes
│   ├── /orders                        # Orders for delivery
│   └── /orders/:id                    # Order delivery details
└── /manager/                          # Manager role routes
    ├── /dashboard                     # Management dashboard
    ├── /users                         # User management
    ├── /reports                       # Analytics and reports
    └── /settings                      # System settings
```

### Protected Route Pattern

The go_router configuration implements centralized role-based access control:

```dart
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../features/auth/providers/auth_provider.dart';
import '../shared/enums/user_role.dart';

/// Centralized router configuration with role-based access control.
/// 
/// This router handles authentication redirects and role-based route protection
/// through a single redirect function, eliminating scattered auth logic.
class AppRouter {
  static GoRouter createRouter() {
    return GoRouter(
      initialLocation: '/',
      redirect: (context, state) {
        final authProvider = context.read<AuthProvider>();
        final isAuthenticated = authProvider.isAuthenticated;
        final currentUser = authProvider.currentUser;
        final location = state.location;
        
        // Public routes - always accessible
        final publicRoutes = ['/login', '/register'];
        if (publicRoutes.contains(location)) {
          // If already authenticated, redirect to role-based home
          if (isAuthenticated) {
            return _getRoleBasedHomePath(currentUser!.role);
          }
          return null; // Allow access to public routes
        }
        
        // Protected routes - require authentication
        if (!isAuthenticated) {
          return '/login';
        }
        
        // Role-based route protection
        if (!_canAccessRoute(location, currentUser!.role)) {
          return _getRoleBasedHomePath(currentUser.role);
        }
        
        // Root path redirect to role-based home
        if (location == '/') {
          return _getRoleBasedHomePath(currentUser.role);
        }
        
        return null; // Allow access
      },
      routes: [
        // Public routes
        GoRoute(
          path: '/login',
          builder: (context, state) => const LoginScreen(),
        ),
        GoRoute(
          path: '/register',
          builder: (context, state) => const RegisterScreen(),
        ),
        
        // Client routes
        GoRoute(
          path: '/client/prescriptions',
          builder: (context, state) => const PrescriptionListScreen(),
        ),
        GoRoute(
          path: '/client/prescriptions/upload',
          builder: (context, state) => const PrescriptionUploadScreen(),
        ),
        GoRoute(
          path: '/client/orders',
          builder: (context, state) => const OrderListScreen(),
        ),
        GoRoute(
          path: '/client/orders/:id',
          builder: (context, state) {
            final orderId = int.parse(state.pathParameters['id']!);
            return OrderDetailScreen(orderId: orderId);
          },
        ),
        
        // Pharmacist routes
        GoRoute(
          path: '/pharmacist/prescriptions',
          builder: (context, state) => const PharmacistPrescriptionScreen(),
        ),
        GoRoute(
          path: '/pharmacist/prescriptions/:id/process',
          builder: (context, state) {
            final prescriptionId = int.parse(state.pathParameters['id']!);
            return ProcessPrescriptionScreen(prescriptionId: prescriptionId);
          },
        ),
        
        // Additional role-based routes...
      ],
    );
  }
  
  /// Determines if a user can access a specific route based on their role.
  static bool _canAccessRoute(String path, UserRole role) {
    if (path.startsWith('/client/')) {
      return role == UserRole.client;
    }
    if (path.startsWith('/pharmacist/')) {
      return role == UserRole.pharmacist;
    }
    if (path.startsWith('/salesperson/')) {
      return role == UserRole.salesperson;
    }
    if (path.startsWith('/delivery/')) {
      return role == UserRole.delivery;
    }
    if (path.startsWith('/manager/')) {
      return role == UserRole.manager;
    }
    return false;
  }
  
  /// Gets the default home path for a user role.
  static String _getRoleBasedHomePath(UserRole role) {
    switch (role) {
      case UserRole.client:
        return '/client/prescriptions';
      case UserRole.pharmacist:
        return '/pharmacist/prescriptions';
      case UserRole.salesperson:
        return '/salesperson/medications';
      case UserRole.delivery:
        return '/delivery/orders';
      case UserRole.manager:
        return '/manager/dashboard';
    }
  }
}
```

## Frontend Services Layer

### API Client Setup

The centralized API client provides a consistent interface for all HTTP communication:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../constants/api_constants.dart';
import 'storage_service.dart';

/// Centralized HTTP client for all API communication.
/// 
/// This service handles authentication token injection, request/response
/// standardization, and error handling for all backend communication.
/// It must be used by all feature-specific API services.
class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal();

  final StorageService _storage = StorageService();
  
  /// Makes a GET request to the specified endpoint.
  /// 
  /// Automatically includes authentication headers if token is available.
  /// Throws [ApiException] for HTTP errors or network failures.
  Future<Map<String, dynamic>> get(String endpoint) async {
    final uri = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    try {
      final response = await http.get(uri, headers: headers);
      return _handleResponse(response);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }
  
  /// Makes a POST request to the specified endpoint.
  /// 
  /// [body] will be JSON encoded if it's a Map, otherwise sent as-is.
  /// Automatically includes authentication headers if token is available.
  Future<Map<String, dynamic>> post(String endpoint, dynamic body) async {
    final uri = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    try {
      final response = await http.post(
        uri,
        headers: headers,
        body: body is Map ? json.encode(body) : body,
      );
      return _handleResponse(response);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }
  
  /// Makes a PUT request to the specified endpoint.
  Future<Map<String, dynamic>> put(String endpoint, dynamic body) async {
    final uri = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    try {
      final response = await http.put(
        uri,
        headers: headers,
        body: body is Map ? json.encode(body) : body,
      );
      return _handleResponse(response);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }
  
  /// Makes a DELETE request to the specified endpoint.
  Future<Map<String, dynamic>> delete(String endpoint) async {
    final uri = Uri.parse('${ApiConstants.baseUrl}$endpoint');
    final headers = await _getHeaders();
    
    try {
      final response = await http.delete(uri, headers: headers);
      return _handleResponse(response);
    } catch (e) {
      throw ApiException('Network error: $e');
    }
  }
  
  /// Builds headers with authentication token if available.
  Future<Map<String, String>> _getHeaders() async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    final token = await _storage.getAuthToken();
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }
  
  /// Handles HTTP response and converts to standardized format.
  /// 
  /// Throws [ApiException] for non-2xx status codes.
  Map<String, dynamic> _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (response.body.isEmpty) {
        return {'success': true};
      }
      return json.decode(response.body);
    } else {
      final errorBody = response.body.isNotEmpty 
          ? json.decode(response.body) 
          : {'message': 'Unknown error'};
      
      throw ApiException(
        errorBody['message'] ?? 'HTTP ${response.statusCode}',
        statusCode: response.statusCode,
      );
    }
  }
}

/// Exception thrown by API operations.
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  
  const ApiException(this.message, {this.statusCode});
  
  @override
  String toString() => message;
}
```

### Service Example

Feature-specific API services use the centralized client:

```dart
import '../core/services/api_client.dart';
import '../shared/models/prescription.dart';

/// API service for prescription-related operations.
/// 
/// This service handles all prescription-related API calls using the
/// centralized [ApiClient]. It follows the mandatory pattern of
/// encapsulating API interactions within feature-specific services.
class PrescriptionsApiService {
  final ApiClient _apiClient = ApiClient();
  
  /// Fetches the list of prescriptions for the authenticated user.
  /// 
  /// Returns a list of [Prescription] objects.
  /// Throws [ApiException] if the request fails.
  Future<List<Prescription>> getPrescriptions() async {
    final response = await _apiClient.get('/prescriptions');
    final List<dynamic> data = response['data'];
    
    return data.map((json) => Prescription.fromJson(json)).toList();
  }
  
  /// Uploads a prescription image file.
  /// 
  /// [imagePath] should be the local file path of the selected image.
  /// Returns the created [Prescription] object.
  /// Throws [ApiException] if upload fails.
  Future<Prescription> uploadPrescription(String imagePath) async {
    // For multipart uploads, we need a different approach
    final request = http.MultipartRequest(
      'POST',
      Uri.parse('${ApiConstants.baseUrl}/prescriptions'),
    );
    
    // Add authentication header
    final token = await StorageService().getAuthToken();
    if (token != null) {
      request.headers['Authorization'] = 'Bearer $token';
    }
    
    // Add image file
    request.files.add(await http.MultipartFile.fromPath('image', imagePath));
    
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    
    if (response.statusCode >= 200 && response.statusCode < 300) {
      final responseData = json.decode(response.body);
      return Prescription.fromJson(responseData['data']);
    } else {
      final errorBody = json.decode(response.body);
      throw ApiException(errorBody['message'] ?? 'Upload failed');
    }
  }
  
  /// Fetches a specific prescription by ID.
  Future<Prescription> getPrescription(int id) async {
    final response = await _apiClient.get('/prescriptions/$id');
    return Prescription.fromJson(response['data']);
  }
}
```