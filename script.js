/* =============================================================
   script.js  —  Lớp Gia Sư Cầu Vồng
   Phiên bản PHP + MySQL  (xóa toàn bộ mock data, dùng fetch API)
============================================================= */

/* ----------------------------------------------------------
   STATE
---------------------------------------------------------- */
let currentUser = null;
let currentRole = null;
let regRole     = 'student';
let allClasses  = [];   // cache lớp học public

/* ----------------------------------------------------------
   MENUS
---------------------------------------------------------- */
const MENUS = {
  admin: [
    { id: 'overview',     label: 'Tổng quan',        icon: '📊' },
    { id: 'users',        label: 'Người dùng',       icon: '👥' },
    { id: 'classes',      label: 'Lớp học',          icon: '📚' },
    { id: 'admin-requests', label: 'Duyệt yêu cầu', icon: '📩' },
    { id: 'applications', label: 'Hồ sơ ứng tuyển', icon: '📋' },
    { id: 'interviews',   label: 'Lịch phỏng vấn',  icon: '📅' },
    { id: 'tuition',      label: 'Học phí',          icon: '💰' },
    { id: 'profile',      label: 'Hồ sơ cá nhân',   icon: '👤' },
  ],
  teacher: [
    { id: 'my-classes', label: 'Lớp học của tôi', icon: '📚'  },
    { id: 'students',   label: 'Học sinh',         icon: '👨‍🎓' },
    { id: 'schedule',   label: 'Lịch dạy',         icon: '🗓️' },
    { id: 'tuition',    label: 'Điểm danh',        icon: '✅' },
    { id: 'profile',    label: 'Hồ sơ cá nhân',   icon: '👤'  },
  ],
  student: [
    { id: 'my-enrolled', label: 'Lớp đã đăng ký', icon: '📖' },
    { id: 'find-class',  label: 'Tìm lớp học',    icon: '🔍' },
    { id: 'teachers-dir',label: 'Kho giáo viên',  icon: '👩‍🏫' },
    { id: 'class-requests', label: 'Góc nhu cầu', icon: '💡' },
    { id: 'tuition',     label: 'Học phí',        icon: '💰' },
    { id: 'profile',     label: 'Hồ sơ cá nhân',  icon: '👤' },
  ],
  parent: [
    { id: 'teachers-dir',label: 'Kho giáo viên',  icon: '👩‍🏫' },
    { id: 'class-requests', label: 'Góc nhu cầu', icon: '💡' },
    { id: 'tuition',     label: 'Học phí con em', icon: '💰' },
    { id: 'profile',     label: 'Hồ sơ cá nhân',  icon: '👤' },
  ]
};

/* ----------------------------------------------------------
   HELPERS
---------------------------------------------------------- */
async function apiFetch(url, options = {}) {
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    ...options,
  });
  return res.json();
}

function fmtDate(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return isNaN(d) ? dateStr : d.toLocaleDateString('vi-VN');
}

function roleBadgeClass(role) {
  return { admin: 'badge-admin', teacher: 'badge-teacher', student: 'badge-student', parent: 'badge-parent' }[role] || '';
}
function roleLabel(role) {
  return { admin: 'Admin', teacher: 'Giáo viên', student: 'Học sinh', parent: 'Phụ huynh' }[role] || role;
}
function statusBadge(status) {
  const m = {
    active:    ['badge-active',  'Hoạt động'],
    pending:   ['badge-pending', 'Chờ duyệt'],
    interview: ['badge-done',    'Đang PV'],
    approved:  ['badge-active',  'Đã duyệt'],
    rejected:  ['badge-danger',  'Từ chối'],
  };
  return m[status] || ['', status];
}

/* ----------------------------------------------------------
   NAVIGATION
---------------------------------------------------------- */
function showPage(p) {
  document.querySelectorAll('.page').forEach(x => x.classList.remove('active'));
  const pg = document.getElementById('page-' + p);
  if (pg) pg.classList.add('active');

  if (p === 'classes')   renderPublicClasses();
  if (p === 'dashboard') {
    if (!currentUser) { showPage('login'); return; }
    renderDashboard();
  }

  document.querySelectorAll('.nav-link').forEach(x => x.classList.remove('active'));
}

/* ----------------------------------------------------------
   AUTH — LOGIN
---------------------------------------------------------- */
async function doLogin() {
  const email = document.getElementById('loginEmail').value.trim();
  const pwd   = document.getElementById('loginPwd').value;
  const errEl = document.getElementById('loginError');
  const btn   = document.getElementById('loginBtn');

  errEl.style.display = 'none';
  btn.disabled = true;
  btn.textContent = 'Đang đăng nhập…';

  try {
    const data = await apiFetch('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'login', email, password: pwd }),
    });

    if (data.success) {
      currentUser = data.user;
      currentRole = data.user.role;
      updateNav();
      showPage('dashboard');
    } else {
      errEl.textContent = data.message || 'Đăng nhập thất bại';
      errEl.style.display = 'block';
    }
  } catch (e) {
    errEl.textContent = 'Lỗi kết nối server';
    errEl.style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Đăng nhập';
  }
}

/* ----------------------------------------------------------
   AUTH — REGISTER
---------------------------------------------------------- */
async function doRegister() {
  const name  = document.getElementById('regName').value.trim();
  const email = document.getElementById('regEmail').value.trim();
  const phone = document.getElementById('regPhone').value.trim();
  const pwd   = document.getElementById('regPwd').value;
  const subject = regRole === 'teacher' ? document.getElementById('regSubject').value : '';
  const errEl = document.getElementById('regError');
  const sucEl = document.getElementById('regSuccess');
  const btn   = document.getElementById('regBtn');

  errEl.style.display = 'none';
  sucEl.style.display = 'none';

  if (!name || !email || !phone || !pwd) {
    errEl.textContent = 'Vui lòng điền đầy đủ thông tin!';
    errEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Đang xử lý…';

  try {
    const data = await apiFetch('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'register', name, email, phone, password: pwd, role: regRole, subject }),
    });

    if (data.success) {
      sucEl.textContent = regRole === 'teacher'
        ? '✅ Đăng ký thành công! Tài khoản giáo viên sẽ được admin duyệt.'
        : '✅ Đăng ký thành công! Bạn có thể đăng nhập ngay.';
      sucEl.style.display = 'block';
      if (regRole === 'student') {
        setTimeout(() => showPage('login'), 1800);
      }
    } else {
      errEl.textContent = data.message || 'Đăng ký thất bại';
      errEl.style.display = 'block';
    }
  } catch (e) {
    errEl.textContent = 'Lỗi kết nối server';
    errEl.style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Tạo tài khoản';
  }
}

