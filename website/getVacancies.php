<?php 

include ("../admin/includes/global.php");?>
  
<?php


$stmt = $conn->prepare("SELECT * FROM vacancie_management");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$departments = get_departments_details();
$positions = get_positions_details();
$status = get_vacancy_status();
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
  
    ?>
    <div class="col-md-6">
    <div class="card h-100 shadow-sm">
    <div class="card-body">
        <h5 class="card-title fw-semibold"> <?php echo $row["title"] ?> </h5>
        <p class="card-text"><?php echo "description: ".$row["description"] ?></p>
        <p class="card-text"><?php echo "status: ".$row["status"] ?></p>
        <p class="card-text"><?php echo "department: ".$department_name[0]["name"]?></p>
        <p class="card-text"><?php echo "positon: ".$pos_name ?></p>
        <a href="#" class="btn btn-white btn-outline-dark ">Apply Now</a>
    </div>
    </div>
</div>
    <?php

  }
  
  


