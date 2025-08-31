
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

// For now, we'll use a placeholder for the auth provider and screens
class FakeAuthProvider extends ChangeNotifier {
  bool isAuthenticated = false;
}

class LoginScreen extends StatelessWidget {
  const LoginScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(body: Center(child: Text('Login Screen')));
  }
}

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(body: Center(child: Text('Dashboard Screen')));
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
}
