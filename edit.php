<?php

require_once "config.php";

/**
* NOTE: PDO Object is store in $pdo
* NOTE: The variable "slug" with a value of desire post is being passed through the url.
*/
$page = [
  "button" => "Update Post"
];

/**
* 1. Add a "title" index to the $page array with the value of the page title
*/
$page['title'] = 'Edit Page';

if (isset($_POST['slug'])) {
  /**
  * 2. Create a Prepared Statement to UPDATE the post and SET the values to
  * named variables WHERE the slug EQUALS a name variable.
  * NOTE: post_date is automatically set by database, and does not need to
  * be included in the prepared statement.
  */
  $sql = 'UPDATE posts SET post_title = :post_title, post_slug = :post_slug, category_id = :category_id, post_excerpt = :post_excerpt, post_body = :post_body WHERE post_slug = :post_slug_url';

  /**
  * 3. Send the Prepared Statement to the Database
  * NOTE: Replace false with command to send the Prepared Statement.
  */
  $stmt = $pdo->prepare($sql);

  /**
  * 4. Create an array to hold the values of the named variable where the values
  * come from the submitted form.
  */
  $params = [
    'post_title' => $_POST['title'],
    'post_slug' => $_POST['slug'],
    'post_excerpt' => $_POST['excerpt'],
    'post_body' => $_POST['body'],
    'category_id' => $_POST['category'],
    'post_slug_url' => $_GET['slug']
  ];

  /**
  * 5. Execute the Prepared Statement with the array holding the value of the named variable
  */
  $stmt->execute($params);

  /**
  * Checking for SQL Errors return from the Database.
  */
  check_for_errors($stmt);

  /**
  * 6. Redirect page to post page for the current post
  */
  header('Location: '.SITE_URL.'/post/' . $_POST['slug']);
  exit();

}

/**
* 7. Check that variable "slug" has been passed throught the url.
* If not, redirect to the index page.
* NOTE: Replace true with the condition.
*/

if ($_GET['slug']) {
  /**
  * 8. Create a Prepared Statment to SELECT the post WHERE the slug EQUALS
  * an anonymous variable.
  */
  $sql = 'SELECT * FROM posts WHERE post_slug = :post_slug';

  /**
  * 9. Send the Prepared Statement to the Database
  * NOTE: Replace false with command to send the Prepared Statement.
  */
  $stmt = $pdo->prepare($sql);

  /**
  * 10. Create an array to hold the value of the anonymous variable
  */
  $params = [
    'post_slug' => $_GET['slug']
  ];


  /**
  * 11. Execute the Prepared Statement with the array holding the value of the anonymous variable
  */
  $stmt->execute($params);

  /**
  * Checking for SQL Errors return from the Database.
  */
  check_for_errors($stmt);

  /**
  * 12. Fetch the post from the excuted statement.
  * Return an associative array
  */
  $results = $stmt->fetch();

  /**
  * 13. Check if a post was returned.
  * NOTE: If no row was return the fetch method will return false
  * NOTE: Replace true with the condition for checking if post was returned
  */
  if ($results) {
    /**
    * 14. Create a SQL query to SELECT all the categories
    */
    $sql = 'SELECT * FROM categories';


    /**
    * 15. Send the SQL query to the Database using the PDO Object
    */
    $stmt = $pdo->query($sql);


    /**
    * 16. Fetch all the results returned from the Database.
    * Return an associative array
    */
    $categories = $stmt->fetchAll();


    /**
    * 17. Load the twig template for displaying the list of posts
    */
    $template = $twig->load('form.html.twig');

    /**
    * 18. Render the template with the post and categories returned from the database
    * and the $page array.
    */
    echo $template->render([
      'categories' => $categories,
      'results' => $results,
      'page' => $page
    ]);
  } else {
    /**
    * 19. Include error.php
    */
    include_once './error.php';

  }
} else {
  /**
  * 20. Redirect to the index page.
  */
  header('Location: '.SITE_URL.'/');
  exit();
}
