# API Documentation

This document provides a complete documentation of the API endpoints for the Order Management System.

## Base URL

The base URL for all API endpoints is `/api`.

## Authentication

Most endpoints require authentication. The API uses token-based authentication. The token must be included in the `Authorization` header of all requests.

Endpoints that require authentication are marked with `Requires authentication`.

## General Endpoints

### `GET /health`

Checks the health of the API.

**Response:**

```json
{
  "status": "Okay",
  "time": "2025-07-06T12:00:00.000000Z"
}
```

### `GET /settings`

Retrieves the application settings.

**Response:**

```json
{
  "data": {
    "setting_key": "setting_value"
  }
}
```

### `GET /top-notifications`

Retrieves the top notifications.

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "title": "Notification Title",
      "content": "Notification Content"
    }
  ]
}
```

### `GET /sliders`

Retrieves the sliders for the homepage.

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "image_url": "https://example.com/slider.jpg",
      "link": "https://example.com"
    }
  ]
}
```

### `POST /contact-us`

Submits a contact form.

**Request Body:**

```json
{
  "name": "John Doe",
  "mobile": "01234567890",
  "email": "johndoe@example.com",
  "subject": "Support Request",
  "message": "This is a support request."
}
```

### `GET /social-links`

Retrieves the social media links.

**Response:**

```json
{
  "data": [
    {
      "name": "Facebook",
      "url": "https://facebook.com/example"
    }
  ]
}
```

### `GET /static-pages/{id}`

Retrieves a static page by its ID.

**Response:**

```json
{
  "data": {
    "id": 1,
    "title": "About Us",
    "content": "This is the about us page."
  }
}
```

## Authentication Endpoints

### `POST /login`

Logs in a user.

**Request Body:**

```json
{
  "email": "user@example.com",
  "password": "password",
  "device": "device_name"
}
```

### `POST /register`

Registers a new user.

**Request Body:**

```json
{
  "name": "John Doe",
  "mobile": "01234567890",
  "email": "johndoe@example.com",
  "password": "password",
  "device": "device_name"
}
```

### `POST /logout`

Logs out a user. `Requires authentication`.

### `POST /send-otp`

Sends an OTP to the user's mobile number for password reset.

**Request Body:**

```json
{
  "mobile": "01234567890"
}
```

### `POST /verify-otp`

Verifies the OTP for password reset.

**Request Body:**

```json
{
  "mobile": "01234567890",
  "otp": "123456"
}
```

### `POST /set-new-pass`

Sets a new password for the user.

**Request Body:**

```json
{
  "mobile": "01234567890",
  "password": "new_password"
}
```

### `GET /user`

Retrieves the authenticated user's information. `Requires authentication`.

## Address Endpoints

### `GET /addresses`

Retrieves the authenticated user's addresses. `Requires authentication`.

### `POST /addresses`

Creates a new address for the authenticated user. `Requires authentication`.

**Request Body:**

```json
{
  "city_id": 1,
  "area_id": 1,
  "name": "John Doe",
  "mobile": "01234567890",
  "address": "123 Main St"
}
```

### `DELETE /addresses/{address}`

Deletes an address. `Requires authentication`.

## Wishlist Endpoints

### `GET /wishlists`

Retrieves the authenticated user's wishlist. `Requires authentication`.

### `POST /wishlists`

Adds or removes a product from the authenticated user's wishlist. `Requires authentication`.

**Request Body:**

```json
{
  "product_id": 1
}
```

## Account Endpoints

### `GET /account/orders`

Retrieves the authenticated user's orders. `Requires authentication`.

### `GET /account/orders/{order}`

Retrieves the details of a specific order. `Requires authentication`.

### `POST /account/change-password`

Changes the authenticated user's password. `Requires authentication`.

**Request Body:**

```json
{
  "old_password": "old_password",
  "new_password": "new_password"
}
```

### `POST /account/details`

Changes the authenticated user's account details. `Requires authentication`.

**Request Body:**

```json
{
  "name": "John Doe",
  "mobile": "01234567890",
  "email": "johndoe@example.com"
}
```

## Review Endpoints

### `GET /review/{productId}`

Retrieves the reviews for a product.

### `POST /review`

Adds a review for a product. `Requires authentication`.

**Request Body:**

```json
{
  "comment": "This is a great product!",
  "star": 5,
  "product_id": 1
}
```

### `POST /review/eligible/{productId}`

Checks if the authenticated user is eligible to review a product. `Requires authentication`.

## Category Endpoints

### `GET /categories`

Retrieves all categories.

## Product Endpoints

### `GET /products/{slug}`

Retrieves the details of a product.

### `GET /new-products`

Retrieves the new products.

### `GET /related-products/{product:slug}`

Retrieves related products.

### `GET /popular-products`

Retrieves popular products.

### `GET /search-products`

Searches for products.

**Query Parameters:**

*   `q`: The search query.

### `GET /hot-products`

Retrieves hot products.

### `GET /category-products/{category}`

Retrieves products in a category.

## Cart Endpoints

### `GET /carts`

Retrieves the contents of the cart.

### `POST /carts`

Adds a product to the cart.

**Request Body:**

```json
{
  "quantity": 1,
  "product_id": 1,
  "product_price_id": 1
}
```

### `DELETE /carts/{cart}`

Removes a product from the cart.

## Checkout Endpoints

### `POST /checkout`

Creates a new order.

**Request Body:**

```json
{
  "payment": "Cash On Delivery",
  "address_id": 1
}
```

### `POST /v2/checkout`

Creates a new order (version 2).

**Request Body:**

```json
{
  "vendor_id": 1,
  "product_id": 1,
  "quantity": 1,
  "city": "Dhaka",
  "name": "John Doe",
  "mobile_no": "01234567890",
  "address": "123 Main St"
}
```

### `GET /order/{orderNo}`

Retrieves an order by its order number.

### `POST /pay/{order}`

Pays for an order.

**Request Body:**

```json
{
  "payment_method": "bKash",
  "amount": 1000,
  "transaction_no": "ABC123XYZ"
}
```

### `GET /cities`

Retrieves a list of cities.

### `GET /areas`

Retrieves a list of areas for a city.

**Query Parameters:**

*   `city_id`: The ID of the city.