/* ----------------------------------------------------------
   AUTH — LOGOUT
---------------------------------------------------------- */
async function logout() {
  await apiFetch('api/auth.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'logout' }),
  });
  currentUser = null;
  currentRole = null;
  updateNav();
  showPage('home');
}

/* ----------------------------------------------------------
   NAV UPDATE
---------------------------------------------------------- */
function updateNav() {
  const el = document.getElementById('navActions');
  if (!currentUser) {
    el.innerHTML = `
      <button class="btn btn-outline" onclick="showPage('login')">Đăng nhập</button>
      <button class="btn btn-primary" onclick="showPage('register')">Đăng ký</button>
    `;
    return;
  }
  const shortName = currentUser.name.split(' ').slice(-1)[0];
  el.innerHTML = `
    <div class="user-info" onclick="showPage('dashboard')" style="cursor:pointer;">
      <div class="user-avatar">${currentUser.name[0]}</div>
      <span style="font-size: 13px; font-weight: 600;">${shortName}</span>
      <span class="badge ${roleBadgeClass(currentRole)}">${roleLabel(currentRole)}</span>
    </div>
    <button class="btn btn-outline btn-sm" onclick="logout()">Đăng xuất</button>
  `;
}

/* ----------------------------------------------------------
   DASHBOARD
---------------------------------------------------------- */
function renderDashboard() {
  if (!currentUser) return;

  const shortName = currentUser.name.split(' ').slice(-1)[0];
  document.getElementById('sideAvatar').textContent  = currentUser.name[0];
  document.getElementById('sideUserName').textContent = shortName;
  document.getElementById('sideRoleBadge').innerHTML  =
    `<span class="badge ${roleBadgeClass(currentRole)}">${roleLabel(currentRole)}</span>`;

  // Fill profile form
  document.getElementById('profileName').value    = currentUser.name    || '';
  document.getElementById('profileEmail').value   = currentUser.email   || '';
  document.getElementById('profilePhone').value   = currentUser.phone   || '';
  document.getElementById('profileAddress').value = currentUser.address || '';

  // Sidebar
  const menu = MENUS[currentRole] || [];
  document.getElementById('sidebarMenu').innerHTML = menu.map((m, i) => `
    <li class="sidebar-item ${i === 0 ? 'active' : ''}" onclick="showSection('${m.id}', this)">
      <span style="font-size: 16px;">${m.icon}</span> ${m.label}
    </li>
  `).join('');

  // Activate first section
  document.querySelectorAll('.dash-section').forEach(x => x.classList.remove('active'));
  if (menu[0]) document.getElementById('sec-' + menu[0].id)?.classList.add('active');

  // Load data per role
  if (currentRole === 'admin') {
    loadOverview();
    loadUsers();
    loadAdminClasses();
    loadApplications();
    renderInterviewGrid();
  }
  if (currentRole === 'teacher') {
    loadMyClasses();
    loadMyStudents();
    renderInterviewGrid('teacherSchedule');
  }
  if (currentRole === 'student') {
    loadEnrolledClasses();
    loadFindClasses();
    if (typeof loadTuition === 'function') loadTuition();
  }
  if (currentRole === 'parent' || currentRole === 'admin' || currentRole === 'teacher') {
    if (typeof loadTuition === 'function') loadTuition();
  }
}

function showSection(id, el) {
  document.querySelectorAll('.sidebar-item').forEach(x => x.classList.remove('active'));
  el.classList.add('active');
  document.querySelectorAll('.dash-section').forEach(x => x.classList.remove('active'));
  document.getElementById('sec-' + id)?.classList.add('active');

  if (id === 'teachers-dir') loadTeachersDir();
  if (id === 'class-requests') loadClassRequests();
  if (id === 'admin-requests') loadAdminRequests();
}

/* ----------------------------------------------------------
   OVERVIEW (admin)
---------------------------------------------------------- */
async function loadOverview() {
  const [users, classes, apps] = await Promise.all([
    apiFetch('api/users.php'),
    apiFetch('api/classes.php'),
    apiFetch('api/applications.php'),
  ]);

  const today = new Date().toISOString().split('T')[0];
  const todayApps = (apps.applications || []).filter(a =>
    a.interview_date === today && a.status === 'interview');

  // Stats cards
  const cards = document.querySelectorAll('#overviewStats .stat-card .stat-card-num');
  if (cards[0]) cards[0].textContent = (users.users || []).length;
  if (cards[1]) cards[1].textContent = (classes.classes || []).length;
  if (cards[2]) cards[2].textContent = (apps.applications || []).filter(a => a.status === 'pending').length;
  if (cards[3]) cards[3].textContent = todayApps.length;

  // Recent activity
  const recent = (apps.applications || []).slice(0, 3);
  document.getElementById('recentActivity').innerHTML = recent.map(a => `
    <div class="notif">
      <div class="notif-dot"></div>
      <div class="notif-text">
        <h4>Hồ sơ mới từ ${a.name}</h4>
        <p>Ứng tuyển môn ${a.subject} • ${fmtDate(a.created_at)}</p>
      </div>
    </div>
  `).join('') || '<p style="color:var(--text3);font-size:13px;">Không có hoạt động</p>';

  // Today interviews
  document.getElementById('todayInterviews').innerHTML = todayApps.length
    ? todayApps.map(a => `
        <div class="interview-card">
          <div class="interview-info"><h4>${a.name}</h4><p>${a.interview_time} • ${a.subject}</p></div>
          <span class="badge badge-pending">Chờ</span>
        </div>
      `).join('')
    : '<p style="color:var(--text3);font-size:13px;">Không có phỏng vấn hôm nay</p>';

  // Home stats
  const students = (users.users || []).filter(u => u.role === 'student').length;
  const teachers = (users.users || []).filter(u => u.role === 'teacher').length;
  const el1 = document.getElementById('statStudents');
  const el2 = document.getElementById('statTeachers');
  const el3 = document.getElementById('statClasses');
  if (el1) el1.textContent = students + '+';
  if (el2) el2.textContent = teachers + '+';
  if (el3) el3.textContent = (classes.classes || []).length + '+';
}

