# GomuAuth

GomuAuth package for Laravel authentication with support for internal and external users.

## Installation

```bash
composer require gomu/gomuauth
```

## Features

- Token-based authentication using Laravel Sanctum
- Support for internal (HRIS) and external users
- Role-based access control
- Employee management integration

## User Types

- **Internal Users**: For HRIS and internal applications (user_type = 'internal')
- **External Users**: For public APIs and external applications (user_type = 'external')

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

// TODO: Add more usage instructions