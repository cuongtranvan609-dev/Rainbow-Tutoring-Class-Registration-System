let tuiData = [];

function switchTuiTab(name) {
  document.querySelectorAll('.tui-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.tui-tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tui-tab-' + name).classList.add('active');
  event.target.classList.add('active');
  if (name === 'history') loadTuiHistory();
  if (name === 'payment') renderTuiPayment();
}

async function loadTuition() {
  if (currentRole === 'teacher') {
    document.getElementById('tabPaymentBtn').style.display = 'none';
  } else {
    document.getElementById('tabPaymentBtn').style.display = 'inline-block';
  }
  
  const res = await apiFetch('api/tuition.php');
  if (res.success) {
    tuiData = res.data;
    renderTuiStudents();
    renderTuiPayment();
    renderTuiStats();
  }
}

function fmt(n) { return Number(n||0).toLocaleString('vi-VN') + 'đ'; }
function getDebt(s) { return Math.max(0, s.price * s.sessions - s.paid); }
function getTotal(s) { return s.price * s.sessions; }
function getStatus(s) {
  const debt = getDebt(s);
  const total = getTotal(s);
  if (debt === 0 && total > 0) return 'paid';
  if (s.paid > 0 && debt > 0) return 'partial';
  return 'unpaid';
}
function tuiBadge(s) {
  const st = getStatus(s);
  if (st === 'paid') return '<span class="badge badge-active">✅ Đã trả đủ</span>';
  if (st === 'partial') return '<span class="badge badge-pending">⚠️ Một phần</span>';
  return '<span class="badge badge-danger">❌ Chưa trả</span>';
}

function renderTuiStats() {
  if (currentRole === 'teacher') {
    document.getElementById('tuiStatsRow').innerHTML = '';
    return;
  }
  const total = tuiData.length;
  const totalFee = tuiData.reduce((a, s) => a + getTotal(s), 0);
  const totalPaid = tuiData.reduce((a, s) => a + Number(s.paid || 0), 0);
  const totalDebt = tuiData.reduce((a, s) => a + getDebt(s), 0);
  
  document.getElementById('tuiStatsRow').innerHTML = `
    <div class="stat-card">
      <div class="stat-card-num">${total}</div>
      <div class="stat-card-label">Học sinh</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-num" style="color:var(--primary);">${fmt(totalFee)}</div>
      <div class="stat-card-label">Tổng học phí</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-num" style="color:var(--green);">${fmt(totalPaid)}</div>
      <div class="stat-card-label">Đã thanh toán</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-num" style="color:var(--red);">${fmt(totalDebt)}</div>
      <div class="stat-card-label">Còn công nợ</div>
    </div>
  `;
}

function renderTuiStudents() {
  const tbody = document.getElementById('tuiStudentTableBody');
  if (!tuiData.length) {
    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:2rem;">Chưa có dữ liệu học sinh</td></tr>';
    return;
  }
  
  tbody.innerHTML = tuiData.map(s => {
    const total = getTotal(s);
    const debt = getDebt(s);
    const isTeacherOrAdmin = (currentRole === 'teacher' || currentRole === 'admin');
    
    // Teacher and Admin can edit sessions
    const sessionsHtml = isTeacherOrAdmin 
      ? `<input class="sessions-input" type="number" value="${s.sessions}" min="0" max="${s.totalSessions}" onchange="updateSessions(${s.student_id}, ${s.class_id}, this.value)">`
      : `<span style="font-weight:600">${s.sessions}</span>`;
      
    // Action buttons based on role
    let actionHtml = '';
    if (currentRole === 'admin' || currentRole === 'student' || currentRole === 'parent') {
      actionHtml += `<button class="action-btn action-btn-primary" onclick="openPaymentModal(${s.student_id}, ${s.class_id})">Thanh toán</button>`;
    }
      
    return `<tr>
      <td>
        <div class="student-info">
          <div class="avatar ${s.avatar}">${s.name[0]}</div>
          <div>
            <div class="student-name">${s.name}</div>
            <div class="student-class">${s.parent ? ''+s.parent : ''} ${s.phone ? '· '+s.phone : ''}</div>
          </div>
        </div>
      </td>
      <td><div style="font-weight:600">${s.class}</div><div style="font-size:11px;color:var(--text3)">${s.subject}</div></td>
      <td><span style="font-weight:600">${fmt(s.price)}</span></td>
      <td style="text-align:center"><span style="font-weight:600">${s.totalSessions}</span></td>
      <td style="text-align:center">${sessionsHtml}</td>
      <td><span class="amount">${fmt(total)}</span></td>
      <td><span class="paid-amount">${fmt(s.paid)}</span></td>
      <td><span class="${debt>0 ? 'debt-amount' : 'paid-amount'}">${fmt(debt)}</span></td>
      <td>${tuiBadge(s)}</td>
      <td><div style="display:flex;gap:4px;">${actionHtml}</div></td>
    </tr>`;
  }).join('');
}

async function updateSessions(studentId, classId, sessions) {
  const res = await apiFetch('api/attendance.php', {
    method: 'POST',
    body: JSON.stringify({ student_id: studentId, class_id: classId, sessions: parseInt(sessions) })
  });
  if (res.success) {
    loadTuition(); // Reload data
  } else {
    alert(res.message);
  }
}

