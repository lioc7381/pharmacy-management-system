import 'package:dio/dio.dart';

class ApiClientService {
  late Dio _dio;

  ApiClientService() {
    _dio = Dio();
    // Set default base URL - this should be configurable in a real app
    _dio.options.baseUrl = 'http://localhost:8000/api/';
  }

  Future<Response> get(String path) async {
    try {
      final response = await _dio.get(path);
      return response;
    } on DioException {
      // Re-throw the exception so it can be handled by the caller
      rethrow;
    }
  }

  // We can add other HTTP methods as needed (post, put, delete, etc.)
}