import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:pharmacy_management_system/features/health_check/screens/health_check_screen.dart';

// For now, we'll use a placeholder for the auth provider and screens
class FakeAuthProvider extends ChangeNotifier {
  bool isAuthenticated = false;
}

class LoginScreen extends StatelessWidget {
  const LoginScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text('Login Screen'),
            const SizedBox(height: 20),
            // Add a button to navigate to the health check screen
            ElevatedButton(
              onPressed: () {
                AppRouter.goToHealthCheck(context);
              },
              // Style it differently to indicate it's a debug tool
              style: ElevatedButton.styleFrom(backgroundColor: Colors.grey),
              child: const Text('Check API Health'),
            ),
          ],
        ),
      ),
    );
  }
}

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Dashboard')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text('Dashboard Screen'),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                AppRouter.goToHealthCheck(context);
              },
              child: const Text('Go to Health Check'),
            ),
          ],
        ),
      ),
    );
  }
}

class AppRouter {
  final FakeAuthProvider authProvider;

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
      GoRoute(
        name: 'dashboard',
        path: '/dashboard',
        builder: (context, state) => const DashboardScreen(),
      ),

      // Health Check Route
      GoRoute(
        name: 'health-check',
        path: '/health-check',
        builder: (context, state) => const HealthCheckScreen(),
      ),
    ],

    redirect: (BuildContext context, GoRouterState state) {
      final bool loggedIn = authProvider.isAuthenticated;

      // Define all public routes that don't require a user to be logged in
      final publicRoutes = ['/login', '/health-check'];

      // Check if the user is trying to access a public route
      final bool isOnPublicRoute = publicRoutes.contains(state.matchedLocation);

      // If the user is NOT logged in and NOT on a public route, redirect to login
      if (!loggedIn && !isOnPublicRoute) {
        return '/login';
      }

      // If the user IS logged in and trying to go to the login page, redirect to the dashboard
      if (loggedIn && state.matchedLocation == '/login') {
        return '/dashboard';
      }

      // In all other cases, no redirect is necessary
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

  static void goToHealthCheck(BuildContext context) {
    context.goNamed('health-check');
  }
}
