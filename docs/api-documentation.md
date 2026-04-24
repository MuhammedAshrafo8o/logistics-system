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

## Order APIs

### Order Rules

- `delivery_governorate_id` is required for every order.
- `delivery_area_id` is optional.
- `payment_type` supports `cod` and `prepaid`.
- `cod` means the customer pays on delivery.
- `prepaid` means the customer already paid online.
- If `shipping_fee` is not sent on create, the system tries area-level rate first, then governorate-level rate where `area_id` is `null`.
- If no shipping rate is found, the order is created with `shipping_fee = "0.00"`, `requires_review = true`, `review_reason = "Shipping rate not found for delivery location"`, and `status = "pending_review"`.
- Order numbers are generated automatically in this format: `ORD-YYYYMMDD-000001`.

### GET `/api/orders`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all orders ordered by newest first with merchant, delivery location, and items eager loaded.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "merchant_id": 1,
      "merchant_name": "Fast Trade",
      "order_number": "ORD-20260424-000001",
      "customer_name": "Ahmed Adel",
      "customer_phone": "01000000000",
      "customer_phone_alt": null,
      "delivery_governorate_id": 2,
      "delivery_governorate_name": "Cairo",
      "delivery_area_id": 5,
      "delivery_area_name": "Nasr City",
      "delivery_address": "Building 10, Street 12",
      "delivery_notes": "Call before delivery",
      "pickup_governorate_id": null,
      "pickup_area_id": null,
      "pickup_address": null,
      "pickup_notes": null,
      "cod_amount": "450.00",
      "shipping_fee": "80.00",
      "fulfillment_type": "pickup_from_merchant",
      "is_fragile": false,
      "allow_inspection": true,
      "requires_packaging": false,
      "package_notes": null,
      "source": "manual",
      "external_source": null,
      "external_order_id": null,
      "external_order_number": null,
      "requires_review": false,
      "review_reason": null,
      "status": "draft",
      "notes": "Priority customer",
      "items": [
        {
          "id": 1,
          "product_name": "Phone Case",
          "sku": "CASE-001",
          "quantity": 2,
          "unit_price": "150.00",
          "weight": "0.20",
          "notes": null,
          "created_at": "2026-04-24T14:00:00.000000Z"
        }
      ],
      "created_at": "2026-04-24T14:00:00.000000Z"
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

### POST `/api/orders`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a manual order with one or more items inside a database transaction.

Request body example:

```json
{
  "merchant_id": 1,
  "customer_name": "Ahmed Adel",
  "customer_phone": "01000000000",
  "customer_phone_alt": "01111111111",
  "delivery_governorate_id": 2,
  "delivery_area_id": 5,
  "delivery_address": "Building 10, Street 12, Cairo",
  "delivery_notes": "Call before delivery",
  "payment_type": "cod",
  "cod_amount": 450,
  "fulfillment_type": "pickup_from_merchant",
  "allow_inspection": true,
  "notes": "Priority customer",
  "items": [
    {
      "product_name": "Phone Case",
      "sku": "CASE-001",
      "quantity": 2,
      "unit_price": 150,
      "weight": 0.2
    },
    {
      "product_name": "Screen Protector",
      "quantity": 1,
      "unit_price": 150
    }
  ]
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "order_number": "ORD-20260424-000001",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "customer_phone_alt": "01111111111",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "delivery_notes": "Call before delivery",
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "payment_type": "cod",
    "fulfillment_type": "pickup_from_merchant",
    "is_fragile": false,
    "allow_inspection": true,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": false,
    "review_reason": null,
    "status": "draft",
    "notes": "Priority customer",
    "items": [
      {
        "id": 1,
        "product_name": "Phone Case",
        "sku": "CASE-001",
        "quantity": 2,
        "unit_price": "150.00",
        "weight": "0.20",
        "notes": null,
        "created_at": "2026-04-24T14:00:00.000000Z"
      },
      {
        "id": 2,
        "product_name": "Screen Protector",
        "sku": null,
        "quantity": 1,
        "unit_price": "150.00",
        "weight": null,
        "notes": null,
        "created_at": "2026-04-24T14:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T14:00:00.000000Z"
  }
}
```

Review-required response example when no shipping rate exists:

```json
{
  "data": {
    "id": 2,
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "order_number": "ORD-20260424-000002",
    "customer_name": "Mona Samir",
    "customer_phone": "01222222222",
    "customer_phone_alt": null,
    "delivery_governorate_id": 3,
    "delivery_governorate_name": "Giza",
    "delivery_area_id": null,
    "delivery_area_name": null,
    "delivery_address": "Haram Street",
    "delivery_notes": null,
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "200.00",
    "shipping_fee": "0.00",
    "payment_type": "cod",
    "fulfillment_type": "pickup_from_merchant",
    "is_fragile": false,
    "allow_inspection": false,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": true,
    "review_reason": "Shipping rate not found for delivery location",
    "status": "pending_review",
    "notes": null,
    "items": [
      {
        "id": 3,
        "product_name": "Small Box",
        "sku": null,
        "quantity": 1,
        "unit_price": "200.00",
        "weight": null,
        "notes": null,
        "created_at": "2026-04-24T14:10:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T14:10:00.000000Z"
  }
}
```

