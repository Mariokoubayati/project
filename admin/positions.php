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

  <h1>Welcome to the positions System</h1>
  <a href="<?=INDEX_PAGE?>?mode=add">
    <button>Add Position</button>
  </a>

<?php

  $s_name = ( isset($_GET["s_name"]) && $_GET["s_name"] != "" ) ? $_GET['s_name'] : "";
  $s_department = ( isset($_GET["s_department"]) && $_GET["s_department"] != "" ) ? $_GET['s_department'] : "";

  $where = "";

  if( $s_name ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " name LIKE '%" . $s_name . "%'";
  }

  if( $s_department ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " department_id = " . ($s_department);
  }

  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  $departments = get_departments_details();

?>

<div class="container">
  <h3>Filter by name</h3>
  <form method="GET" action="<?= INDEX_PAGE ?>">

    <input type="text" name="s_name" placeholder="Enter name to filter" value="<?= $s_name ?>">
    <br><br>
    
    <select name="s_department">
      <option value="">All Departments</option>
      <?php foreach($departments as $dep){ ?>
        <option value="<?= $dep['id'] ?>" <?= ($s_department == $dep['id']) ? 'Selected' : '' ?>>
          <?= $dep['name']?>
        </option>
      <?php } ?>
    
    </select>
    <br><br>

    <button type="submit" name="submit">Search</button>
    <a href="<?= INDEX_PAGE ?>">Clear Search</a>
  </form>
</div>

  <div class="container">
    <h4>Positions List</h4>
  <br> 

<?php
try {

  $query = "SELECT * FROM position_management $where";
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
    <th style='border: 1px solid black;'>Name</th>
    <th style='border: 1px solid black;'>Dep</th>
    <th style='border: 1px solid black;'>Updated at</th>
    <th style='border: 1px solid black;'>Created at</th>
    <th style='border: 1px solid black;'>Actions</th>
  </tr>
<?php

    

  foreach ($result as $row) {
      $dep_id = $row["department_id"];
      $dep_name = get_department_details($dep_id)["name"];
  
      echo "<tr>";
      echo "<td style='border: 1px solid black; padding:10px;'>" . $row["id"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["name"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $dep_name . "</td>";
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

    $name = trim($_POST['name']);
    //print_r($name);
    $department_id = $_POST['department_id'];
    //print_r($department_id);
    $proc = true;

    if(!$name){
      $proc = false;
      $form_message .= "The name field is required!";
    }
    else{

      $stmt = $conn->prepare("SELECT * FROM `position_management` WHERE `name`=:name AND `id`<>:id");
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
        $stmt = $conn->prepare("UPDATE `position_management` SET name=:name, department_id=:department_id WHERE id=:id ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $form_message = "Position Updated!";

      } catch(PDOException $e) {
        $form_message = $e->getMessage();
      }
    }

  }
  else{

    $stmt = $conn->prepare("SELECT * FROM `position_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["name"];
    $department_id = $result[0]["department_id"];
  }
  
?>

<?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  $departments = get_departments_details();
  ?>

<form method="POST" action="<?= INDEX_PAGE ?>?mode=<?= $mode ?>&id=<?= $id ?>">
    <label for="name">Position Name:</label><br><br>
    <input type="text" id="name" name="name" value="<?= $name ?>" required>
    <br><br>

    <label for="department_id">Department:</label><br><br>
    <select name="department_id">
        <option value="">All Departments</option>
        <?php foreach($departments as $dep){ ?>
            <option value="<?= $dep['id'] ?>" <?= ($department_id == $dep['id']) ? 'selected' : '' ?>>
                <?= $dep['name']?>
            </option>
        <?php }?>
    </select>   
    <br><br>
    
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


      
      $name = trim($_POST['name']);
      $department_id = trim($_POST['department_id']);
      $proc = true;

      if(!$name){
        $proc = false;
        $form_message .= "The name field is required!";
      }else{

        $stmt = $conn->prepare("SELECT * FROM `position_management` WHERE `name`=:name");
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
          $stmt = $conn->prepare("INSERT INTO `position_management` (name, department_id) VALUES (:name, :department_id)");
          $stmt->bindParam(':name', $name);
          $stmt->bindParam(':department_id', $department_id);
          $stmt->execute();
          
          $form_message = "New Position Created!";
          $name = "";

        } catch(PDOException $e) {
          $form_message = $e->getMessage();
        }
      }

  }else{
    $name = "";
    $department_id = "";
  }
  ?>

  <?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  $departments = get_departments_details();
  ?>
  <form method="POST" action="<?=INDEX_PAGE?>?mode=<?=$mode?>">
      <label for="name">Position Name:</label><br><br>
      <input type="text" id="name" name="name" value="<?=$name?>" required>
      <br><br>
      <label for="name">Department Name:</label><br><br>
      <select name="department_id">
          <option value="">All Departments</option>
          <?php foreach($departments as $dep){ ?>
              <option value="<?= $dep['id'] ?>" <?= ($department_id == $dep['id']) ? 'selected' : '' ?>>
                  <?= $dep['name']?>
              </option>
          <?php }?>
      </select> 
      <button type="submit" name="submit">Add Department</button>
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
          $stmt = $conn->prepare("DELETE FROM `position_management` WHERE id=:id ");
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          
          header("Location: " . INDEX_PAGE . "?message=ENTRY_DELETED");
          exit;

        } catch(PDOException $e) {
          $form_message = $sql . "<br>" . $e->getMessage();
        }
      }

  }else{

    $stmt = $conn->prepare("SELECT * FROM `position_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["name"];
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
