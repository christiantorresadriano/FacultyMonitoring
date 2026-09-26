<?php
// FacultySchedule.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Faculty Schedule</title>

<style>

* { box-sizing: border-box; font-family: "Segoe UI", Roboto, sans-serif; }
body { margin:0; background:#f4f6f8; color:#243b53; display:flex; min-height:100vh; }

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
}
.sidebar a:hover { background:#457b9d; }
.sidebar a.active { background:#e63946; }

.content { margin-left:250px; padding:24px; flex:1; }

.header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.title { font-size:22px; font-weight:700; color:#102a43; }
.tabs { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
.tab { padding:8px 14px; background:#1d3557; color:#fff; border:none; border-radius:8px; cursor:pointer; }
.tab.active { background:#457b9d; }

.add-btn { background:#10b981; color:#fff; border:none; padding:8px 14px; border-radius:8px; cursor:pointer; }

.views { display:flex; gap:18px; flex-direction:column; }

.calendar { background:#fff; border-radius:10px; padding:12px; box-shadow:0 6px 18px rgba(16,42,67,0.06); }
.calendar-grid { display:grid; grid-template-columns: repeat(7, 1fr); gap:6px; }
.calendar-day { min-height:90px; border-radius:8px; padding:8px; background:#f8fbff; display:flex; flex-direction:column; overflow:hidden; }
.calendar-day .date { font-weight:700; margin-bottom:6px; color:#102a43; font-size:14px; }
.name-pill { display:block; padding:4px 6px; border-radius:6px; margin:2px 0; background:#e6f4ea; color:#0b6b3a; cursor:pointer; font-size:13px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; }

.weekly { background:#fff; border-radius:10px; padding:12px; box-shadow:0 6px 18px rgba(16,42,67,0.06); overflow:auto; }
.week-grid { display:grid; grid-template-columns: 120px 1fr; gap:8px; align-items:start; }
.time-column { display:flex; flex-direction:column; gap:8px; padding-right:6px; }
.time-slot { padding:8px; border-radius:6px; background:#f1f7fb; font-weight:600; text-align:center; }
.week-columns { display:grid; grid-template-columns:repeat(6,1fr); gap:8px; }
.day-column { background:#f9fbff; padding:8px; border-radius:8px; min-height:120px; }
.slot { padding:6px; border-radius:6px; background:#fff; margin-bottom:6px; box-shadow:0 2px 6px rgba(16,42,67,0.04); }

.daily-panel { background:#fff; border-radius:10px; padding:12px; box-shadow:0 6px 18px rgba(16,42,67,0.06); }
.day-buttons { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
.day-button { padding:8px 12px; border-radius:6px; background:#1d3557; color:#fff; border:none; cursor:pointer; }
.day-button.active { background:#457b9d; }
.table { width:100%; border-collapse:collapse; }
.table th { background:#1d3557; color:#fff; padding:10px; text-align:left; }
.table td { padding:8px; border-bottom:1px solid #eef4fb; }

.btn { padding:6px 8px; border-radius:6px; border:none; cursor:pointer; font-size:13px; }
.btn.edit { background:#facc15; }
.btn.del { background:#ef4444; color:#fff; margin-left:6px; }

.modal { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(6, 10, 12, .45); z-index:999; }
.modal-card { width:520px; max-width:95%; background:#fff; border-radius:10px; padding:18px; box-shadow:0 10px 30px rgba(2,6,23,.2); }
.modal-card h3 { margin:0 0 12px 0; color:#102a43; }
.form-row { display:flex; gap:8px; flex-wrap:wrap; }
.form-field { margin-bottom:10px; min-width:0; }
label { display:block; font-weight:600; margin-bottom:6px; color:#0b3454; font-size:13px; }
input[type="text"], select, input[type="url"] { width:100%; padding:8px 10px; border-radius:8px; border:1px solid #d6e6f3; }

.readonly { background:#f7fafc; }

.modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
.btn-close { background:#ef4444; color:#fff; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; }
.btn-ok { background:#1d3557; color:#fff; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; }

.toast { position:fixed; right:18px; top:18px; padding:10px 14px; border-radius:8px; color:#fff; display:none; z-index:1200; }
.toast.ok { background:#2a9d8f; }
.toast.err { background:#e63946; }

@media(max-width:900px){
  .week-grid{ grid-template-columns:1fr; }
  .sidebar{ display:none; }
  .content{ margin-left:0; padding:14px; }
  body{ display:block; }
}
</style>
</head>
<body>

<div class="sidebar">
  <a href="index.php">Home</a>
  <a href="FacultyList.php">Faculty List</a>
  <a href="FacultySchedule.php" class="active">Faculty Schedule</a>
  <a href="FacultyMonitoring.php">Faculty Monitoring</a>
  <a href="AttendanceReport.php">Attendance Report</a>
</div>

<div class="content">
  <div class="header">
    <div>
      <div class="title">Faculty Schedule</div>
      <div style="color:#3b5167; font-size:13px">Monthly / Weekly / Daily — synchronized</div>
    </div>
    <div class="tabs">
      <button id="tabMonthly" class="tab active">Monthly</button>
      <button id="tabWeekly" class="tab">Weekly</button>
      <button id="tabDaily" class="tab">Daily</button>
      <button class="add-btn" onclick="openAddModal()">+ Add Schedule</button>
    </div>
  </div>

  <div class="views">
    <!-- Monthly -->
    <div id="viewMonthly">
      <div class="calendar-toolbar" style="margin-bottom:10px;">
        <select id="monthSelect" class="month-select"></select>
        <div style="flex:1"></div>
      </div>
      <div class="calendar">
        <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-bottom:6px;">
          <div style="text-align:center; font-weight:700;">Sun</div>
          <div style="text-align:center; font-weight:700;">Mon</div>
          <div style="text-align:center; font-weight:700;">Tue</div>
          <div style="text-align:center; font-weight:700;">Wed</div>
          <div style="text-align:center; font-weight:700;">Thu</div>
          <div style="text-align:center; font-weight:700;">Fri</div>
          <div style="text-align:center; font-weight:700;">Sat</div>
        </div>
        <div id="calendarGrid" class="calendar-grid"></div>
      </div>
    </div>

    <div id="viewWeekly" style="display:none; margin-top:8px;">
      <div class="weekly">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <div style="font-weight:700;">Weekly Schedule (Mon — Sat)</div>
          <div>
            <button onclick="prevWeek()" class="tab">Prev</button>
            <button onclick="nextWeek()" class="tab">Next</button>
            <button onclick="goToCurrentWeek()" class="tab">Today</button>
          </div>
        </div>
        <div class="week-grid" id="weeklyGrid">
          <div class="time-column">
            <div style="height:36px;"></div>
            <div class="time-slot">7:00 AM - 10:00 AM</div>
            <div class="time-slot">10:00 AM - 1:00 PM</div>
            <div class="time-slot">1:00 PM - 4:00 PM</div>
            <div class="time-slot">4:00 PM - 7:00 PM</div>
          </div>
          <div id="weeklyColumns" class="week-columns"></div>
        </div>
      </div>
    </div>

    <div id="viewDaily" style="display:none; margin-top:8px;">
      <div class="daily-panel">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-weight:700; margin-bottom:8px;">Daily Schedule</div>
            <div class="day-buttons" id="dayButtons"></div>
          </div>
          <div style="color:#6b7c90; font-size:13px;">Use Edit to modify; Delete to remove (changes reflect in all views)</div>
        </div>

        <div style="margin-top:12px;">
          <table class="table" id="dailyTable">
            <thead>
              <tr>
                <th>#</th><th>Name</th><th>Section</th><th>Subject</th><th>Room</th><th>Time</th><th>Mode</th><th>Actions</th>
              </tr>
            </thead>
            <tbody id="dailyBody"><tr><td colspan="8" style="text-align:center; padding:12px;">Select a day to view schedules</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="modal">
  <div class="modal-card">
    <h3 id="modalTitle">Add Schedule</h3>
    <div style="max-height:65vh; overflow:auto;">
      <form id="form">
        <input type="hidden" id="editKey" />
        <div class="form-row">
          <div style="flex:1;" class="form-field">
            <label>Faculty</label>
            <select id="facultySelect" required></select>
          </div>
          <div style="width:140px;" class="form-field">
            <label>Time</label>
            <select id="timeSelect" required>
              <option value="">--</option>
              <option>7:00 AM - 10:00 AM</option>
              <option>10:00 AM - 1:00 PM</option>
              <option>1:00 PM - 4:00 PM</option>
              <option>4:00 PM - 7:00 PM</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div style="flex:1;" class="form-field">
            <label>Section</label>
            <input id="sectionInput" type="text" required />
          </div>
          <div style="flex:1;" class="form-field">
            <label>Subject Code</label>
            <input id="subjectInput" type="text" required />
          </div>
        </div>

        <div class="form-row">
          <div style="width:220px;" class="form-field">
            <label>Room</label>
            <input id="roomInput" type="text" required />
          </div>
          <div style="width:180px;" class="form-field">
            <label>Day</label>
            <select id="daySelect" required>
              <option value="">--</option>
              <option>Monday</option>
              <option>Tuesday</option>
              <option>Wednesday</option>
              <option>Thursday</option>
              <option>Friday</option>
              <option>Saturday</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div style="flex:1;" class="form-field">
            <label>Mode</label>
            <select id="modeSelect" required onchange="modeChange()">
              <option value="">--</option>
              <option>Face to Face</option>
              <option>Online/Asynchronous</option>
              <option>NC - Holiday</option>
              <option>NC - Cancelled</option>
            </select>
          </div>
          <div style="flex:1; display:none;" class="form-field" id="linkField">
            <label>Link (if Online)</label>
            <input id="linkInput" type="url" placeholder="https://..." />
          </div>
        </div>
      </form>
    </div>

    <div class="modal-actions">
      <button class="btn-close" onclick="closeModal()">Cancel</button>
      <button class="btn-ok" onclick="saveSchedule()">Save</button>
    </div>
  </div>
</div>

<div id="viewModal" class="modal">
  <div class="modal-card">
    <h3>Schedule Info</h3>
    <div style="max-height:60vh; overflow:auto; padding-right:6px;">
      <div class="form-field"><label>Faculty</label><input id="vFaculty" class="readonly" readonly></div>
      <div class="form-field"><label>Section</label><input id="vSection" class="readonly" readonly></div>
      <div class="form-field"><label>Subject</label><input id="vSubject" class="readonly" readonly></div>
      <div class="form-field"><label>Room</label><input id="vRoom" class="readonly" readonly></div>
      <div class="form-field"><label>Day</label><input id="vDay" class="readonly" readonly></div>
      <div class="form-field"><label>Time</label><input id="vTime" class="readonly" readonly></div>
      <div class="form-field"><label>Mode</label><input id="vMode" class="readonly" readonly></div>
      <div class="form-field"><label>Link</label><input id="vLink" class="readonly" readonly></div>
    </div>
    <div class="modal-actions">
      <button class="btn-close" onclick="closeView()">Close</button>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>

<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>

<script>

const firebaseConfig = {
  /*sample firebase config hahahahahahhahah*/
  apiKey: "YOUR_API_KEY",
  authDomain: "YOUR_PROJECT.firebaseapp.com",
  databaseURL: "https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_PROJECT.appspot.com",
  messagingSenderId: "SENDER_ID",
  appId: "APP_ID"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

const scheduleBase = "https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/Schedule";
const scheduleUrl = scheduleBase + ".json";
const facultyUrl = "https://faculty-monitoring-5eb2b-default-rtdb.firebaseio.com/Faculties.json";

let faculties = [];        
let schedulesRaw = {};     
let parsedSchedules = {};  

let currentMonth = {y:2025, m:9}; 
let monthRange = []; 
let weekOffset = 0;
let currentDaily = "Monday";
let viewModalKey = null;

function showToast(msg, ok=true){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + (ok ? 'ok' : 'err');
  t.style.display = 'block';
  setTimeout(()=> t.style.display='none', 3000);
}
function safeParse(v){
  if(v === null || v === undefined) return null;
  if(typeof v === 'string'){
    try { return JSON.parse(v); } catch(e){ return v; }
  }
  return v;
}
function normalizeStr(s){ return (s||'').toString().trim().toLowerCase(); }
function escapeHtml(s){ if(!s) return ''; return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;'); }
function encodeKey(k){ return encodeURIComponent(k); }

(function(){ const start = new Date(2025,9,1); const end = new Date(2025,11,1); let cur=new Date(start); while(cur<=end){ monthRange.push({y:cur.getFullYear(), m:cur.getMonth()}); cur.setMonth(cur.getMonth()+1);} })();

function populateMonthSelect(){
  const sel = document.getElementById('monthSelect');
  sel.innerHTML = '';
  monthRange.forEach((mm,idx)=>{
    const d = new Date(mm.y, mm.m, 1);
    const opt = document.createElement('option');
    opt.value = idx;
    opt.textContent = d.toLocaleString('default', {month:'long', year:'numeric'});
    sel.appendChild(opt);
  });
    
  const found = monthRange.findIndex(x=>x.y===currentMonth.y && x.m===currentMonth.m);
  sel.selectedIndex = found >= 0 ? found : 0;
  sel.onchange = ()=> { const idx = parseInt(sel.value); currentMonth = monthRange[idx]; renderCalendar(); };
}

async function initData(){
  populateMonthSelect();

  await loadFaculties();

  try {
    const schedulesRef = db.ref('Schedule');
    schedulesRef.on('value', (snapshot) => {
      const val = snapshot.val() || {};
      schedulesRaw = val;
      parseSchedules();
      renderCalendar();
      renderWeekly();
      buildDayButtons();
      renderDaily();
    });
  } catch(e){
    console.warn('Realtime listener failed, falling back to REST fetch', e);
    await loadSchedulesREST();
  }
}

async function loadFaculties(){
  try {
    const res = await fetch(facultyUrl);
    const data = await res.json();
    faculties = [];
    if(data){
      for(const k in data){
        let f = data[k];
        if(typeof f === 'string'){ try { f = JSON.parse(f);}catch{} }
        f.firebaseKey = k;
        faculties.push(f);
      }
      faculties.sort((a,b)=>{
        const la=(a.LastName||'').toLowerCase(), lb=(b.LastName||'').toLowerCase();
        if(la===lb) return (a.FirstName||'').localeCompare(b.FirstName||'');
        return la.localeCompare(lb);
      });
    }
    populateFacultyDropdown();
  } catch(e){
    console.error('loadFaculties err', e);
    showToast('Failed to load faculties', false);
  }
}
function populateFacultyDropdown(){
  const sel = document.getElementById('facultySelect');
  sel.innerHTML = '<option value="">-- Select Faculty --</option>';
  const seen = new Set();
  faculties.forEach(f=>{
    const name = `${f.LastName}, ${f.FirstName}${f.MiddleInitial ? ' ' + f.MiddleInitial : ''}`.trim();
    if(!seen.has(name)){
      seen.add(name);
      const opt = document.createElement('option');
      opt.value = name;
      opt.textContent = name;
      sel.appendChild(opt);
    }
  });
}

function parseSchedules(){
  parsedSchedules = {};
  if(!schedulesRaw) return;
  for(const key in schedulesRaw){
    let raw = schedulesRaw[key];
    let parsed = safeParse(raw);
    if(parsed && typeof parsed === 'string'){
        
      parsed = { Faculty: parsed };
    }
    if(parsed && typeof parsed === 'object'){
      parsedSchedules[key] = {
        Faculty: parsed.Faculty || parsed.Name || parsed.Instructor || parsed.InstructorName || '',
        Section: parsed.Section || '',
        Subject: parsed.Subject || parsed.SubjectCode || parsed.SubjectName || '',
        Room: parsed.Room || parsed.RoomNo || '',
        Day: parsed.Day || parsed.ScheduleDay || '',
        Time: parsed.Time || parsed.TimeSlot || '',
        Mode: parsed.Mode || '',
        Link: parsed.Link || parsed.OnlineLink || ''
      };
    } else {
      parsedSchedules[key] = { Faculty: String(parsed||''), Section:'', Subject:'', Room:'', Day:'', Time:'', Mode:'', Link:'' };
    }
  }
}

async function loadSchedulesREST(){
  try {
    const res = await fetch(scheduleUrl);
    const data = await res.json();
    schedulesRaw = data || {};
    parseSchedules();
    renderCalendar();
    renderWeekly();
    buildDayButtons();
    renderDaily();
  } catch(e){
    console.error('loadSchedulesREST err', e);
    showToast('Failed to load schedules', false);
  }
}

function renderCalendar(){
  const grid = document.getElementById('calendarGrid');
  grid.innerHTML = '';
  const y = currentMonth.y, m = currentMonth.m;
  const first = new Date(y,m,1);
  const startWeekday = first.getDay(); // 0 = Sun
  const daysInMonth = new Date(y,m+1,0).getDate();

  for(let i=0;i<startWeekday;i++){
    const placeholder = document.createElement('div');
    placeholder.className = 'calendar-day';
    placeholder.style.opacity = 0.35;
    grid.appendChild(placeholder);
  }

  const dayNameFromDate = d => ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];

  for(let date=1; date<=daysInMonth; date++){
    const dt = new Date(y,m,date);
    const dayName = dayNameFromDate(dt);
    const cell = document.createElement('div');
    cell.className = 'calendar-day';
    const dateSpan = document.createElement('div');
    dateSpan.className = 'date';
    dateSpan.textContent = date;
    cell.appendChild(dateSpan);

    for(const key in parsedSchedules){
      const s = parsedSchedules[key];
      if(normalizeStr(s.Day) === normalizeStr(dayName)){
        const pill = document.createElement('span');
        pill.className = 'name-pill';
        pill.textContent = s.Faculty || '(No name)';
        pill.title = s.Faculty || '';
        pill.onclick = (ev)=> { ev.stopPropagation(); openViewModal(key); }; 
        cell.appendChild(pill);
      }
    }
    grid.appendChild(cell);
  }
}

function computeWeekDates(offset){
  const today = new Date();
  const cur = new Date(today);
  cur.setDate(today.getDate() + offset*7);
  
  const day = cur.getDay();
  const monday = new Date(cur);
  const diff = (day === 0) ? -6 : (1 - day);
  monday.setDate(cur.getDate() + diff);
  const arr = [];
  for(let i=0;i<6;i++){
    const d = new Date(monday);
    d.setDate(monday.getDate()+i);
    arr.push(d);
  }
  return arr;
}

function renderWeekly(){
  const container = document.getElementById('weeklyColumns');
  container.innerHTML = '';
  const weekDates = computeWeekDates(weekOffset);
  const weekdayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const times = ['7:00 AM - 10:00 AM','10:00 AM - 1:00 PM','1:00 PM - 4:00 PM','4:00 PM - 7:00 PM'];

  for(let col=0; col<6; col++){
    const colDiv = document.createElement('div');
    colDiv.className = 'day-column';
    const header = document.createElement('div');
    header.style.fontWeight = '700';
    header.style.marginBottom = '8px';
    header.textContent = `${weekdayNames[col]} (${weekDates[col].toLocaleDateString()})`;
    colDiv.appendChild(header);

    times.forEach(t=>{
      const matches = [];
      for(const key in parsedSchedules){
        const s = parsedSchedules[key];
        if(normalizeStr(s.Day) === normalizeStr(weekdayNames[col]) && normalizeStr(s.Time) === normalizeStr(t)){
          matches.push({key,s});
        }
      }
      if(matches.length){
        const slotDiv = document.createElement('div');
        slotDiv.className = 'slot';
        const slotTitle = document.createElement('div');
        slotTitle.style.fontWeight='600';
        slotTitle.style.marginBottom='6px';
        slotTitle.textContent = t;
        slotDiv.appendChild(slotTitle);
        matches.forEach(it=>{
          const p = document.createElement('div');
          p.style.cursor = 'pointer';
          p.style.color = '#0b4660';
          p.textContent = it.s.Faculty || '(No name)';
          p.onclick = ()=> openViewModal(it.key); 
          slotDiv.appendChild(p);
        });
        colDiv.appendChild(slotDiv);
      }
    });

    container.appendChild(colDiv);
  }
}

function prevWeek(){ weekOffset--; renderWeekly(); }
function nextWeek(){ weekOffset++; renderWeekly(); }
function goToCurrentWeek(){ weekOffset = 0; renderWeekly(); }

const weekdays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
function buildDayButtons(){
  const container = document.getElementById('dayButtons');
  container.innerHTML = '';
  weekdays.forEach(d=>{
    const btn = document.createElement('button');
    btn.className = 'day-button' + (d === currentDaily ? ' active' : '');
    btn.textContent = d;
    btn.onclick = ()=> { currentDaily = d; document.querySelectorAll('.day-button').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderDaily(); };
    container.appendChild(btn);
  });
}

function renderDaily(){
  const tbody = document.getElementById('dailyBody');
  tbody.innerHTML = '';
  const list = [];
  for(const key in parsedSchedules){
    const s = parsedSchedules[key];
    if(normalizeStr(s.Day) === normalizeStr(currentDaily)){
      list.push({key,s});
    }
  }
  if(list.length === 0){
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:12px;">No schedules for ${currentDaily}</td></tr>`;
    return;
  }
  list.forEach((it, idx)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${idx+1}</td>
      <td>${escapeHtml(it.s.Faculty)}</td>
      <td>${escapeHtml(it.s.Section)}</td>
      <td>${escapeHtml(it.s.Subject)}</td>
      <td>${escapeHtml(it.s.Room)}</td>
      <td>${escapeHtml(it.s.Time)}</td>
      <td>${escapeHtml(it.s.Mode)}</td>
      <td>
        <button class="btn edit" onclick="openEdit('${it.key}')">Edit</button>
        <button class="btn del" onclick="confirmDelete('${it.key}')">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function openAddModal(){
  document.getElementById('modalTitle').textContent = 'Add Schedule';
  document.getElementById('editKey').value = '';
  document.getElementById('form').reset();
  document.getElementById('linkField').style.display = 'none';
  document.getElementById('modal').style.display = 'flex';
}
function closeModal(){ document.getElementById('modal').style.display = 'none'; }
function modeChange(){
  const mode = document.getElementById('modeSelect').value;
  const div = document.getElementById('linkField');
  div.style.display = (mode === 'Online/Asynchronous') ? 'block' : 'none';
}
function openEdit(key){
  const s = parsedSchedules[key];
  if(!s) { showToast('Schedule not found', false); return; }
  document.getElementById('modalTitle').textContent = 'Edit Schedule';
  document.getElementById('editKey').value = key;
  document.getElementById('facultySelect').value = s.Faculty || '';
  document.getElementById('sectionInput').value = s.Section || '';
  document.getElementById('subjectInput').value = s.Subject || '';
  document.getElementById('roomInput').value = s.Room || '';
  document.getElementById('daySelect').value = s.Day || '';
  document.getElementById('timeSelect').value = s.Time || '';
  document.getElementById('modeSelect').value = s.Mode || '';
  document.getElementById('linkInput').value = s.Link || '';
  modeChange();
  document.getElementById('modal').style.display = 'flex';
}
function confirmDelete(key){
  if(!confirm('Delete schedule?')) return;
  performDelete(key);
}
async function performDelete(key){
  try {
    const res = await fetch(`${scheduleBase}/${encodeKey(key)}.json`, { method:'DELETE' });
    if(res.ok){
      showToast('Deleted', true);
     
    } else {
      showToast('Delete failed', false);
    }
  } catch(e){
    console.error('performDelete', e);
    showToast('Delete failed', false);
  }
}


async function saveSchedule(){
  const key = document.getElementById('editKey').value || null;
  const obj = {
    Faculty: document.getElementById('facultySelect').value.trim(),
    Section: document.getElementById('sectionInput').value.trim(),
    Subject: document.getElementById('subjectInput').value.trim(),
    Room: document.getElementById('roomInput').value.trim(),
    Day: document.getElementById('daySelect').value,
    Time: document.getElementById('timeSelect').value,
    Mode: document.getElementById('modeSelect').value,
    Link: document.getElementById('linkInput').value.trim()
  };
  if(!obj.Faculty || !obj.Day || !obj.Time || !obj.Room){
    showToast('Please fill required fields', false);
    return;
  }


  for(const k in parsedSchedules){
    if(k === key) continue;
    const s = parsedSchedules[k];
    if(normalizeStr(s.Room) === normalizeStr(obj.Room) && normalizeStr(s.Day) === normalizeStr(obj.Day) && normalizeStr(s.Time) === normalizeStr(obj.Time)){
      showToast('Room already assigned for that Day + Time.', false);
      return;
    }
  }

  try {
    if(!key){
      
      const r = await fetch(scheduleUrl, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(obj) });
      if(!r.ok) throw new Error('add failed');
      showToast('Added', true);
    } else {
     
      const r = await fetch(`${scheduleBase}/${encodeKey(key)}.json`, { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(obj) });
      if(!r.ok) throw new Error('update failed');
      showToast('Updated', true);
    }
    closeModal();
  } catch(e){
    console.error('saveSchedule', e);
    showToast('Save failed', false);
  }
}


function openViewModal(key){
  viewModalKey = key;
  const s = parsedSchedules[key] || {};
  document.getElementById('vFaculty').value = s.Faculty || '';
  document.getElementById('vSection').value = s.Section || '';
  document.getElementById('vSubject').value = s.Subject || '';
  document.getElementById('vRoom').value = s.Room || '';
  document.getElementById('vDay').value = s.Day || '';
  document.getElementById('vTime').value = s.Time || '';
  document.getElementById('vMode').value = s.Mode || '';
  document.getElementById('vLink').value = s.Link || '';
  document.getElementById('viewModal').style.display = 'flex';
}
function closeView(){ document.getElementById('viewModal').style.display = 'none'; viewModalKey = null; }
function openEditFromView(){ if(!viewModalKey) return; closeView(); openEdit(viewModalKey); }


(function setup(){

  currentMonth = monthRange.find(x=>x.y===2025 && x.m===9) || monthRange[0] || currentMonth;
  populateMonthSelect();


  document.getElementById('tabMonthly').onclick = ()=>{ document.getElementById('viewMonthly').style.display='block'; document.getElementById('viewWeekly').style.display='none'; document.getElementById('viewDaily').style.display='none'; document.getElementById('tabMonthly').classList.add('active'); document.getElementById('tabWeekly').classList.remove('active'); document.getElementById('tabDaily').classList.remove('active'); };
  document.getElementById('tabWeekly').onclick = ()=>{ document.getElementById('viewMonthly').style.display='none'; document.getElementById('viewWeekly').style.display='block'; document.getElementById('viewDaily').style.display='none'; document.getElementById('tabWeekly').classList.add('active'); document.getElementById('tabMonthly').classList.remove('active'); document.getElementById('tabDaily').classList.remove('active'); renderWeekly(); };
  document.getElementById('tabDaily').onclick = ()=>{ document.getElementById('viewMonthly').style.display='none'; document.getElementById('viewWeekly').style.display='none'; document.getElementById('viewDaily').style.display='block'; document.getElementById('tabDaily').classList.add('active'); document.getElementById('tabMonthly').classList.remove('active'); document.getElementById('tabWeekly').classList.remove('active'); buildDayButtons(); renderDaily(); };


  window.addEventListener('click', (e)=> { if(e.target.classList.contains('modal')) e.target.style.display='none'; });

 
  initData();
})();
</script>
</body>
</html>
