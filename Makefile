#!/usr/bin/make -f

REDIS_NAME = some-redis

server:
	php -S localhost:8000 -t public/

redis:
	docker start $(REDIS_NAME) || \
	docker run --name $(REDIS_NAME) -p 6379:6379 -d redis

redis-cli:
	docker run -it --rm --link $(REDIS_NAME):red redis redis-cli -h red
