import 'package:flutter/material.dart';
import '../models/medication.dart';

/// A widget that displays a single medication item in a list.
class MedicationListItem extends StatelessWidget {
  /// The medication to display.
  final Medication medication;

  /// Creates a new [MedicationListItem] instance.
  const MedicationListItem({super.key, required this.medication});

  @override
  Widget build(BuildContext context) {
    return ListTile(
      title: Text(medication.name),
      subtitle: Text('\$${medication.price.toStringAsFixed(2)}'),
      trailing: Text(
        medication.isInStock ? 'In Stock' : 'Out of Stock',
        style: TextStyle(
          color: medication.isInStock ? Colors.green : Colors.red,
        ),
      ),
    );
  }
}
