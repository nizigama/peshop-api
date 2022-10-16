## Pet Shop API

### Description
This project is intended to portray skills and knowledge of the laravel framework. Not all routes, tables and features are covered. Only few features were added just to display skills in structure, test, DRY and solid principles.

### First Setup
These steps should be run after cloning the repo

- Install docker and docker-compose, instructions for supported environments available [here](https://docs.docker.com/get-docker/)
- Copy .env.example to .env
    ```bash
    cp .env.example .env
    ```
- Build environment and install composer dependencies
    ```bash
    docker-compose up --build
    ```
- Generate application key
    ```bash
    docker container exec -it <app-container-name> php artisan key:generate
    ```
- Run migrations and seeds
    ```bash
    docker container exec -it <app-container-name> php artisan migrate --seed
    ```
- Generate the swagger documentation
    ```bash
    docker container exec -it <app-container-name> php artisan l5-swagger:generate
    ```

### Regular Setup
Run this command any other time you want to boot up the app after the first setup went well
```bash
docker-compose up
```

### Commands
Run the following commands whenever needed

- Running tests
    ```bash
    docker container exec -it <app-container-name> php artisan test
    ```
