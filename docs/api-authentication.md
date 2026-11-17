# Authentication & Registration API (Laravel Sanctum)

This API powers user registration for **Clients** and **Providers**, authentication, and profile management using **Laravel Sanctum**.

---

## Base URL
```
http://localhost:8000/api/v1
```

---

## 1. Register as a Client

Creates a client account and returns an API Bearer token with `['role:client']` abilities.

* **Endpoint**: `POST /auth/register/client` (or `/api/register/client`)
* **Headers**: `Content-Type: application/json`, `Accept: application/json`

### Request Body
```json
{
  "name": "Sara Client",
  "email": "sara@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "phone": "+21620000001",
  "city": "Tunis",
  "address": "15 Avenue Habib Bourguiba"
}
```

### Response (201 Created)
```json
{
  "message": "Client account created successfully.",
  "access_token": "1|qXy...sanctum_token",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Sara Client",
    "email": "sara@example.com",
    "role": "client",
    "phone": "+21620000001",
    "status": "active",
    "email_verified": false,
    "client_profile": {
      "id": 1,
      "user_id": 1,
      "city": "Tunis",
      "address": "15 Avenue Habib Bourguiba"
    },
    "created_at": "2026-11-17T19:52:16.000000Z",
    "updated_at": "2026-11-17T19:52:16.000000Z"
  }
}
```

---

## 2. Register as a Service Provider

Creates a provider account with professional details and returns a Bearer token with `['role:provider']` abilities.

* **Endpoint**: `POST /auth/register/provider` (or `/api/register/provider`)
* **Headers**: `Content-Type: application/json`, `Accept: application/json`

### Request Body
```json
{
  "name": "Ahmed Electrician",
  "email": "ahmed@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "phone": "+21620000002",
  "category": "Electricity",
  "business_name": "Ahmed Elec Services",
  "bio": "Certified electrician with 7 years of experience in Grand Tunis.",
  "skills": ["wiring", "circuit breakers", "smart lighting"],
  "hourly_rate": 45.00,
  "city": "Tunis"
}
```

### Response (201 Created)
```json
{
  "message": "Provider account created successfully.",
  "access_token": "2|rYz...sanctum_token",
  "token_type": "Bearer",
  "user": {
    "id": 2,
    "name": "Ahmed Electrician",
    "email": "ahmed@example.com",
    "role": "provider",
    "phone": "+21620000002",
    "status": "active",
    "email_verified": false,
    "provider_profile": {
      "id": 1,
      "user_id": 2,
      "business_name": "Ahmed Elec Services",
      "category": "Electricity",
      "bio": "Certified electrician with 7 years of experience in Grand Tunis.",
      "skills": ["wiring", "circuit breakers", "smart lighting"],
      "hourly_rate": "45.00",
      "city": "Tunis",
      "is_available": true
    },
    "created_at": "2026-11-17T19:52:16.000000Z",
    "updated_at": "2026-11-17T19:52:16.000000Z"
  }
}
```

---

## 3. Login

* **Endpoint**: `POST /auth/login` (or `/api/login`)
* **Request Body**:
```json
{
  "email": "client@khedma.ma",
  "password": "Password123!"
}
```
* **Response (200 OK)**:
```json
{
  "message": "Login successful.",
  "access_token": "3|aBc...sanctum_token",
  "token_type": "Bearer",
  "user": { ... }
}
```

---

## 4. Get Current User Profile (Protected)

* **Endpoint**: `GET /auth/user` (or `/api/user`)
* **Headers**: `Authorization: Bearer <access_token>`, `Accept: application/json`
* **Response (200 OK)**: Returns the user object with their corresponding role profile (`client_profile` or `provider_profile`).

---

## 5. Logout (Protected)

* **Endpoint**: `POST /auth/logout` (or `/api/logout`)
* **Headers**: `Authorization: Bearer <access_token>`, `Accept: application/json`
* **Response (200 OK)**:
```json
{
  "message": "Logged out successfully."
}
```

---

## Seeded Test Accounts

| Role | Email | Password |
| :--- | :--- | :--- |
| **Client** | `client@khedma.ma` | `Password123!` |
| **Provider** | `provider@khedma.ma` | `Password123!` |
| **Admin** | `admin@khedma.ma` | `AdminPass123!` |
