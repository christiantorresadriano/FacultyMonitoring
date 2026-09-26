<?php
// FacultyList.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty List</title>
<style>

* { box-sizing: border-box; margin:0; padding:0; font-family: 'Segoe UI', sans-serif;}
body { background: #f4f6f8; color:#333; }

a { text-decoration:none; color:inherit; }

.sidebar {
    height: 100vh;
    width: 250px;
    position: fixed;
    top:0;
    left:0;
    background: #1d3557;
    padding-top: 20px;
    transition: 0.3s;
}
.sidebar a {
    display:block;
    padding: 15px 25px;
    color:#fff;
    font-weight: 500;
    transition: 0.3s;
}
.sidebar a:hover { background: #457b9d; }
.sidebar a.active { background:#e63946; }

.main-content {
    margin-left: 250px;
    padding: 20px 30px;
    transition:0.3s;
}

h2 {
    margin-bottom:20px;
    color: #1d3557;
    font-size:1.8em;
    font-weight:700;
}

.table-container {
    background:white;
    padding:15px 20px;
    border-radius:10px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    overflow-x:auto;
}

table {
    width:100%;
    border-collapse:collapse;
    min-width:800px;
}

th, td {
    padding:12px 10px;
    text-align:left;
}

th {
    background:#1d3557;
    color:white;
    font-weight:600;
    text-transform:uppercase;
}

tr:nth-child(even) { background:#f8f9fa; }
tr:hover { background:#eef4fa; }

button {
    border:none;
    padding:7px 12px;
    border-radius:6px;
    cursor:pointer;
    transition:0.3s;
    font-size:0.9em;
}

.edit-btn { background:#facc15; color:#000; }
.edit-btn:hover { background:#eab308; }

.delete-btn { background:#ef4444; color:white; }
.delete-btn:hover { background:#b91c1c; }

.view-btn { background:#3b82f6; color:white; }
.view-btn:hover { background:#2563eb; }

.add-btn {
    background:#10b981;
    color:white;
    padding:10px 18px;
    margin-bottom:15px;
}
.add-btn:hover { background:#059669; }

.modal {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:100;
}
.modal-content {
    background:white;
    padding:20px 25px;
    border-radius:10px;
    width:400px;
    max-width:90%;
    box-shadow:0 5px 20px rgba(0,0,0,0.3);
    position:relative;
}
.modal-content h3 {
    margin-bottom:15px;
    text-align:center;
    color:#1d3557;
}
.close {
    position:absolute;
    top:10px;
    right:15px;
    font-size:22px;
    cursor:pointer;
    color:#888;
}
.close:hover { color:#e63946; }

.modal-content label {
    display:block;
    margin-top:10px;
    font-weight:600;
    color:#1d3557;
}
.modal-content input, .modal-content select {
    width:100%;
    padding:8px 10px;
    margin-top:5px;
    border:1px solid #cbd5e1;
    border-radius:6px;
}
.modal-content input:focus, .modal-content select:focus {
    outline:none;
    border-color:#1d3557;
    box-shadow:0 0 5px rgba(29,53,87,0.3);
}
.modal-content button.save-btn {
    margin-top:15px;
    width:100%;
    background:#1d3557;
    color:white;
    padding:10px;
}
.modal-content button.save-btn:hover { background:#457b9d; }

.popup {
    position:fixed;
    top:20px;
    right:20px;
    padding:12px 20px;
    border-radius:8px;
    color:white;
    display:none;
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
    z-index:101;
}
.popup.success { background:#2a9d8f; }
.popup.error { background:#e63946; }

.search-box {
    margin-bottom:10px;
    display:flex;
    justify-content:flex-end;
}
.search-box input {
    padding:8px 10px;
    width:250px;
    border:1px solid #cbd5e1;
    border-radius:6px;
}
.pagination {
    margin-top:10px;
    text-align:right;
}
.pagination button {
    margin-left:5px;
}
</style>
</head>
<body>

<div class="sidebar">
    <a href="index.php">Home</a>
    <a href="FacultyList.php" class="active">Faculty List</a>
    <a href="FacultySchedule.php">Faculty Schedule</a>
    <a href="FacultyMonitoring.php">Faculty Monitoring</a>
    <a href="AttendanceReport.php">Attendance Report</a>
</div>

<div class="main-content">
    <h2>Faculty List</h2>

    <button class="add-btn" onclick="openModal()">+ Add New Faculty</button>
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search faculty..." oninput="searchFaculty()">
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID No.</th>
                    <th>Faculty Name</th>
                    <th>Department</th>
                    <th>Faculty Rank</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="facultyTable"></tbody>
        </table>
    </div>

    <div class="pagination">
        <button onclick="prevPage()">Previous</button>
        <button onclick="nextPage()">Next</button>
    </div>
</div>

<div class="modal" id="facultyModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Add Faculty</h3>
        <form id="facultyForm">
            <input type="hidden" id="firebaseKey">
            <label>ID No.</label>
            <input type="text" id="idNo" required>
            <label>Last Name</label>
            <input type="text" id="lastName" required>
            <label>First Name</label>
            <input type="text" id="firstName" required>
            <label>Middle Initial</label>
            <input type="text" id="middleInitial">
            <label>Gender</label>
            <select id="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <label>Faculty Rank</label>
            <select id="rank">
                <option value="Permanent">Permanent</option>
                <option value="Temporary">Temporary</option>
                <option value="Part-Time">Part-Time</option>
            </select>
            <label>Department</label>
            <input type="text" id="department" required>
            <button type="submit" class="save-btn">Save</button>
        </form>
    </div>
</div>

<div class="popup" id="popup"></div>

<script>
const firebaseUrl = 'https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/Faculties.json';
let faculties = [];
let currentPage = 1;
const rowsPerPage = 10;

const modal = document.getElementById('facultyModal');
const popup = document.getElementById('popup');

async function fetchFaculties(){
    const res = await fetch(firebaseUrl);
    const data = await res.json();
    faculties = [];
    for(const key in data){
        let f = data[key]; 
        if(typeof f==='string') f=JSON.parse(f);
        f.firebaseKey=key;
        faculties.push(f);
    }
    faculties.sort((a,b)=>{
        if(a.LastName.toLowerCase()===b.LastName.toLowerCase()){
            return a.FirstName.localeCompare(b.FirstName);
        }
        return a.LastName.localeCompare(b.LastName);
    });
    currentPage=1;
    renderTable();
}

function renderTable(){
    const tbody = document.getElementById('facultyTable');
    tbody.innerHTML='';
    const search = document.getElementById('searchInput').value.toLowerCase();
    const filtered = faculties.filter(f=>{
        const name = `${f.LastName}, ${f.FirstName} ${f.MiddleInitial||''}`.toLowerCase();
        return name.includes(search) || f.Department.toLowerCase().includes(search) || f.Rank.toLowerCase().includes(search);
    });

    const start = (currentPage-1)*rowsPerPage;
    const paginated = filtered.slice(start,start+rowsPerPage);

    paginated.forEach((f,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML=`
            <td>${start+i+1}</td>
            <td>${f.IDNo}</td>
            <td>${f.LastName}, ${f.FirstName} ${f.MiddleInitial||''}</td>
            <td>${f.Department}</td>
            <td>${f.Rank}</td>
            <td>
                <button class="view-btn" onclick="viewFaculty('${f.firebaseKey}')">View</button>
                <button class="edit-btn" onclick="editFaculty('${f.firebaseKey}')">Edit</button>
                <button class="delete-btn" onclick="deleteFaculty('${f.firebaseKey}')">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function prevPage(){ if(currentPage>1){ currentPage--; renderTable(); } }
function nextPage(){ if(currentPage*rowsPerPage<faculties.length){ currentPage++; renderTable(); } }
function searchFaculty(){ currentPage=1; renderTable(); }

function openModal(){ 
    document.getElementById('modalTitle').innerText='Add Faculty'; 
    document.getElementById('facultyForm').reset(); 
    document.getElementById('firebaseKey').value=''; 
    modal.style.display='flex'; 
}
function closeModal(){ modal.style.display='none'; }

document.getElementById('facultyForm').addEventListener('submit',async function(e){
    e.preventDefault();
    const idNo=document.getElementById('idNo').value.trim();
    const lastName=document.getElementById('lastName').value.trim();
    const firstName=document.getElementById('firstName').value.trim();
    const middleInitial=document.getElementById('middleInitial').value.trim();
    const gender=document.getElementById('gender').value;
    const rank=document.getElementById('rank').value;
    const department=document.getElementById('department').value.trim();
    const firebaseKey=document.getElementById('firebaseKey').value;

    const duplicate = faculties.some(f=>f.LastName.toLowerCase()===lastName.toLowerCase() && f.FirstName.toLowerCase()===firstName.toLowerCase() && (f.MiddleInitial||'').toLowerCase()===middleInitial.toLowerCase() && f.firebaseKey!==firebaseKey);
    if(duplicate){ showPopup('Faculty already exists!', 'error'); return; }

    const payload={IDNo:idNo, LastName:lastName, FirstName:firstName, MiddleInitial:middleInitial, Gender:gender, Rank:rank, Department:department};

    let url=firebaseUrl, method='POST';
    if(firebaseKey){ url=`https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/Faculties/${firebaseKey}.json`; method='PUT'; }

    try{
        await fetch(url,{method:method, headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)});
        showPopup(firebaseKey?'Faculty updated successfully!':'Faculty added successfully!','success');
        closeModal();
        fetchFaculties();
    }catch(err){ showPopup('Error saving faculty!','error'); }
});

async function deleteFaculty(key){
    if(!confirm('Are you sure you want to delete this faculty?')) return;
    try{
        await fetch(`https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/Faculties/${key}.json`,{method:'DELETE'});
        showPopup('Faculty deleted successfully!','success');
        fetchFaculties();
    }catch(err){ showPopup('Error deleting faculty!','error'); }
}

function editFaculty(key){
    const f=faculties.find(f=>f.firebaseKey===key);
    if(!f) return;
    document.getElementById('modalTitle').innerText='Edit Faculty';
    document.getElementById('firebaseKey').value=key;
    document.getElementById('idNo').value=f.IDNo;
    document.getElementById('lastName').value=f.LastName;
    document.getElementById('firstName').value=f.FirstName;
    document.getElementById('middleInitial').value=f.MiddleInitial||'';
    document.getElementById('gender').value=f.Gender;
    document.getElementById('rank').value=f.Rank;
    document.getElementById('department').value=f.Department;
    modal.style.display='flex';
}

function viewFaculty(key){
    const f=faculties.find(f=>f.firebaseKey===key);
    if(!f) return;
    alert(`ID: ${f.IDNo}\nName: ${f.LastName}, ${f.FirstName} ${f.MiddleInitial||''}\nGender: ${f.Gender}\nRank: ${f.Rank}\nDepartment: ${f.Department}`);
}

function showPopup(msg,type){ popup.textContent=msg; popup.className=`popup ${type}`; popup.style.display='block'; setTimeout(()=>popup.style.display='none',3000); }

fetchFaculties();
</script>
</body>
</html>
