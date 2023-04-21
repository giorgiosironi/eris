.PHONY: run-php-7.4
run-php-7.4:
	docker-compose run php-7.4 sh

.PHONY: run-php-8.1
run-php-8.1:
	docker-compose run php-8.1 sh

.PHONY: run-php-8.2
run-php-8.2:
	docker-compose run php-8.2 sh
