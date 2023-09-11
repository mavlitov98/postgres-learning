first-init:
	make up
	make composer-install
	make run

up:
	docker-compose up -d --build

composer-install:
	docker-compose exec learning-php composer install

run:
	docker-compose exec learning-php php ./src/Run.php