Common validation error response example:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "delivery_governorate_id": [
      "The delivery governorate id field is required."
    ]
  }
}
```

### GET `/api/orders/{order}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one order by ID with merchant, delivery location, and items eager loaded.

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "order_number": "ORD-20260424-000001",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "customer_phone_alt": "01111111111",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "delivery_notes": "Call before delivery",
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "fulfillment_type": "pickup_from_merchant",
    "is_fragile": false,
    "allow_inspection": true,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": false,
    "review_reason": null,
    "status": "draft",
    "notes": "Priority customer",
    "items": [
      {
        "id": 1,
        "product_name": "Phone Case",
        "sku": "CASE-001",
        "quantity": 2,
        "unit_price": "150.00",
        "weight": "0.20",
        "notes": null,
        "created_at": "2026-04-24T14:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T14:00:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Modules\\Order\\Models\\Order] 999"
}
```

### POST `/api/orders/{order}/confirm`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Confirms an order after admin/system review. This endpoint only changes the order status to `confirmed` when confirmation rules pass.

Confirmation rules:

- Order can be confirmed only when current status is `draft` or `pending_review`.
- Order cannot be confirmed when `requires_review = true`.
- Order cannot be confirmed when it has no items.
- Already confirmed orders return `422`.
- Cancelled orders return `422`.

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "order_number": "ORD-20260424-000001",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "customer_phone_alt": "01111111111",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "delivery_notes": "Call before delivery",
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "fulfillment_type": "pickup_from_merchant",
    "is_fragile": false,
    "allow_inspection": true,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": false,
    "review_reason": null,
    "status": "confirmed",
    "notes": "Priority customer",
    "items": [
      {
        "id": 1,
        "product_name": "Phone Case",
        "sku": "CASE-001",
        "quantity": 2,
        "unit_price": "150.00",
        "weight": "0.20",
        "notes": null,
        "created_at": "2026-04-24T14:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T14:00:00.000000Z"
  }
}
```

Already confirmed error response example:

```json
{
  "message": "Order is already confirmed."
}
```

Cancelled order error response example:

```json
{
  "message": "Cancelled order cannot be confirmed."
}
```

Review-required error response example:

```json
{
  "message": "Order requires review before confirmation."
}
```

No items error response example:

```json
{
  "message": "Order must have at least one item before confirmation."
}
```

### PUT `/api/orders/{order}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an order inside a database transaction. If `items` is sent, all existing order items are replaced with the new array.

Request body example:

```json
{
  "customer_name": "Ahmed Adel Updated",
  "delivery_governorate_id": 2,
  "delivery_area_id": 6,
  "delivery_address": "Updated address",
  "shipping_fee": 95,
  "status": "confirmed",
  "items": [
    {
      "product_name": "Phone Case Premium",
      "sku": "CASE-002",
      "quantity": 1,
      "unit_price": 300,
      "weight": 0.25
    }
  ]
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "order_number": "ORD-20260424-000001",
    "customer_name": "Ahmed Adel Updated",
    "customer_phone": "01000000000",
    "customer_phone_alt": "01111111111",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 6,
    "delivery_area_name": "Heliopolis",
    "delivery_address": "Updated address",
    "delivery_notes": "Call before delivery",
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "450.00",
    "shipping_fee": "95.00",
    "fulfillment_type": "pickup_from_merchant",
    "is_fragile": false,
    "allow_inspection": true,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": false,
    "review_reason": null,
    "status": "confirmed",
    "notes": "Priority customer",
    "items": [
      {
        "id": 4,
        "product_name": "Phone Case Premium",
        "sku": "CASE-002",
        "quantity": 1,
        "unit_price": "300.00",
        "weight": "0.25",
        "notes": null,
        "created_at": "2026-04-24T14:20:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T14:00:00.000000Z"
  }
}
```

Common validation error response example:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "delivery_area_id": [
      "The selected delivery area does not belong to the selected delivery governorate."
    ]
  }
}
```

### DELETE `/api/orders/{order}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes an order.

Success response example:

```json
{
  "message": "Order deleted successfully"
}
```

Common error response example:

```json
{
  "message": "Unauthenticated."
}
```

## Shipment APIs

### Shipment Rules

- Shipment is the operational logistics entity created from a confirmed order.
- One order can have only one shipment for now.
- Shipment creation copies key delivery and financial fields from the order.
- Every shipment starts with status `pending_pickup`.
- Every shipment status change creates a row in `shipment_status_histories`.

### GET `/api/track/{shipment_number}`

- Method: `GET`
- Authentication: `No`
- Purpose: Public customer tracking endpoint using shipment number. This endpoint does not require Sanctum authentication.
- Response is intentionally limited to customer-safe shipment tracking data.

Success response example:

```json
{
  "data": {
    "shipment_number": "SHP-20260424-000001",
    "status": "in_transit",
    "customer_name": "Ahmed Adel",
    "delivery_governorate_name": "Cairo",
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "histories": [
      {
        "status": "pending_pickup",
        "notes": "Shipment created from order.",
        "created_at": "2026-04-24T15:00:00.000000Z"
      },
      {
        "status": "in_transit",
        "notes": "Shipment left the sorting hub.",
        "created_at": "2026-04-24T15:30:00.000000Z"
      }
    ]
  }
}
```

Not found response example:

```json
{
  "message": "Shipment not found."
}
```

### GET `/api/shipments`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all shipments ordered by newest first with merchant, delivery location, and status histories.
- Optional filters:
- `status`
- `merchant_id`
- `assigned_driver_id`
- `delivery_governorate_id`
- `delivery_area_id`
- `date_from`
- `date_to`
- `search` on `shipment_number`, `customer_name`, or `customer_phone`

Request examples:

```text
GET /api/shipments?status=in_transit
GET /api/shipments?merchant_id=1&assigned_driver_id=1
GET /api/shipments?delivery_governorate_id=1&delivery_area_id=2
GET /api/shipments?date_from=2026-04-01&date_to=2026-04-24
GET /api/shipments?search=010
```

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 1,
      "shipment_number": "SHP-20260424-000001",
      "merchant_id": 1,
      "merchant_name": "Fast Trade",
      "customer_name": "Ahmed Adel",
      "customer_phone": "01000000000",
      "delivery_governorate_id": 2,
      "delivery_governorate_name": "Cairo",
      "delivery_area_id": 5,
      "delivery_area_name": "Nasr City",
      "delivery_address": "Building 10, Street 12, Cairo",
      "cod_amount": "450.00",
      "shipping_fee": "80.00",
      "status": "pending_pickup",
      "tracking_notes": null,
      "histories": [
        {
          "id": 1,
          "shipment_id": 1,
          "status": "pending_pickup",
          "notes": "Shipment created from order.",
          "changed_by": 1,
          "changed_by_name": "Admin User",
          "created_at": "2026-04-24T15:00:00.000000Z"
        }
      ],
      "created_at": "2026-04-24T15:00:00.000000Z"
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

### GET `/api/shipments/{shipment}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one shipment by ID with merchant, delivery location, and status history.

Success response example:

```json
{
  "data": {
    "id": 1,
    "order_id": 1,
    "shipment_number": "SHP-20260424-000001",
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "status": "pending_pickup",
    "tracking_notes": null,
    "histories": [
      {
        "id": 1,
        "shipment_id": 1,
        "status": "pending_pickup",
        "notes": "Shipment created from order.",
        "changed_by": 1,
        "changed_by_name": "Admin User",
        "created_at": "2026-04-24T15:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T15:00:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "No query results for model [App\\Modules\\Shipment\\Models\\Shipment] 999"
}
```

### POST `/api/orders/{order}/shipments`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Converts a confirmed order into a shipment and creates the first shipment status history row.

Creation rules:

- Order must be confirmed.
- Order must not require review.
- Order must have at least one item.
- Order must not already have a shipment.

Success response example:

```json
{
  "data": {
    "id": 1,
    "order_id": 1,
    "shipment_number": "SHP-20260424-000001",
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "status": "pending_pickup",
    "tracking_notes": null,
    "histories": [
      {
        "id": 1,
        "shipment_id": 1,
        "status": "pending_pickup",
        "notes": "Shipment created from order.",
        "changed_by": 1,
        "changed_by_name": "Admin User",
        "created_at": "2026-04-24T15:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T15:00:00.000000Z"
  }
}
```

Order not confirmed error response example:

```json
{
  "message": "Only confirmed orders can be converted to shipment."
}
```

Review-required error response example:

```json
{
  "message": "Order requires review before shipment creation."
}
```