/* ----------------------------------------------------------
   USERS TABLE (admin)
---------------------------------------------------------- */
async function loadUsers(role = '') {
  const url = role ? `api/users.php?role=${role}` : 'api/users.php';
  const data = await apiFetch(url);
  const el   = document.getElementById('userTable');
  if (!el) return;

  const users = data.users || [];
  if (!users.length) {
    el.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text3);">Không có dữ liệu</td></tr>';
    return;
  }

  el.innerHTML = users.map(u => `
    <tr>
      <td><strong>${u.name}</strong></td>
      <td style="color: var(--text3);">${u.email}</td>
      <td><span class="badge ${roleBadgeClass(u.role)}">${roleLabel(u.role)}</span></td>
      <td><span class="badge ${u.status === 'active' ? 'badge-active' : 'badge-pending'}">
        ${u.status === 'active' ? 'Hoạt động' : 'Chờ duyệt'}
      </span></td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button class="action-btn action-btn-primary" onclick="editUser(${u.id}, '${u.name}', '${u.role}', '${u.status}')">Sửa</button>
          <button class="action-btn action-btn-danger" onclick="deleteUser(${u.id}, '${u.name}')">Xoá</button>
        </div>
      </td>
    </tr>
  `).join('');
}

async function deleteUser(id, name) {
  if (!confirm(`Xoá người dùng "${name}"?`)) return;
  const data = await apiFetch(`api/users.php?id=${id}`, { method: 'DELETE' });
  if (data.success) loadUsers();
  else alert(data.error || 'Lỗi khi xoá');
}

function editUser(id, name, role, status) {
  showModal('editUser', { id, name, role, status });
}

/* ----------------------------------------------------------
   CLASSES TABLE (admin)
---------------------------------------------------------- */
async function loadAdminClasses() {
  const data = await apiFetch('api/classes.php');
  const el   = document.getElementById('classTable');
  if (!el) return;

  const classes = data.classes || [];
  if (!classes.length) {
    el.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text3);">Chưa có lớp học</td></tr>';
    return;
  }

  el.innerHTML = classes.map(c => `
    <tr>
      <td><strong>${c.name}</strong></td>
      <td>${c.subject}</td>
      <td>${c.teacher_name || '—'}</td>
      <td>${c.schedule || '—'}</td>
      <td>${c.enrolled}/${c.total_slots}</td>
      <td>
        <div style="display: flex; gap: 6px;">
          <button class="action-btn action-btn-primary" onclick="showModal('editClass', ${JSON.stringify(c).replace(/"/g,"'")})">Sửa</button>
          <button class="action-btn action-btn-danger" onclick="deleteClass(${c.id})">Xoá</button>
        </div>
      </td>
    </tr>
  `).join('');
}

async function deleteClass(id) {
  if (!confirm('Xoá lớp học này?')) return;
  const data = await apiFetch(`api/classes.php?id=${id}`, { method: 'DELETE' });
  if (data.success) loadAdminClasses();
  else alert(data.error || 'Lỗi khi xoá');
}

/* ----------------------------------------------------------
   APPLICATIONS TABLE (admin)
---------------------------------------------------------- */
let currentAppFilter = 'all';

async function loadApplications(filter) {
  if (filter !== undefined) currentAppFilter = filter;
  const url = currentAppFilter !== 'all'
    ? `api/applications.php?status=${currentAppFilter}`
    : 'api/applications.php';
  const data = await apiFetch(url);
  const el   = document.getElementById('appTable');
  if (!el) return;

  const apps = data.applications || [];
  if (!apps.length) {
    el.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text3);">Không có hồ sơ</td></tr>';
    return;
  }

  el.innerHTML = apps.map(a => {
    const [cls, lbl] = statusBadge(a.status);
    return `
      <tr>
        <td><strong>${a.name}</strong><br><small style="color:var(--text3);">${a.email}</small></td>
        <td>${a.subject}</td>
        <td>${fmtDate(a.created_at)}</td>
        <td>${a.interview_date ? fmtDate(a.interview_date) + ' ' + (a.interview_time || '') : 'Chưa đặt'}</td>
        <td><span class="badge ${cls}">${lbl}</span></td>
        <td>
          <div style="display: flex; gap: 4px; flex-wrap: wrap;">
            ${a.cv_file ? `<button class="action-btn action-btn-primary" onclick="window.open('uploads/${a.cv_file}')">Xem CV</button>` : ''}
            ${a.status === 'pending'   ? `<button class="action-btn action-btn-accent" onclick="updateApp(${a.id},'interview')">Mời PV</button>` : ''}
            ${a.status === 'interview' ? `<button class="action-btn action-btn-primary" onclick="updateApp(${a.id},'approved')">Duyệt</button>` : ''}
            ${a.status !== 'rejected' && a.status !== 'approved' ? `<button class="action-btn action-btn-danger" onclick="updateApp(${a.id},'rejected')">Từ chối</button>` : ''}
          </div>
        </td>
      </tr>
    `;
  }).join('');
}

