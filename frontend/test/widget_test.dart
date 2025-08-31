// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.


import 'package:flutter_test/flutter_test.dart';

import 'package:pharmacy_management_system/main.dart';

import 'package:pharmacy_management_system/core/navigation/app_router.dart';

void main() {
  testWidgets('App starts smoke test', (WidgetTester tester) async {
    // Build our app and trigger a frame.
    final authProvider = FakeAuthProvider();
    final appRouter = AppRouter(authProvider);
    await tester.pumpWidget(MyApp(appRouter: appRouter));

    // Verify that the login screen is shown.
    expect(find.text('Login Screen'), findsOneWidget);
  });
}