Shipment already exists error response example:

```json
{
  "message": "Shipment already exists for this order."
}
```

No items error response example:

```json
{
  "message": "Order must have at least one item before shipment creation."
}
```

### PATCH `/api/shipments/{shipment}/status`

- Method: `PATCH`
- Authentication: `Yes`
- Purpose: Updates the current shipment status and adds a new shipment status history record.

Request body example:

```json
{
  "status": "in_transit",
  "notes": "Shipment left the sorting hub."
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "order_id": 1,
    "shipment_number": "SHP-20260424-000001",
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "status": "in_transit",
    "tracking_notes": null,
    "histories": [
      {
        "id": 1,
        "shipment_id": 1,
        "status": "pending_pickup",
        "notes": "Shipment created from order.",
        "changed_by": 1,
        "changed_by_name": "Admin User",
        "created_at": "2026-04-24T15:00:00.000000Z"
      },
      {
        "id": 2,
        "shipment_id": 1,
        "status": "in_transit",
        "notes": "Shipment left the sorting hub.",
        "changed_by": 2,
        "changed_by_name": "Operations User",
        "created_at": "2026-04-24T15:30:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T15:00:00.000000Z"
  }
}
```

Common validation error response example:

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

### POST `/api/shipments/{shipment}/assign-driver`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Assigns or reassigns an active driver to a shipment without changing shipment status.

Request body example:

```json
{
  "driver_id": 1
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "order_id": 1,
    "shipment_number": "SHP-20260424-000001",
    "merchant_id": 1,
    "merchant_name": "Fast Trade",
    "customer_name": "Ahmed Adel",
    "customer_phone": "01000000000",
    "delivery_governorate_id": 2,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 5,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 10, Street 12, Cairo",
    "cod_amount": "450.00",
    "shipping_fee": "80.00",
    "status": "pending_pickup",
    "tracking_notes": null,
    "assigned_driver_id": 1,
    "assigned_driver_name": "Mahmoud Hassan",
    "histories": [
      {
        "id": 1,
        "shipment_id": 1,
        "status": "pending_pickup",
        "notes": "Shipment created from order.",
        "changed_by": 1,
        "changed_by_name": "Admin User",
        "created_at": "2026-04-24T15:00:00.000000Z"
      }
    ],
    "created_at": "2026-04-24T15:00:00.000000Z"
  }
}
```

Inactive driver error response example:

```json
{
  "message": "Selected driver is inactive."
}
```

Delivered shipment error response example:

```json
{
  "message": "Delivered shipment cannot be reassigned."
}
```

Cancelled shipment error response example:

```json
{
  "message": "Cancelled shipment cannot be assigned."
}
```

## Driver APIs

### Driver Rules

- Driver is a separate operational entity and is not a system user by default.
- `user_id` is optional for future linking with an internal user account.
- Only active drivers can be assigned to shipments.

### GET `/api/drivers`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all drivers ordered by newest first.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "user_id": null,
      "name": "Mahmoud Hassan",
      "phone": "01012345678",
      "national_id": "29801011234567",
      "vehicle_type": "Motorcycle",
      "vehicle_plate": "ABC-1234",
      "status": "active",
      "notes": "Primary Cairo driver",
      "created_at": "2026-04-24T16:00:00.000000Z"
    }
  ]
}
```

### POST `/api/drivers`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a driver record.

Request body example:

```json
{
  "name": "Mahmoud Hassan",
  "phone": "01012345678",
  "national_id": "29801011234567",
  "vehicle_type": "Motorcycle",
  "vehicle_plate": "ABC-1234",
  "status": "active",
  "notes": "Primary Cairo driver"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "user_id": null,
    "name": "Mahmoud Hassan",
    "phone": "01012345678",
    "national_id": "29801011234567",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC-1234",
    "status": "active",
    "notes": "Primary Cairo driver",
    "created_at": "2026-04-24T16:00:00.000000Z"
  }
}
```

### GET `/api/drivers/{driver}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one driver by ID.

Success response example:

```json
{
  "data": {
    "id": 1,
    "user_id": null,
    "name": "Mahmoud Hassan",
    "phone": "01012345678",
    "national_id": "29801011234567",
    "vehicle_type": "Motorcycle",
    "vehicle_plate": "ABC-1234",
    "status": "active",
    "notes": "Primary Cairo driver",
    "created_at": "2026-04-24T16:00:00.000000Z"
  }
}
```

### PUT `/api/drivers/{driver}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a driver record.

Request body example:

```json
{
  "vehicle_type": "Van",
  "vehicle_plate": "XYZ-5678",
  "status": "inactive"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "user_id": null,
    "name": "Mahmoud Hassan",
    "phone": "01012345678",
    "national_id": "29801011234567",
    "vehicle_type": "Van",
    "vehicle_plate": "XYZ-5678",
    "status": "inactive",
    "notes": "Primary Cairo driver",
    "created_at": "2026-04-24T16:00:00.000000Z"
  }
}
```

### DELETE `/api/drivers/{driver}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a driver.

Success response example:

```json
{
  "message": "Driver deleted successfully"
}
```

### GET `/api/merchants/{merchant}/dashboard/summary`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns merchant-scoped dashboard summary cards using only this merchant's shipments.

Card definitions:

- `company_profit`: sum of `shipping_fee` for delivered shipments of this merchant.
- `cod_collected`: sum of `cod_amount` for delivered shipments of this merchant where `order.payment_type = cod`.
- `merchants_payables`: sum of `max(cod_amount - shipping_fee, 0)` for delivered shipments of this merchant.
- Shipment counts use only this merchant's shipments.

Optional query filters:

- `date_from`
- `date_to`
- `assigned_driver_id`

Request examples:

```text
GET /api/merchants/1/dashboard/summary
GET /api/merchants/1/dashboard/summary?date_from=2026-04-01&date_to=2026-04-24
GET /api/merchants/1/dashboard/summary?assigned_driver_id=1
```

Success response example:

```json
{
  "data": {
    "company_profit": "120.00",
    "merchants_payables": "940.00",
    "cod_collected": "1000.00",
    "shipments": {
      "total": 10,
      "pending_pickup": 2,
      "in_transit": 3,
      "delivered": 4,
      "assigned": 6,
      "unassigned": 4
    },
    "filters": {
      "date_from": null,
      "date_to": null,
      "merchant_id": 1,
      "assigned_driver_id": null
    }
  }
}
```

Common validation error response example:

```json
{
  "message": "The date to field must be a date after or equal to date from.",
  "errors": {
    "date_to": [
      "The date to field must be a date after or equal to date from."
    ]
  }
}
```

