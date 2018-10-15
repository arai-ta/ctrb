#!/usr/bin/make -f


server:
	php -S localhost:8000 public/index.php

redis:
	docker run --name some-redis -p 6379:6379 -d redis

redis-cli:
	docker run -it --rm --link some-redis:red redis redis-cli -h red
