<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Monitoring</title>
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

table {
  width:100%;
  border-collapse:collapse;
  background:white;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 4px 15px rgba(0,0,0,0.1);
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

.record-btn {
  margin-bottom:20px;
  background:#e63946;
}

select, input[type=text] {
  padding:6px;
  border:1px solid #ccc;
  border-radius:6px;
  width:100%;
}
</style>
</head>
<body>

<div class="sidebar">
  <a href="index.php">Home</a>
  <a href="FacultyList.php">Faculty List</a>
  <a href="FacultySchedule.php">Faculty Schedule</a>
  <a href="FacultyMonitoring.php" class="active">Faculty Monitoring</a>
  <a href="AttendanceReport.php">Attendance Report</a>
</div>

<div class="main-content">
  <h2>Faculty Monitoring - Today's Schedule</h2>
  <button class="record-btn" onclick="recordAttendance()">Record Attendance</button>

  <table id="facultyTable">
    <thead>
      <tr>
        <th>Faculty</th>
        <th>Section</th>
        <th>Subject</th>
        <th>Room</th>
        <th>Mode</th>
        <th>Day</th>
        <th>Time</th>
        <th>Attendance</th>
        <th>Dress Code</th>
        <th>Remarks</th>
        <th>Link</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
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

const tableBody = document.querySelector("#facultyTable tbody");
const today = new Date().toLocaleDateString('en-US', { weekday: 'long' });
const currentDate = new Date().toISOString().split('T')[0]; // e.g. "2025-11-04"

function loadSchedules() {
  db.ref("Schedule").once("value").then(snapshot => {
    tableBody.innerHTML = "";
    snapshot.forEach(child => {
      const data = child.val();
      if (data.Day === today) {
        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${data.Faculty}</td>
          <td>${data.Section || ""}</td>
          <td>${data.Subject || ""}</td>
          <td>${data.Room || ""}</td>
          <td>${data.Mode || ""}</td>
          <td>${data.Day || ""}</td>
          <td>${data.Time || ""}</td>
          <td>
            <select class="attendance">
              <option value="">Select</option>
              <option>Present</option>
              <option>Late</option>
              <option>Absent</option>
              <option>Excused</option>
            </select>
          </td>
          <td>
            <select class="dress">
              <option value="">Select</option>
              <option>Filipiniana</option>
              <option>ASEAN</option>
              <option>Uniform</option>
              <option>Casual</option>
            </select>
          </td>
          <td><input type="text" class="remarks" placeholder="Remarks"></td>
          <td>${data.Mode === "Online/asynchronous" ? `<input type="text" class="link" placeholder="Enter link">` : `N/A`}</td>
          <td><button onclick="saveAttendance(this, '${child.key}')">Save</button></td>
        `;

        tableBody.appendChild(row);
      }
    });
  });
}

function saveAttendance(btn, key) {
  const row = btn.closest("tr");
  const data = {
    Date: currentDate,
    Faculty: row.cells[0].innerText,
    Section: row.cells[1].innerText,
    Subject: row.cells[2].innerText,
    Room: row.cells[3].innerText,
    Mode: row.cells[4].innerText,
    Day: row.cells[5].innerText,
    Time: row.cells[6].innerText,
    Attendance: row.querySelector(".attendance").value,
    DressCode: row.querySelector(".dress").value,
    Remarks: row.querySelector(".remarks").value,
    Link: row.querySelector(".link") ? row.querySelector(".link").value : ""
  };

  if (!data.Attendance || !data.DressCode) {
    alert("Please select Attendance and Dress Code.");
    return;
  }

  const attendanceRef = db.ref(`Attendance/${currentDate}/${key}`);
  const logRef = db.ref(`RecordLog/${currentDate}/${key}`);

  attendanceRef.set(data).then(() => {
    logRef.set(data).then(() => {
      alert("Attendance successfully saved!");
      loadSchedules();
    });
  });
}

function recordAttendance() {
  const now = new Date().toLocaleString();
  db.ref(`RecordLog/${currentDate}`).update({
    summary: {
      date: now,
      message: `Attendance recorded for ${today}`
    }
  }).then(() => {
    alert("Attendance recording logged successfully!");
  });
}

loadSchedules();
</script>
</body>
</html>
