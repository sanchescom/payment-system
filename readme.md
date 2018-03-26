## Run
Clone the repo
```sh
git clone https://github.com/sanchescom/payment-system.git
cd asciishapes
```
Add host in hosts file
```sh
echo "127.0.0.1 payment-system.d" >> /etc/hosts
```
Install [Docker](https://docs.docker.com/) and [Docker Compose](https://docs.docker.com/compose/)

Build and run the Docker containers
```sh
docker-compose up -d && docker-compose up