<?php
/*==================
* GENERAL
*===================*/
function generate_top_page_message($message_code){
    if( !$message_code ){
        return false;
    }

    $message = "";
    $type = "";

    switch($message_code){
        case "INVALID_ID":{
            $message = "The ID is missing";
            $type = "danger";
        }break;

        case "ENTRY_DELETED":{
            $message = "The selected record is deleted successfully";
            $type = "success";
        }break;

        default: {
        }break;
    }

    if($message){
        return '<div class="alert alert-' . $type . '">' . $message . '</div>';
    }
    return false;
}

/*==================
* Departments
*===================*/
function get_departments_details(){ 
    global $conn;
    $stmt = $conn->prepare("SELECT id, name FROM department_management");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_department_details($id){
    global $conn;
    $stmt = $conn->prepare("SELECT id, name FROM department_management WHERE id=:id");
    $stmt->bindParam(":id",$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
}


/*==================
* Positions
*===================*/
function get_positions_details(){ 
    global $conn;
    $stmt = $conn->prepare("SELECT id, name FROM position_management");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/*==================
* Vacancies
*===================*/
function get_vacancies_details(){ 
    global $conn;
    $stmt = $conn->prepare("SELECT id, title FROM vacancie_management");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_vacancy_status(){
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM vacancie_management");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/*==================
* Users
*===================*/
function get_user_roles(){
    global $conn;
    $stmt = $conn->prepare("SELECT role FROM user_management");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




/*
====================
       Login 
====================
*/