async function updateApp(id, status) {
  const labels = { interview: 'mời phỏng vấn', approved: 'duyệt', rejected: 'từ chối' };
  if (!confirm(`Xác nhận ${labels[status]} hồ sơ này?`)) return;
  const data = await apiFetch('api/applications.php', {
    method: 'PUT',
    body: JSON.stringify({ id, status }),
  });
  if (data.success) loadApplications();
  else alert(data.error || 'Lỗi');
}

function filterApps(filter, el) {
  document.querySelectorAll('.tab-btn').forEach(x => x.classList.remove('active'));
  el.classList.add('active');
  loadApplications(filter);
}

/* ----------------------------------------------------------
   PUBLIC CLASS LIST
---------------------------------------------------------- */
async function renderPublicClasses() {
  const el = document.getElementById('classGrid');
  if (!el) return;

  el.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>';
  const data = await apiFetch('api/classes.php');
  allClasses = data.classes || [];

  // Sau khi cache xong, lọc ngay (giữ keyword/select hiện tại)
  _doFilterClasses();
}
/* ----------------------------------------------------------
   FILTER CLASSES — Tối ưu hiệu năng với Debounce & Virtual DOM
---------------------------------------------------------- */
let _filterTimer = null;

function filterClasses() {
  // Xóa bộ đếm thời gian cũ nếu người dùng vẫn đang gõ
  clearTimeout(_filterTimer);
  
  // Chỉ thực hiện tìm kiếm sau khi ngừng gõ 300ms
  _filterTimer = setTimeout(_doFilterClasses, 300);
}

function _doFilterClasses() {
  const kwInput = document.getElementById('filterKeyword');
  const sbInput = document.getElementById('filterSubject');
  const lvInput = document.getElementById('filterLevel');
  const el      = document.getElementById('classGrid');
  
  if (!el) return;

  const keyword = (kwInput?.value || '').toLowerCase().trim();
  const subject = (sbInput?.value || '');
  const level   = (lvInput?.value   || '');

  // Thực hiện lọc dữ liệu từ cache đã có (allClasses)
  const filtered = allClasses.filter(c => {
    const matchKeyword = !keyword
      || c.name.toLowerCase().includes(keyword)
      || (c.teacher_name || '').toLowerCase().includes(keyword)
      || (c.location || '').toLowerCase().includes(keyword);
    const matchSubject = !subject || c.subject === subject;
    const matchLevel   = !level   || c.level   === level;
    return matchKeyword && matchSubject && matchLevel;
  });

  // Cập nhật badge số lượng nếu có
  const badge = document.getElementById('classCountBadge');
  if (badge) {
    badge.textContent = keyword || subject || level
      ? `Tìm thấy ${filtered.length} kết quả`
      : `${allClasses.length} lớp học`;
  }

  // Hiển thị kết quả
  if (!filtered.length) {
    el.innerHTML = `
      <div style="text-align:center;padding:3rem;color:var(--text3);grid-column: 1/-1;">
        <div style="font-size:2.5rem;margin-bottom:1rem;">🔍</div>
        <div style="font-weight:600;">Không tìm thấy lớp nào phù hợp</div>
        <div style="font-size:13px;">Thử thay đổi từ khóa hoặc bộ lọc</div>
      </div>`;
    return;
  }

  // Sử dụng join để tạo chuỗi HTML duy nhất, tránh ghi vào innerHTML nhiều lần
  el.innerHTML = filtered.map(c => classCardHTML(c, true)).join('');
}

// Hàm reset bộ lọc nhanh
function clearClassFilters() {
  const inputs = ['filterKeyword', 'filterSubject', 'filterLevel'];
  inputs.forEach(id => {
    const item = document.getElementById(id);
    if (item) item.value = '';
  });
  _doFilterClasses(); // Thực hiện lọc lại ngay lập tức
}

/* ----------------------------------------------------------
   TEACHER: My classes
---------------------------------------------------------- */
async function loadMyClasses() {
  const el = document.getElementById('myClassGrid');
  if (!el || !currentUser) return;
  const data = await apiFetch(`api/classes.php?teacher_id=${currentUser.id}`);
  const classes = data.classes || [];
  el.innerHTML = classes.length
    ? classes.map(c => classCardHTML(c, false, 'teacher')).join('')
    : '<div style="text-align:center;padding:2rem;color:var(--text3);">Chưa có lớp nào</div>';
}

/* ----------------------------------------------------------
   TEACHER: My students
---------------------------------------------------------- */
async function loadMyStudents() {
  const el = document.getElementById('myStudentTable');
  if (!el || !currentUser) return;
  // Lấy danh sách lớp của giáo viên, rồi lấy enrollments
  const data = await apiFetch(`api/classes.php?teacher_id=${currentUser.id}`);
  const classes = data.classes || [];
  if (!classes.length) {
    el.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--text3);">Chưa có học sinh</td></tr>';
    return;
  }
  // Hiển thị placeholder tên lớp (enrollments API cần student_id, giáo viên xem qua lớp)
  el.innerHTML = classes.map(c => `
    <tr>
      <td colspan="3">
        <strong>📚 ${c.name}</strong>
        — ${c.enrolled}/${c.total_slots} học sinh
      </td>
    </tr>
  `).join('');
}

/* ----------------------------------------------------------
   STUDENT: Enrolled classes
---------------------------------------------------------- */
async function loadEnrolledClasses() {
  const el = document.getElementById('enrolledList');
  if (!el) return;
  const data = await apiFetch('api/enrollments.php');
  const classes = data.classes || [];
  el.innerHTML = classes.length
    ? classes.map(c => classCardHTML(c, false, 'enrolled')).join('')
    : '<div style="text-align:center;padding:2rem;color:var(--text3);">Bạn chưa đăng ký lớp nào.<br><a href="#" onclick="showSection(\'find-class\', document.querySelector(\'[onclick*=find-class]\'))">Tìm lớp học ngay →</a></div>';
}

/* ----------------------------------------------------------
   STUDENT: Find classes
---------------------------------------------------------- */
async function loadFindClasses() {
  const el = document.getElementById('findClassGrid');
  if (!el) return;
  const data = await apiFetch('api/classes.php');
  const classes = data.classes || [];
  el.innerHTML = classes.length
    ? classes.map(c => classCardHTML(c, true, 'enroll')).join('')
    : '<div style="text-align:center;padding:2rem;color:var(--text3);">Không có lớp nào</div>';
}