### GET `/api/drivers/{driver}/shipments`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all shipments assigned to the selected driver with merchant, delivery location, assigned driver, and histories loaded.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 1,
      "shipment_number": "SHP-20260424-000001",
      "merchant_id": 1,
      "merchant_name": "Fast Trade",
      "customer_name": "Ahmed Adel",
      "customer_phone": "01000000000",
      "delivery_governorate_id": 2,
      "delivery_governorate_name": "Cairo",
      "delivery_area_id": 5,
      "delivery_area_name": "Nasr City",
      "delivery_address": "Building 10, Street 12, Cairo",
      "cod_amount": "450.00",
      "shipping_fee": "80.00",
      "status": "pending_pickup",
      "tracking_notes": null,
      "assigned_driver_id": 1,
      "assigned_driver_name": "Mahmoud Hassan",
      "histories": [
        {
          "id": 1,
          "shipment_id": 1,
          "status": "pending_pickup",
          "notes": "Shipment created from order.",
          "changed_by": 1,
          "changed_by_name": "Admin User",
          "created_at": "2026-04-24T15:00:00.000000Z"
        }
      ],
      "created_at": "2026-04-24T15:00:00.000000Z"
    }
  ]
}
```

### GET `/api/drivers/{driver}/manifest`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns a printable JSON manifest for a driver with shipment list, totals, and merchant phone for operational support.
- Merchant phone is included so the driver can contact the merchant when the customer is not responding.
- `total_cod_shipments` counts assigned shipments where `shipment -> order -> payment_type = cod`.
- `total_prepaid_shipments` counts assigned shipments where `shipment -> order -> payment_type = prepaid`.
- Optional query params:
- `status`
- `date` in `YYYY-MM-DD`

Request examples:

```text
GET /api/drivers/1/manifest
GET /api/drivers/1/manifest?status=pending_pickup
GET /api/drivers/1/manifest?date=2026-04-24
```

Success response example:

```json
{
  "data": {
    "driver": {
      "id": 1,
      "name": "Mahmoud Hassan",
      "phone": "01012345678",
      "vehicle_type": "Motorcycle",
      "vehicle_plate": "ABC-1234"
    },
    "generated_at": "2026-04-24T17:00:00.000000Z",
    "summary": {
      "total_shipments": 2,
      "total_cod_shipments": 1,
      "total_prepaid_shipments": 1,
      "total_cod_amount": "650.00",
      "total_shipping_fee": "140.00"
    },
    "shipments": [
      {
        "shipment_id": 1,
        "shipment_number": "SHP-20260424-000001",
        "status": "pending_pickup",
        "customer_name": "Ahmed Adel",
        "customer_phone": "01000000000",
        "delivery_governorate_name": "Cairo",
        "delivery_area_name": "Nasr City",
        "delivery_address": "Building 10, Street 12, Cairo",
        "cod_amount": "450.00",
        "shipping_fee": "80.00",
        "merchant_name": "Fast Trade",
        "merchant_phone": "0223456789",
        "notes": null
      },
      {
        "shipment_id": 2,
        "shipment_number": "SHP-20260424-000002",
        "status": "pending_pickup",
        "customer_name": "Mona Samir",
        "customer_phone": "01222222222",
        "delivery_governorate_name": "Giza",
        "delivery_area_name": null,
        "delivery_address": "Haram Street",
        "cod_amount": "200.00",
        "shipping_fee": "60.00",
        "merchant_name": "Fast Trade",
        "merchant_phone": "0223456789",
        "notes": "Leave at reception"
      }
    ]
  }
}
```

Common validation error response example:

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

## Other Existing API Route

## Dashboard APIs

### GET `/api/dashboard/summary`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns admin dashboard summary cards based on existing shipments data.

Card definitions:

- `company_profit`: sum of `shipping_fee` for delivered shipments.
- `merchants_payables`: sum of `max(cod_amount - shipping_fee, 0)` for delivered shipments.
- `cod_collected`: sum of `cod_amount` for delivered shipments.
- `shipments.total`: count of all shipments.
- `shipments.pending_pickup`: count of shipments with status `pending_pickup`.
- `shipments.in_transit`: count of shipments with status `pending_pickup`, `picked_up`, `in_transit`, or `out_for_delivery`.
- `shipments.delivered`: count of shipments with status `delivered`.
- `shipments.assigned`: count of shipments where `assigned_driver_id` is not `null`.
- `shipments.unassigned`: count of shipments where `assigned_driver_id` is `null`.

Optional query filters:

- `date_from`
- `date_to`
- `merchant_id`
- `assigned_driver_id`

Request examples:

```text
GET /api/dashboard/summary
GET /api/dashboard/summary?date_from=2026-04-01&date_to=2026-04-24
GET /api/dashboard/summary?merchant_id=1
GET /api/dashboard/summary?assigned_driver_id=1
```

Success response example:

```json
{
  "data": {
    "company_profit": "120.00",
    "merchants_payables": "940.00",
    "cod_collected": "1000.00",
    "shipments": {
      "total": 10,
      "pending_pickup": 2,
      "in_transit": 3,
      "delivered": 4,
      "assigned": 6,
      "unassigned": 4
    },
    "filters": {
      "date_from": null,
      "date_to": null,
      "merchant_id": null,
      "assigned_driver_id": null
    }
  }
}
```

Common validation error response example:

```json
{
  "message": "The date to field must be a date after or equal to date from.",
  "errors": {
    "date_to": [
      "The date to field must be a date after or equal to date from."
    ]
  }
}
```

## Finance APIs

### Finance Rules

- Finance Phase 1 uses existing delivered shipments and merchant payouts only.
- Prepaid delivered shipments contribute to company shipping profit, but not to merchant payable or COD collected.
- Cancelled payouts do not count in `paid_out`.

### GET `/api/merchant-payouts`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns merchant payouts with optional filters.
- Optional filters:
- `merchant_id`
- `status`
- `payment_method`
- `date_from`
- `date_to`

Request examples:

```text
GET /api/merchant-payouts
GET /api/merchant-payouts?merchant_id=1
GET /api/merchant-payouts?status=completed
GET /api/merchant-payouts?payment_method=bank_transfer
GET /api/merchant-payouts?date_from=2026-04-01&date_to=2026-04-24
```

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "amount": "2000.00",
      "status": "completed",
      "payment_method": "bank_transfer",
      "reference_number": "TRX-1001",
      "notes": "First payout batch",
      "created_by": 1,
      "created_by_name": "Admin User",
      "paid_at": "2026-04-24T18:00:00.000000Z",
      "created_at": "2026-04-24T17:30:00.000000Z"
    }
  ]
}
```

### POST `/api/merchant-payouts`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a merchant payout record.

Request body example:

```json
{
  "merchant_id": 1,
  "amount": 2000,
  "status": "completed",
  "payment_method": "bank_transfer",
  "reference_number": "TRX-1001",
  "notes": "First payout batch"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "amount": "2000.00",
    "status": "completed",
    "payment_method": "bank_transfer",
    "reference_number": "TRX-1001",
    "notes": "First payout batch",
    "created_by": 1,
    "created_by_name": "Admin User",
    "paid_at": "2026-04-24T18:00:00.000000Z",
    "created_at": "2026-04-24T17:30:00.000000Z"
  }
}
```

