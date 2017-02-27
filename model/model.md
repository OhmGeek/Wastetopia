# Models

All pages on the site will have a Model, View and Control

The model class will encapsulate all database and SQL interaction. Model classes should contain functions to get data from the database using db.php

A model class is only excuted by its corresponding control class, it encapsulates the data for just that page

## db.php
This allows access to the database.

In order to get the database object, use:

```php
$db = DB::getDB();
```

From here, you can query the database using PDO as usual:

```php
        $statement = $db->prepare("SELECT * 
                             FROM Item 
                             WHERE Name 
                             LIKE :searchTerm");

        $statement->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $statement->execute();

        //return the retrieved array as an associative array
        return $statement->fetchAll(PDO::FETCH_ASSOC);
```

The above method is good practice, and should be followed at all times.

The method below is one that SHOULD NEVER be used, as it is possible to SQL inject:

```php
        $statement = $db->prepare("SELECT * 
                             FROM Item 
                             WHERE Name 
                             LIKE $searchTerm");

        $statement->execute();
```

