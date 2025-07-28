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

  <h1>Welcome to the Department System</h1>
  <a href="<?=INDEX_PAGE?>?mode=add">
    <button>Add Department</button>
  </a>

  <?php
  $s_name = ( isset($_GET["s_name"]) && $_GET["s_name"] != "" ) ? $_GET['s_name'] : "";

  $where = "";

  if( $s_name ){
    $where .= ( !$where ) ? "WHERE " : " AND";
    $where .= " name LIKE '%" . $s_name . "%'";
  }

  ?>

  <div class="container">
    <h3>Filter by name</h3>
    <form method="GET" action="<?=INDEX_PAGE?>">
      <input type="text" name="s_name" placeholder="Enter name to filter" value="<?=$s_name?>" >
      <button type="submit" name="submitt">Search</button>
      <a href="<?=INDEX_PAGE?>">Clear Search</a>
    </form>
  </div>

  <div class="container">
    <h4>Department List</h4>
  <br>

<?php
try {

  $query = "SELECT * FROM department_management $where";
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
          <th style='border: 1px solid black;'>Updated at</th>
          <th style='border: 1px solid black;'>Created at</th>
          <th style='border: 1px solid black;'>Actions</th>
          </tr>
<?php

    

      foreach ($result as $row) {
          
        echo "<tr'>";
        echo "<td style='border: 1px solid black; padding:10px;'>" . $row["id"] . "</td>";
        echo "<td style='border: 1px solid black; padding:10px;' >" . $row["name"] . "</td>";
        echo "<td style='border: 1px solid black;'>" . $row["updated_at"] . "</td>";
        echo "<td style='border: 1px solid black;padding:10px;'>" . $row["created_at"] . "</td>";
        echo "<td style='border: 1px solid black;padding:10px;'>";
        echo "<a href = '" . INDEX_PAGE . "?mode=edit&id=" . $row["id"] . "'>Edit</a>";
        echo "<a href = '" . INDEX_PAGE . "?mode=delete&id=" . $row["id"] . "'>Delete</a>";
        echo "</td>";
        echo "</tr>";
      }
      
    


}elseif($mode == "edit"){
  
  $id = $_GET["id"];
  if(!$id){
    header("Location: " . INDEX_PAGE . "?message=INVALID_ID");
  }


  $form_message = "";
  if( isset( $_POST["submit"] ) ){

    $name = trim($_POST['name']);
    $proc = true;

    if(!$name){
      $proc = false;
      $form_message .= "The name field is required!";
    }
    else{

      $stmt = $conn->prepare("SELECT * FROM `department_management` WHERE `name`=:name AND `id`<>:id");
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
        $stmt = $conn->prepare("UPDATE `department_management` SET name=:name WHERE id=:id ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $form_message = "Department Updated!";

      } catch(PDOException $e) {
        $form_message =$e->getMessage();
      }
    }

  }
  else{

    $stmt = $conn->prepare("SELECT * FROM `department_management` WHERE `id`=:id");
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
      <label for="name">Department Name:</label><br><br>
      <input type="text" id="name" name="name" value="<?=$name?>" required>
      <button type="submit" name="submit">Edit Department</button>
  </form>
<?php
}

/*
==========================
          ADD
==========================
*/

elseif($mode == "add"){
  // print_r($_POST);

  $form_message = "";
  if( isset( $_POST["submit"] ) ){


      
      $name = trim($_POST['name']);
      $proc = true;

      if(!$name){
        $proc = false;
        $form_message .= "The name field is required!";
      }else{

        $stmt = $conn->prepare("SELECT * FROM `department_management` WHERE `name`=:name");
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
          $stmt = $conn->prepare("INSERT INTO `department_management` (name) VALUES (:name)");
          $stmt->bindParam(':name', $name);
          $stmt->execute();
          
          $form_message = "New Department Created!";
          $name = "";

        } catch(PDOException $e) {
          $form_message = $e->getMessage();
        }
      }

  }else{
    $name = "";
  }
  ?>

  <?php
  if($form_message){
    echo $form_message;
  }
  ?>
  <form method="POST" action="<?=INDEX_PAGE?>?mode=<?=$mode?>">
      <label for="name">Department Name:</label><br><br>
      <input type="text" id="name" name="name" value="<?=$name?>" required>
      <button type="submit" name="submit">Add Department</button>
  </form>
<?php
}

/*
==========================
          DELETE
==========================
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
          $stmt = $conn->prepare("DELETE FROM `department_management` WHERE id=:id ");
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          
          header("Location: " . INDEX_PAGE . "?message=ENTRY_DELETED");
          exit;

        } catch(PDOException $e) {
          $form_message = $e->getMessage();
        }
      }

  }else{

    $stmt = $conn->prepare("SELECT * FROM `department_management` WHERE `id`=:id");
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
