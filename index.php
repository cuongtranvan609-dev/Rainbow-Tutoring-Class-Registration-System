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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css?v=2.0" />
  <link rel="stylesheet" href="tuition.css" />
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
      <button class="nav-link" onclick="showPage('about')">Giới thiệu</button>
      <button class="nav-link" onclick="showPage('guide')">Hướng dẫn</button>
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
      <div class="hero-bg"></div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div>
          <h1>Đánh thức<br>tiềm năng học tập</h1>
          <p>Nền tảng gia sư trực tuyến hiện đại — đăng ký lớp học, quản lý lịch trình và phát triển tri thức trên cùng một không gian số.</p>
          <div class="hero-btns">
            <button class="btn-hero btn-hero-white" onclick="showPage('classes')">Khám phá lớp học</button>
            <button class="btn-hero btn-hero-outline" onclick="showPage('apply')">Trở thành Gia sư</button>
          </div>
        </div>
        <div>
          <!-- Placeholder if needed for right side or just balanced by overlay -->
        </div>
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

    <div style="background: linear-gradient(135deg, var(--primary-dark), var(--primary)); padding: 5rem 2rem; text-align: center; color: white;">
      <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">
          Bắt đầu ngay hôm nay
        </div>
        <p style="color: #d1d5db; margin-bottom: 2.5rem; font-size: 1.1rem;">
          Đăng ký tài khoản miễn phí, khám phá hàng chục lớp học chất lượng
        </p>
        <button class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;" onclick="showPage('register')">
          Tạo tài khoản ngay →
        </button>
      </div>
    </div>
    
  </div><!-- /#page-home -->

  <!-- ============================================================
       PAGE: ABOUT (GIỚI THIỆU)
  ============================================================ -->
  <div id="page-about" class="page">
    <div class="about-hero">
      <div class="about-hero-content">
        <h1>Hành trình thắp sáng tri thức</h1>
        <p>Không chỉ là một nền tảng gia sư, chúng tôi là người bạn đồng hành trên con đường chinh phục ước mơ của mỗi học sinh.</p>
      </div>
    </div>
    
    <div class="about-container">
      <div class="about-story">
        <div class="story-text">
          <h2>Câu chuyện của Cầu Vồng</h2>
          <p>Mỗi đứa trẻ là một hạt giống mang trong mình những tiềm năng vô hạn, và mỗi giáo viên là một người gieo trồng cần mẫn. Tuy nhiên, việc tìm được một "người lái đò" thực sự thấu hiểu và phù hợp không phải là điều dễ dàng.</p>
          <p>Hiểu được nỗi trăn trở của hàng ngàn phụ huynh, <strong>Lớp Gia Sư Cầu Vồng</strong> ra đời với một khát vọng duy nhất: Xây dựng một hệ sinh thái giáo dục tử tế, minh bạch và đầy cảm hứng. Chúng tôi ứng dụng công nghệ để kết nối nhanh chóng, nhưng giữ lại trọn vẹn giá trị nhân văn cốt lõi của giáo dục.</p>
          <p>Tại Cầu Vồng, sự an tâm của phụ huynh, nụ cười của học sinh và niềm tự hào của giáo viên chính là thước đo thành công duy nhất.</p>
        </div>
        <div class="story-image">
          <div class="image-placeholder">
            🌟 Môi trường tử tế, minh bạch và đầy cảm hứng
          </div>
        </div>
      </div>

      <div class="mission-vision">
        <div class="mv-card">
          <div class="mv-icon">🎯</div>
          <h3>Sứ mệnh</h3>
          <p>Xóa bỏ rào cản tìm kiếm gia sư, kiến tạo một môi trường học tập cá nhân hóa, giúp học sinh phát triển tối đa tiềm năng nội tại.</p>
        </div>
        <div class="mv-card">
          <div class="mv-icon">👁️</div>
          <h3>Tầm nhìn</h3>
          <p>Trở thành nền tảng giáo dục đáng tin cậy nhất Việt Nam, nơi mọi nỗ lực đều được đền đáp bằng sự tiến bộ thực sự.</p>
        </div>
        <div class="mv-card">
          <div class="mv-icon">❤️</div>
          <h3>Giá trị cốt lõi</h3>
          <p>Tận tâm – Minh bạch – Hiện đại. Chúng tôi cam kết chất lượng thông qua quy trình xét duyệt giáo viên nghiêm ngặt nhất.</p>
        </div>
      </div>
    </div>
  </div><!-- /#page-about -->

  <!-- ============================================================
       PAGE: CONTACT (LIÊN HỆ)
  ============================================================ -->
  <div id="page-contact" class="page">
    <div class="contact-hero">
      <h1>Liên hệ với chúng tôi</h1>
      <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn một cách tốt nhất. Vui lòng chọn kênh liên hệ phù hợp.</p>
    </div>
    
    <div class="contact-wrap">
      <div class="contact-cards">
        <div class="contact-card">
          <div class="contact-icon icon-fb">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
          </div>
          <h3>Liên hệ tư vấn</h3>
          <p>Đối với nhu cầu học tập, ứng tuyển làm gia sư hoặc sử dụng dịch vụ của Gia sư Cầu Vồng, quý phụ huynh và học viên vui lòng liên hệ qua trang Facebook chính thức.</p>
          <a href="#" class="contact-btn-fb">Nhắn tin trên Facebook</a>
        </div>
        
        <div class="contact-card">
          <div class="contact-icon icon-bug">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-8M5.5 10A6.5 6.5 0 0 1 18.5 10M12 2v2M4 7l2 2M20 7l-2 2M2 13h2M20 13h2M4 19l2-2M20 19l-2-2"></path><path d="M12 12a3 3 0 0 0-3 3v2a3 3 0 0 0 6 0v-2a3 3 0 0 0-3-3z"></path></svg>
          </div>
          <h3>Vấn đề về website</h3>
          <p>Bạn gặp lỗi, trang bị hỏng hoặc gặp khó khăn khi sử dụng website? Đừng lo lắng, hãy gửi email trực tiếp cho đội ngũ kỹ thuật để được hỗ trợ nhanh nhất.</p>
          <a href="mailto:cuongtranvan609@gmail.com" class="contact-btn-outline">Gửi email hỗ trợ</a>
          <div class="contact-email">cuongtranvan609@gmail.com</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       PAGE: GUIDE (HƯỚNG DẪN)
  ============================================================ -->
  <div id="page-guide" class="page">
    <div class="guide-hero">
      <div class="guide-hero-content">
        <h1>Cẩm nang Cầu Vồng</h1>
        <p>Hướng dẫn chi tiết từ A-Z giúp bạn sử dụng nền tảng một cách hiệu quả nhất.</p>
      </div>
    </div>

    <div class="guide-container">
      <div class="guide-tabs">
        <button class="guide-tab-btn active" onclick="switchGuideTab('parent')">👨‍👩‍👧 Dành cho Phụ huynh</button>
        <button class="guide-tab-btn" onclick="switchGuideTab('student')">👨‍🎓 Dành cho Học sinh</button>
        <button class="guide-tab-btn" onclick="switchGuideTab('teacher')">👨‍🏫 Dành cho Giáo viên</button>
      </div>

      <!-- TAB: PHỤ HUYNH -->
      <div id="guide-parent" class="guide-section active">
        <h2 class="guide-section-title">Quy trình tìm gia sư cho con</h2>
        <div class="guide-steps">
          <div class="guide-step">
            <div class="step-num">1</div>
            <div class="step-content">
              <h3>Khám phá kho giáo viên</h3>
              <p>Phụ huynh có thể xem hồ sơ chi tiết, kinh nghiệm và đánh giá của các thầy cô trên hệ thống để chọn ra người phù hợp nhất với con mình.</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">2</div>
            <div class="step-content">
              <h3>Tạo yêu cầu mở lớp</h3>
              <p>Gửi yêu cầu lớp học (kèm môn học, khối lớp) và có thể chỉ định đích danh giáo viên. Hệ thống sẽ gom nhóm các học sinh có cùng nhu cầu.</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">3</div>
            <div class="step-content">
              <h3>Thanh toán & Bắt đầu học</h3>
              <p>Sau khi Admin duyệt lớp, phụ huynh tiến hành thanh toán học phí qua ví điện tử hoặc chuyển khoản. Học sinh sẽ nhận được lịch học ngay lập tức.</p>
            </div>
          </div>
        </div>

        <h2 class="guide-section-title" style="margin-top: 3rem;">Câu hỏi thường gặp (FAQ)</h2>
        <div class="faq-list">
          <details class="faq-item">
            <summary>Làm sao để tôi biết giáo viên có chất lượng hay không?</summary>
            <div class="faq-answer">Tất cả giáo viên trên Cầu Vồng đều phải trải qua vòng phỏng vấn chuyên môn khắt khe và phải cung cấp bằng cấp chứng chỉ xác thực. Ngoài ra, bạn có thể xem các đánh giá từ phụ huynh khác.</div>
          </details>
          <details class="faq-item">
            <summary>Nếu con tôi nghỉ ốm thì có được học bù không?</summary>
            <div class="faq-answer">Có. Bạn vui lòng báo trước cho giáo viên ít nhất 12 tiếng. Hệ thống điểm danh sẽ ghi nhận và sắp xếp lịch học bù mà không mất thêm phí.</div>
          </details>
        </div>
      </div>

      <!-- TAB: HỌC SINH -->
      <div id="guide-student" class="guide-section">
        <h2 class="guide-section-title">Hướng dẫn học tập hiệu quả</h2>
        <div class="guide-steps">
          <div class="guide-step">
            <div class="step-num">1</div>
            <div class="step-content">
              <h3>Theo dõi lịch học</h3>
              <p>Đăng nhập vào hệ thống thường xuyên để xem thời khóa biểu trong phần "Lớp đã đăng ký". Đừng quên vào lớp đúng giờ nhé!</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">2</div>
            <div class="step-content">
              <h3>Tương tác với giáo viên</h3>
              <p>Trong quá trình học, nếu có bài tập khó, đừng ngại nhắn tin hỏi trực tiếp giáo viên. Các thầy cô luôn sẵn sàng hỗ trợ.</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">3</div>
            <div class="step-content">
              <h3>Bầu chọn lớp mới</h3>
              <p>Nếu em muốn học một môn nào đó mà chưa có lớp, hãy vào "Góc nhu cầu mở lớp" để gửi yêu cầu hoặc Vote cho yêu cầu của bạn bè.</p>
            </div>
          </div>
        </div>

        <h2 class="guide-section-title" style="margin-top: 3rem;">Câu hỏi thường gặp (FAQ)</h2>
        <div class="faq-list">
          <details class="faq-item">
            <summary>Em quên mật khẩu đăng nhập thì phải làm sao?</summary>
            <div class="faq-answer">Em có thể nhờ Phụ huynh liên hệ với Admin hoặc nhấn vào nút "Quên mật khẩu" ở màn hình đăng nhập để đặt lại mật khẩu mới.</div>
          </details>
        </div>
      </div>

      <!-- TAB: GIÁO VIÊN -->
      <div id="guide-teacher" class="guide-section">
        <h2 class="guide-section-title">Quy trình hợp tác giảng dạy</h2>
        <div class="guide-steps">
          <div class="guide-step">
            <div class="step-num">1</div>
            <div class="step-content">
              <h3>Tạo hồ sơ ứng tuyển</h3>
              <p>Đăng ký tài khoản Giáo viên và điền đầy đủ thông tin về trình độ, bằng cấp, môn học thế mạnh. Một hồ sơ đẹp sẽ thu hút nhiều phụ huynh.</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">2</div>
            <div class="step-content">
              <h3>Phỏng vấn chuyên môn</h3>
              <p>Admin sẽ xếp lịch phỏng vấn online hoặc offline để kiểm tra năng lực sư phạm. Vượt qua vòng này, hồ sơ của bạn sẽ được kích hoạt (Active).</p>
            </div>
          </div>
          <div class="guide-step">
            <div class="step-num">3</div>
            <div class="step-content">
              <h3>Nhận lớp & Báo cáo điểm danh</h3>
              <p>Bạn có thể chủ động nhận lớp từ "Góc nhu cầu". Sau mỗi buổi học, hãy nhớ điểm danh học sinh trên hệ thống để tính lương chính xác.</p>
            </div>
          </div>
        </div>

        <h2 class="guide-section-title" style="margin-top: 3rem;">Câu hỏi thường gặp (FAQ)</h2>
        <div class="faq-list">
          <details class="faq-item">
            <summary>Bao lâu thì tôi được thanh toán lương?</summary>
            <div class="faq-answer">Hệ thống sẽ tổng hợp số buổi dạy và tiến hành thanh toán lương qua chuyển khoản vào ngày mùng 5 đến mùng 10 hàng tháng.</div>
          </details>
          <details class="faq-item">
            <summary>Làm thế nào để được phụ huynh chỉ định đích danh?</summary>
            <div class="faq-answer">Hãy tối ưu hồ sơ cá nhân: cập nhật ảnh đại diện chuyên nghiệp, ghi rõ thành tích giảng dạy và luôn nhiệt tình trong quá trình dạy để nhận được đánh giá cao.</div>
          </details>
        </div>
      </div>
      
      <div class="guide-cta">
        <h3>Bạn vẫn còn thắc mắc?</h3>
        <p>Đội ngũ hỗ trợ của Cầu Vồng luôn sẵn sàng lắng nghe và giải đáp mọi vấn đề của bạn.</p>
        <button class="btn btn-primary" onclick="alert('Tính năng Live Chat đang được phát triển!')">💬 Chat với Admin ngay</button>
      </div>

    </div>
  </div><!-- /#page-guide -->

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

    <div style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 4rem 2rem; text-align: center;">
      <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.5rem; margin-bottom: 0.8rem;">Ứng tuyển Giáo viên</h2>
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

        <details class="demo-accounts-box">
          <summary class="demo-title">Xem tài khoản Demo (Mật khẩu chung: <strong>123456</strong>)</summary>
          <div class="demo-list">
            <div class="demo-item"><span>Admin</span> admin@rainbow.vn</div>
            <div class="demo-item"><span>Giáo viên</span> teacher@rainbow.vn</div>
            <div class="demo-item"><span>Học sinh</span> student@rainbow.vn</div>
            <div class="demo-item"><span>Phụ huynh</span> parent@rainbow.vn</div>
          </div>
        </details>

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
          <button class="role-tab" onclick="setRegRole('parent', this)">Phụ huynh</button>
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
          <label class="form-label" id="regPhoneLabel">Số điện thoại phụ huynh *</label>
          <input type="tel" class="form-control" id="regPhone" placeholder="0912345678" />
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
              <option value="parent">Phụ huynh</option>
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

        <!-- ALL: Tuition & Attendance -->
        <div id="sec-tuition" class="dash-section">
          <div class="dash-title">💰 Học Phí & Điểm Danh</div>
          
          <div class="tui-header-tabs" style="margin-bottom: 20px;">
            <button class="tui-tab-btn active" onclick="switchTuiTab('students')">👨🎓 Học Sinh</button>
            <button class="tui-tab-btn" onclick="switchTuiTab('payment')" id="tabPaymentBtn">💳 Thanh Toán</button>
            <button class="tui-tab-btn" onclick="switchTuiTab('history')">📋 Lịch Sử</button>
          </div>

          <!-- TAB: Học Sinh -->
          <div id="tui-tab-students" class="tui-section active">
            <div class="tui-stats-row" id="tuiStatsRow"></div>
            <div class="tui-table-wrapper">
              <div class="tui-table-header">
                <span class="tui-table-title">Danh sách</span>
              </div>
              <div style="overflow-x:auto">
                <table class="tui-table">
                  <thead>
                    <tr>
                      <th>Học sinh</th>
                      <th>Lớp / Môn</th>
                      <th>Giá/buổi</th>
                      <th style="text-align:center">Tổng buổi</th>
                      <th style="text-align:center">Đã học</th>
                      <th>Học phí</th>
                      <th>Đã trả</th>
                      <th>Còn nợ</th>
                      <th>Trạng thái</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody id="tuiStudentTableBody"></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- TAB: Thanh Toán -->
          <div id="tui-tab-payment" class="tui-section">
            <div class="tui-table-wrapper">
              <div class="tui-table-header">
                <span class="tui-table-title">Danh sách cần thanh toán</span>
              </div>
              <div style="overflow-x:auto">
                <table class="tui-table">
                  <thead>
                    <tr>
                      <th>Học sinh</th>
                      <th>Tháng</th>
                      <th>Đã học</th>
                      <th>Học phí</th>
                      <th>Đã trả</th>
                      <th>Còn nợ</th>
                      <th>Trạng thái</th>
                      <th>Thanh toán</th>
                    </tr>
                  </thead>
                  <tbody id="tuiPaymentTableBody"></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- TAB: Lịch Sử -->
          <div id="tui-tab-history" class="tui-section">
            <div id="tuiHistoryList"></div>
          </div>
        </div>

        <!-- PARENT/STUDENT: Teacher Directory -->
        <div id="sec-teachers-dir" class="dash-section">
          <div class="dash-title">👩‍🏫 Kho giáo viên</div>
          <div class="class-grid" id="teachersGrid">
            <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>
          </div>
        </div>

        <!-- PARENT/STUDENT: Class Requests -->
        <div id="sec-class-requests" class="dash-section">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="dash-title" style="margin: 0;">💡 Góc nhu cầu mở lớp</div>
            <button class="btn btn-primary btn-sm" onclick="showModal('createRequest')">+ Tạo yêu cầu mới</button>
          </div>
          <div class="class-grid" id="requestsGrid">
            <div style="text-align:center;padding:3rem;color:var(--text3);">Đang tải…</div>
          </div>
        </div>

        <!-- ADMIN: Review Requests -->
        <div id="sec-admin-requests" class="dash-section">
          <div class="dash-title">📩 Duyệt yêu cầu mở lớp</div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Yêu cầu</th><th>Môn / Lớp</th><th>Người gửi</th><th>Lượt Vote</th><th>Giáo viên yc</th><th>Trạng thái</th><th>Hành động</th></tr>
              </thead>
              <tbody id="adminRequestsTable"><tr><td colspan="7" style="text-align:center;color:var(--text3);">Đang tải…</td></tr></tbody>
            </table>
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
          <div class="form-card" style="max-width: 600px; margin-top: 1.5rem;">
            <div class="form-card-title">Đổi mật khẩu</div>
            <div class="form-group">
              <label class="form-label">Mật khẩu hiện tại</label>
              <input type="password" class="form-control" id="pwdCurrent" />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control" id="pwdNew" />
              </div>
              <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control" id="pwdConfirm" />
              </div>
            </div>
            <div id="pwdMsg" style="display:none;margin-bottom:0.5rem;"></div>
            <button class="btn btn-outline btn-sm" onclick="changePassword()">Cập nhật mật khẩu</button>
          </div>
        </div>

      </div><!-- /.dash-content -->
    </div><!-- /.dash-wrap -->
  </div><!-- /#page-dashboard -->

  <!-- ============================================================
       PAGE: TERMS OF SERVICE
  ============================================================ -->
  <div id="page-terms" class="page">
    <div class="legal-wrap">
      <h1>Điều khoản Dịch vụ</h1>
      <p class="legal-date">Cập nhật lần cuối: 23/05/2026</p>
      
      <div class="legal-content">
        <h2>1. Giới thiệu</h2>
        <p>Chào mừng bạn đến với Lớp Gia sư Cầu Vồng. Bằng việc truy cập và sử dụng nền tảng của chúng tôi, bạn đồng ý tuân thủ các điều khoản và điều kiện dưới đây.</p>
        
        <h2>2. Tài khoản người dùng</h2>
        <ul>
          <li>Bạn cam kết cung cấp thông tin chính xác và đầy đủ khi đăng ký tài khoản.</li>
          <li>Bạn chịu trách nhiệm bảo mật thông tin đăng nhập của mình (bao gồm cả các tài khoản phụ huynh/học sinh liên kết).</li>
          <li>Nền tảng có quyền khóa tài khoản nếu phát hiện hành vi gian lận, vi phạm đạo đức học đường hoặc cung cấp thông tin sai lệch.</li>
        </ul>
        
        <h2>3. Quyền và Trách nhiệm của Giáo viên</h2>
        <p>Giáo viên trên nền tảng Cầu Vồng phải đảm bảo truyền đạt kiến thức chuẩn xác, tuân thủ đúng thời gian biểu đã cam kết và duy trì thái độ sư phạm chuẩn mực. Nền tảng sẽ tạm giữ học phí và chỉ thanh toán cho giáo viên sau khi hoàn thành các buổi học theo đúng thỏa thuận.</p>
        
        <h2>4. Đăng ký và Thanh toán Học phí</h2>
        <p>Học sinh hoặc phụ huynh có trách nhiệm đóng học phí theo đúng thông báo của mỗi lớp. Học phí sau khi đã nộp sẽ không được hoàn trả trừ trường hợp lớp học bị hủy do lỗi từ phía trung tâm hoặc giáo viên.</p>
        
        <h2>5. Yêu cầu mở lớp mới (Crowdsourcing)</h2>
        <p>Mọi yêu cầu mở lớp đều phải tuân thủ chuẩn mực giáo dục. Quản trị viên (Admin) có toàn quyền quyết định việc duyệt, hủy bỏ hoặc điều chỉnh yêu cầu mở lớp để phù hợp với định hướng của trung tâm.</p>
      </div>
    </div>
  </div>

  <!-- ============================================================
       PAGE: PRIVACY POLICY
  ============================================================ -->
  <div id="page-privacy" class="page">
    <div class="legal-wrap">
      <h1>Chính sách Bảo mật</h1>
      <p class="legal-date">Cập nhật lần cuối: 23/05/2026</p>
      
      <div class="legal-content">
        <h2>1. Mục đích thu thập thông tin</h2>
        <p>Lớp Gia sư Cầu Vồng thu thập thông tin cá nhân (Họ tên, số điện thoại, email) nhằm mục đích:</p>
        <ul>
          <li>Xác thực danh tính và hỗ trợ đăng nhập, khôi phục tài khoản.</li>
          <li>Liên lạc thông báo lịch học, thay đổi lớp, thông báo đóng học phí.</li>
          <li>Cải thiện trải nghiệm nền tảng và cung cấp các lớp học phù hợp với nhu cầu.</li>
        </ul>
        
        <h2>2. Bảo mật thông tin</h2>
        <p>Chúng tôi cam kết sử dụng các biện pháp kỹ thuật và tổ chức phù hợp để bảo vệ thông tin cá nhân của bạn khỏi truy cập trái phép, tiết lộ hoặc phá hoại.</p>
        
        <h2>3. Chia sẻ thông tin</h2>
        <p>Chúng tôi tuyệt đối không bán, trao đổi hoặc cho thuê thông tin cá nhân của bạn cho bên thứ ba. Thông tin chỉ có thể được chia sẻ nội bộ giữa Giáo viên và Học sinh (để phục vụ việc liên lạc học tập) hoặc theo yêu cầu hợp pháp từ cơ quan nhà nước.</p>
        
        <h2>4. Quyền của người dùng</h2>
        <p>Bạn có quyền truy cập, chỉnh sửa hoặc yêu cầu xóa bỏ thông tin cá nhân của mình thông qua phần <strong>Hồ sơ cá nhân</strong> hoặc bằng cách liên hệ với đội ngũ hỗ trợ kỹ thuật của chúng tôi.</p>
      </div>
    </div>
  </div>

  <!-- ============================================================
       FOOTER
  ============================================================ -->
  <footer class="site-footer">
    <div class="footer-left">
      &copy; 2026 Lớp Gia sư Cầu Vồng. Bản quyền thuộc về Lớp Gia sư Cầu Vồng.
    </div>
    <div class="footer-right">
      <a href="#" onclick="showPage('about'); return false;">Giới thiệu</a>
      <a href="#" onclick="showPage('contact'); return false;">Liên hệ</a>
      <a href="#" onclick="showPage('terms'); return false;">Điều khoản Dịch vụ</a>
      <a href="#" onclick="showPage('privacy'); return false;">Chính sách Bảo mật</a>
    </div>
  </footer>

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
  <script src="tuition.js"></script>
</body>
</html>