# HostIran Finance service

This application developed and customized by [Laravel Framework](https://laravel.com/)  version 10.*.

## Setup
1. Copy root `.env.example` into `.env` and change the env variables
   1. `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   2. Check for `WWW_DATA_USER_ID` to match your host's ID
2. RUN `docker-compose up -d` to build and run containers
3. RUN ` docker-compose exec finance_service composer install`
4. RUN ` docker-compose exec finance_service php artisan migrate --seed` - check DatabaseSeeder.php before seeding
5. OPEN `http://localhost:6090` to check if everything is working
6. Make sure config values are set in MainApp refer to `Commands` section of this readme file


## Project structure
1. Controller `app\Http\Controllers`
   - A `Controller` is responsible for taking validated data from RequestForms and passing it down to an `Action` and then take its result and using JsonResponse to return it
2. Action `app\Actions`
    - An `Action` is responsible for high level business logic , this class usually consumes one or more `Service` in a linear order to complete its "task/action"
    - An `Action` CAN consume another `Action` if needed
      - for example, `Wallet\DeductCreditAction` can consume `Wallet\StoreCreditTransactionAction`
3. Service `app\Services`
   - A `Service` is responsible for low level logic which mostly involves with read/writing data via one or more `Repository`
   - A `Service` cannot consume (dependency inject) another `Service`, this should be done via an `Action` on a higher level, for example when making an "Invoice" which has two "Items" one `service` is responsible for making an invoice first and then another `service` is responsible for attaching those items to the Invoice created on the first step, `Action` should handle this procedure, low level logic should not be responsible for this data flow
4. Repository `app\Repositories`
    - A `Repository` is responsible to interact with DataStore like mySql or mongoDB
    - A `Repository` is the lowest level of logic and should not consume any `Service` `Action` or other `Repositories`
- Class Hierarchy Example:
  - POST `/api/invoice`
    - StoreInvoiceController
      - $data = StoreInvoiceRequest 
      - $invoice = StoreInvoiceAction($data)
        - $invoice = StoreInvoiceService($data)
          - InvoiceRepository
        - StoreItemService($invoice)
          - ItemRepository
        - return $invoice
      - return InvoiceResource($invoice)


## Config
    Required config values in main app:
    - REFER TO MainAppConfig.php and DatabaseSeeder.php
## Commands/Cron
    - TODO complete later
## Jobs
| Job                         | Queue                   | Worker    |
|-----------------------------|-------------------------|-----------|
| AssignInvoiceNumberJob      | process_invoices_number | WORKER_2  |
| GenerateInvoiceNumberJob    | process_invoices_number | WORKER_2  |
| OverrideInvoiceReminderJob  | process_invoice_reminder| WORKER_2  |
| SendInvoiceReminderJob      | process_invoice_reminder| WORKER_2  |
| UpdateSystemLog             | update_system_log       | WORKER_1  |

### Author
* **Esmaeel Cheshmeh Khavar** ([Gmail](mailto:e.cheshmehkhavar@gmail.com))
* **Mahdi Rahmani** ([Gmail](mailto:rahmanimahdi16@gmail.com))
