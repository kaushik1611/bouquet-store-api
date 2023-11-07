# shopping-cart-api

[![GitHub license](https://img.shields.io/github/license/gothinkster/laravel-realworld-example-app.svg)](https://raw.githubusercontent.com/gothinkster/laravel-realworld-example-app/master/LICENSE)

> ### This is a Laravel APIs application that serves as a backend for a shopping cart application.

This repo is functionality complete — PRs and issues welcome!

----------

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)

## Getting Started

To get started with this application, follow the steps below:

### Prerequisites

- PHP (version 8.1)
- Composer
- MySQL
- Laravel (version 8.1.24)

### Installation

1. Clone the repository:
```
git clone https://github.com/kaushik1611/bouquet-store-api.git
```
2. Navigate to the project directory:
```
cd shopping-cart-api
```
3. Install dependencies:
```
composer install
```
4. Set up the database configurations in the .env file.

5. Run migrations and seeders: 
```
php artisan migrate --seed
```
6. Start the development server: 
```
php artisan serve
```

### Request headers (APIs)

| **Required** |     **Key**    |   **Value**     |
| :---         | :---           | :---            |
| Yes          | Content-Type   | application/json|
| Yes          | Accept         | application/json|

### APIs
```sh  
# Product list api
```
- **Endpoint 1:** `/api/bouquets 

```sh  
# Product detail api
```
- **Endpoint 2:** `/api/bouquets/:id
 ```markdown
- id: product id
   ```
```sh  
# Cart list api
```
- **Endpoint 3:** `/api/cart 

```sh  
# Add to cart api
```
- **Endpoint 4:** `/api/cart/add

 ```markdown
- product_id: product id
- quantity: quantity
   ```
```sh  
# Remove from cart api
```
- **Endpoint 5:** `/api/cart/remove

 ```markdown
- product_id: product id
   ```
   
 ```sh  
 # Voucher cart api
```
- **Endpoint 5:** `/api/voucher/apply

 ```markdown
- code: Voucher code
   ```