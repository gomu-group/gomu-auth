# GomuAuth

GomuAuth package for Laravel authentication with support for internal and external users.

## Installation

```bash
composer require gomu/gomuauth
```

## Features

- Token-based authentication using Laravel Sanctum
- OAuth 2.0 integration with external Passport server
- Device-aware token creation with automatic naming
- Comprehensive token management (list, revoke)
- Support for internal (HRIS) and external users
- Role-based access control
- Employee management integration
- Stateful domains support for SPA authentication

## User Types

- **Internal Users**: For HRIS and internal applications (user_type = 'internal')
- **External Users**: For public APIs and external applications (user_type = 'external')

## Authentication Methods

### 1. Sanctum Token Authentication

#### Login
```json
POST /auth/token
{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "data": {
    "access_token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

#### Logout
```json
DELETE /auth/token
Authorization: Bearer {token}

Response:
{
  "message": "Token revoked successfully"
}
```

### 2. OAuth 2.0 Authentication

#### Initiate OAuth Flow
```
GET /auth/oauth/passport/redirect
```
Redirects to external OAuth server

#### OAuth Callback
```
GET /auth/oauth/passport/callback?code={auth_code}&state={state}
```
Handles OAuth callback and creates user session

### 3. Token Management

#### List User Tokens
```json
GET /auth/user-token
Authorization: Bearer {token}

Response:
{
  "data": {
    "tokens": [
      {
        "id": 1,
        "name": "Chrome - Windows (Chrome 120.0)",
        "abilities": ["*"],
        "created_at": "2025-01-01T00:00:00Z",
        "last_used_at": null,
        "expires_at": null
      }
    ]
  }
}
```

#### Revoke Specific Token
```json
DELETE /auth/user-token/{tokenId}
Authorization: Bearer {token}

Response:
{
  "message": "Token revoked successfully"
}
```

## Authentication Endpoints

### General (Any User Type)
- `POST /auth/token` - Login (any user type)
- `POST /auth/register` - Register (specify user_type)
- `DELETE /auth/token` - Logout
- `GET /user-information` - Get user profile

### Internal Users
- `POST /auth/internal/token` - Login (internal only)
- `POST /auth/internal/register` - Register internal user
- `GET /internal/user-information` - Get internal user profile

### External Users
- `POST /auth/external/token` - Login (external only)
- `POST /auth/external/register` - Register external user
- `GET /external/user-information` - Get external user profile

### Token Management
- `GET /auth/user-token` - List user tokens
- `DELETE /auth/user-token/{tokenId}` - Revoke specific token

### OAuth Integration
- `GET /auth/oauth/passport/redirect` - Initiate OAuth flow
- `GET /auth/oauth/passport/callback` - Handle OAuth callback

## Configuration

Add to your `.env` file:

```env
# Database
AUTH_DB_CONNECTION=pgsql
AUTH_DB_SCHEMA=account

# Password hashing (legacy support)
AUTH_HASH_PASSWORD=true

# OAuth Passport (optional)
AUTH_PASSPORT_ENABLED=false
AUTH_PASSPORT_BASE_URL=https://passport.example.com
AUTH_PASSPORT_CLIENT_ID=your-client-id
AUTH_PASSPORT_CLIENT_SECRET=your-client-secret
AUTH_PASSPORT_CALLBACK_URL=https://yourapp.com/auth/oauth/passport/callback
```

## Usage

### Register User
```json
POST /auth/register
{
  "username": "johndoe",
  "email": "john@example.com",
  "password": "password123",
  "user_type": "internal",
  "role_id": "uuid-of-role"
}
```

### Login User
```json
POST /auth/token
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Using Authentication in Requests
```bash
curl -X GET \
  https://yourapp.com/user-information \
  -H "Authorization: Bearer {access_token}" \
  -H "Accept: application/json"
```

### OAuth Flow
1. Redirect user to `/auth/oauth/passport/redirect`
2. User authenticates on external OAuth server
3. OAuth server redirects back to `/auth/oauth/passport/callback`
4. Package creates/updates user and returns access token

## Middleware

### Check User Type
```php
Route::middleware('gomu.internal')->get('/internal-only', function () {
    // Only internal users can access
});

Route::middleware('gomu.external')->get('/external-only', function () {
    // Only external users can access
});
```

## Security Features

- **Device Tracking**: Automatic token naming based on device/browser
- **Token Scoping**: Ability-based token permissions
- **User Type Isolation**: Separate endpoints for internal/external users
- **OAuth State Protection**: CSRF protection for OAuth flows
- **Password Hashing**: Configurable legacy MD5 support

## Testing

Run the test suite:

```bash
vendor/bin/phpunit
```

Test files include:
- `TokenAuthControllerTest.php` - Token authentication tests
- `UserTokenControllerTest.php` - Token management tests
- `PassportControllerTest.php` - OAuth integration tests