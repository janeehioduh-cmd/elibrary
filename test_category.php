<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/classes/Category.php";

$db = new Database();
$conn = $db->connect();

$category = new Category($conn);

$categories = $category->getAllCategories();

foreach ($categories as $row) {

    echo $row['id'] . " - " .
         htmlspecialchars($row['name']) . "<br>";

}

?>

