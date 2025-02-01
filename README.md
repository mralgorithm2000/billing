# REST API биллинговой системы

## Содержание

1. [Обзор](#Обзор)
2. [Возможности](#Возможности)
3. [Требования](#Требования)
4. [Установка и настройка](#Установка-и-настройка)
5. [Документация API](#Документация-API)
6. [Аутентификация](#Аутентификация)
8. [Тестирование](#Тестирование)
10. [Лицензия](#Лицензия)
---

## Обзор

Это API биллинговой системы, разработанное с использованием Laravel 11 и MySQL. Оно позволяет пользователям регистрироваться, проверять баланс, выполнять операции по пополнению и снятию средств, а также просматривать историю транзакций. API построено по RESTful-принципам, содержит Swagger-документацию и реализует паттерн репозитория для чистого и поддерживаемого кода. Транзакции обрабатываются безопасно, исключая отрицательный баланс.

Система использует Redis для эффективного управления очередями транзакций, что повышает производительность и надежность. Кроме того, в проекте имеются автоматизированные тесты, обеспечивающие стабильность и корректность работы приложения.

---

## Возможности

- **Аутентификация пользователей** – Регистрация и безопасный вход в систему
  
- **Управление балансом** – Просмотр текущего баланса

- **Транзакции** – Пополнение и снятие средств (нельзя уйти в минус)

- **История транзакций**

- **Поддержка очередей** – Использует Redis для асинхронной обработки транзакций

- **Документация API (Swagger)** – Хорошо задокументированный API для удобной интеграции

- **Паттерн репозитория** – Обеспечивает чистую, модульную и поддерживаемую архитектуру

- **Автоматизированные тесты** – Включены тесты для основных функций

---

## Требования

- Docker (с Docker Compose), установленный на вашей системе.

Это всё! Все остальные зависимости управляются внутри контейнеров Docker, настроенных в проекте.

## Установка и настройка


1. **Клонирование репозитория**:
   ```bash
   git clone https://github.com/mralgorithm2000/billing.git
   cd billing
   ```

2. **Соберите Docker-контейнеры**:
    Выполните следующую команду для сборки Docker-контейнеров с использованием Dockerfile и конфигурации docker-compose.yml:
    ```bash
    docker-compose build
    ```

3. **Запустите Docker-контейнеры**:
    После завершения процесса сборки, запустите контейнеры с помощью следующей команды:
    ```bash
    docker-compose up -d
    ```
    Это запустит контейнеры приложения и базы данных в фоновом режиме.

4. **Запустите миграции и заполнив базу данных**
    Выполните следующую команду для настройки базы данных и заполнения её тестовыми данными:
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

5. **Проверьте настройку**:
   Теперь вы можете запустить тесты, чтобы убедиться, что всё работает корректно:
    ```bash
   docker-compose exec app php artisan test
    ```

6. **Запуск очередей Redis**:
   Чтобы начать обработку очередей с использованием Redis, выполните следующую команду:
    ```bash
   docker-compose exec app php artisan queue:work
    ```

## Документация API

Документация API доступна по адресу http://localhost:8000/api/documentation. 
Посетите этот URL после запуска сервера, чтобы изучить методы API, параметры и ожидаемые ответы.

## Аутентификация

Некоторые маршруты в API, такие как выполнение транзакций, получение истории транзакций и получение информации о пользователе, требуют аутентификации. Это может быть выполнено с использованием Bearer Token. Bearer Token должен быть включён в заголовок Authorization каждого запроса для доступа к этим защищённым маршрутам.


## Тестирование

Убедитесь, что все функции работают корректно, запустив:

```bash
docker-compose exec app php artisan test
```

## Лицензия

Этот проект является открытым программным обеспечением и доступен в соответствии с MIT License.