### GET `/api/merchant-payouts/{merchantPayout}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one merchant payout by ID.

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "amount": "2000.00",
    "status": "completed",
    "payment_method": "bank_transfer",
    "reference_number": "TRX-1001",
    "notes": "First payout batch",
    "created_by": 1,
    "created_by_name": "Admin User",
    "paid_at": "2026-04-24T18:00:00.000000Z",
    "created_at": "2026-04-24T17:30:00.000000Z"
  }
}
```

### PUT `/api/merchant-payouts/{merchantPayout}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a merchant payout. If status becomes `completed` and `paid_at` is not sent, `paid_at` is set automatically.

Request body example:

```json
{
  "status": "completed",
  "payment_method": "cash",
  "notes": "Paid in office"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "amount": "2000.00",
    "status": "completed",
    "payment_method": "cash",
    "reference_number": "TRX-1001",
    "notes": "Paid in office",
    "created_by": 1,
    "created_by_name": "Admin User",
    "paid_at": "2026-04-24T18:15:00.000000Z",
    "created_at": "2026-04-24T17:30:00.000000Z"
  }
}
```

### DELETE `/api/merchant-payouts/{merchantPayout}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a merchant payout.

Success response example:

```json
{
  "message": "Merchant payout deleted successfully"
}
```

### GET `/api/merchants/{merchant}/finance/summary`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns merchant financial summary based on delivered shipments and merchant payouts.

Calculation formulas:

- `cod_collected`: sum of `cod_amount` for delivered shipments where `order.payment_type = cod`.
- `shipping_fees`: sum of `shipping_fee` for delivered shipments.
- `warehouse_charges`: sum of non-cancelled `warehouse_charges.amount`.
- `merchant_payable`: sum of `max(cod_amount - shipping_fee, 0)` for delivered shipments.
- `paid_out`: sum of completed merchant payouts.
- `pending_payouts`: sum of pending merchant payouts.
- `remaining_balance`: `merchant_payable - paid_out - pending_payouts`.
- `company_profit_from_shipping`: same as `shipping_fees`.
- `warehouse_charges` is displayed separately and does not reduce merchant payout balance yet.

Optional query filters:

- `date_from`
- `date_to`

Request examples:

```text
GET /api/merchants/1/finance/summary
GET /api/merchants/1/finance/summary?date_from=2026-04-01&date_to=2026-04-24
```

Success response example:

```json
{
  "data": {
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "cod_collected": "5000.00",
    "shipping_fees": "600.00",
    "warehouse_charges": "250.00",
    "merchant_payable": "4400.00",
    "paid_out": "2000.00",
    "pending_payouts": "500.00",
    "remaining_balance": "1900.00",
    "company_profit_from_shipping": "600.00",
    "shipments": {
      "delivered_count": 10,
      "cod_delivered_count": 6,
      "prepaid_delivered_count": 4
    },
    "filters": {
      "date_from": null,
      "date_to": null
    }
  }
}
```

Common validation error response example:

```json
{
  "message": "The date to field must be a date after or equal to date from.",
  "errors": {
    "date_to": [
      "The date to field must be a date after or equal to date from."
    ]
  }
}
```

### GET `/api/driver-cash-closures`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns driver cash closures with optional filters.
- Optional filters:
- `driver_id`
- `status`
- `date_from`
- `date_to`

### POST `/api/driver-cash-closures`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a driver cash closure record.
- `difference_amount` is calculated as `received_amount - expected_amount`.
- If status is `verified`, `verified_by` and `verified_at` are set automatically.

Request body example:

```json
{
  "driver_id": 1,
  "expected_amount": 5000,
  "received_amount": 4800,
  "status": "verified",
  "notes": "Cash counted in office"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "driver_id": 1,
    "driver_name": "Mahmoud Hassan",
    "expected_amount": "5000.00",
    "received_amount": "4800.00",
    "difference_amount": "-200.00",
    "status": "verified",
    "notes": "Cash counted in office",
    "created_by": 1,
    "created_by_name": "Admin User",
    "verified_by": 1,
    "verified_by_name": "Admin User",
    "verified_at": "2026-04-24T19:00:00.000000Z",
    "created_at": "2026-04-24T19:00:00.000000Z"
  }
}
```

### GET `/api/driver-cash-closures/{driverCashClosure}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one driver cash closure.

### PUT `/api/driver-cash-closures/{driverCashClosure}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a driver cash closure and recalculates `difference_amount`.

### DELETE `/api/driver-cash-closures/{driverCashClosure}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a driver cash closure.

### GET `/api/drivers/{driver}/cash-expected`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Calculates expected COD cash for a driver from delivered COD shipments assigned to that driver.

Success response example:

```json
{
  "data": {
    "driver_id": 1,
    "expected_amount": "5000.00"
  }
}
```

### GET `/api/expenses`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns expenses with optional filters.
- Optional filters:
- `category`
- `payment_method`
- `date_from`
- `date_to`

### POST `/api/expenses`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates an expense record.

Request body example:

```json
{
  "category": "fuel",
  "amount": 300,
  "expense_date": "2026-04-24",
  "payment_method": "cash",
  "reference_number": "EXP-1001",
  "notes": "Delivery fuel refill"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "category": "fuel",
    "amount": "300.00",
    "expense_date": "2026-04-24",
    "payment_method": "cash",
    "reference_number": "EXP-1001",
    "notes": "Delivery fuel refill",
    "created_by": 1,
    "created_by_name": "Admin User",
    "created_at": "2026-04-24T19:15:00.000000Z"
  }
}
```

### GET `/api/expenses/{expense}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one expense.

### PUT `/api/expenses/{expense}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates an expense.

### DELETE `/api/expenses/{expense}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes an expense.

### GET `/api/finance/company-profit-summary`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns company profit summary from delivered shipments, warehouse charges, and expenses.

Calculation formulas:

- `total_shipping_fees`: sum of `shipping_fee` for delivered shipments.
- `total_warehouse_charges`: sum of non-cancelled `warehouse_charges.amount`.
- `total_expenses`: sum of `expenses.amount`.
- `net_company_profit`: `total_shipping_fees + total_warehouse_charges - total_expenses`.
- `total_cod_collected`: sum of `cod_amount` for delivered COD shipments.
- `delivered_shipments_count`: count delivered shipments.

Request examples:

```text
GET /api/finance/company-profit-summary
GET /api/finance/company-profit-summary?date_from=2026-04-01&date_to=2026-04-24
```

Success response example:

```json
{
  "data": {
    "total_shipping_fees": "1000.00",
    "total_warehouse_charges": "250.00",
    "total_expenses": "300.00",
    "net_company_profit": "950.00",
    "total_cod_collected": "5000.00",
    "delivered_shipments_count": 20,
    "filters": {
      "date_from": null,
      "date_to": null
    }
  }
}
```

