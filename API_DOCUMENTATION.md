# SOD API Documentation

Base URL: `http://localhost:9090/api`

## Headers Required
- `Accept: application/json`
- `Content-Type: application/json` (for POST/PUT requests)
- `Vendor: {vendor_id}` (required for most endpoints)
- `Authorization: Bearer {token}` (for authenticated endpoints)

## Health Check

### Check API Status
```bash
curl -X GET "http://localhost:9090/api/health" \
  -H "Accept: application/json"
```

## Authentication (Customer)

### Register Customer
```bash
curl -X POST "http://localhost:9090/api/register" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "name": "John Doe",
    "mobile": "01712345678",
    "email": "john@example.com",
    "password": "password123",
    "device": "web"
  }'
```

### Login Customer
```bash
curl -X POST "http://localhost:9090/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "email": "john@example.com",
    "password": "password123",
    "device": "web"
  }'
```

### Send OTP for Reset Password
```bash
curl -X POST "http://localhost:9090/api/send-otp" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "mobile": "01712345678"
  }'
```

### Verify OTP
```bash
curl -X POST "http://localhost:9090/api/verify-otp" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "mobile": "01712345678",
    "otp": "123456"
  }'
```

### Set New Password
```bash
curl -X POST "http://localhost:9090/api/set-new-pass" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "mobile": "01712345678",
    "password": "newpassword123"
  }'
```

### Get User Details (Authenticated)
```bash
curl -X GET "http://localhost:9090/api/user" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Logout
```bash
curl -X POST "http://localhost:9090/api/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

## General Information

### Get Settings
```bash
curl -X GET "http://localhost:9090/api/settings" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Top Notifications
```bash
curl -X GET "http://localhost:9090/api/top-notifications" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Sliders
```bash
curl -X GET "http://localhost:9090/api/sliders" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Social Links
```bash
curl -X GET "http://localhost:9090/api/social-links" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Static Page
```bash
curl -X GET "http://localhost:9090/api/static-pages/1" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Contact Us
```bash
curl -X POST "http://localhost:9090/api/contact-us" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Support Request",
    "message": "I need help with my order"
  }'
```

## Products

### Get Categories
```bash
curl -X GET "http://localhost:9090/api/categories" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Product Details
```bash
curl -X GET "http://localhost:9090/api/products/product-slug" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get New Products
```bash
curl -X GET "http://localhost:9090/api/new-products" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Popular Products
```bash
curl -X GET "http://localhost:9090/api/popular-products" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Hot Products
```bash
curl -X GET "http://localhost:9090/api/hot-products" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Related Products
```bash
curl -X GET "http://localhost:9090/api/related-products/product-slug" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Search Products
```bash
curl -X GET "http://localhost:9090/api/search-products?q=search_term" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Products by Category
```bash
curl -X GET "http://localhost:9090/api/category-products/1" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

## Cart Management

### Get Cart Items
```bash
curl -X GET "http://localhost:9090/api/carts" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Add to Cart
```bash
curl -X POST "http://localhost:9090/api/carts" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "product_id": 1,
    "product_price_id": 1,
    "quantity": 2
  }'
```

### Remove from Cart
```bash
curl -X DELETE "http://localhost:9090/api/carts/1" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

## Checkout & Orders

### Get Cities
```bash
curl -X GET "http://localhost:9090/api/cities" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Get Areas
```bash
curl -X GET "http://localhost:9090/api/areas?city_id=1" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Place Order
```bash
curl -X POST "http://localhost:9090/api/checkout" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "name": "John Doe",
    "mobile": "01712345678",
    "email": "john@example.com",
    "address": "123 Main Street",
    "city_id": 1,
    "area_id": 1,
    "payment_method": "cash_on_delivery",
    "items": [
      {
        "product_id": 1,
        "product_price_id": 1,
        "quantity": 2
      }
    ]
  }'
```

### Place Order V2 (Enhanced)
```bash
curl -X POST "http://localhost:9090/api/v2/checkout" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "name": "John Doe",
    "mobile": "01712345678",
    "email": "john@example.com",
    "address": "123 Main Street",
    "city_id": 1,
    "area_id": 1,
    "payment_method": "cash_on_delivery",
    "courier_id": 1,
    "note": "Please deliver in the evening"
  }'
