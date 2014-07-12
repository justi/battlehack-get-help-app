all:
	composer.phar install
	bin/console doctrine:schema:update --force

prod: all
	bin/console --env=prod assets:install
	bin/console --env=prod assetic:dump

test:
	phpunit
