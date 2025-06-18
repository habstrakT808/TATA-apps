# Mobile App Authentication with Laravel Sanctum

This guide explains how to implement authentication for your mobile application using Laravel Sanctum's token-based authentication.

## Overview

For the mobile app, we use Laravel Sanctum to implement token-based (stateless) authentication. Unlike the web admin panel which uses stateful authentication with cookies, mobile apps will use API tokens.

## Authentication Flow

1. User logs in with email and password
2. Server validates credentials and generates a Sanctum token
3. Token is returned to the mobile app and should be stored securely
4. The token must be included in all subsequent API requests in the Authorization header
5. When the user logs out, the token is invalidated

## API Endpoints

### Authentication

- **Login**: `POST /api/users/login`
  - Request: `{ "email": "user@example.com", "password": "yourpassword" }`
  - Response: 
    ```json
    {
      "status": "success",
      "message": "Login berhasil",
      "data": {
        "access_token": "YOUR_TOKEN_HERE",
        "token_type": "Bearer",
        "user": {
          "id": "user-uuid",
          "name": "User Name",
          "email": "user@example.com",
          "role": "user"
        }
      }
    }
    ```

- **Register**: `POST /api/users/register`
  - Request: `{ "name": "New User", "email": "newuser@example.com", "password": "password", "password_confirmation": "password" }`
  - Response: `{ "status": "success", "message": "User registered successfully. Please login to continue." }`

- **Logout (Current Device)**: `POST /api/users/logout`
  - Headers: `Authorization: Bearer YOUR_TOKEN_HERE`
  - Response: `{ "status": "success", "message": "Logged out from current device" }`

- **Logout (All Devices)**: `POST /api/users/logout-all`
  - Headers: `Authorization: Bearer YOUR_TOKEN_HERE`
  - Response: `{ "status": "success", "message": "Logged out from all devices" }`

## Making Authenticated Requests

For all authenticated API requests, include the token in the Authorization header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Token Expiration

Tokens expire after 7 days by default. This can be configured in `config/sanctum.php`.

## Security Recommendations for Mobile Apps

1. **Store Tokens Securely**: Use secure storage options on the device (Keychain for iOS, EncryptedSharedPreferences for Android).

2. **HTTPS Only**: Ensure all API communications use HTTPS.

3. **Token Refresh**: Implement a token refresh mechanism if needed for longer sessions.

4. **Validate Responses**: Always validate responses and handle authentication errors (401 responses) by redirecting to the login screen.

5. **Logout on App Uninstall**: Clear tokens when the app is uninstalled.

## Example Mobile App Implementation

### React Native Example
```javascript
// Login function
const login = async (email, password) => {
  try {
    const response = await fetch('https://your-api.com/api/users/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email, password }),
    });
    
    const result = await response.json();
    
    if (result.status === 'success') {
      // Store token securely
      await SecureStore.setItemAsync('userToken', result.data.access_token);
      return { success: true, user: result.data.user };
    } else {
      return { success: false, message: result.message };
    }
  } catch (error) {
    return { success: false, message: 'Network error' };
  }
};

// Authenticated request example
const fetchUserData = async () => {
  try {
    const token = await SecureStore.getItemAsync('userToken');
    
    if (!token) {
      // Redirect to login
      return;
    }
    
    const response = await fetch('https://your-api.com/api/mobile/user/profile', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    
    if (response.status === 401) {
      // Token expired, redirect to login
      await SecureStore.deleteItemAsync('userToken');
      return;
    }
    
    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};
```

### Android (Kotlin) Example
```kotlin
// Login function
suspend fun login(email: String, password: String): LoginResult {
    return try {
        val response = apiService.login(LoginRequest(email, password))
        
        if (response.status == "success") {
            // Store token securely
            tokenManager.saveToken(response.data.accessToken)
            LoginResult.Success(response.data.user)
        } else {
            LoginResult.Error(response.message)
        }
    } catch (e: Exception) {
        LoginResult.Error("Network error")
    }
}

// Authenticated request with interceptor
class AuthInterceptor(private val tokenManager: TokenManager) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val request = chain.request()
        val token = tokenManager.getToken()
        
        val authenticatedRequest = if (token != null) {
            request.newBuilder()
                .header("Authorization", "Bearer $token")
                .build()
        } else {
            request
        }
        
        return chain.proceed(authenticatedRequest)
    }
}
```

### iOS (Swift) Example
```swift
// Login function
func login(email: String, password: String, completion: @escaping (Result<User, Error>) -> Void) {
    let url = URL(string: "https://your-api.com/api/users/login")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.addValue("application/json", forHTTPHeaderField: "Content-Type")
    
    let body: [String: Any] = ["email": email, "password": password]
    request.httpBody = try? JSONSerialization.data(withJSONObject: body)
    
    URLSession.shared.dataTask(with: request) { data, response, error in
        if let error = error {
            completion(.failure(error))
            return
        }
        
        guard let data = data else {
            completion(.failure(NSError(domain: "", code: -1, userInfo: [NSLocalizedDescriptionKey: "No data received"])))
            return
        }
        
        do {
            let loginResponse = try JSONDecoder().decode(LoginResponse.self, from: data)
            
            if loginResponse.status == "success" {
                // Store token securely in Keychain
                KeychainService.save(key: "userToken", data: loginResponse.data.accessToken)
                completion(.success(loginResponse.data.user))
            } else {
                completion(.failure(NSError(domain: "", code: -1, userInfo: [NSLocalizedDescriptionKey: loginResponse.message])))
            }
        } catch {
            completion(.failure(error))
        }
    }.resume()
}

// Add token to requests
func getAuthenticatedRequest(url: URL) -> URLRequest {
    var request = URLRequest(url: url)
    
    if let token = KeychainService.load(key: "userToken") {
        request.addValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
    }
    
    return request
}
``` 