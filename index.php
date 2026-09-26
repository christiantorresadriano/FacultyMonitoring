<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Monitoring System</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
body { background:#f4f6f8; color:#333; }

.sidebar {
    height:100vh;
    width:250px;
    position:fixed;
    top:0;
    left:0;
    background:#1d3557;
    padding-top:20px;
    transition:0.3s;
}
.sidebar a {
    display: block;
    padding: 15px 25px;
    color: #fff;
    font-weight: 500;
    text-decoration: none; 
    transition: 0.3s;
}

.sidebar a:hover { background:#457b9d; }
.sidebar a.active { background:#e63946; }


.main-content {
    margin-left:250px;
    padding:30px 40px;
    transition:0.3s;
}
h2 {
    margin-bottom:25px;
    color:#1d3557;
    font-size:2em;
    font-weight:700;
    text-align:center;
}

.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}
.card {
    background:white;
    padding:25px 20px;
    border-radius:12px;
    box-shadow:0 4px 20px rgba(0,0,0,0.1);
    text-align:center;
    cursor:pointer;
    transition:0.3s;
}
.card h3 {
    margin-bottom:10px;
    color:#1d3557;
}
.card p {
    font-size:0.9em;
    color:#555;
}
.card:hover {
    background:#1d3557;
    color:white;
    transform:translateY(-5px);
}

@media(max-width:900px){
    .main-content { margin-left:200px; padding:20px; }
}
@media(max-width:600px){
    .sidebar { width:180px; }
    .main-content { margin-left:180px; padding:15px; }
    .cards { grid-template-columns:1fr; }
}
</style>
</head>
<body>

<div class="sidebar">
    <a href="index.php" class="active">Home</a>
    <a href="FacultyList.php">Faculty List</a>
    <a href="FacultySchedule.php">Faculty Schedule</a>
    <a href="FacultyMonitoring.php">Faculty Monitoring</a>
    <a href="AttendanceReport.php">Attendance Report</a>
</div>

<div class="main-content">
    <h2>Welcome to Faculty Monitoring System</h2>

    <div class="cards">
        <div class="card" onclick="location.href='FacultyList.php'">
            <h3>Faculty List</h3>
            <p>View and manage faculty information</p>
        </div>
        <div class="card" onclick="location.href='FacultySchedule.php'">
            <h3>Faculty Schedule</h3>
            <p>View and manage faculty schedules</p>
        </div>
        <div class="card" onclick="location.href='FacultyMonitoring.php'">
            <h3>Faculty Monitoring</h3>
            <p>Monitor faculty attendance and performance</p>
        </div>
        <div class="card" onclick="location.href='AttendanceReport.php'">
            <h3>Attendance Report</h3>
            <p>Generate daily to monthly reports</p>
        </div>
    </div>
</div>

</body>
</html>
