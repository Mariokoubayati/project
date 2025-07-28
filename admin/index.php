<?php

// session_start();


include("templates/header.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<style>
  .container {
	max-width: 1140px;
}
.row {
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	margin-right: -15px;
	margin-left: -15px;
}
.col-3 {
	-webkit-box-flex: 0;
	-ms-flex: 0 0 25%;
	flex: 0 0 25%;
	max-width: 25%;
}
</style>

<div class="container">
  <div class="row">
      <div class="col-3"><a href="admin/users.php">Users</a></div>
      <div class="col-3"><a href="admin/departments.php">Departments</a></div>
      <div class="col-3"><a href="admin/positions.php">Position</a></div>
      <div class="col-3"><a href="admin/vacancies.php">Vacancies</a></div>
  </div>
</div>

<?


include("admin/templates/footer.php");
?>