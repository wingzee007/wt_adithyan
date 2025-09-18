<?php
$conn = mysqli_connect("localhost", "root", "", "project");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // success → go to homepage
    header("Location: index.html");
    exit();
} else {
    echo "<script>
            alert('Invalid username or password!');
            window.location.href='login.html';
          </script>";
}

mysqli_close($conn);
?>