async function enrollClass(classId, className) {
  if (!currentUser) { showPage('login'); return; }
  if (!confirm(`Đăng ký lớp "${className}"?`)) return;
  const data = await apiFetch('api/enrollments.php', {
    method: 'POST',
    body: JSON.stringify({ class_id: classId }),
  });
  if (data.success) {
    alert('✅ Đăng ký thành công!');
    loadEnrolledClasses();
    loadFindClasses();
  } else {
    alert(data.error || 'Lỗi khi đăng ký');
  }
}

async function unenrollClass(classId, className) {
  if (!confirm(`Hủy đăng ký lớp "${className}"?`)) return;
  const data = await apiFetch(`api/enrollments.php?class_id=${classId}`, { method: 'DELETE' });
  if (data.success) { loadEnrolledClasses(); loadFindClasses(); }
  else alert(data.error || 'Lỗi');
}

/* ----------------------------------------------------------
   CLASS CARD HTML
---------------------------------------------------------- */
function classCardHTML(c, showEnroll, mode = 'view') {
  const total = c.total_slots ?? c.total ?? 1;
  const pct   = Math.round(((c.enrolled || 0) / total) * 100);
  const full  = pct >= 100;

  let footerBtn = '';
  if (mode === 'enroll') {
    footerBtn = full
      ? `<button class="btn btn-outline btn-sm" disabled>Đã đầy</button>`
      : `<button class="btn btn-primary btn-sm" onclick="enrollClass(${c.id}, '${c.name.replace(/'/g,"\\'")}')">Đăng ký</button>`;
  } else if (mode === 'enrolled') {
    footerBtn = `<button class="btn btn-outline btn-sm" onclick="unenrollClass(${c.id}, '${c.name.replace(/'/g,"\\'")}')">Hủy đăng ký</button>`;
  } else if (mode === 'teacher') {
    footerBtn = `<button class="btn btn-outline btn-sm" onclick="showModal('editClass', ${encodeClassForAttr(c)})">Chỉnh sửa</button>`;
  } else {
    footerBtn = `<button class="btn btn-outline btn-sm">Chi tiết</button>`;
  }

  return `
    <div class="class-card">
      <div class="class-card-header">
        <div class="class-level">${c.subject} • ${c.level}</div>
        <div class="class-name">${c.name}</div>
        <div class="class-teacher">GV: ${c.teacher_name || c.teacher || 'Chưa phân công'}</div>
      </div>
      <div class="class-card-body">
        <div class="class-info">
          <div class="class-info-row">📅 ${c.schedule || 'Chưa cập nhật'}</div>
          <div class="class-info-row">📍 ${c.location || 'Online'}</div>
          <div class="class-info-row">👥 ${c.enrolled || 0}/${total} học sinh</div>
        </div>
        <div class="slots-bar">
          <div class="slots-fill" style="width: ${pct}%;"></div>
        </div>
      </div>
      <div class="class-card-footer">
        <span class="badge ${full ? 'badge-done' : 'badge-active'}">${full ? 'Đã đầy' : 'Còn chỗ'}</span>
        ${footerBtn}
      </div>
    </div>
  `;
}

function encodeClassForAttr(c) {
  return "'" + JSON.stringify(c).replace(/'/g, "\\'").replace(/"/g, '&quot;') + "'";
}

/* ----------------------------------------------------------
   SCHEDULE GRID
---------------------------------------------------------- */
function renderInterviewGrid(targetId = 'interviewGrid') {
  const el = document.getElementById(targetId);
  if (!el) return;

  const days  = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
  const slots = ['08:00', '09:00', '10:00', '14:00', '15:00', '16:00'];

  el.innerHTML = days.map(d => `
    <div class="schedule-day">
      <div class="schedule-day-label">${d}</div>
      ${slots.map(s => `
        <div class="schedule-slot" onclick="this.classList.toggle('booked')" style="margin-bottom: 4px;">${s}</div>
      `).join('')}
    </div>
  `).join('');
}

/* ----------------------------------------------------------
   PROFILE SAVE
---------------------------------------------------------- */
async function saveProfile() {
  const name    = document.getElementById('profileName').value.trim();
  const phone   = document.getElementById('profilePhone').value.trim();
  const address = document.getElementById('profileAddress').value.trim();
  const msgEl   = document.getElementById('profileMsg');

  const data = await apiFetch('api/auth.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'update_profile', name, phone, address }),
  });

  msgEl.style.display = 'block';
  if (data.success) {
    msgEl.className = 'alert alert-success';
    msgEl.textContent = '✅ Đã lưu thay đổi!';
    currentUser.name = name;
    updateNav();
    document.getElementById('sideUserName').textContent = name.split(' ').slice(-1)[0];
    document.getElementById('sideAvatar').textContent = name[0];
  } else {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = data.error || 'Lỗi khi lưu';
  }
  setTimeout(() => { msgEl.style.display = 'none'; }, 3000);
}

async function changePassword() {
  const pwdCurrent = document.getElementById('pwdCurrent').value;
  const pwdNew     = document.getElementById('pwdNew').value;
  const pwdConfirm = document.getElementById('pwdConfirm').value;
  const msgEl      = document.getElementById('pwdMsg');

  if (!pwdCurrent || !pwdNew || !pwdConfirm) {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = 'Vui lòng nhập đầy đủ thông tin!';
    msgEl.style.display = 'block';
    return;
  }
  
  if (pwdNew !== pwdConfirm) {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = 'Mật khẩu xác nhận không khớp!';
    msgEl.style.display = 'block';
    return;
  }

  const data = await apiFetch('api/auth.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'change_password', pwdCurrent, pwdNew }),
  });

  msgEl.style.display = 'block';
  if (data.success) {
    msgEl.className = 'alert alert-success';
    msgEl.textContent = '✅ Đổi mật khẩu thành công!';
    document.getElementById('pwdCurrent').value = '';
    document.getElementById('pwdNew').value = '';
    document.getElementById('pwdConfirm').value = '';
  } else {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = data.error || 'Lỗi khi đổi mật khẩu';
  }
  setTimeout(() => { msgEl.style.display = 'none'; }, 3000);
}


