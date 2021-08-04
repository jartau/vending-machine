# vending-machine

## Installation

Clone repository
```
git clone https://github.com/jartau/vending-machine.git
cd vending-machine
```

Build and init services
```
docker-compose build
docker-compose up -d
docker-compose exec app composer install
```

create .env file
```
cp src/.env.example src/.env
```

Set the following DB parameters on .env file
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=lara_db
DB_USERNAME=lara_user
DB_PASSWORD=lara_password
```

Generate application key
```
.docker/artisan key:generate
```

Create DB tables and fill it
```
.docker/artisan migrate --seed
```

URL: http://localhost:8008/

## Commands
```
# open bash
bash command: docker exec -it laravelapp-app bash

# use artisan
./docker/artisan

# run tests
./docker/artisan test
```

## How to use

### How to order drink

* Insert coins until you can pay for the drink:
    ```
    curl --location --request POST 'http://localhost:8008/order/insert-coin' --form 'value="1"'
    ```

* Choose the product:
    ```
    curl --location --request POST 'http://localhost:8008/order/choose-product' --form 'code="WATER"'
    ```

* Or ask for the change:
    ```
    curl --location --request GET 'localhost:8008/order/return-coin'
    ```

### Staff service actions

* Show status information (product stock, coin stock and earned)
    ```
    curl --location --request GET 'localhost:8008/service/info'
    ```
* Update product stock
    ```
    curl --location --request POST 'http://localhost:8008/service/add-product' --form 'code="JUICE"' --form 'quantity="15"'
    ```
* Update coin stock
    ```
    curl --location --request POST 'http://localhost:8008/service/add-coin' --form 'value="0.05"' --form 'quantity="15"'
    ```
* Collect earned coins
    ```
    curl --location --request GET 'localhost:8008/service/collect-coins' 
    ```