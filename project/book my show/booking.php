<?php
$time = null;
$seat = null;
$message = "";

$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "project"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if($conn->connect_error)
{
     die("❌ Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $time = $_POST['time'] ?? null;
    $seat = $_POST['seat'] ?? null;

    if ($time && $seat) {
        
        $sql = "INSERT INTO bookings (time, seat_selected) VALUES ('$time', '$seat')";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Booking Successful<br><br>Time: <b>$time</b><br>Seat: <b>$seat</b>";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    } else {
        $message = "⚠ Please select both time and seat!";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking</title>
  <style>
    body {
      font-family: Arial, sans-serif; 
      text-align: center;
      background-image: url(logo.png);
      position: relative;
      justify-content: center;
      color: antiquewhite;
      height: 160vh;
      width: 100vw;
    }
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.8);
      z-index: -1;
    }
    .time-slot, .seat {
      display: inline-block;
      margin: 8px;
      padding: 10px 15px;
      border: 3px solid #f8e7e7;
      border-radius: 8px;
      cursor: pointer;
    }
    .time-slot:hover, .seat:hover {
      background: #f0f0f0;
    }
    .selected {
      background: #4CAF50;
      color: white;
    }
    button {
      margin-top: 20px;
      padding: 12px 20px;
      font-size: 16px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
    }
    #result {
      margin-top: 20px;
      font-size: 18px;
      color: yellow;
    }
  </style>
</head>
<body>
  <h1>Booking</h1>

  <form method="POST">
    <h2>Select Time</h2>
    <input type="hidden" name="time" id="timeInput">
    <div>
      <div class="time-slot" onclick="selectTime(this)">10:00 AM</div>
      <div class="time-slot" onclick="selectTime(this)">1:00 PM</div>
      <div class="time-slot" onclick="selectTime(this)">4:00 PM</div>
      <div class="time-slot" onclick="selectTime(this)">7:00 PM</div>
    </div>

    <h2>Select Seat</h2>
    <input type="hidden" name="seat" id="seatInput">
    <div>
      <div class="seat" onclick="selectSeat(this)">A1</div>
      <div class="seat" onclick="selectSeat(this)">A2</div>
      <div class="seat" onclick="selectSeat(this)">A3</div>
      <div class="seat" onclick="selectSeat(this)">A4</div>
      <div class="seat" onclick="selectSeat(this)">B1</div>
      <div class="seat" onclick="selectSeat(this)">B2</div>
      <div class="seat" onclick="selectSeat(this)">B3</div>
      <div class="seat" onclick="selectSeat(this)">B4</div>
    </div>

    <br><br>
    <button type="submit" class="seat">Confirm Booking</button>
  </form>

  <p id="result"><?php echo $message; ?></p>

  <script>
    function selectTime(element) {
      document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
      element.classList.add('selected');
      document.getElementById("timeInput").value = element.innerText;
    }

    function selectSeat(element) {
      document.querySelectorAll('.seat').forEach(el => el.classList.remove('selected'));
      element.classList.add('selected');
      document.getElementById("seatInput").value = element.innerText;
    }
  </script>
</body>
</html>