### GET `/api/merchant-invoices`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns merchant invoices with optional filters.
- Optional filters:
- `merchant_id`
- `status`
- `date_from`
- `date_to`

### POST `/api/merchant-invoices`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a merchant invoice and calculates shipment totals plus non-cancelled warehouse charges within the period.
- Invoice number is generated automatically in format `INV-YYYYMMDD-000001`.
- If status is `issued`, `issued_at` is set automatically.

Request body example:

```json
{
  "merchant_id": 1,
  "period_start": "2026-04-01",
  "period_end": "2026-04-24",
  "status": "issued",
  "notes": "April statement"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "invoice_number": "INV-20260424-000001",
    "period_start": "2026-04-01",
    "period_end": "2026-04-24",
    "total_cod": "5000.00",
    "total_shipping_fees": "600.00",
    "total_warehouse_charges": "250.00",
    "total_payable": "4400.00",
    "status": "issued",
    "notes": "April statement",
    "created_by": 1,
    "created_by_name": "Admin User",
    "issued_at": "2026-04-24T19:30:00.000000Z",
    "created_at": "2026-04-24T19:30:00.000000Z"
  }
}
```

### GET `/api/merchant-invoices/{merchantInvoice}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one merchant invoice.

### GET `/api/merchant-invoices/{merchantInvoice}/preview`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns structured invoice preview data including merchant info, period, totals, and shipment list.

Success response example:

```json
{
  "data": {
    "merchant": {
      "id": 1,
      "name": "Ahmed Store",
      "phone": "01011111111"
    },
    "invoice_id": 1,
    "invoice_number": "INV-20260424-000001",
    "status": "issued",
    "period": {
      "start": "2026-04-01",
      "end": "2026-04-24"
    },
    "totals": {
      "total_cod": "5000.00",
      "total_shipping_fees": "600.00",
      "total_warehouse_charges": "250.00",
      "total_payable": "4400.00"
    },
    "warehouse_charges": [
      {
        "id": 3,
        "type": "storage",
        "description": "April storage fee",
        "quantity": "10.00",
        "unit_price": "25.00",
        "amount": "250.00",
        "status": "pending",
        "charge_date": "2026-04-24T00:00:00.000000Z",
        "notes": null
      }
    ],
    "shipments": [
      {
        "shipment_id": 1,
        "shipment_number": "SHP-20260424-000001",
        "customer_name": "Ahmed Adel",
        "customer_phone": "01000000000",
        "payment_type": "cod",
        "cod_amount": "450.00",
        "shipping_fee": "80.00",
        "delivered_at": "2026-04-24T15:00:00.000000Z"
      }
    ]
  }
}
```

### GET `/api/merchant-invoices/{merchantInvoice}/download`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Generates and downloads invoice PDF, stores it under `storage/app/private/invoices`, and reuses it if it already exists.
- On each download, `download_count` is incremented and `last_downloaded_at` is updated.

### PUT `/api/merchant-invoices/{merchantInvoice}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a merchant invoice and recalculates totals from delivered shipments for the selected period.

### DELETE `/api/merchant-invoices/{merchantInvoice}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a merchant invoice.

### GET `/api/finance/reconciliation-summary`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns high-level reconciliation totals between expected COD, verified driver cash, and merchant payouts.

Calculation formulas:

- `total_cod_expected`: sum of `cod_amount` for delivered COD shipments.
- `total_driver_cash_verified`: sum of verified driver cash closure `received_amount`.
- `cod_difference`: `total_driver_cash_verified - total_cod_expected`.
- `total_merchant_payable`: sum of `max(cod_amount - shipping_fee, 0)` for delivered shipments.
- `total_merchant_paid_out`: sum of completed merchant payouts.
- `merchant_balance_remaining`: `total_merchant_payable - total_merchant_paid_out`.

Request examples:

```text
GET /api/finance/reconciliation-summary
GET /api/finance/reconciliation-summary?date_from=2026-04-01&date_to=2026-04-24
```

Success response example:

```json
{
  "data": {
    "total_cod_expected": "5000.00",
    "total_driver_cash_verified": "4800.00",
    "cod_difference": "-200.00",
    "total_merchant_payable": "4400.00",
    "total_merchant_paid_out": "2000.00",
    "merchant_balance_remaining": "2400.00",
    "filters": {
      "date_from": null,
      "date_to": null
    }
  }
}
```

### GET `/api/finance/reports/overview`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns high-level finance overview report.
- Optional filters:
- `date_from`
- `date_to`
- `merchant_id`
- `driver_id`
- `status`

Success response example:

```json
{
  "data": {
    "total_orders": 15,
    "total_shipments": 12,
    "delivered_shipments": 8,
    "cod_expected": "5000.00",
    "cod_collected_verified": "4800.00",
    "shipping_fees": "1000.00",
    "warehouse_charges": "250.00",
    "merchant_payables": "4400.00",
    "merchant_paid_out": "2000.00",
    "merchant_remaining_balance": "2400.00",
    "expenses": "300.00",
    "company_net_profit": "950.00",
    "filters": {
      "date_from": null,
      "date_to": null,
      "merchant_id": null,
      "driver_id": null,
      "status": null
    }
  }
}
```

### GET `/api/finance/reports/merchant/{merchant}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns merchant-specific finance report.

### GET `/api/finance/reports/drivers`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns driver finance report items with shipment counts, expected COD, received COD, and differences.

Success response example:

```json
{
  "data": {
    "items": [
      {
        "driver_id": 1,
        "driver_name": "Mahmoud Hassan",
        "shipments": 5,
        "delivered": 3,
        "expected_cod": "5000.00",
        "received_cod": "4800.00",
        "differences": "-200.00"
      }
    ],
    "filters": {
      "date_from": null,
      "date_to": null,
      "driver_id": null,
      "status": null
    }
  }
}
```

### GET `/api/finance/reports/expenses`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns total expenses and grouped amounts by category.

### GET `/api/finance/reports/payouts`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns total payouts, pending payouts, and completed payouts.

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

## Warehouse APIs

### Warehouse Notes

- Warehouse products always belong to a merchant.
- Stock adjustment in Phase 1 supports `in`, `out`, `damaged`, and basic `adjustment`.
- Warehouse Phase 2 adds order stock reservation, release, and fulfillment for orders where `fulfillment_type = from_warehouse`.
- Reservation decreases `quantity_available` and increases `quantity_reserved`.
- Releasing stock moves quantity back from `reserved` to `available`.
- Fulfilling warehouse stock deducts from `quantity_reserved` and records stock movement type `out`.

### GET `/api/warehouses`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all warehouses.

### POST `/api/warehouses`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a warehouse.

Request body example:

