# Work Order
Work Orders Application

# Kebutuhan
1. Postgresql (Database)
2. Php (min. 8.0)

# Instalation
1. Buat database di postgresql
2. copy .env.sample menjadi .env
3. buka .env ganti database dengan database yang telah dibuat di postgresql
4. Jalankan ```php artisan migrate:fresh --seed```
5. jalankan ```php artisan serve``` , buka browser 127.0.0.1:8000

# Database sample kebutuhan work order
```
Table = work_orders, berikut column yang digenerate
id
work_order_number
product_name
quantity
deadline
status
assigned_operator_id
created_by
updated_by
created_at
updated_at
Table = work_orders_updates, berikut column yang digenerate
id
work_order_id
status
quantity_updated
notes
created_by
updated_by
created_at
updated_at
assigned_operator_id
