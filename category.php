<?php

require_once "config.php";

/**
* NOTE: PDO Object is store in $pdo
* NOTE: The variable "slug" with a value of desire category is being passed
* through the url.
*/
$slug = $_GET['slug'];

$page = [];

/**
* 1. Check that variable "slug" has been passed throught the url.
* If not, redirect to the index page.
* NOTE: Replace true with the condition.
*/
if ($slug) {
  /**
  * 2. Create a Prepared Statment to SELECT the category WHERE the slug EQUALS
  * an anonymous variable.
  */
  $sql = 'SELECT * FROM categories WHERE category_slug = :category_slug';

  /**
  * 3. Send the Prepared Statement to the Database
  * NOTE: Replace false with command to send the Prepared Statement.
  */
  $stmt = $pdo->prepare($sql);

  /**
  * 4. Create an array to hold the value of the anonymous variable
  */
  $params = [
      'category_slug' => $slug
  ];

  /**
  * 5. Execute the Prepared Statement with the array holding the value of the anonymous variable
  */
  $stmt->execute(
      $params
  );

  /**
  * Checking for SQL Errors return from the Database.
  */
  check_for_errors($stmt);

  /**
  * 6. Fetch the category from the excuted statement.
  * Return an associative array
  */
  $categories = $stmt->fetch();

  /**
  * 7. Check if a category was returned.
  * NOTE: If no row was return the fetch method will return false
  * NOTE: Replace true with the condition to check if a category was returned
  */
  if($categories) {
    /**
    * 8. Add a "title" index to the $page array with the value of the category name
    */
    $page['title'] = $categories['category_name'];

    /**
    * 9. Create a Prepared Statment to SELECT the posts WHERE the post category id
    * EQUALS category_id from $category.
    */
    $sql = 'SELECT * FROM posts WHERE category_id = ' . $categories['category_id'];


    /**
    * 10. Send the SQL query to the Database using the PDO Object
    */
    $stmt = $pdo->query($sql);

    /**
    * 11. Fetch all the results returned from the Database.
    * Return an associative array
    */
    $results = [];
    while($row = $stmt->fetch()) {
      $results[] = [
          'slug' => $row['post_slug'],
          'title' => $row['post_title'],
          'date' => $row['post_date'],
          'excerpt' => $row['post_excerpt']
      ];
    }

    /**
    * 12. Load the twig template for displaying the list of posts
    */
    $template = $twig->load('list.html.twig');

    /**
    * 13. Render the template with the posts returned from the database and the $page array.
    */
    echo $template->render([
      'results' => $results,
      'page' => $page
    ]);
  } else {
    /**
    * 14. Include error.php
    */
    include_once './error.php';

  }

} else {
  /**
  * 15. Redirect to the index page.
  */
  header('Location: /');
  exit();
}
