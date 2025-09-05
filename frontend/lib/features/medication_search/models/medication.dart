/// Represents a medication item.
class Medication {
  /// The name of the medication.
  final String name;

  /// The price of the medication.
  final double price;

  /// The stock status of the medication.
  final bool isInStock;

  /// Creates a new [Medication] instance.
  Medication({
    required this.name,
    required this.price,
    required this.isInStock,
  });
}