/* ----------------------------------------------------------
   APPLY (ứng tuyển giáo viên)
---------------------------------------------------------- */
function showFile(input) {
  if (input.files.length > 0) {
    const el = document.getElementById('fileInfo');
    el.style.display = 'block';
    el.textContent   = '✅ File đã chọn: ' + input.files[0].name;
  }
}

async function submitApply() {
  const errEl = document.getElementById('applyError');
  const sucEl = document.getElementById('applySuccess');
  errEl.style.display = 'none';
  sucEl.style.display = 'none';

  const name    = document.getElementById('applyName').value.trim();
  const email   = document.getElementById('applyEmail').value.trim();
  const phone   = document.getElementById('applyPhone').value.trim();
  const subject = document.getElementById('applySubject').value;
  const edu     = document.getElementById('applyEducation').value;
  const bio     = document.getElementById('applyBio').value.trim();
  const intDate = document.getElementById('interviewDate').value;
  const intTime = document.getElementById('applyTime').value;
  const intMode = document.getElementById('applyMode').value;
  const cvFile  = document.getElementById('cvFile').files[0];

  if (!name || !email || !subject || !bio) {
    errEl.textContent = 'Vui lòng điền đầy đủ: tên, email, môn dạy và giới thiệu!';
    errEl.style.display = 'block';
    return;
  }

  // Dùng FormData để upload file
  const fd = new FormData();
  fd.append('name',           name);
  fd.append('email',          email);
  fd.append('phone',          phone);
  fd.append('subject',        subject);
  fd.append('education',      edu);
  fd.append('bio',            bio);
  fd.append('interview_date', intDate);
  fd.append('interview_time', intTime);
  fd.append('interview_mode', intMode);
  if (cvFile) fd.append('cv', cvFile);

  const res  = await fetch('api/applications.php', { method: 'POST', body: fd });
  const data = await res.json();

  if (data.success) {
    sucEl.style.display = 'block';
    // Reset form
    ['applyName','applyEmail','applyPhone','applyBio'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('applySubject').selectedIndex = 0;
    document.getElementById('cvFile').value = '';
    document.getElementById('fileInfo').style.display = 'none';
  } else {
    errEl.textContent   = data.error || 'Lỗi khi gửi hồ sơ';
    errEl.style.display = 'block';
  }
}

/* ----------------------------------------------------------
   REGISTER — toggle subject field
---------------------------------------------------------- */
function setRegRole(role, el) {
  regRole = role;
  document.querySelectorAll('.role-tab').forEach(x => x.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('subjectGroup').style.display = role === 'teacher' ? 'block' : 'none';
  const phoneLabel = document.getElementById('regPhoneLabel');
  if (phoneLabel) {
    phoneLabel.textContent = role === 'student' ? 'Số điện thoại phụ huynh *' : 'Số điện thoại của bạn *';
  }
}

/* ----------------------------------------------------------
   MODAL — add / edit class & user
---------------------------------------------------------- */
async function showModal(type, data) {
  const overlay = document.getElementById('modalOverlay');
  const content = document.getElementById('modalContent');
  let html = '';

  if (type === 'addClass') {
    html = await modalClassForm(null);
  } else if (type === 'editClass') {
    const c = (typeof data === 'string') ? JSON.parse(data.replace(/&quot;/g, '"')) : data;
    html = await modalClassForm(c);
  } else if (type === 'addUser') {
    html = modalUserForm(null);
  } else if (type === 'editUser') {
    html = modalUserForm(data);
  } else if (type === 'createRequest') {
    html = `
      <div class="modal-header">
        <h3 style="margin:0">Tạo yêu cầu mở lớp</h3>
        <button class="btn btn-outline btn-sm" onclick="closeModal()">Đóng</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Môn học *</label>
          <input type="text" id="reqSubject" class="form-control" placeholder="VD: Tiếng Anh giao tiếp" />
        </div>
        <div class="form-group">
          <label class="form-label">Khối lớp *</label>
          <input type="text" id="reqLevel" class="form-control" placeholder="VD: Lớp 5, Mất gốc" />
        </div>
        <div class="form-group">
          <label class="form-label">Hình thức *</label>
          <select id="reqFormat" class="form-control">
            <option value="Online">Online</option>
            <option value="Offline">Offline</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Mô tả / Yêu cầu thêm</label>
          <textarea id="reqNotes" class="form-control" rows="3" placeholder="VD: Lịch rảnh tối 3,5,7..."></textarea>
        </div>
        <button class="btn btn-primary" style="width:100%" onclick="submitRequest(${data || 'null'})">Gửi yêu cầu</button>
      </div>
    `;
  }

  content.innerHTML = html;
  overlay.style.display = 'flex';
}

function closeModal() {
  document.getElementById('modalOverlay').style.display = 'none';
}

async function modalClassForm(c) {
  const title = c ? 'Chỉnh sửa lớp học' : 'Thêm lớp học mới';
  const id    = c ? c.id : '';

  // Lấy danh sách giáo viên active
  let teacherOptions = '<option value="">— Chưa phân công —</option>';
  try {
    const res = await apiFetch('api/users.php?role=teacher');
    const teachers = (res.users || []).filter(u => u.status === 'active');
    teacherOptions += teachers.map(t =>
      `<option value="${t.id}" ${c && (c.teacher_id == t.id) ? 'selected' : ''}>${t.name}</option>`
    ).join('');
  } catch (e) { /* giữ nguyên placeholder nếu lỗi */ }

  return `
    <div class="modal-title">${title}<button class="modal-close" onclick="closeModal()">✕</button></div>
    <input type="hidden" id="mClassId" value="${id}" />
    <div class="form-group">
      <label class="form-label">Tên lớp *</label>
      <input type="text" class="form-control" id="mClassName" value="${c ? c.name : ''}" />
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Môn học</label>
        <select class="form-select" id="mClassSubject">
          ${['Toán','Anh văn','Vật lý','Văn','Hóa học','Sinh học'].map(s =>
            `<option ${c && c.subject === s ? 'selected' : ''}>${s}</option>`).join('')}
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Cấp độ</label>
        <select class="form-select" id="mClassLevel">
          ${['Tiểu học','THCS','THPT'].map(l =>
            `<option ${c && c.level === l ? 'selected' : ''}>${l}</option>`).join('')}
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Giáo viên phụ trách</label>
      <select class="form-select" id="mClassTeacher">
        ${teacherOptions}
      </select>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Sĩ số tối đa</label>
        <input type="number" class="form-control" id="mClassSlots" value="${c ? (c.total_slots||c.total||15) : 15}" min="1" />
      </div>
      <div class="form-group">
        <label class="form-label">Hình thức</label>
        <select class="form-select" id="mClassLocation">
          <option ${c && c.location === 'Online' ? 'selected' : ''}>Online</option>
          <option ${c && c.location === 'Trực tiếp' ? 'selected' : ''}>Trực tiếp</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Lịch học</label>
      <input type="text" class="form-control" id="mClassSchedule" value="${c ? (c.schedule||'') : ''}" placeholder="VD: T2,T4,T6 19:00" />
    </div>
    <div id="mClassMsg" style="display:none;margin-bottom:0.5rem;"></div>
    <div style="display: flex; gap: 0.8rem; margin-top: 0.5rem;">
      <button class="btn btn-primary" style="flex: 1;" onclick="saveClass()">Lưu lớp học</button>
      <button class="btn btn-outline" onclick="closeModal()">Huỷ</button>
    </div>
  `;
}

async function saveClass() {
  const id         = document.getElementById('mClassId').value;
  const name       = document.getElementById('mClassName').value.trim();
  const subject    = document.getElementById('mClassSubject').value;
  const level      = document.getElementById('mClassLevel').value;
  const slots      = document.getElementById('mClassSlots').value;
  const location   = document.getElementById('mClassLocation').value;
  const schedule   = document.getElementById('mClassSchedule').value.trim();
  const teacherVal = document.getElementById('mClassTeacher')?.value;
  const msgEl      = document.getElementById('mClassMsg');

  if (!name) { msgEl.className='alert alert-error'; msgEl.textContent='Vui lòng nhập tên lớp'; msgEl.style.display='block'; return; }

  const payload = {
    name, subject, level,
    total_slots: parseInt(slots),
    location, schedule,
    teacher_id: teacherVal ? parseInt(teacherVal) : null,
  };
  if (id) payload.id = parseInt(id);

  const data = await apiFetch('api/classes.php', {
    method: id ? 'PUT' : 'POST',
    body: JSON.stringify(payload),
  });

  if (data.success || data.id) {
    closeModal();
    loadAdminClasses();
    if (currentRole === 'teacher') loadMyClasses();
  } else {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = data.error || 'Lỗi khi lưu';
    msgEl.style.display = 'block';
  }
}

function modalUserForm(u) {
  const title = u ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới';
  return `
    <div class="modal-title">${title}<button class="modal-close" onclick="closeModal()">✕</button></div>
    <input type="hidden" id="mUserId" value="${u ? u.id : ''}" />
    <div class="form-group">
      <label class="form-label">Họ và tên *</label>
      <input type="text" class="form-control" id="mUserName" value="${u ? u.name : ''}" />
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Vai trò</label>
        <select class="form-select" id="mUserRole">
          ${['student','teacher','parent','admin'].map(r =>
            `<option value="${r}" ${u && u.role === r ? 'selected' : ''}>${roleLabel(r)}</option>`).join('')}
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" id="mUserStatus">
          <option value="active"  ${u && u.status === 'active'  ? 'selected' : ''}>Hoạt động</option>
          <option value="pending" ${u && u.status === 'pending' ? 'selected' : ''}>Chờ duyệt</option>
        </select>
      </div>
    </div>
    ${!u ? `<div class="form-group">
      <label class="form-label">Email *</label>
      <input type="email" class="form-control" id="mUserEmail" />
    </div>
    <div class="form-group">
      <label class="form-label">Mật khẩu tạm</label>
      <input type="text" class="form-control" id="mUserPwd" value="Rainbow@2026" />
    </div>` : ''}
    <div id="mUserMsg" style="display:none;margin-bottom:0.5rem;"></div>
    <div style="display: flex; gap: 0.8rem; margin-top: 0.5rem;">
      <button class="btn btn-primary" style="flex: 1;" onclick="saveUser()">Lưu</button>
      <button class="btn btn-outline" onclick="closeModal()">Huỷ</button>
    </div>
  `;
}

async function saveUser() {
  const id     = document.getElementById('mUserId')?.value;
  const name   = document.getElementById('mUserName').value.trim();
  const role   = document.getElementById('mUserRole').value;
  const status = document.getElementById('mUserStatus').value;
  const email  = document.getElementById('mUserEmail')?.value.trim();
  const pwd    = document.getElementById('mUserPwd')?.value;
  const msgEl  = document.getElementById('mUserMsg');

  if (!name) { msgEl.className='alert alert-error'; msgEl.textContent='Vui lòng nhập tên'; msgEl.style.display='block'; return; }

  const payload = id ? { id: parseInt(id), name, role, status } : { name, email, role, status, password: pwd };
  const data = await apiFetch('api/users.php', {
    method: id ? 'PUT' : 'POST',
    body: JSON.stringify(payload),
  });

  if (data.success || data.id) {
    closeModal();
    loadUsers();
  } else {
    msgEl.className = 'alert alert-error';
    msgEl.textContent = data.error || 'Lỗi khi lưu';
    msgEl.style.display = 'block';
  }
}

/* ============================================================
   CROWDSOURCING CLASS REQUESTS
============================================================ */
async function loadTeachersDir() {
  const grid = document.getElementById('teachersGrid');
  grid.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải...</div>';
  const data = await apiFetch('api/teachers.php?action=list');
  if (data.success && data.teachers.length) {
    grid.innerHTML = data.teachers.map(t => `
      <div class="class-card">
        <div class="class-card-body">
          <h3 class="class-name">${t.name}</h3>
          <p class="class-teacher">Giáo viên môn: ${t.subject}</p>
        </div>
        <div class="class-card-footer">
          <button class="btn btn-primary btn-sm" onclick="showModal('createRequest', ${t.id})">Yêu cầu mở lớp</button>
        </div>
      </div>
    `).join('');
  } else {
    grid.innerHTML = '<div style="text-align:center;grid-column:1/-1;">Chưa có giáo viên nào</div>';
  }
}

async function loadClassRequests() {
  const grid = document.getElementById('requestsGrid');
  grid.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải...</div>';
  const data = await apiFetch('api/requests.php?action=list');
  if (data.success && data.requests.length) {
    grid.innerHTML = data.requests.filter(r => r.status === 'pending').map(r => `
      <div class="class-card">
        <div class="class-card-body">
          <div class="class-level">${r.subject} - ${r.level}</div>
          <h3 class="class-name">Lớp ${r.subject} (${r.format})</h3>
          <p class="class-teacher">Người gửi: ${r.requester_name}</p>
          ${r.teacher_name ? `<p style="color:var(--accent);font-size:13px;font-weight:bold;">Đích danh: Cô/Thầy ${r.teacher_name}</p>` : ''}
          <div style="margin-top:10px; font-size:13px;">👍 <b>${r.vote_count}</b> người muốn học</div>
        </div>
        <div class="class-card-footer">
          ${r.has_voted > 0 ? 
            `<button class="btn btn-outline btn-sm" disabled>Đã Vote</button>` : 
            `<button class="btn btn-primary btn-sm" onclick="voteRequest(${r.id})">Tôi cũng muốn học</button>`
          }
        </div>
      </div>
    `).join('');
  } else {
    grid.innerHTML = '<div style="text-align:center;grid-column:1/-1;">Chưa có yêu cầu nào</div>';
  }
}

async function voteRequest(id) {
  const data = await apiFetch('api/requests.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'vote', request_id: id })
  });
  if (data.success) {
    alert('Đã vote thành công!');
    loadClassRequests();
  } else {
    alert(data.message);
  }
}

async function submitRequest(teacherId = null) {
  const subject = document.getElementById('reqSubject').value;
  const level = document.getElementById('reqLevel').value;
  const format = document.getElementById('reqFormat').value;
  const notes = document.getElementById('reqNotes').value;
  const data = await apiFetch('api/requests.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'create', subject, level, format, teacher_id: teacherId, notes })
  });
  if (data.success) {
    closeModal();
    loadClassRequests();
    alert('Yêu cầu mở lớp đã được gửi!');
  } else {
    alert(data.message);
  }
}

