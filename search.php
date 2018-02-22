<?php

require_once "config.php";

/**
* NOTE: PDO Object is store in $pdo
*/
$search = $_POST['search'];

$page['title'] = 'Blog Search Result:';

if ($search) {

    $sql = 'SELECT * FROM posts WHERE post_title LIKE ? OR post_excerpt LIKE ? OR post_body LIKE ?';

    $stmt = $pdo->prepare($sql);

    $params = [
        "%$search%",
        "%$search%",
        "%$search%"
    ];

    $stmt->execute(
        $params
    );

    check_for_errors($stmt);

    $results = [];

    while ($row = $stmt->fetch()) {
        $results[] = [
            'slug' => $row['post_slug'],
            'title' => $row['post_title'],
            'date' => $row['post_date'],
            'excerpt' => $row['post_excerpt']
        ];
    }

    $message = '';

    if (!$results) {
        $message = 'No result found.';
    }

    $template = $twig->load('list.html.twig');

    echo $template->render([
        'page' => $page,
        'results' => $results,
        'message' => $message
    ]);

} else {
    header('Location: '.SITE_URL.'/');
    exit();
}