```json
{
  "name": "Main Warehouse",
  "code": "WH-CAI-01",
  "address": "Industrial Zone",
  "governorate_id": 1,
  "area_id": 2,
  "status": "active",
  "notes": "Primary storage location"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "name": "Main Warehouse",
    "code": "WH-CAI-01",
    "address": "Industrial Zone",
    "governorate_id": 1,
    "governorate_name": "Cairo",
    "area_id": 2,
    "area_name": "Nasr City",
    "status": "active",
    "notes": "Primary storage location",
    "created_at": "2026-04-25T09:00:00.000000Z"
  }
}
```

### GET `/api/warehouse-products`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns warehouse products.
- Optional filters:
- `merchant_id`
- `status`
- `search` on `name`, `sku`, `barcode`

### POST `/api/warehouse-products`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a warehouse product for a merchant.

Request body example:

```json
{
  "merchant_id": 1,
  "name": "Blue T-Shirt",
  "sku": "TSHIRT-BLUE-M",
  "barcode": "1234567890",
  "description": "Cotton T-Shirt",
  "unit_weight": 0.35,
  "is_fragile": false,
  "requires_packaging": true,
  "status": "active"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "name": "Blue T-Shirt",
    "sku": "TSHIRT-BLUE-M",
    "barcode": "1234567890",
    "description": "Cotton T-Shirt",
    "unit_weight": "0.35",
    "unit_length": null,
    "unit_width": null,
    "unit_height": null,
    "is_fragile": false,
    "requires_packaging": true,
    "status": "active",
    "notes": null,
    "created_at": "2026-04-25T09:10:00.000000Z"
  }
}
```

### GET `/api/inventory-stocks`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns inventory stock rows.
- Optional filters:
- `warehouse_id`
- `merchant_id`
- `warehouse_product_id`
- `low_stock=true`

### POST `/api/inventory-stocks/adjust`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Applies a manual stock adjustment and records a stock movement.

Request body example:

```json
{
  "warehouse_id": 1,
  "warehouse_product_id": 1,
  "type": "in",
  "quantity": 100,
  "notes": "Initial stock intake"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "warehouse_id": 1,
    "warehouse_name": "Main Warehouse",
    "warehouse_product_id": 1,
    "product_name": "Blue T-Shirt",
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "sku": "TSHIRT-BLUE-M",
    "barcode": "1234567890",
    "quantity_available": 100,
    "quantity_reserved": 0,
    "quantity_damaged": 0,
    "created_at": "2026-04-25T09:15:00.000000Z",
    "updated_at": "2026-04-25T09:15:00.000000Z"
  }
}
```

Common error response example:

```json
{
  "message": "Not enough available stock."
}
```

### GET `/api/stock-movements`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns stock movement history.
- Optional filters:
- `warehouse_id`
- `merchant_id`
- `warehouse_product_id`
- `type`
- `date_from`
- `date_to`

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 1,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "type": "in",
      "quantity": 100,
      "before_available": 0,
      "after_available": 100,
      "before_reserved": 0,
      "after_reserved": 0,
      "before_damaged": 0,
      "after_damaged": 0,
      "reference_type": null,
      "reference_id": null,
      "notes": "Initial stock intake",
      "created_by": 1,
      "created_by_name": "Admin User",
      "created_at": "2026-04-25T09:15:00.000000Z"
    }
  ]
}
```

### GET `/api/merchants/{merchant}/warehouse/inventory`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns inventory stocks for the selected merchant only.

### GET `/api/merchants/{merchant}/warehouse/movements`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns stock movements for the selected merchant only.

### POST `/api/warehouse-returns`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Records a warehouse return and updates stock based on the returned condition inside a database transaction.
- Conditions:
- `sellable`: increases `quantity_available` and creates stock movement type `in`.
- `damaged`: increases `quantity_damaged` and creates stock movement type `damaged`.
- `disposed`: leaves stock quantities unchanged and creates stock movement type `adjustment`.

Request body example:

```json
{
  "shipment_id": 8,
  "order_id": 15,
  "warehouse_id": 1,
  "warehouse_product_id": 4,
  "quantity": 2,
  "condition": "sellable",
  "notes": "Customer refused delivery, items are intact"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "shipment_id": 8,
    "order_id": 15,
    "warehouse_id": 1,
    "warehouse_name": "Main Warehouse",
    "warehouse_product_id": 4,
    "product_name": "Blue T-Shirt",
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "quantity": 2,
    "condition": "sellable",
    "notes": "Customer refused delivery, items are intact",
    "created_by": 1,
    "created_by_name": "Admin User",
    "created_at": "2026-04-25T12:00:00.000000Z"
  }
}
```

### GET `/api/warehouse-returns`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns warehouse returns with optional filters.
- Optional filters:
- `shipment_id`
- `order_id`
- `warehouse_id`
- `merchant_id`
- `warehouse_product_id`
- `condition`
- `date_from`
- `date_to`

Request examples:

```text
GET /api/warehouse-returns
GET /api/warehouse-returns?condition=damaged&warehouse_id=1
GET /api/warehouse-returns?merchant_id=1&date_from=2026-04-01&date_to=2026-04-25
```

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "shipment_id": 8,
      "order_id": 15,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 4,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "quantity": 2,
      "condition": "sellable",
      "notes": "Customer refused delivery, items are intact",
      "created_by": 1,
      "created_by_name": "Admin User",
      "created_at": "2026-04-25T12:00:00.000000Z"
    }
  ]
}
```

### GET `/api/warehouse-returns/{warehouseReturn}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one warehouse return by ID.

### GET `/api/warehouse-charges`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns warehouse charges with optional filters.
- Optional filters:
- `merchant_id`
- `warehouse_id`
- `type`
- `status`
- `date_from`
- `date_to`

### POST `/api/warehouse-charges`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Creates a warehouse-related merchant charge.
- Logic:
- `amount = quantity * unit_price`
- `quantity` defaults to `1.00` if omitted.
- `charge_date` defaults to today if omitted.

Request body example:

```json
{
  "merchant_id": 1,
  "warehouse_id": 1,
  "order_id": 15,
  "shipment_id": 8,
  "warehouse_product_id": 4,
  "type": "return_handling",
  "description": "Returned shipment handling",
  "quantity": 1,
  "unit_price": 35,
  "status": "pending",
  "charge_date": "2026-04-25",
  "notes": "Manual charge"
}
```

Success response example:

```json
{
  "data": {
    "id": 1,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "warehouse_id": 1,
    "warehouse_name": "Main Warehouse",
    "order_id": 15,
    "shipment_id": 8,
    "warehouse_product_id": 4,
    "product_name": "Blue T-Shirt",
    "type": "return_handling",
    "description": "Returned shipment handling",
    "quantity": "1.00",
    "unit_price": "35.00",
    "amount": "35.00",
    "status": "pending",
    "charge_date": "2026-04-25T00:00:00.000000Z",
    "notes": "Manual charge",
    "created_by": 1,
    "created_by_name": "Admin User",
    "created_at": "2026-04-25T12:10:00.000000Z"
  }
}
```

