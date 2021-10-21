# qmarkets

Steps to make it work

1. Setup
--------
https://symfony.com/doc/current/setup.html#setting-up-an-existing-symfony-project

git clone https://github.com/tonysurf55/qmarkets.git
cd qmarkets/
composer install
symfony server:start

2. configure the DATABASE_URL into the .env file
------------------------------------------------
Enter your database user, password and the name the database you want to create

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

example:
DATABASE_URL="mysql://user_search:12345@127.0.0.1:3306/user_search?serverVersion=5.7"

3. Create the database
---------------------
Run the SQL script

sql-script/version.1.0.0.sql

it will create the tables as well as 4 store procedures, 1 function and 1 view
Also it runs the main store procedure (call generate_keywords();) which will fill the keyword and score table. 

4. start the server
--------------------
symfony server:start

5. open the page
----------------
open the page given by the previous command
example:
http://localhost:8000/

