<?php
include("templates/header.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mode = (isset($_GET["mode"]) && $_GET["mode"] != "") ? $_GET["mode"] : "";


if(!$mode){

  if( isset($_GET["message"]) && $_GET["message"] != "" ){
    echo generate_top_page_message($_GET["message"]);
  }

?>

  <h1>Welcome to the Users System</h1>
  <a href="<?=INDEX_PAGE?>?mode=add">
    <button>Add User</button>
  </a>

<?php

  $s_name = ( isset($_GET["s_name"]) && $_GET["s_name"] != "" ) ? $_GET['s_name'] : "";
  $s_email = ( isset($_GET["s_email"]) && $_GET["s_email"] != "" ) ? $_GET['s_email'] : "";
  $s_role = ( isset($_GET["s_role"]) && $_GET["s_role"] != "" ) ? $_GET['s_role'] : "";
//   $s_department = ( isset($_GET["s_department"]) && $_GET["s_department"] != "" ) ? $_GET['s_department'] : "";
//   $s_position = ( isset($_GET["s_position"]) && $_GET["s_position"] != "" ) ? $_GET['s_position'] : "";
//   $s_vacancie = ( isset($_GET["s_vacancie"]) && $_GET["s_vacancie"] != "" ) ? $_GET['s_vacancie'] : "";

  $where = "";

  if( $s_name ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " fullname LIKE '%" . $s_name . "%'";
  }
  if( $s_email ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " email LIKE '%" . $s_email . "%'";
  }
  if( $s_role ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " role = '" . $s_role ."'";
  }

//   if( $s_department ){
//     $where .= ( !$where ) ? "WHERE " : " AND ";
//     $where .= " department_id = " . ($s_department);
//   }

//   if( $s_position ){
//     $where .= ( !$where ) ? "WHERE " : " AND ";
//     $where .= " position_id = " . ($s_position);
//   }

//   if( $s_vacancie ){
//     $where .= ( !$where ) ? "WHERE " : " AND ";
//     $where .= " title LIKE '%" . $s_vacancie . "%'";
//   }

  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();


//   $departments = get_departments_details();
//   $positions = get_positions_details();
//   $vacancie = get_vacancies_details();
    $roles = get_user_roles();

?>

<div class="container">
  <h3>Filter by name</h3>
  <form method="GET" action="<?= INDEX_PAGE ?>">

    <input type="text" name="s_name" placeholder="Enter name to filter" value="<?= $s_name ?>">
    <br><br>
    <input type="text" name="s_email" placeholder="Enter email to filter" value="<?= $s_email ?>">
    <br><br>
    <select name="s_role">
      <option value="">All Roles</option>
      <option value="User" <?= ($s_role == 'User') ? 'selected' : '' ?>>User</option>
      <option value="Admin" <?= ($s_role == 'Admin') ? 'selected' : '' ?>>Admin</option>
    </select>

    <!-- <select name="s_position">
      <option value="">All Positions</option>
      <?php foreach($positions as $pos){ ?>
        <option value="<?= $pos['id'] ?>" <?= ($s_position == $pos['id']) ? 'Selected' : '' ?>>
          <?= $pos['name']?>
        </option>
      <?php } ?>
    
    </select> -->

    <button type="submit" name="submit">Search</button>
    <a href="<?= INDEX_PAGE ?>">Clear Search</a>
  </form>
</div>

  <div class="container">
    <h4>User List</h4>
  <br> 

<?php
try {

  $query = "SELECT * FROM user_management $where";
  $stmt = $conn->prepare($query);
  // // $stmt->bindParam(':name', $name);
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }

?>
  
<table class="listing-table">
  <tr>
    <th style='border: 1px solid black;'>Id</th>
    <th style='border: 1px solid black;'>Email</th>
    <th style='border: 1px solid black;'>password</th>
    <th style='border: 1px solid black;'>Fullname</th>
    <th style='border: 1px solid black;'>Role</th>
    <th style='border: 1px solid black;'>Updated at</th>
    <th style='border: 1px solid black;'>Created at</th>
    <th style='border: 1px solid black;'>Actions</th>
  </tr>
<?php

    
  foreach ($result as $row) {
      //$dep_id = $row["department_id"];
      //$dep_name = get_department_details($dep_id)["name"];

    //   $pos_id = $row["position_id"];
    //   $pos_name = $position_name[$pos_id];
  
      echo "<tr>";
      echo "<td style='border: 1px solid black; padding:10px;'>" . $row["id"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["email"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["password"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["fullname"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["role"] . "</td>";
      echo "<td style='border: 1px solid black;'>" . $row["updated_at"] . "</td>";
      echo "<td style='border: 1px solid black;padding:10px;'>" . $row["created_at"] . "</td>";
      echo "<td style='border: 1px solid black;padding:10px;'>";
      echo "<a href = '" . INDEX_PAGE . "?mode=edit&id=" . $row["id"] . "'>Edit</a>";
      echo "<a href = '" . INDEX_PAGE . "?mode=delete&id=" . $row["id"] . "'>Delete</a>";
      echo "</td>";
      echo "</tr>";
  }
  
  }



/* 
=============================
            EDIT
=============================
*/

elseif($mode == "edit"){
  
  $id = $_GET["id"];
  //$department_id="";
  if(!$id){
    header("Location: " . INDEX_PAGE . "?message=INVALID_ID");
  }


  $form_message = "";
  if( isset( $_POST["submit"] ) ){

    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $proc = true;

    if(!$name){
      $proc = false;
      $form_message .= "The name field is required!";
    }
    else{

      $stmt = $conn->prepare("SELECT * FROM `user_management` WHERE `fullname`=:name AND `id`<>:id");
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      if(count($result) > 0){          
        $proc = false;
        $form_message .= "The name already exists";
      }

    }

    if( $proc ){
      try{
        $stmt = $conn->prepare("UPDATE `user_management` SET fullname=:name, email=:email, role=:role,
        password=:password WHERE id=:id ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':password', md5($password));
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $form_message = "Position Updated!";

      } catch(PDOException $e) {
        $form_message = $e->getMessage();
      }
    }

  }
  else{

    $stmt = $conn->prepare("SELECT * FROM `user_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["fullname"];
    $email = $result[0]["email"];
    $password = $result[0]["password"];
    $role = $result[0]["role"];
  }
  
?>

<?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
// $departments = get_departments_details();
//   $positions = get_positions_details();
  ?>

    <form method="POST" action="<?= INDEX_PAGE ?>?mode=<?= $mode ?>&id=<?= $id ?>">
      <label for="name">Full Name:</label><br><br>
      <input type="text" id="name" name="fullname" value="<?=$name?>" required>
      <br><br>

      <label for="email">Email:</label><br><br>
      <input type="email" id="name" name="email" value="<?=$email?>" required>
      <br><br>

      <label for="password">Password:</label><br><br>
      <input type="hidden" id="name" name="password" value="<?=$password?>" required>
      <br><br>

      <label for="role">Role:</label><br><br>
      <input type="radio" name="role" value="User" <?= ($role == 'User') ? 'checked' : '' ?>> User
      <input type="radio" name="role" value="Admin" <?= ($role == 'Admin') ? 'checked' : '' ?>> Admin
    
    <button type="submit" name="submit">Edit Position</button>
</form>
<?php
}

/* 
=============================
            ADD
=============================
*/

elseif($mode == "add"){
  // print_r($_POST);

  $form_message = "";
  if( isset( $_POST["submit"] ) ){


      
      $name = trim($_POST['fullname']);
      $email = trim($_POST['email']);
      $password = trim($_POST['password']);
      $role = trim($_POST['role']);

      $proc = true;

      if(!$name){
        $proc = false;
        $form_message .= "The name field is required!";
      }else{

        $stmt = $conn->prepare("SELECT * FROM `user_management` WHERE `fullname`=:name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($result) > 0){          
          $proc = false;
          $form_message .= "The name already exists";
        }
      }

      if( $proc ){
        try{
          $stmt = $conn->prepare("INSERT INTO `user_management` (email, password, fullname,role) 
                                  VALUES (:email, :password, :fullname, :role)");
          $stmt->bindParam(':fullname', $name);
          $stmt->bindParam(':email', $email);
          $stmt->bindParam(':password', md5($password));
          $stmt->bindParam(':role', $role);
          $stmt->execute();
          
          $form_message = "New Position Created!";
          $name = "";

        } catch(PDOException $e) {
          $form_message = $e->getMessage();
        }
      }

  }else{
    $name = "";
    $email = "";
    $password="";
    $role="";
  }
  ?>

  <?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  //$departments = get_departments_details();
  //$positions = get_positions_details();
  ?>
  <form method="POST" action="<?=INDEX_PAGE?>?mode=<?=$mode?>">
      <label for="name">Full Name:</label><br><br>
      <input type="text" id="name" name="fullname" value="<?=$name?>" required>
      <br><br>

      <label for="email">Email:</label><br><br>
      <input type="email" id="name" name="email" value="<?=$email?>" required>
      <br><br>

      <label for="password">Password:</label><br><br>
      <input type="password" id="name" name="password" value="<?=$password?>" required>
      <br><br>

      <label for="role">Role:</label><br><br>
      <input type="radio" name="role" value="User" <?= ($role == 'User') ? 'checked' : '' ?>> User
      <input type="radio" name="role" value="Admin" <?= ($role == 'Admin') ? 'checked' : '' ?>> Admin

      
      <button type="submit" name="submit">Add Vacancie</button>
  </form>
<?php
}
/* 
=============================
            DELETE
=============================
*/

elseif($mode == "delete"){
  $id = $_GET["id"];
  if(!$id){
    header("Location: " . INDEX_PAGE . "?message=INVALID_ID");
    exit;
  }


  $form_message = "";
  if( isset( $_POST["submit"] ) ){


      $proc = true;

      if( $proc ){
        try{
          $stmt = $conn->prepare("DELETE FROM `user_management` WHERE id=:id ");
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          
          header("Location: " . INDEX_PAGE . "?message=ENTRY_DELETED");
          exit;

        } catch(PDOException $e) {
          $form_message = $sql . "<br>" . $e->getMessage();
        }
      }

  }else{

    $stmt = $conn->prepare("SELECT * FROM `user_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["fullname"];
  }
  ?>

  <?php
  if($form_message){
    echo $form_message;
  }
  ?>
  <form method="POST" action="<?=INDEX_PAGE?>?mode=<?=$mode?>&id=<?=$id?>">
      Are you sure you want to delete "<?=$name?>" ?
      <button type="submit" name="submit">Delete</button>
      <a href="<?=INDEX_PAGE?>">Cancel action</a>
  </form>
<?php
}
