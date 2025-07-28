<?php 
include("templates/header.php")
?>
<?php
// print_r($_SESSION);

if(isset($_POST["submit"])){
    $fullname = $_POST['fullname'];
    $password = md5($_POST['password']);
    //echo $password;

    $stmt = $conn->prepare("SELECT * FROM user_management WHERE fullname = :fullname AND password = :password");
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

    if (is_array($user) && count($user ) == 1 ) {
        $_SESSION['user_id'] = $user[0]['id'];
        $_SESSION['fullname'] = $user[0]['fullname'];

        header("Location: index.php");
        exit();
    } else {
        echo "incorrect";
    }
}

?>
<form action="login.php" method="POST">
    <label>Username:</label>
    <input type="text" name="fullname" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="submit" value="Login">
</form>
