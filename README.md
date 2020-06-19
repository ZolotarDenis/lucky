## 1. Install:
```
1. composer install
2. php bin/console doctrine:database:create
3. php bin/console doctrine:schema:create
4. bin/console server:start
```
## 2. Cron:
```
 1 * * * * php /project_path/bin/console app:guest:logout >> /project_path/crons/logout.cron.txt

```

## 3. Routes:
```
http://127.0.0.1:8000 - index page
http://127.0.0.1:8000/statistics?date_went_in=19.06.2020T00:00:00&date_went_out=19.06.2020T02:00:00
```

