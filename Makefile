.PHONY: dev install test format analyse lint-html

dev:
	docker-compose up -d

stop:
	docker-compose down

install:
	docker-compose exec web composer install

test:
	docker-compose exec web composer test

format:
	docker-compose exec web composer format

analyse:
	docker-compose exec web composer analyse

lint-html:
	docker-compose exec web composer lint-html

shell:
	docker-compose exec web bash

db-shell:
	docker-compose exec db mysql -u app_user -p foto_app

logs:
	docker-compose logs -f