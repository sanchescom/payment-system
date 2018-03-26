## Run
Clone the repo
```sh
git clone https://github.com/sanchescom/payment-system.git
cd payment-system
```
Add host in hosts file
```sh
echo "127.0.0.1 payment-system.d" >> /etc/hosts
```
Install [Docker](https://docs.docker.com/) and [Docker Compose](https://docs.docker.com/compose/)

Build and run the Docker containers
```sh
docker-compose up -d && docker-compose up
```

### API Resources

  - [POST /users](#post-users)
  - [GET /users](#get-users)
  - [POST /currencies](#post-currencies)
  - [GET /payments/operations](#get-operations)
  - [GET /payments/download](#get-download)
  - [POST /payments/recharge](#post-recharge)
  - [POST /payments/transfer](#post-transfer)

### POST /users

Create user: http://payment-system.d:8082/api/users

Request body:

    {
        "name": "Alex",
        "country": "RU",
        "city": "Moscow",
        "currency": "RUB",
        "email": "alex@email.ru"
    }

Response body:
    
    {
        "user": {
            "id": 1,
            "name": "Alex",
            "country": "RU",
            "city": "Moscow",
            "currency": "RUB",
            "email": "alex@email.ru"
        },
        "secret": "wRw",
        "account": "RUB00000000001"
    }

### GET /users

Users list: http://payment-system.d:8082/api/users

Response body:

    [
        {
            "id": "1",
            "name": "Alex",
            "account": "RUB00000000001",
            "currency": "RUB"
        }
    ]


### POST /currencies

Currencies list: http://payment-system.d:8082/api/currencies

Request body:

    [
        {
            "date": "2018-03-12",
            "rate": "57.45",
            "currency": "RUB"
        }
    ]

### GET /payments/operations

User payments operations list: http://payment-system.d:8082/api/payments/operations

Request body:

    {
        "account": "RUB00000000001",
        "date_from": "2018-03-18",
        "date_to": "2018-03-18"
    }
    
Response body:

    {
        "data": [
            "id": 1,
            "payee": "RUB00000000002",
            "amount": "2000",
            "currency": "RUB"
        ],
        "meta": {
            "user": {
                "id": 1,
                "name": "Alex",
                "currency": "RUB",
                "account": "RUB00000000001"
            },
            "sums": {
                "native": 2000,
                "deafult": 10000
            }
        }
    }
    
### POST /payments/recharge

Recharge user account: http://payment-system.d:8082/api/payments/recharge

Request body:

    {
        "payee": "RUB00000000001",
        "amount": "1000",
        "currency": "RUB"
    }

Response:

- `204 No Content` - everything worked as expected.

### POST /payments/transfer

Sending money between users: http://payment-system.d:8082/api/payments/transfer

Request body:

    {
        "payee": "RUB00000000003",
        "amount": "RU",
        "currency": "USD"
    }

Response body:
    
    {
       "id": 1,
       "payee": "RUB00000000002",
       "amount": "2000",
       "currency": "RUB"
    }