import 'package:flutter/material.dart';
import 'package:pharmacy_management_system/core/api/api_client_service.dart';

class HealthCheckScreen extends StatefulWidget {
  const HealthCheckScreen({super.key});

  @override
  State<HealthCheckScreen> createState() => _HealthCheckScreenState();
}

class _HealthCheckScreenState extends State<HealthCheckScreen> {
  final ApiClientService _apiClient = ApiClientService();
  String _response = '';
  bool _isLoading = false;

  Future<void> _checkHealth() async {
    setState(() {
      _isLoading = true;
      _response = '';
    });

    try {
      final response = await _apiClient.get('health');
      setState(() {
        _response = 'Status: ${response.data['status']}';
      });
    } catch (e) {
      setState(() {
        _response = 'Error: ${e.toString()}';
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Health Check')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              ElevatedButton(
                onPressed: _isLoading ? null : _checkHealth,
                child: _isLoading
                    ? const CircularProgressIndicator()
                    : const Text('Check Health'),
              ),
              const SizedBox(height: 16),
              Text(
                _response,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 16),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
