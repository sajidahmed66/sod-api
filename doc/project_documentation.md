# SOD API Project Documentation

This document outlines the features of the SOD API, which is divided into two main parts: the Customer Facing API (Front) and the Vendor API.

## Customer Facing Features (Front API)

### Authentication
-   **Login/Register/Logout:** Standard user authentication.
-   **OTP:** OTP-based password reset functionality.

### Product
-   **Product Discovery:** Users can browse products by category, view new, popular, hot, and related products.
-   **Product Details:** View detailed information for a single product.
-   **Search:** Search for products.

### Cart
-   **Add/View/Remove:** Users can manage items in their shopping cart.

### Checkout
-   **Place Order:** Customers can place orders for the items in their cart.
-   **Payment:** Pay for an existing order.
-   **Order Tracking:** Get order details by the order number.

### Account Management
-   **Order History:** View past orders and their details.
-   **Profile Management:** Change password and update account details.
-   **Address Book:** Manage shipping addresses.

### Wishlist
-   **Add/View:** Users can add products to a wishlist and view their wishlist.

### Reviews
-   **Submit Review:** Users can write reviews for products they have purchased.

### General
-   **Settings:** Fetch general application settings.
-   **Notifications:** View top notifications.
-   **Content:** View sliders, social links, and static pages.
-   **Contact:** Submit a contact form.

## Vendor Features (Vendor API)

### Authentication
-   **Login:** Vendor-specific login.
-   **Password Reset:** Vendors can reset their passwords.

### Dashboard
-   **Analytics:** View dashboard widgets with key metrics and a sales chart.

### Product Management
-   **CRUD:** Full create, read, update, and delete functionality for products.
-   **Price Management:** Manage prices for products.

### Category Management
-   **CRUD:** Manage categories and sub-categories.

### Order Management
-   **CRUD:** Manage orders.
-   **Status Updates:** Change the status of an order.
-   **Invoicing:** Generate and view invoices for orders.
-   **Export:** Download orders in Excel format.

### Customer Management
-   **CRUD:** Manage customer information.
-   **Lookup:** Find customer data by mobile number.

### Settings
-   **General:** Configure general store settings.
-   **Payment & Shipping:** Manage payment methods and shipping costs.
-   **Content:** Manage sliders, top notifications, static pages, and social links.
-   **Couriers:** Manage courier services.

### Financials
-   **Transactions:** Manage financial transactions.
-   **Accounting:** Manage accounting entries.

### Inventory
-   **Logging:** Log and manage inventory changes.

### Communication
-   **SMS Logs:** View logs of sent SMS messages.
