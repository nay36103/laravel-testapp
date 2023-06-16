# Project Setup Instructions

## local ImageDocker

To use a local Docker image, follow these steps:

1. Clone the repository:
   ```shell
   git clone https://github.com/nay36103/laravel-testapp.git

2. Change to the project directory:
   ```shell
   cd laravel-testapp

3. Copy the example environment file:
   ```shell
   cp .env.example .env

4. Build the Docker image:
   ```shell
   docker build -t laravel-app .

5. Run the Docker container:
   ```shell
   docker run -p 8000:80 laravel-app

## DockerHub

To use the DockerHub image, follow these steps:

1. Pull the DockerHub image:

   ```shell
   docker pull yamsombut/laravel-app:0.1

2. Build the Docker image:

   ```shell
   docker build -t laravel-app:0.1 .

3. Run Docker image:

   ```shell
   docker run -p 8000:80 laravel-app:0.1

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
