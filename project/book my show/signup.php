<?php

$conn = mysqli_connect("localhost", "root", "", "project");


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_POST['email'];
$name = $_POST['name'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];


if ($password != $confirmPassword) {
    die("Passwords do not match! <a href='signup.html'>Try again</a>");
}


$sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$name', '$password')";

if (mysqli_query($conn, $sql)) {
    
    header("Location: login.html");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
