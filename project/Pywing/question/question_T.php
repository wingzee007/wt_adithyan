<?php
// ---------- DATABASE CONNECTION ----------
$conn = new mysqli(
    "sql307.infinityfree.com",
    "if0_40816908",
    "AACCSS2005",
    "if0_40816908_python"
);

if ($conn->connect_error) {
    die("Database connection failed");
}

// ---------- FETCH QUESTIONS ----------
$q = $conn->query("SELECT * FROM quiz ORDER BY id ASC");
$total_questions = $q ? $q->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PyWing | Theory Assessment</title>

<style>
:root {
  --bg-dark:#0a0c10;
  --card-bg:#161b22;
  --py-blue:#3776ab;
  --py-yellow:#ffd343;
  --text-main:#e6edf3;
  --text-dim:#8b949e;
  --border:#30363d;
}

*{box-sizing:border-box}

body{
  background:radial-gradient(circle at top,#0f141b,var(--bg-dark));
  color:var(--text-main);
  font-family:system-ui,Inter,sans-serif;
  margin:0;
  padding:40px 15px 80px;
}

h1{
  text-align:center;
  font-size:clamp(2rem,4vw,3rem);
  margin-bottom:40px;
  background:linear-gradient(90deg,var(--py-blue),var(--py-yellow));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.qa-box{
  max-width:820px;
  margin:0 auto 28px;
  padding:28px;
  border-radius:18px;
  background:linear-gradient(145deg,#1a2029,#11151c);
  border:1px solid var(--border);
  box-shadow:0 15px 35px rgba(0,0,0,.6);
}

.question{
  font-weight:700;
  margin-bottom:16px;
}

textarea{
  width:100%;
  min-height:90px;
  padding:14px;
  border-radius:14px;
  background:#0d1117;
  color:var(--text-main);
  border:1px solid var(--border);
  resize:vertical;
}

textarea:focus{
  outline:none;
  border-color:var(--py-yellow);
  box-shadow:0 0 0 3px rgba(255,211,67,.15);
}

button{
  margin-top:14px;
  padding:10px 26px;
  border:none;
  border-radius:999px;
  background:linear-gradient(135deg,var(--py-blue),#4a90e2);
  color:#fff;
  font-weight:700;
  font-size:.8rem;
  cursor:pointer;
}

button:hover{transform:scale(1.05)}

.result-display{
  margin-top:14px;
  padding:12px;
  border-radius:12px;
  font-weight:600;
}

#viewResultsBtn{
  display:none;
  margin:50px auto 20px;
  background:transparent;
  border:2px solid var(--py-yellow);
  color:var(--py-yellow);
}

#summaryBox{
  display:none;
  margin:30px auto;
  max-width:520px;
  padding:30px;
  border-radius:20px;
  text-align:center;
  border:1px solid var(--py-yellow);
  background:linear-gradient(145deg,#1c2128,#111418);
}
</style>
</head>

<body>

<h1>Theory Assessment</h1>

<?php if ($total_questions === 0): ?>
<p style="text-align:center;color:var(--py-yellow)">No questions found.</p>
<?php endif; ?>

<?php while ($row = $q->fetch_assoc()): ?>
<div class="qa-box">
  <div class="question">
    <?= (int)$row['id'] ?>.
    <?= htmlspecialchars($row['question'],ENT_QUOTES,'UTF-8') ?>
  </div>

  <textarea
    id="user<?= (int)$row['id'] ?>"
    placeholder="Type your explanation..."
    onkeydown="handleEnter(event,<?= (int)$row['id'] ?>)"
  ></textarea>

  <button onclick="checkAnswer(<?= (int)$row['id'] ?>)">Submit Answer</button>
  <div id="result<?= (int)$row['id'] ?>" class="result-display"></div>
</div>
<?php endwhile; ?>

<button id="viewResultsBtn" onclick="showResults()">View Final Score</button>
<div id="summaryBox"></div>

<script>
const scores = {};
const totalQuestions = <?= $total_questions ?>;

function handleEnter(e,id){
  if(e.key==="Enter" && !e.shiftKey){
    e.preventDefault();
    checkAnswer(id);
  }
}

function checkAnswer(id){
  const textarea=document.getElementById("user"+id);
  const resultBox=document.getElementById("result"+id);
  const userAnswer=textarea.value.trim();

  if(!userAnswer){
    alert("Please type your answer");
    return;
  }

  fetch("check.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:"id="+id+"&user_answer="+encodeURIComponent(userAnswer)
  })
  .then(r=>r.text())
  .then(data=>{
    const [status,correct]=data.split("||");

    if(status==="correct"){
      resultBox.style.color="#4dff88";
      resultBox.innerHTML=`✔ Correct<br><b>${correct}</b>`;
      scores[id]=1;
    }else{
      resultBox.style.color="#ff6b6b";
      resultBox.innerHTML=`✘ Wrong<br><b>${correct}</b>`;
      scores[id]=0;
    }

    textarea.disabled=true;

    if(Object.keys(scores).length===totalQuestions){
      document.getElementById("viewResultsBtn").style.display="block";
    }
  });
}

function showResults(){
  let attempted=Object.keys(scores).length;
  let correct=Object.values(scores).filter(v=>v===1).length;
  let wrong=attempted-correct;
  let percentage=((correct/totalQuestions)*100).toFixed(2);

  const box=document.getElementById("summaryBox");
  box.style.display="block";
  box.innerHTML=`
    <h2>Final Result</h2>
    <p>Total Questions: <b>${totalQuestions}</b></p>
    <p>Attempted: <b>${attempted}</b></p>
    <p style="color:#4dff88">Correct: <b>${correct}</b></p>
    <p style="color:#ff6b6b">Wrong: <b>${wrong}</b></p>
    <hr>
    <p style="font-size:1.4rem"><b>${percentage}%</b></p>
  `;
  box.scrollIntoView({behavior:"smooth"});
}
</script>

</body>
</html>
