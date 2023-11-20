## Mintos homework

Built with Laravel 10

## Requirements

* PHP 8.1 - 8.2
* PHP-CURL
* Composer

## Installation

* `git clone https://github.com/boomix/Mintos.git .`
* `composer install`
* Rename .env.example to .env and change DB connection access
* `php artisan migrate` (creates tables)
* `php artisan currencies:update`
* `php artisan db:seed --class=ClientsSeeder`
* `php artisan serve`

## Commands

* `php artisan currencies:update` (updates latest currencies)
* `php artisan db:seed --class=ClientsSeeder` (adds random clients with 0-3 accounts)

## REST API docs

---
URL: 

`GET /accounts`

Params:
```
client_id
```

Response example
```
{
   "success": true,
   "data": [
      {
         "id": 53,
         "client_id": 133,
         "balance": 61,
         "currency": "USD"
      },
      {
         "id": 52,
         "client_id": 133,
         "balance": 21.91,
         "currency": "AZN"
      },
      {
         "id": 51,
         "client_id": 133,
         "balance": 65,
         "currency": "PKR"
      }
   ]
}
```
---
URL: 

`GET /transactions`

Params:
```
account_id
limit
offset
```

Response example
```
{
   "success": true,
   "data": [
      {
         "id": 5,
         "account_id": 54,
         "target_account_id": 53,
         "amount": 10,
         "transfered_at": "2023-11-20 10:29:30"
      },
      {
         "id": 3,
         "account_id": 54,
         "target_account_id": 53,
         "amount": 10,
         "transfered_at": "2023-11-20 10:29:01"
      },
      {
         "id": 2,
         "account_id": 54,
         "target_account_id": 53,
         "amount": 10,
         "transfered_at": "2023-11-20 10:29:01"
      }
   ]
}
```
---
URL: 

`POST /transfer`

Params:
```
account_id
target_account_id
amount
currency
```

Response example
```
{
   "success":true,
   "data":[]
}
```