### GET `/api/warehouse-charges/{warehouseCharge}`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns one warehouse charge by ID.

### PUT `/api/warehouse-charges/{warehouseCharge}`

- Method: `PUT`
- Authentication: `Yes`
- Purpose: Updates a warehouse charge and recalculates `amount` when `quantity` or `unit_price` changes.

### DELETE `/api/warehouse-charges/{warehouseCharge}`

- Method: `DELETE`
- Authentication: `Yes`
- Purpose: Soft deletes a warehouse charge.

Success response example:

```json
{
  "message": "Warehouse charge deleted successfully"
}
```

### GET `/api/merchants/{merchant}/warehouse/charges`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns warehouse charges for the selected merchant only.
- Optional filters:
- `warehouse_id`
- `type`
- `status`
- `date_from`
- `date_to`

### POST `/api/orders/{order}/reserve-stock`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Reserves warehouse stock for a warehouse-fulfilled order inside a single database transaction.
- Rules:
- Order must have `fulfillment_type = from_warehouse`.
- Order must not be `cancelled`.
- Order must not already have active `reserved` stock reservations.
- Every `order_item_id` must belong to the order.
- Every `warehouse_product_id` must belong to the same merchant as the order.
- If any item fails, the whole reservation request is rolled back.

Request body example:

```json
{
  "warehouse_id": 1,
  "items": [
    {
      "order_item_id": 10,
      "warehouse_product_id": 4,
      "quantity": 2
    },
    {
      "order_item_id": 11,
      "warehouse_product_id": 5,
      "quantity": 1
    }
  ]
}
```

Success response example:

```json
{
  "data": {
    "id": 15,
    "merchant_id": 1,
    "merchant_name": "Ahmed Store",
    "order_number": "ORD-20260425-000015",
    "customer_name": "Mona Hossam",
    "customer_phone": "01012345678",
    "customer_phone_alt": null,
    "delivery_governorate_id": 1,
    "delivery_governorate_name": "Cairo",
    "delivery_area_id": 2,
    "delivery_area_name": "Nasr City",
    "delivery_address": "Building 18",
    "delivery_notes": null,
    "pickup_governorate_id": null,
    "pickup_area_id": null,
    "pickup_address": null,
    "pickup_notes": null,
    "cod_amount": "350.00",
    "shipping_fee": "60.00",
    "payment_type": "cod",
    "fulfillment_type": "from_warehouse",
    "is_fragile": false,
    "allow_inspection": true,
    "requires_packaging": false,
    "package_notes": null,
    "source": "manual",
    "external_source": null,
    "external_order_id": null,
    "external_order_number": null,
    "requires_review": false,
    "review_reason": null,
    "status": "confirmed",
    "notes": null,
    "items": [
      {
        "id": 10,
        "product_name": "Blue T-Shirt",
        "sku": "TSHIRT-BLUE-M",
        "quantity": 2,
        "unit_price": "120.00",
        "weight": "0.35",
        "notes": null,
        "created_at": "2026-04-25T10:15:00.000000Z"
      }
    ],
    "stock_reservations": [
      {
        "id": 1,
        "order_id": 15,
        "order_item_id": 10,
        "warehouse_id": 1,
        "warehouse_name": "Main Warehouse",
        "warehouse_product_id": 4,
        "product_name": "Blue T-Shirt",
        "merchant_id": 1,
        "merchant_name": "Ahmed Store",
        "quantity": 2,
        "status": "reserved",
        "notes": null,
        "created_by": 1,
        "created_by_name": "Admin User",
        "fulfilled_at": null,
        "released_at": null,
        "created_at": "2026-04-25T10:20:00.000000Z"
      }
    ],
    "created_at": "2026-04-25T10:15:00.000000Z"
  }
}
```

Common error response examples:

```json
{
  "message": "Only warehouse fulfillment orders can reserve stock."
}
```

```json
{
  "message": "Order already has active stock reservations."
}
```

```json
{
  "message": "Not enough available stock."
}
```

### POST `/api/orders/{order}/release-stock`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Releases all active `reserved` reservations for the order and moves stock back to available.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 15,
      "order_item_id": 10,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 4,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "quantity": 2,
      "status": "released",
      "notes": null,
      "created_by": 1,
      "created_by_name": "Admin User",
      "fulfilled_at": null,
      "released_at": "2026-04-25T10:30:00.000000Z",
      "created_at": "2026-04-25T10:20:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Cancelled orders cannot manage warehouse stock."
}
```

### POST `/api/orders/{order}/fulfill-from-warehouse`

- Method: `POST`
- Authentication: `Yes`
- Purpose: Fulfills all active `reserved` reservations for the order and deducts from reserved stock without creating a shipment.
- Notes:
- Shipment creation stays in the existing shipment flow.
- Fulfillment records stock movement type `out`.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 15,
      "order_item_id": 10,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 4,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "quantity": 2,
      "status": "fulfilled",
      "notes": null,
      "created_by": 1,
      "created_by_name": "Admin User",
      "fulfilled_at": "2026-04-25T10:40:00.000000Z",
      "released_at": null,
      "created_at": "2026-04-25T10:20:00.000000Z"
    }
  ]
}
```

Common error response example:

```json
{
  "message": "Only warehouse fulfillment orders can reserve stock."
}
```

### GET `/api/orders/{order}/stock-reservations`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns all stock reservations for one order ordered by newest first.

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 15,
      "order_item_id": 10,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 4,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "quantity": 2,
      "status": "reserved",
      "notes": null,
      "created_by": 1,
      "created_by_name": "Admin User",
      "fulfilled_at": null,
      "released_at": null,
      "created_at": "2026-04-25T10:20:00.000000Z"
    }
  ]
}
```

### GET `/api/stock-reservations`

- Method: `GET`
- Authentication: `Yes`
- Purpose: Returns stock reservations with optional filters.
- Optional filters:
- `order_id`
- `warehouse_id`
- `warehouse_product_id`
- `status`
- `merchant_id`

Request examples:

```text
GET /api/stock-reservations
GET /api/stock-reservations?status=reserved&warehouse_id=1
GET /api/stock-reservations?merchant_id=1&order_id=15
```

Success response example:

```json
{
  "data": [
    {
      "id": 1,
      "order_id": 15,
      "order_item_id": 10,
      "warehouse_id": 1,
      "warehouse_name": "Main Warehouse",
      "warehouse_product_id": 4,
      "product_name": "Blue T-Shirt",
      "merchant_id": 1,
      "merchant_name": "Ahmed Store",
      "quantity": 2,
      "status": "reserved",
      "notes": null,
      "created_by": 1,
      "created_by_name": "Admin User",
      "fulfilled_at": null,
      "released_at": null,
      "created_at": "2026-04-25T10:20:00.000000Z"
    }
  ]
}
```

Common validation error response example:

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
