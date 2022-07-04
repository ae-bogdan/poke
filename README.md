**Set up docker**

`docker-compose build`

`docker-compose up -d`

**Set up the project dependencies**

`docker exec -it app sh -c 'composer install'`

**Set up the database:**

`docker exec -it app sh -c 'php bin/console --no-interaction doctrine:database:create'`

`docker exec -it app sh -c 'php bin/console --no-interaction doctrine:migrations:migrate'`

`docker exec -it app sh -c 'php bin/console --no-interaction doctrine:fixtures:load'`

**Access site:**

`http://localhost:8080`
