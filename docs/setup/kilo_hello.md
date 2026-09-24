готов
1) Учебный сервис предварительной оценки заявки на заём под ПТС: принимает заявку, считает LTV и возвращает `approve` / `review` / `reject`.
2) Makefile: `make up`, `make down`, `make ps`, `make logs`, `make install`, `make test`, `make lint`, `make seed`, `make help`; docker-compose.yml: backend запускается `php -S 0.0.0.0:8080 -t backend/public backend/public/router.php`, база MySQL 8.
3) Решение `approve` / `review` / `reject` считается в папке `backend/src/Domain`.
модель: training-2026-09-minimax-m3
