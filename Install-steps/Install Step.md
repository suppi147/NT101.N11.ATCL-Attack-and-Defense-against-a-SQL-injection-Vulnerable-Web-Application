## Requirement
- Install apache2
- Install postgreSQL and configure admin credential password and authentication method.
    - Change admin password
    - ![](https://i.imgur.com/n8VF3t3.png)
    - Change authentication method to md5
    - ![](https://i.imgur.com/cexfg2q.png)
- Install PHP

## How to run step by step
- Step 1: Git clone the project
- Step 2: Copy /Attack-and-Defense-against-a-SQL-injection-Vulnerable-Web-Application/ATM-Project-Vulnerable/Source-Code folder to /var/www/html
- Step 3: self-deploy database
    - Create your own a database with a table with products with columns like id, price and description, data input is up to you
- Step 4: Change database connection in getProducts.php in the way that is appropriate with your configuration
- Step 5: start postgres and apache2 service
- Step 6: type localhost/location-of-Source-code into search bar to load the web service.