async function loadAdminRequests() {
  const tbody = document.getElementById('adminRequestsTable');
  const data = await apiFetch('api/requests.php?action=list');
  if (data.success) {
    const rows = data.requests.map(r => `
      <tr>
        <td>Lớp ${r.subject}</td>
        <td>${r.subject} - ${r.level}</td>
        <td>${r.requester_name}</td>
        <td style="font-weight:bold;color:var(--accent);">${r.vote_count}</td>
        <td>${r.teacher_name || 'Không'}</td>
        <td><span class="badge badge-${r.status === 'pending' ? 'pending' : (r.status === 'approved' ? 'active' : 'danger')}">${r.status}</span></td>
        <td>
          ${r.status === 'pending' ? `
            <button class="action-btn action-btn-primary" onclick="adminApproveRequest(${r.id}, ${r.teacher_id || 'null'})">Duyệt</button>
            <button class="action-btn action-btn-danger" onclick="adminRejectRequest(${r.id})">Từ chối</button>
          ` : ''}
        </td>
      </tr>
    `).join('');
    tbody.innerHTML = rows || '<tr><td colspan="7" style="text-align:center">Trống</td></tr>';
  }
}

async function adminApproveRequest(id, teacherId) {
  if (!confirm('Duyệt yêu cầu này và tự động tạo lớp?')) return;
  const data = await apiFetch('api/requests.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'approve', request_id: id, teacher_id: teacherId })
  });
  if (data.success) {
    loadAdminRequests();
    if(typeof loadAdminClasses === 'function') loadAdminClasses();
    alert('Đã tạo lớp thành công!');
  } else {
    alert(data.message);
  }
}

