up: docker-build

#make init - начальная команда
init: docker-clear docker-build api-composer perms api-env frontend-env frontend-install frontend-build

#После запустить в другой вкладке: make api-migration

docker-clear:
	sudo docker-compose down --remove-orphans
	sudo rm -rf api/var/docker

docker-build:
	sudo docker-compose up --build -d

docker-ps:
	sudo docker-compose ps

docker-down:
	sudo docker-compose down

perms:
	sudo chmod 777 api -R
	sudo chmod 777 frontend -R


api-env:
	sudo docker-compose exec api-php-cli rm -f .env
	sudo docker-compose exec api-php-cli ln -sr .env.example .env


api-composer:
	sudo docker-compose exec api-php-cli composer install

api-migration:
	sudo docker-compose exec api-php-cli php artisan migrate


frontend-env:
	sudo docker-compose exec frontend-nodejs rm -f .env
	sudo docker-compose exec frontend-nodejs ln -sr .env.example .env

frontend-install:
	sudo docker-compose exec frontend-nodejs npm install

frontend-build:
	sudo docker-compose exec frontend-nodejs npm run watch


#up-frontend:
#	cd frontend; echo "I'm in frontend"; \
	npm install; \
	npm run serve; \
	cd ..; \


#up-api:
#	cd api; echo "I'm in api"; \
	composer update; \
	php artisan serve; \

