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

db-backup:
	docker-compose exec db mysqldump -u root -proot_password foto_app > backup_$(shell date +%Y%m%d_%H%M%S).sql

db-restore:
	@echo "Usage: make db-restore FILE=backup.sql"
	@if [ -z "$(FILE)" ]; then echo "Error: FILE parameter required"; exit 1; fi
	docker-compose exec -T db mysql -u root -proot_password foto_app < $(FILE)

logs:
	docker-compose logs -f