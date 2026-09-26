<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Report</title>
<style>

* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
body { background:#f4f6f8; color:#333; }


.sidebar {
  height:100vh;
  width:250px;
  position:fixed;
  top:0; left:0;
  background:#1d3557;
  padding-top:20px;
}
.sidebar a {
  display:block;
  padding:15px 25px;
  color:#fff;
  font-weight:500;
  text-decoration:none;
  transition:0.3s;
}
.sidebar a:hover { background:#457b9d; }
.sidebar a.active { background:#e63946; }

.main-content {
  margin-left:250px;
  padding:30px 40px;
}
h2 {
  margin-bottom:25px;
  color:#1d3557;
  font-size:2em;
  font-weight:700;
  text-align:center;
}

.filter-section {
  display:flex;
  flex-wrap:wrap;
  gap:15px;
  justify-content:center;
  align-items:center;
  margin-bottom:25px;
}
select, input[type="month"] {
  padding:8px 10px;
  border:1px solid #ccc;
  border-radius:6px;
  font-size:1em;
}
button {
  background:#1d3557;
  color:white;
  border:none;
  padding:8px 14px;
  border-radius:6px;
  cursor:pointer;
  transition:0.3s;
}
button:hover { background:#457b9d; }

table {
  width:100%;
  border-collapse:collapse;
  background:white;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 4px 15px rgba(0,0,0,0.1);
  margin-top:20px;
}
th, td {
  padding:12px 15px;
  border-bottom:1px solid #ddd;
  text-align:center;
}
th {
  background:#1d3557;
  color:white;
  text-transform:uppercase;
  font-size:0.9em;
}
tr:hover { background:#f1f3f6; }

.summary {
  margin-top:25px;
  text-align:center;
  font-size:1.1em;
}
.summary span {
  display:inline-block;
  margin:0 10px;
  font-weight:600;
}
.summary .present { color:green; }
.summary .late { color:orange; }
.summary .absent { color:red; }
.summary .excused { color:blue; }
</style>
</head>
<body>

<div class="sidebar">
  <a href="index.php">Home</a>
  <a href="FacultyList.php">Faculty List</a>
  <a href="FacultySchedule.php">Faculty Schedule</a>
  <a href="FacultyMonitoring.php">Faculty Monitoring</a>
  <a href="AttendanceReport.php" class="active">Attendance Report</a>
</div>

<div class="main-content">
  <h2>Attendance Report</h2>

  <div class="filter-section">
    <select id="facultyFilter">
      <option value="">All Faculty</option>
    </select>
    <input type="month" id="monthFilter">
    <button onclick="applyFilters()">Filter</button>
  </div>

  <table id="reportTable">
    <thead>
      <tr>
        <th>Date</th>
        <th>Faculty</th>
        <th>Subject</th>
        <th>Room</th>
        <th>Attendance</th>
        <th>Dress Code</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <div class="summary" id="summaryStats"></div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>
<script>
const firebaseConfig = {
  apiKey: "AIzaSyD0Rz3X7H1Zt5lZ5a7JEtVtVfR3_1sG-4",
  authDomain: "faculty-monitoring-5eb2b.firebaseapp.com",
  databaseURL: "https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/",
  projectId: "faculty-monitoring-5eb2b",
  storageBucket: "faculty-monitoring-5eb2b.appspot.com",
  messagingSenderId: "491363066465",
  appId: "1:491363066465:web:b1f3e1ce356e244fbf712f"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

const tableBody = document.querySelector("#reportTable tbody");
const facultyFilter = document.getElementById("facultyFilter");
let allData = [];

function loadAttendanceData() {
  db.ref("Attendance").once("value").then(snapshot => {
    allData = [];
    const facultySet = new Set();

    snapshot.forEach(dateSnap => {
      const date = dateSnap.key;
      dateSnap.forEach(item => {
        const val = item.val();
        allData.push({ ...val, Date: date });
        facultySet.add(val.Faculty);
      });
    });

    facultySet.forEach(name => {
      const option = document.createElement("option");
      option.value = name;
      option.textContent = name;
      facultyFilter.appendChild(option);
    });

    displayData(allData);
  });
}

function applyFilters() {
  const faculty = facultyFilter.value;
  const month = document.getElementById("monthFilter").value;
  let filtered = allData;

  if (faculty) filtered = filtered.filter(d => d.Faculty === faculty);
  if (month) filtered = filtered.filter(d => d.Date.startsWith(month));

  displayData(filtered);
}

function displayData(data) {
  tableBody.innerHTML = "";
  const counts = { Present:0, Late:0, Absent:0, Excused:0 };

  data.forEach(d => {
    counts[d.Attendance] = (counts[d.Attendance] || 0) + 1;

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${d.Date}</td>
      <td>${d.Faculty}</td>
      <td>${d.Subject || ""}</td>
      <td>${d.Room || ""}</td>
      <td>${d.Attendance || ""}</td>
      <td>${d.DressCode || ""}</td>
      <td>${d.Remarks || ""}</td>
    `;
    tableBody.appendChild(row);
  });

  updateSummary(counts);
}

function updateSummary(counts) {
  document.getElementById("summaryStats").innerHTML = `
    <span class="present">Present: ${counts.Present || 0}</span>
    <span class="late">Late: ${counts.Late || 0}</span>
    <span class="absent">Absent: ${counts.Absent || 0}</span>
    <span class="excused">Excused: ${counts.Excused || 0}</span>
  `;
}

loadAttendanceData();
</script>
</body>
</html>
