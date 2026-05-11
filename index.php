<?php
// ============================================================
//  index.php  —  Entry point — giữ nguyên HTML gốc,
//  chỉ thêm session start để PHP biết user đang đăng nhập
// ============================================================
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lớp Gia Sư Cầu Vồng</title>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- ============================================================
       NAV BAR
  ============================================================ -->
  <nav>
    <div class="logo">
      <div class="logo-icon"></div>
      Lớp Gia Sư Cầu Vồng
    </div>

    <div class="nav-links" id="navLinks">
      <button class="nav-link active" onclick="showPage('home')">Trang chủ</button>
      <button class="nav-link" onclick="showPage('classes')">Lớp học</button>
      <button class="nav-link" onclick="showPage('apply')">Ứng tuyển</button>
    </div>

    <div class="nav-actions" id="navActions">
      <button class="btn btn-outline" onclick="showPage('login')">Đăng nhập</button>
      <button class="btn btn-primary" onclick="showPage('register')">Đăng ký</button>
    </div>
  </nav>

  <!-- ============================================================
       PAGE: HOME
  ============================================================ -->
  <div id="page-home" class="page active">

    <div class="hero">
      <h1>Kết nối Giáo viên<br>&amp; Học sinh tài năng</h1>
      <div class="rainbow-bar"></div>
      <p>Nền tảng gia sư trực tuyến uy tín — đăng ký lớp học, gửi CV, đặt lịch phỏng vấn dễ dàng</p>
      <div class="hero-btns">
        <button class="btn-hero btn-hero-white" onclick="showPage('classes')">Xem lớp học</button>
        <button class="btn-hero btn-hero-outline" onclick="showPage('apply')">Ứng tuyển giáo viên</button>
      </div>
    </div>

    <div class="stats-bar">
      <div class="stat-item"><div class="stat-num" id="statStudents">…</div><div class="stat-label">Học sinh</div></div>
      <div class="stat-item"><div class="stat-num" id="statTeachers">…</div><div class="stat-label">Giáo viên</div></div>
      <div class="stat-item"><div class="stat-num" id="statClasses">…</div><div class="stat-label">Lớp đang mở</div></div>
      <div class="stat-item"><div class="stat-num">98%</div><div class="stat-label">Hài lòng</div></div>
    </div>

    <div class="section">
      <div class="section-title">Vì sao chọn chúng tôi?</div>
      <div class="section-sub">Hệ thống quản lý lớp học chuyên nghiệp, minh bạch</div>
      <div class="cards-grid">
        <div class="feature-card">
          <div class="feature-icon fi-green">📚</div>
          <h3>Lớp học đa dạng</h3>
          <p>Toán, Văn, Anh, Lý, Hóa... nhiều cấp độ từ Tiểu học đến THPT</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-orange">✅</div>
          <h3>Giáo viên được xét duyệt</h3>
          <p>Mọi giáo viên đều qua phỏng vấn và thẩm định kỹ năng chuyên môn</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-blue">📅</div>
          <h3>Lịch linh hoạt</h3>
          <p>Đặt lịch học online, chọn khung giờ phù hợp với bạn</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-red">🔒</div>
          <h3>Phân quyền rõ ràng</h3>
          <p>Admin, Giáo viên, Học sinh — mỗi vai trò có quyền hạn riêng</p>
        </div>
      </div>
    </div>

    <div style="background: var(--primary-light); padding: 3rem 2rem; text-align: center;">
      <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; margin-bottom: 0.5rem;">
          Bắt đầu ngay hôm nay
        </div>
        <p style="color: var(--text3); margin-bottom: 1.5rem;">
          Đăng ký tài khoản miễn phí, khám phá hàng chục lớp học chất lượng
        </p>
        <button class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;" onclick="showPage('register')">
          Tạo tài khoản ngay →
        </button>
      </div>
    </div>

  </div><!-- /#page-home -->

  <!-- ============================================================
       PAGE: CLASSES (public)
  ============================================================ -->
  <div id="page-classes" class="page">

    <div style="background: white; border-bottom: 1px solid var(--border); padding: 1.5rem 2rem;">
      <div style="max-width: 1100px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
          <div>
            <div style="font-size: 1.3rem; font-weight: 800;">Danh sách lớp học</div>
            <div style="font-size: 13px; color: var(--text3);">Tìm lớp phù hợp với bạn</div>
          </div>
          <div id="classCountBadge" style="font-size: 13px; color: var(--text3);"></div>
        </div>
        <!-- Search bar -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
          <div style="position: relative; flex: 1; min-width: 220px;">
            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 15px; pointer-events: none;">🔍</span>
            <input
              type="text"
              class="form-control"
              id="filterKeyword"
              placeholder="Tìm theo tên lớp, giáo viên, địa điểm…"
              style="padding-left: 36px;"
              oninput="filterClasses()"
            />
          </div>
          <select class="form-select" style="width: 140px;" id="filterSubject" onchange="filterClasses()">
            <option value="">Tất cả môn</option>
            <option value="Toán">Toán</option>
            <option value="Anh văn">Anh văn</option>
            <option value="Văn">Văn</option>
            <option value="Vật lý">Vật lý</option>
            <option value="Hóa học">Hóa học</option>
          </select>
          <select class="form-select" style="width: 140px;" id="filterLevel" onchange="filterClasses()">
            <option value="">Tất cả cấp độ</option>
            <option value="Tiểu học">Tiểu học</option>
            <option value="THCS">THCS</option>
            <option value="THPT">THPT</option>
          </select>
          <button class="btn btn-outline btn-sm" onclick="clearClassFilters()" style="white-space: nowrap;">✕ Xoá bộ lọc</button>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="class-grid" id="classGrid">
        <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải lớp học…</div>
      </div>
    </div>

  </div><!-- /#page-classes -->

  <!-- ============================================================
       PAGE: APPLY
  ============================================================ -->
  <div id="page-apply" class="page">

    <div style="background: linear-gradient(135deg, #185fa5, #378add); color: white; padding: 3rem 2rem; text-align: center;">
      <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 0.5rem;">Ứng tuyển Giáo viên</h2>
      <p style="opacity: 0.9;">Gửi CV và đặt lịch phỏng vấn ngay hôm nay</p>
    </div>

    <div class="section" style="max-width: 700px;">

      <div class="form-card">
        <div class="form-card-title">📄 Thông tin ứng viên</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Họ và tên *</label>
            <input type="text" class="form-control" id="applyName" placeholder="Nguyễn Văn A" />
          </div>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" id="applyEmail" placeholder="email@example.com" />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="tel" class="form-control" id="applyPhone" placeholder="0912345678" />
          </div>
          <div class="form-group">
            <label class="form-label">Môn dạy *</label>
            <select class="form-select" id="applySubject">
              <option value="">-- Chọn môn --</option>
              <option>Toán</option>
              <option>Anh văn</option>
              <option>Văn</option>
              <option>Vật lý</option>
              <option>Hóa học</option>
              <option>Sinh học</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Trình độ học vấn</label>
          <select class="form-select" id="applyEducation">
            <option>Sinh viên đại học</option>
            <option>Cử nhân</option>
            <option>Thạc sĩ</option>
            <option>Tiến sĩ</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Giới thiệu bản thân *</label>
          <textarea class="form-textarea" id="applyBio" placeholder="Kinh nghiệm dạy học, thành tích nổi bật..."></textarea>
        </div>
      </div>

      <div class="form-card">
        <div class="form-card-title">📎 Tải lên CV</div>
        <div class="file-upload" onclick="document.getElementById('cvFile').click()">
          <div class="file-upload-icon">📁</div>
          <p><strong>Nhấn để chọn file</strong> hoặc kéo thả vào đây</p>
          <p style="margin-top: 4px; font-size: 12px;">PDF, DOCX (tối đa 5MB)</p>
        </div>
        <input type="file" id="cvFile" style="display: none;" accept=".pdf,.doc,.docx" onchange="showFile(this)" />
        <div id="fileInfo" style="margin-top: 0.8rem; font-size: 13px; color: var(--primary); display: none;"></div>
      </div>

      <div class="form-card">
        <div class="form-card-title">📅 Đặt lịch phỏng vấn</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Ngày phỏng vấn *</label>
            <input type="date" class="form-control" id="interviewDate" />
          </div>
          <div class="form-group">
            <label class="form-label">Khung giờ *</label>
            <select class="form-select" id="applyTime">
              <option value="09:00">09:00 - 10:00</option>
              <option value="10:00">10:00 - 11:00</option>
              <option value="14:00">14:00 - 15:00</option>
              <option value="15:00">15:00 - 16:00</option>
              <option value="16:00">16:00 - 17:00</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Hình thức phỏng vấn</label>
          <select class="form-select" id="applyMode">
            <option>Online (Google Meet)</option>
            <option>Trực tiếp tại văn phòng</option>
          </select>
        </div>
      </div>

      <div id="applyError" class="alert alert-error" style="display:none;margin-bottom:1rem;"></div>

      <button class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 1rem;" onclick="submitApply()">
        Gửi hồ sơ ứng tuyển ✓
      </button>
      <div id="applySuccess" class="alert alert-success" style="display: none; margin-top: 1rem;">
        ✅ Hồ sơ đã gửi thành công! Chúng tôi sẽ liên hệ bạn trong 2–3 ngày làm việc.
      </div>

    </div>
  </div><!-- /#page-apply -->

  <!-- ============================================================
       PAGE: LOGIN
  ============================================================ -->
  <div id="page-login" class="page">
    <div class="auth-wrap">
      <div class="auth-card">
        <div class="auth-title">Đăng nhập</div>
        <div class="auth-sub">Chào mừng trở lại! Vui lòng đăng nhập.</div>

        <div class="alert alert-success" style="font-size: 12px; margin-bottom: 1rem;">
          <strong>Demo tài khoản:</strong><br />
          Admin: admin@rainbow.vn / 123456<br />
          Giáo viên: teacher@rainbow.vn / 123456<br />
          Học sinh: student@rainbow.vn / 123456
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" id="loginEmail" placeholder="email@example.com" value="admin@rainbow.vn" />
        </div>
        <div class="form-group">
          <label class="form-label">Mật khẩu</label>
          <input type="password" class="form-control" id="loginPwd" placeholder="••••••••" value="123456"
            onkeydown="if(event.key==='Enter') doLogin()" />
        </div>

        <div id="loginError" class="alert alert-error" style="display: none;">
          Email hoặc mật khẩu không đúng!
        </div>

        <button class="btn btn-primary" id="loginBtn"
          style="width: 100%; padding: 12px; font-size: 1rem; margin-top: 0.5rem;"
          onclick="doLogin()">
          Đăng nhập
        </button>

        <p style="text-align: center; margin-top: 1rem; font-size: 14px; color: var(--text3);">
          Chưa có tài khoản?
          <a href="#" class="form-link" onclick="showPage('register')">Đăng ký</a>
        </p>
      </div>
    </div>
  </div><!-- /#page-login -->

  <!-- ============================================================
       PAGE: REGISTER
  ============================================================ -->
  <div id="page-register" class="page">
    <div class="auth-wrap">
      <div class="auth-card">
        <div class="auth-title">Tạo tài khoản</div>
        <div class="auth-sub">Đăng ký để bắt đầu hành trình học tập</div>

        <div class="role-tabs">
          <button class="role-tab active" onclick="setRegRole('student', this)">Học sinh</button>
          <button class="role-tab" onclick="setRegRole('teacher', this)">Giáo viên</button>
        </div>

        <div class="form-group">
          <label class="form-label">Họ và tên *</label>
          <input type="text" class="form-control" id="regName" placeholder="Nguyễn Văn A" />
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input type="email" class="form-control" id="regEmail" placeholder="email@example.com" />
        </div>
        <div class="form-group">
          <label class="form-label">Mật khẩu * (tối thiểu 6 ký tự)</label>
          <input type="password" class="form-control" id="regPwd" placeholder="••••••••" />
        </div>

        <div class="form-group" id="subjectGroup" style="display: none;">
          <label class="form-label">Môn dạy</label>
          <select class="form-select" id="regSubject">
            <option>Toán</option>
            <option>Anh văn</option>
            <option>Văn</option>
            <option>Vật lý</option>
            <option>Hóa học</option>
          </select>
        </div>

        <div id="regError" class="alert alert-error" style="display:none; margin-bottom: 0.5rem;"></div>
        <div id="regSuccess" class="alert alert-success" style="display:none; margin-bottom: 0.5rem;"></div>

        <button class="btn btn-primary" id="regBtn"
          style="width: 100%; padding: 12px; font-size: 1rem; margin-top: 0.3rem;"
          onclick="doRegister()">
          Tạo tài khoản
        </button>

        <p style="text-align: center; margin-top: 1rem; font-size: 14px; color: var(--text3);">
          Đã có tài khoản?
          <a href="#" class="form-link" onclick="showPage('login')">Đăng nhập</a>
        </p>
      </div>
    </div>
  </div><!-- /#page-register -->

  <!-- ============================================================
       PAGE: DASHBOARD
  ============================================================ -->
  <div id="page-dashboard" class="page">
    <div class="dash-wrap">

      <div class="sidebar">
        <div style="padding: 0 1.5rem 1rem; border-bottom: 1px solid var(--border); margin-bottom: 0.5rem;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <div class="user-avatar" id="sideAvatar">A</div>
            <div>
              <div style="font-size: 13px; font-weight: 700;" id="sideUserName">…</div>
              <div id="sideRoleBadge"></div>
            </div>
          </div>
        </div>
        <ul class="sidebar-menu" id="sidebarMenu"></ul>
      </div>

      <div class="dash-content">

        <!-- ADMIN: Overview -->
        <div id="sec-overview" class="dash-section active">
          <div class="dash-title">📊 Tổng quan</div>
          <div class="stats-row" id="overviewStats">
            <div class="stat-card"><div class="stat-card-num">…</div><div class="stat-card-label">Tổng người dùng</div></div>
            <div class="stat-card"><div class="stat-card-num">…</div><div class="stat-card-label">Lớp đang hoạt động</div></div>
            <div class="stat-card"><div class="stat-card-num">…</div><div class="stat-card-label">Hồ sơ chờ duyệt</div></div>
            <div class="stat-card"><div class="stat-card-num">…</div><div class="stat-card-label">Phỏng vấn hôm nay</div></div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
              <div style="font-weight: 700; margin-bottom: 1rem; font-size: 14px;">Hoạt động gần đây</div>
              <div id="recentActivity"><div style="color:var(--text3);font-size:13px;">Đang tải…</div></div>
            </div>
            <div>
              <div style="font-weight: 700; margin-bottom: 1rem; font-size: 14px;">Phỏng vấn hôm nay</div>
              <div id="todayInterviews"><div style="color:var(--text3);font-size:13px;">Đang tải…</div></div>
            </div>
          </div>
        </div>

        <!-- ADMIN: Users -->
        <div id="sec-users" class="dash-section">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="dash-title" style="margin: 0;">👥 Quản lý người dùng</div>
            <button class="btn btn-primary btn-sm" onclick="showModal('addUser')">+ Thêm người dùng</button>
          </div>
          <div style="display:flex;gap:8px;margin-bottom:1rem;">
            <select class="form-select" style="width:140px;" onchange="loadUsers(this.value)">
              <option value="">Tất cả vai trò</option>
              <option value="admin">Admin</option>
              <option value="teacher">Giáo viên</option>
              <option value="student">Học sinh</option>
            </select>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Hành động</th></tr>
              </thead>
              <tbody id="userTable"><tr><td colspan="5" style="text-align:center;color:var(--text3);">Đang tải…</td></tr></tbody>
            </table>
          </div>
        </div>

        <!-- ADMIN: Classes -->
        <div id="sec-classes" class="dash-section">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="dash-title" style="margin: 0;">📚 Quản lý lớp học</div>
            <button class="btn btn-primary btn-sm" onclick="showModal('addClass')">+ Thêm lớp học</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Tên lớp</th><th>Môn</th><th>Giáo viên</th><th>Lịch</th><th>Sĩ số</th><th>Hành động</th></tr>
              </thead>
              <tbody id="classTable"><tr><td colspan="6" style="text-align:center;color:var(--text3);">Đang tải…</td></tr></tbody>
            </table>
          </div>
        </div>

        <!-- ADMIN: Applications -->
        <div id="sec-applications" class="dash-section">
          <div class="dash-title">📋 Hồ sơ ứng tuyển</div>
          <div class="tab-bar">
            <button class="tab-btn active" onclick="filterApps('all', this)">Tất cả</button>
            <button class="tab-btn" onclick="filterApps('pending', this)">Chờ duyệt</button>
            <button class="tab-btn" onclick="filterApps('interview', this)">Phỏng vấn</button>
            <button class="tab-btn" onclick="filterApps('approved', this)">Đã duyệt</button>
            <button class="tab-btn" onclick="filterApps('rejected', this)">Từ chối</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Ứng viên</th><th>Môn</th><th>Ngày ứng tuyển</th><th>Lịch PV</th><th>Trạng thái</th><th>Hành động</th></tr>
              </thead>
              <tbody id="appTable"><tr><td colspan="6" style="text-align:center;color:var(--text3);">Đang tải…</td></tr></tbody>
            </table>
          </div>
        </div>

        <!-- ADMIN: Interviews -->
        <div id="sec-interviews" class="dash-section">
          <div class="dash-title">📅 Lịch phỏng vấn</div>
          <div class="schedule-grid" id="interviewGrid"></div>
        </div>

        <!-- TEACHER: My classes -->
        <div id="sec-my-classes" class="dash-section">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="dash-title" style="margin: 0;">📚 Lớp học của tôi</div>
            <button class="btn btn-primary btn-sm" onclick="showModal('addClass')">+ Thêm lớp</button>
          </div>
          <div class="class-grid" id="myClassGrid">
            <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>
          </div>
        </div>

        <!-- TEACHER: Students -->
        <div id="sec-students" class="dash-section">
          <div class="dash-title">👨‍🎓 Học sinh của tôi</div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Học sinh</th><th>Lớp</th><th>Ngày tham gia</th></tr>
              </thead>
              <tbody id="myStudentTable"><tr><td colspan="3" style="text-align:center;color:var(--text3);">Đang tải…</td></tr></tbody>
            </table>
          </div>
        </div>

        <!-- TEACHER: Schedule -->
        <div id="sec-schedule" class="dash-section">
          <div class="dash-title">🗓️ Lịch dạy của tôi</div>
          <div class="schedule-grid" id="teacherSchedule"></div>
        </div>

        <!-- STUDENT: Enrolled -->
        <div id="sec-my-enrolled" class="dash-section">
          <div class="dash-title">📖 Lớp học đã đăng ký</div>
          <div id="enrolledList" class="class-grid">
            <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>
          </div>
        </div>

        <!-- STUDENT: Find class -->
        <div id="sec-find-class" class="dash-section">
          <div class="dash-title">🔍 Tìm lớp học</div>
          <div class="class-grid" id="findClassGrid">
            <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>
          </div>
        </div>

        <!-- ALL: Profile -->
        <div id="sec-profile" class="dash-section">
          <div class="dash-title">👤 Hồ sơ cá nhân</div>
          <div class="form-card" style="max-width: 600px;">
            <div class="form-card-title">Thông tin cơ bản</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="profileName" />
              </div>
              <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="profileEmail" readonly style="background:#f5f5f5;" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Số điện thoại</label>
              <input type="tel" class="form-control" id="profilePhone" placeholder="0912345678" />
            </div>
            <div class="form-group">
              <label class="form-label">Địa chỉ</label>
              <input type="text" class="form-control" id="profileAddress" placeholder="Hà Nội, Việt Nam" />
            </div>
            <div id="profileMsg" style="display:none;margin-bottom:0.5rem;"></div>
            <button class="btn btn-primary btn-sm" onclick="saveProfile()">Lưu thay đổi</button>
          </div>
        </div>

      </div><!-- /.dash-content -->
    </div><!-- /.dash-wrap -->
  </div><!-- /#page-dashboard -->

  <!-- ============================================================
       MODAL
  ============================================================ -->
  <div id="modalOverlay" class="modal-overlay" style="display: none;"
    onclick="if (event.target === this) closeModal()">
    <div class="modal">
      <div id="modalContent"></div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>