```

### Get Order by Order Number
```bash
curl -X GET "http://localhost:9090/api/order/ORD-123456" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Pay for Order
```bash
curl -X POST "http://localhost:9090/api/pay/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Vendor: 1" \
  -d '{
    "payment_method": "bkash",
    "transaction_id": "TXN123456"
  }'
```

## Account Management (Authenticated)

### Get User Orders
```bash
curl -X GET "http://localhost:9090/api/account/orders" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Get Order Details
```bash
curl -X GET "http://localhost:9090/api/account/orders/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Change Password
```bash
curl -X POST "http://localhost:9090/api/account/change-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
  }'
```

### Update Account Details
```bash
curl -X POST "http://localhost:9090/api/account/details" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "name": "John Updated",
    "email": "john.updated@example.com",
    "mobile": "01712345679"
  }'
```

## Address Management (Authenticated)

### Get Addresses
```bash
curl -X GET "http://localhost:9090/api/addresses" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Create Address
```bash
curl -X POST "http://localhost:9090/api/addresses" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "name": "Home",
    "address": "123 Main Street",
    "city_id": 1,
    "area_id": 1,
    "mobile": "01712345678"
  }'
```

### Update Address
```bash
curl -X PUT "http://localhost:9090/api/addresses/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "name": "Home Updated",
    "address": "456 Oak Avenue",
    "city_id": 1,
    "area_id": 2,
    "mobile": "01712345678"
  }'
```

### Delete Address
```bash
curl -X DELETE "http://localhost:9090/api/addresses/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

## Wishlist (Authenticated)

### Get Wishlist
```bash
curl -X GET "http://localhost:9090/api/wishlists" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Add to Wishlist
```bash
curl -X POST "http://localhost:9090/api/wishlists" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "product_id": 1
  }'
```

## Reviews

### Get Product Reviews
```bash
curl -X GET "http://localhost:9090/api/review/1" \
  -H "Accept: application/json" \
  -H "Vendor: 1"
```

### Check Review Eligibility (Authenticated)
```bash
curl -X POST "http://localhost:9090/api/review/eligible/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1"
```

### Add Review (Authenticated)
```bash
curl -X POST "http://localhost:9090/api/review" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {your_token}" \
  -H "Vendor: 1" \
  -d '{
    "product_id": 1,
    "rating": 5,
    "comment": "Great product, highly recommended!"
  }'
```

---

# Vendor API Documentation

Base URL: `http://localhost:9090/api/vendor`

## Vendor Authentication

### Vendor Login
```bash
curl -X POST "http://localhost:9090/api/vendor/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vendor@example.com",
    "password": "password123",
    "device": "web"
  }'
```

### Get Vendor Details
```bash
curl -X GET "http://localhost:9090/api/vendor/vendor-details" \
  -H "Accept: application/json"
```

### Get Vendor User (Authenticated)
```bash
curl -X GET "http://localhost:9090/api/vendor/user" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Change Vendor Password (Authenticated)
```bash
curl -X POST "http://localhost:9090/api/vendor/change-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
  }'
```

### Forgot Password
```bash
curl -X POST "http://localhost:9090/api/vendor/forgot-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vendor@example.com"
  }'
```

### Reset Password
```bash
curl -X POST "http://localhost:9090/api/vendor/password/reset" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "reset_token",
    "email": "vendor@example.com",
    "password": "newpassword",
    "password_confirmation": "newpassword"
  }'
```

## Dashboard (Authenticated)

### Get Top Widgets
```bash
curl -X GET "http://localhost:9090/api/vendor/dashboard/top-widgets" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Get Sales Chart
```bash
curl -X GET "http://localhost:9090/api/vendor/dashboard/sales-chart" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Get Dashboard Data by Date Range
```bash
curl -X POST "http://localhost:9090/api/vendor/dashboard-data" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "start_date": "2024-01-01",
    "end_date": "2024-01-31"
  }'
```

## Category Management (Authenticated)

### Get Categories
```bash
curl -X GET "http://localhost:9090/api/vendor/categories" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Create Category
```bash
curl -X POST "http://localhost:9090/api/vendor/categories" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "name": "Electronics",
    "active": true
  }'
```

### Update Category
```bash
curl -X PUT "http://localhost:9090/api/vendor/categories/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "name": "Electronics Updated",
    "active": true
  }'
```

