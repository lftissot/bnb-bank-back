#### 🏦 About the project
BNBBank is a simplified banking application created for TurnoverBNB`s coding challenge.

![Transactions screen](https://github.com/lftissot/bnb-bank-back/blob/master/docs/print.png)

#### 📝 Key features
- Two user profiles:
	 - Customers, that can be freely signup
	 - Admins, pre-registered accounts
- Customer features:
	 - View balance and transactions, filtering by date
	 - List and create expenses (composed of amont, date and description)
	 - List incomes, by current status (pending, approved and denied)
	 - Submit checks (composed of amount, date and image)
- Admin features:
	 - List all pending checks
	 - View check details
	 - Approve or deny checks

#### ⚙️ Stack
- Laravel 8
	- spatie/permission
	- tymon/jwt-auth
- VueJS 3
	- Axios
	- fontawesome
	 - element-plus
	 - vue-router
	 - vue3-cookies
	 - vuex
- MySQL
- Google Compute Engine
- NGINX

#### 🏁 Running it
###### Project setup
```
composer install
```

###### Prepare for development
```
php artisan migrate:fresh --seed
php artisan key:generate
php artisan jwt:secret
php artisan storage:link
```

###### Project serve
```
php artisan serve
```

###### Running feature tests
```
 ./vendor/bin/phpunit
```

#### 🔑 Admin account
- User: admin
- Password: 1234
