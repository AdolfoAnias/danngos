# Based on Laravel v12

## How to use:
- Clone the repository https://github.com/AdolfoAnias/danngos.git in Laragon or Wamp
- Copy `.env.example` to `.env` and configure the database variables with the correct values ​​as established in your mysql: 
- DB_DATABASE=danngos
- DB_USERNAME=root
- DB_PASSWORD=
- Change `APP_NAME` in .env with the following:
  APP_NAME=Danngos
- To run de app: 
- `composer run dev`
- Create the database exposed in .env file in your mysql manager
- Run `php artisan migrate:fresh --seed`
- Open the browser at http://localhost/danngos/public

- To see the API documentation go to the following URL
- http://localhost/danngos/public/docs 