### Delete Category
```bash
curl -X DELETE "http://localhost:9090/api/vendor/categories/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Get Sub-Categories
```bash
curl -X GET "http://localhost:9090/api/vendor/categories/1/sub-categories" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Create Sub-Category
```bash
curl -X POST "http://localhost:9090/api/vendor/categories/1/sub-categories" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "name": "Smartphones",
    "active": true
  }'
```

## Product Management (Authenticated)

### Get Products
```bash
curl -X GET "http://localhost:9090/api/vendor/products?active=1&stock_out=0" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Create Product (with file upload)
```bash
curl -X POST "http://localhost:9090/api/vendor/products" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -F "name=iPhone 14" \
  -F "description=Latest iPhone model" \
  -F "category_id=1" \
  -F "sub_category_id=1" \
  -F "active=true" \
  -F "stock_out=false" \
  -F "available_qty=10" \
  -F "image=@/path/to/image.jpg" \
  -F "variety[0][name]=64GB" \
  -F "variety[0][price]=80000" \
  -F "variety[0][original_price]=85000" \
  -F "variety[1][name]=128GB" \
  -F "variety[1][price]=90000" \
  -F "variety[1][original_price]=95000"
```

### Update Product
```bash
curl -X PUT "http://localhost:9090/api/vendor/products/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -F "name=iPhone 14 Updated" \
  -F "description=Updated description" \
  -F "active=true" \
  -F "variety[0][name]=64GB" \
  -F "variety[0][price]=75000"
```

### Get Product Prices
```bash
curl -X GET "http://localhost:9090/api/vendor/product/1/prices" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Delete Product
```bash
curl -X DELETE "http://localhost:9090/api/vendor/products/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

## Order Management (Authenticated)

### Get Orders
```bash
curl -X GET "http://localhost:9090/api/vendor/orders" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Get Single Order
```bash
curl -X GET "http://localhost:9090/api/vendor/orders/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Update Order Status
```bash
curl -X POST "http://localhost:9090/api/vendor/orders/1/status" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "status": "processing",
    "note": "Order is being processed"
  }'
```

### Get Order Invoice
```bash
curl -X GET "http://localhost:9090/api/vendor/orders/1/invoice" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Download Orders Excel
```bash
curl -X GET "http://localhost:9090/api/vendor/orders-download" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

## Customer Management (Authenticated)

### Get Customers
```bash
curl -X GET "http://localhost:9090/api/vendor/customers" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Get Customer by Mobile
```bash
curl -X GET "http://localhost:9090/api/vendor/customer-info/01712345678" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Create Customer
```bash
curl -X POST "http://localhost:9090/api/vendor/customers" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "name": "Jane Doe",
    "mobile": "01798765432",
    "email": "jane@example.com",
    "password": "password123"
  }'
```

## Settings Management (Authenticated)

### Get Settings
```bash
curl -X GET "http://localhost:9090/api/vendor/settings" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

### Update Settings
```bash
curl -X POST "http://localhost:9090/api/vendor/settings" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -F "name=My Store" \
  -F "phone=01712345678" \
  -F "email=store@example.com" \
  -F "logo=@/path/to/logo.jpg"
```

### Update Payment Methods
```bash
curl -X POST "http://localhost:9090/api/vendor/settings/payment-methods" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "bkash": true,
    "nogod": true,
    "cash_on_delivery": true
  }'
```

### Update Shipping Cost
```bash
curl -X POST "http://localhost:9090/api/vendor/settings/shipping-cost" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {vendor_token}" \
  -d '{
    "inside_dhaka": 60,
    "outside_dhaka": 120
  }'
```

### Get Couriers
```bash
curl -X GET "http://localhost:9090/api/vendor/couriers" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {vendor_token}"
```

## Common Response Formats

### Success Response
```json
{
  "data": {
    // Response data here
  },
  "message": "Success message",
  "status": true
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  },
  "status": false
}
```

### Authentication Token Response
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "mobile": "01712345678",
    "token": "1|abc123def456..."
  }
}
```

## Notes

1. Replace `{your_token}` and `{vendor_token}` with actual tokens received from login endpoints
2. Replace `{vendor_id}` with actual vendor ID (usually 1 for single vendor setup)
3. For file uploads, use `-F` flag with curl instead of `-d`
4. All timestamps are in UTC format
5. Pagination is supported on list endpoints with `page` and `per_page` parameters
6. Most endpoints support filtering and searching via query parameters