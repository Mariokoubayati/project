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

  <h1>Welcome to the Vacancies System</h1>
  <a href="<?=INDEX_PAGE?>?mode=add">
    <button>Add Vacancie</button>
  </a>

<?php

  $s_name = ( isset($_GET["s_name"]) && $_GET["s_name"] != "" ) ? $_GET['s_name'] : "";
  $s_department = ( isset($_GET["s_department"]) && $_GET["s_department"] != "" ) ? $_GET['s_department'] : "";
  $s_position = ( isset($_GET["s_position"]) && $_GET["s_position"] != "" ) ? $_GET['s_position'] : "";
  $s_status = ( isset($_GET["s_status"]) && $_GET["s_status"] != "" ) ? $_GET['s_status'] : "";

  $where = "";

  if( $s_name ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " title LIKE '%" . $s_name . "%'";
  }

  if( $s_department ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " department_id = " . ($s_department);
  }

  if( $s_position ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " position_id = " . ($s_position);
  }
  if( $s_status ){
    $where .= ( !$where ) ? "WHERE " : " AND ";
    $where .= " status = '" . $s_status ."'";
  }

  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  $departments = get_departments_details();
  $positions = get_positions_details();
  $status = get_vacancy_status();

?>

<div class="container">
  <h3>Filter by name</h3>
  <form method="GET" action="<?= INDEX_PAGE ?>">

    <input type="text" name="s_name" placeholder="Enter name to filter" value="<?= $s_name ?>">
    <br><br>
    
    <!-- <select name="s_department">
      <option value="">All Departments</option>
      <?php foreach($departments as $dep){ ?>
        <option value="<?= $dep['id'] ?>" <?= ($s_department == $dep['id']) ? 'Selected' : '' ?>>
          <?= $dep['name']?>
        </option>
      <?php } ?> -->
    
    </select>
    <select name="s_position">
      <option value="">All Positions</option>
      <?php foreach($positions as $pos){ ?>
        <option value="<?= $pos['id'] ?>" <?= ($s_position == $pos['id']) ? 'Selected' : '' ?>>
          <?= $pos['name']?>
        </option>
      <?php } ?>
    
    </select>

    <select name="s_status">
        <option value="">All Status</option>
        <option value="Active" <?= ($s_status == 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= ($s_status == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
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

  $query = "SELECT * FROM vacancie_management $where";
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
    <th style='border: 1px solid black;'>Title</th>
    <th style='border: 1px solid black;'>Description</th>
    <th style='border: 1px solid black;'>Status</th>
    <th style='border: 1px solid black;'>Position</th>
    <th style='border: 1px solid black;'>Updated at</th>
    <th style='border: 1px solid black;'>Created at</th>
    <th style='border: 1px solid black;'>Actions</th>
  </tr>
<?php

    
$position_name = [];
foreach ($positions as $pos) {
    $position_name[$pos['id']] = $pos['name'];
}
  foreach ($result as $row) {
      //$dep_id = $row["department_id"];
      //$dep_name = get_department_details($dep_id)["name"];

      $pos_id = $row["position_id"];
      $pos_name = $position_name[$pos_id];

      $department = $conn->prepare("
        SELECT `name` FROM `department_management`
        WHERE `id` = (
          SELECT `department_id` FROM `position_management`
            WHERE `id` = " . $row["position_id"] . "
        );
    ");
    $department->execute();
    $department_name = $department->fetchAll(PDO::FETCH_ASSOC);
  
      echo "<tr>";
      echo "<td style='border: 1px solid black; padding:10px;'>" . $row["id"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["title"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["description"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $row["status"] . "</td>";
      echo "<td style='border: 1px solid black; padding:10px;' >" . $department_name[0]["name"] . " - " . $pos_name . "</td>";
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
    //$department_id = trim($_POST['department_id']);
    $position_id = $_POST['position_id'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $proc = true;

    if(!$name){
      $proc = false;
      $form_message .= "The name field is required!";
    }
    else{

      $stmt = $conn->prepare("SELECT * FROM `vacancie_management` WHERE `title`=:name AND `id`<>:id");
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
        $stmt = $conn->prepare("UPDATE `vacancie_management` SET title=:name, description=:description, status=:status,
        position_id=:position_id WHERE id=:id ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':position_id', $position_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $form_message = "Position Updated!";

      } catch(PDOException $e) {
        $form_message = $e->getMessage();
      }
    }

  }
  else{

    $stmt = $conn->prepare("SELECT * FROM `vacancie_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["title"];
    $position_id = $result[0]["position_id"];
    $description = $result[0]["description"];
    $status = $result[0]["status"];
  }
  
?>

<?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  $departments = get_departments_details();
  $positions = get_positions_details();
  ?>

<form method="POST" action="<?= INDEX_PAGE ?>?mode=<?= $mode ?>&id=<?= $id ?>">
<label for="name">Position Name:</label><br><br>
      <input type="text" id="name" name="name" value="<?=$name?>" required>
      <br><br>

      <label for="name">Description:</label><br><br>
      <textarea id="description" name="description" required><?= $description ?></textarea>
      <br><br>

      <label for="status">Status:</label><br><br>
      <input type="radio" name="status" value="Active" <?= ($status == 'Active') ? 'checked' : '' ?>> Active
      <input type="radio" name="status" value="Inactive" <?= ($status == 'Inactive') ? 'checked' : '' ?>> Inactive

      <br><br>


      <label for="name">Position Name:</label><br><br>
      <select name="position_id">
          <option value="">All Positions</option>
          <?php foreach($positions as $pos){ ?>
              <option value="<?= $pos['id'] ?>" <?= ($position_id == $pos['id']) ? 'selected' : '' ?>>
                  <?= $pos['name']?>
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
      //$department_id = trim($_POST['department_id']);
      $position_id = $_POST['position_id'];
      $description = $_POST['description'];
      $status = $_POST['status'];

      $proc = true;

      if(!$name){
        $proc = false;
        $form_message .= "The name field is required!";
      }else{

        $stmt = $conn->prepare("SELECT * FROM `vacancie_management` WHERE `title`=:name");
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
          $stmt = $conn->prepare("INSERT INTO `vacancie_management` (title, description, status,position_id) 
                                  VALUES (:name, :description, :status, :position_id)");
          $stmt->bindParam(':name', $name);
          $stmt->bindParam(':description', $description);
          $stmt->bindParam(':status', $status);
          $stmt->bindParam(':position_id', $position_id);
          $stmt->execute();
          
          $form_message = "New Position Created!";
          $name = "";

        } catch(PDOException $e) {
          $form_message = $e->getMessage();
        }
      }

  }else{
    $name = "";
    $description = "";
    $status="";
    $position_id="";
  }
  ?>

  <?php
  if($form_message){
    echo $form_message;
  }
  // $stmt = $conn->prepare("SELECT id, name FROM department_management");
  // $stmt->execute();
  //$departments = get_departments_details();
  $positions = get_positions_details();
  ?>

<?php
$departments_array = get_departments_details();
print_r($departments_array);
?>
<select class="" name="department">
  <option value="">Any</option>
  <?php foreach($departments_array as $department){
    ?>
    <option value="<?=$department["id"]?>">
      <?=$department["name"]?>
    </option>
    <?php
  }
  ?>
</select>

<select name="positions" id="">
  <option value="">any</option>
</select>


  <form method="POST" action="<?=INDEX_PAGE?>?mode=<?=$mode?>">
      <label for="name">Position Name:</label><br><br>
      <input type="text" id="name" name="name" value="<?=$name?>" required>
      <br><br>

      <label for="name">Description:</label><br><br>
      <textarea id="description" name="description" required><?= $description ?></textarea>
      <br><br>

      <label for="status">Status:</label><br><br>
      <input type="radio" name="status" value="Active" required> Active
      <input type="radio" name="status" value="Inactive" required> Inactive

      <br><br>


      <label for="name">Position Name:</label><br><br>
      <select name="position_id">
          <option value="">All Positions</option>
          <?php foreach($positions as $pos){ ?>
              <option value="<?= $pos['id'] ?>" <?= ($position_id == $pos['id']) ? 'selected' : '' ?>>
                  <?= $pos['name']?>
              </option>
          <?php }?>
      </select> 
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
          $stmt = $conn->prepare("DELETE FROM `vacancie_management` WHERE id=:id ");
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          
          header("Location: " . INDEX_PAGE . "?message=ENTRY_DELETED");
          exit;

        } catch(PDOException $e) {
          $form_message = $sql . "<br>" . $e->getMessage();
        }
      }

  }else{

    $stmt = $conn->prepare("SELECT * FROM `vacancie_management` WHERE `id`=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $name = $result[0]["title"];
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



// include("admin/templates/footer.php");
?>