async function adminRejectRequest(id) {
  const reply = prompt('Lý do từ chối:');
  if (reply === null) return;
  const data = await apiFetch('api/requests.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'reject', request_id: id, reply })
  });
  if (data.success) {
    loadAdminRequests();
  } else {
    alert(data.message);
  }
}

function switchGuideTab(tab) {
  document.querySelectorAll('.guide-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.guide-section').forEach(sec => sec.classList.remove('active'));
  
  event.currentTarget.classList.add('active');
  document.getElementById('guide-' + tab).classList.add('active');
}

/* ----------------------------------------------------------
   INIT
---------------------------------------------------------- */
(async function init() {
  // Set min date cho lịch phỏng vấn
  const today  = new Date();
  const mm     = String(today.getMonth() + 1).padStart(2, '0');
  const dd     = String(today.getDate()).padStart(2, '0');
  const dateEl = document.getElementById('interviewDate');
  if (dateEl) dateEl.min = `${today.getFullYear()}-${mm}-${dd}`;

  // Kiểm tra session PHP còn hiệu lực không
  try {
    const data = await apiFetch('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'me' }),
    });
    if (data.success && data.user) {
      currentUser = data.user;
      currentRole = data.user.role;
      updateNav();
    }
  } catch (e) { /* chưa đăng nhập */ }

  renderPublicClasses();
})();