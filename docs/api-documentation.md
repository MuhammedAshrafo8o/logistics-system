# API Documentation

> This file must be updated whenever a new API endpoint is added or changed.

## Documentation Maintenance Rules

- Any new endpoint must be added here immediately.
- Any request validation change must be reflected here.
- Any response structure change must be reflected here.
- Keep examples simple and realistic.
- Group endpoints by module.

## Authentication Notes

- Protected endpoints use Sanctum bearer token authentication.
- Send header: `Authorization: Bearer YOUR_TOKEN`

## Auth APIs

### POST `/api/auth/login`

- Method: `POST`
- Authentication: `No`
- Purpose: Authenticates an internal user and returns a Sanctum token.

Request body example:

```json
{
  "email": "admin@example.com",
  "password": "secret123"
}
```

Success response example:

```json
{
  "message": "Login successful",
  "token": "1|example-sanctum-token",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "email_verified_at": null,
    "created_at": "2026-04-24T10:00:00.000000Z",
    "updated_at": "2026-04-24T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

Common error response example:

```json
{
  "message": "Invalid credentials"
}
```

### GET `/api/auth/me`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns the currently authenticated user.

Success response example:

```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "email_verified_at": null,
    "created_at": "2026-04-24T10:00:00.000000Z",
    "updated_at": "2026-04-24T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/auth/logout`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Deletes the current Sanctum access token and logs the user out.

Success response example:

```json
{
  "message": "Logged out successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### GET `/api/auth/ping`

- Method: `GET`
- Authentication: `No`
- Purpose: Simple health check for the Auth module.

Success response example:

```json
{
  "message": "Auth module is working"
}
```

Common error response example:

```json
{
  "message": "Server error"
}
```

## User Management APIs

### GET `/api/users`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all internal users ordered by newest first.

Success response example:

```json
{
  "data": [
    {
      "id": 2,
      "name": "Warehouse Manager",
      "email": "warehouse.manager@example.com",
      "role": "warehouse",
      "created_at": "2026-04-24T11:00:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/users`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a new internal company user.

Request body example:

```json
{
  "name": "Finance Officer",
  "email": "finance@example.com",
  "password": "secret123",
  "role": "finance"
}
```

Success response example:

```json
{
  "data": {
    "id": 3,
    "name": "Finance Officer",
    "email": "finance@example.com",
    "role": "finance",
    "created_at": "2026-04-24T11:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The selected role is invalid.",
  "errors": {
    "role": [
      "The selected role is invalid."
    ]
  }
}
```

### GET `/api/users/{user}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one internal user by ID.

Success response example:

```json
{
  "data": {
    "id": 3,
    "name": "Finance Officer",
    "email": "finance@example.com",
    "role": "finance",
    "created_at": "2026-04-24T11:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Models\\User] 999"
}
```

### PUT `/api/users/{user}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an existing internal user. Password is re-hashed if provided.

Request body example:

```json
{
  "name": "Updated Finance Officer",
  "email": "finance.updated@example.com",
  "password": "newsecret123",
  "role": "admin"
}
```

Success response example:

```json
{
  "data": {
    "id": 3,
    "name": "Updated Finance Officer",
    "email": "finance.updated@example.com",
    "role": "admin",
    "created_at": "2026-04-24T11:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

### DELETE `/api/users/{user}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes an internal user.

Success response example:

```json
{
  "message": "User deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

## Merchant APIs

### GET `/api/merchants`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all merchants ordered by newest first.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Ahmed Ali",
      "company_name": "Fast Cargo LLC",
      "phone": "+201001234567",
      "email": "merchant@example.com",
      "address": "Cairo, Nasr City",
      "status": "active",
      "created_at": "2026-04-24T12:00:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/merchants`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a new merchant record.

Request body example:

```json
{
  "name": "Ahmed Ali",
  "company_name": "Fast Cargo LLC",
  "phone": "+201001234567",
  "email": "merchant@example.com",
  "address": "Cairo, Nasr City",
  "status": "active"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "name": "Ahmed Ali",
    "company_name": "Fast Cargo LLC",
    "phone": "+201001234567",
    "email": "merchant@example.com",
    "address": "Cairo, Nasr City",
    "status": "active",
    "created_at": "2026-04-24T12:00:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The selected status is invalid.",
  "errors": {
    "status": [
      "The selected status is invalid."
    ]
  }
}
```

### GET `/api/merchants/{merchant}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one merchant by ID.

Success response example:

```json
{
  "data": {
    "id": 1,
    "name": "Ahmed Ali",
    "company_name": "Fast Cargo LLC",
    "phone": "+201001234567",
    "email": "merchant@example.com",
    "address": "Cairo, Nasr City",
    "status": "active",
    "created_at": "2026-04-24T12:00:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Models\\Merchant] 999"
}
```

### PUT `/api/merchants/{merchant}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an existing merchant record.

Request body example:

```json
{
  "company_name": "Fast Cargo Logistics",
  "status": "inactive"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "name": "Ahmed Ali",
    "company_name": "Fast Cargo Logistics",
    "phone": "+201001234567",
    "email": "merchant@example.com",
    "address": "Cairo, Nasr City",
    "status": "inactive",
    "created_at": "2026-04-24T12:00:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

### DELETE `/api/merchants/{merchant}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a merchant.

Success response example:

```json
{
  "message": "Merchant deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

## Location Pricing APIs

### GET `/api/governorates`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all governorates ordered by newest first.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Cairo",
      "is_active": true,
      "created_at": "2026-04-24T13:00:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/governorates`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a new governorate for shipping coverage and pricing setup.

Request body example:

```json
{
  "name": "Giza",
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 2,
    "name": "Giza",
    "is_active": true,
    "created_at": "2026-04-24T13:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The name has already been taken.",
  "errors": {
    "name": [
      "The name has already been taken."
    ]
  }
}
```

### GET `/api/governorates/{governorate}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one governorate by ID.

Success response example:

```json
{
  "data": {
    "id": 2,
    "name": "Giza",
    "is_active": true,
    "created_at": "2026-04-24T13:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Modules\\LocationPricing\\Models\\Governorate] 999"
}
```

### PUT `/api/governorates/{governorate}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a governorate record.

Request body example:

```json
{
  "name": "Greater Cairo",
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 2,
    "name": "Greater Cairo",
    "is_active": true,
    "created_at": "2026-04-24T13:05:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The name has already been taken.",
  "errors": {
    "name": [
      "The name has already been taken."
    ]
  }
}
```

### DELETE `/api/governorates/{governorate}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a governorate.

Success response example:

```json
{
  "message": "Governorate deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### GET `/api/areas`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all areas with their related governorate name.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "governorate_id": 2,
      "governorate_name": "Greater Cairo",
      "name": "Nasr City",
      "is_active": true,
      "created_at": "2026-04-24T13:10:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/areas`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates an area inside a governorate.

Request body example:

```json
{
  "governorate_id": 2,
  "name": "Nasr City",
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "name": "Nasr City",
    "is_active": true,
    "created_at": "2026-04-24T13:10:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The name has already been taken for this governorate.",
  "errors": {
    "name": [
      "The name has already been taken for this governorate."
    ]
  }
}
```

### GET `/api/areas/{area}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one area by ID.

Success response example:

```json
{
  "data": {
    "id": 1,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "name": "Nasr City",
    "is_active": true,
    "created_at": "2026-04-24T13:10:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Modules\\LocationPricing\\Models\\Area] 999"
}
```

### PUT `/api/areas/{area}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an area and keeps area names unique inside the same governorate.

Request body example:

```json
{
  "name": "New Nasr City",
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "name": "New Nasr City",
    "is_active": true,
    "created_at": "2026-04-24T13:10:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The selected governorate id is invalid.",
  "errors": {
    "governorate_id": [
      "The selected governorate id is invalid."
    ]
  }
}
```

### DELETE `/api/areas/{area}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes an area.

Success response example:

```json
{
  "message": "Area deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### GET `/api/shipping-rates`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all shipping rates with governorate and area names when available.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "governorate_id": 2,
      "governorate_name": "Greater Cairo",
      "area_id": null,
      "area_name": null,
      "shipping_fee": "60.00",
      "is_active": true,
      "created_at": "2026-04-24T13:20:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/shipping-rates`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a shipping rate for a full governorate or a specific area.

Request body example:

```json
{
  "governorate_id": 2,
  "area_id": 1,
  "shipping_fee": 80,
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 2,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "area_id": 1,
    "area_name": "New Nasr City",
    "shipping_fee": "80.00",
    "is_active": true,
    "created_at": "2026-04-24T13:25:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "The selected area does not belong to the selected governorate.",
  "errors": {
    "area_id": [
      "The selected area does not belong to the selected governorate."
    ]
  }
}
```

### GET `/api/shipping-rates/{shippingRate}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one shipping rate by ID.

Success response example:

```json
{
  "data": {
    "id": 2,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "area_id": 1,
    "area_name": "New Nasr City",
    "shipping_fee": "80.00",
    "is_active": true,
    "created_at": "2026-04-24T13:25:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Modules\\LocationPricing\\Models\\ShippingRate] 999"
}
```

### PUT `/api/shipping-rates/{shippingRate}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an existing shipping rate.

Request body example:

```json
{
  "shipping_fee": 90,
  "is_active": true
}
```

Success response example:

```json
{
  "data": {
    "id": 2,
    "governorate_id": 2,
    "governorate_name": "Greater Cairo",
    "area_id": 1,
    "area_name": "New Nasr City",
    "shipping_fee": "90.00",
    "is_active": true,
    "created_at": "2026-04-24T13:25:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "A shipping rate already exists for this location.",
  "errors": {
    "area_id": [
      "A shipping rate already exists for this location."
    ]
  }
}
```

### DELETE `/api/shipping-rates/{shippingRate}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a shipping rate.

Success response example:

```json
{
  "message": "Shipping rate deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

### POST `/api/shipping-rates/calculate`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Calculates the shipping fee for a location. It first checks for an active area-specific rate, then falls back to the active governorate-level rate where `area_id` is `null`.

Request body example:

```json
{
  "governorate_id": 2,
  "area_id": 1
}
```

Success response example:

```json
{
  "data": {
    "governorate_id": 2,
    "area_id": 1,
    "shipping_fee": "80.00",
    "source": "area"
  }
}
```

Common error response example:

```json
{
  "message": "No active shipping rate found for this location."
}
```

## Other Existing API Route

### GET `/api/user`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns the authenticated user from the default route defined in `routes/api.php`.

Success response example:

```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@example.com",
  "role": "admin",
  "email_verified_at": null,
  "created_at": "2026-04-24T10:00:00.000000Z",
  "updated_at": "2026-04-24T10:00:00.000000Z",
  "deleted_at": null
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```
