import 'package:flutter/material.dart';
import '../models/medication.dart';
import '../widgets/medication_list_item.dart';

/// A screen that allows users to search for medications.
class MedicationSearchScreen extends StatelessWidget {
  /// Creates a new [MedicationSearchScreen] instance.
  const MedicationSearchScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Dummy data for the list view
    final List<Medication> dummyMedications = [
      Medication(name: 'Aspirin', price: 5.99, isInStock: true),
      Medication(name: 'Ibuprofen', price: 8.49, isInStock: false),
      Medication(name: 'Paracetamol', price: 4.99, isInStock: true),
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Medication Search'),
      ),
      body: Column(
        children: [
          const Padding(
            padding: EdgeInsets.all(8.0),
            child: TextField(
              decoration: InputDecoration(
                hintText: 'Search for medications...',
                border: OutlineInputBorder(),
              ),
            ),
          ),
          Expanded(
            child: ListView.builder(
              itemCount: dummyMedications.length,
              itemBuilder: (context, index) {
                return MedicationListItem(medication: dummyMedications[index]);
              },
            ),
          ),
        ],
      ),
    );
  }
}
