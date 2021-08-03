# vending-machine

## Installation
```
git clone https://github.com/jartau/vending-machine.git
cd vending-machine
cp .env.example .env
./docker/artisan key:generate
docker-compose build
docker-compose up -d
docker-compose exec app composer install
./docker/artisan migrate
```
bash command: docker exec -it laravelapp-app bash

URL: http://localhost:8008/

## Commands
```
# open bash
bash command: docker exec -it laravelapp-app bash

# run tests
./docker/artisan test
```