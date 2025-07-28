<?php require_once ("includes/global.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php
?>
    <!-- Menu Start -->
    <nav>
        <ul class="nav-bar">
            <li class="<?php if( INDEX_PAGE == "login.php") { echo "active"; } ?>" ><a href="login.php">Login</a></li>
            <li class="<?php if( INDEX_PAGE == "logout.php") { echo "active"; } ?>" ><a href="logout.php">Logout</a></li>
            <li class="<?php if( INDEX_PAGE == "index.php") { echo "active"; } ?>" ><a href="index.php">Home</a></li>
            <li class="<?php if( INDEX_PAGE == "departments.php") { echo "active"; } ?>" ><a href="departments.php">Department</a></li>
            <li class="<?php if( INDEX_PAGE == "positions.php") { echo "active"; } ?>" ><a href="positions.php">Position</a></li>
            <li class="<?php if( INDEX_PAGE == "vacancies.php") { echo "active"; } ?>" ><a href="vacancies.php">Vacancies</a></li>
            <li class="<?php if( INDEX_PAGE == "users.php") { echo "active"; } ?>" ><a href="users.php">User</a></li>
        </ul>
    </nav>
    <!-- Menu End -->