function renderTuiPayment() {
  const tbody = document.getElementById('tuiPaymentTableBody');
  const debtors = tuiData.filter(s => getDebt(s) > 0);
  if (!debtors.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;">🎉 Không có công nợ cần thanh toán</td></tr>';
    return;
  }
  
  tbody.innerHTML = debtors.map(s => {
    const total = getTotal(s);
    const debt = getDebt(s);
    return `<tr>
      <td>
        <div class="student-info">
          <div class="avatar ${s.avatar}">${s.name[0]}</div>
          <div><div class="student-name">${s.name}</div><div class="student-class">${s.subject} · ${s.class}</div></div>
        </div>
      </td>
      <td>${s.month}</td>
      <td style="text-align:center;font-weight:600">${s.sessions} buổi</td>
      <td><span class="amount">${fmt(total)}</span></td>
      <td><span class="paid-amount">${fmt(s.paid)}</span></td>
      <td><span class="${debt>0?'debt-amount':'paid-amount'}">${fmt(debt)}</span></td>
      <td>${tuiBadge(s)}</td>
      <td><button class="action-btn action-btn-primary" onclick="openPaymentModal(${s.student_id}, ${s.class_id})">Thanh toán</button></td>
    </tr>`;
  }).join('');
}

async function loadTuiHistory() {
  const res = await apiFetch('api/payments.php');
  const list = document.getElementById('tuiHistoryList');
  if (res.success && res.data.length) {
    list.innerHTML = res.data.map(h => {
      const isPaid = h.status === 'verified';
      let verifyBtn = '';
      if (currentRole === 'admin' && h.status === 'pending') {
        verifyBtn = `<button class="action-btn action-btn-primary" onclick="verifyPayment(${h.id})" style="margin-left: 10px;">Duyệt</button>`;
      }
      return `
      <div class="history-item">
        <div class="history-icon ${isPaid ? 'paid' : 'pending'}">${isPaid ? '✅' : '⏳'}</div>
        <div class="history-info">
          <div class="history-student">${h.studentName} - ${h.class_name || ''}</div>
          <div class="history-meta">${h.method} ${h.note ? '· '+h.note : ''}</div>
        </div>
        <div class="history-amount-col">
          <div class="history-amount ${isPaid ? 'paid-amount' : 'debt-amount'}">${fmt(h.amount)}</div>
          <div class="history-date">${fmtDate(h.date)}</div>
          ${verifyBtn}
        </div>
      </div>
      `;
    }).join('');
  } else {
    list.innerHTML = '<div style="text-align:center;padding:2rem;">Chưa có giao dịch nào</div>';
  }
}

let activePayment = null;
function openPaymentModal(studentId, classId) {
  activePayment = tuiData.find(x => x.student_id == studentId && x.class_id == classId);
  if (!activePayment) return;
  const debt = getDebt(activePayment);
  
  const html = `
    <div class="modal-title">💳 Thanh Toán Học Phí</div>
    <div style="background:var(--primary-light); border-radius:12px; padding:20px; margin-bottom:20px;">
      <div style="font-weight:700; font-size:16px;">${activePayment.name}</div>
      <div style="font-size:13px; color:var(--text3); margin-bottom:10px;">${activePayment.class} · ${activePayment.subject}</div>
      <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>Đã học:</span><span>${activePayment.sessions} buổi</span></div>
      <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>Tổng học phí:</span><span>${fmt(getTotal(activePayment))}</span></div>
      <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span>Đã trả:</span><span>${fmt(activePayment.paid)}</span></div>
      <div style="display:flex; justify-content:space-between; font-weight:700; font-size:16px; border-top:1px solid rgba(0,0,0,0.1); padding-top:10px; margin-top:10px; color:var(--red);"><span>Còn phải trả:</span><span>${fmt(debt)}</span></div>
    </div>
    
    <div class="form-group">
      <label class="form-label">Số tiền thanh toán (VNĐ)</label>
      <input type="number" class="form-control" id="payAmount" value="${debt}" min="0">
    </div>
    <div class="form-group" style="margin-top:10px;">
      <label class="form-label">Phương thức</label>
      <select class="form-select" id="payMethod">
        <option value="Chuyển khoản">🏦 Chuyển khoản ngân hàng</option>
        <option value="Tiền mặt">💵 Tiền mặt</option>
        <option value="MoMo">📱 Ví MoMo</option>
      </select>
    </div>
    <div class="form-group" style="margin-top:10px;">
      <label class="form-label">Ghi chú</label>
      <input type="text" class="form-control" id="payNote" placeholder="Thanh toán đợt 1...">
    </div>
    
    <div style="display:flex; gap:10px; margin-top:20px;">
      <button class="btn btn-outline" style="flex:1;" onclick="closeModal()">Hủy</button>
      <button class="btn btn-primary" style="flex:1;" onclick="submitPayment()">Xác nhận</button>
    </div>
  `;
  document.getElementById('modalContent').innerHTML = html;
  document.getElementById('modalOverlay').style.display = 'flex';
}

async function submitPayment() {
  const amount = document.getElementById('payAmount').value;
  const method = document.getElementById('payMethod').value;
  const note = document.getElementById('payNote').value;
  
  if (!amount || amount <= 0) {
    alert("Vui lòng nhập số tiền hợp lệ");
    return;
  }
  
  const res = await apiFetch('api/payments.php', {
    method: 'POST',
    body: JSON.stringify({
      student_id: activePayment.student_id,
      class_id: activePayment.class_id,
      amount: amount,
      method: method,
      note: note
    })
  });
  
  if (res.success) {
    alert(res.message);
    document.getElementById('modalOverlay').style.display = 'none';
    loadTuition();
  } else {
    alert(res.message);
  }
}

async function verifyPayment(id) {
  if (!confirm("Xác nhận đã nhận đủ tiền cho giao dịch này?")) return;
  const res = await apiFetch('api/payments.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'verify', id: id })
  });
  if (res.success) {
    loadTuiHistory();
    loadTuition(); // update stats
  }
}
