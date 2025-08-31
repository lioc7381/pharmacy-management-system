
import 'package:flutter/material.dart';
import 'package:pharmacy_management_system/core/navigation/app_router.dart';

void main() {
  // For now, we'll instantiate the fake auth provider here.
  // In the future, this will be a real provider from the `provider` package.
  final authProvider = FakeAuthProvider();
  final appRouter = AppRouter(authProvider);

  runApp(MyApp(appRouter: appRouter));
}

class MyApp extends StatelessWidget {
  final AppRouter appRouter;

  const MyApp({super.key, required this.appRouter});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      routerConfig: appRouter.router,
      title: 'Pharmacy Management System',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
    );
  }
}
