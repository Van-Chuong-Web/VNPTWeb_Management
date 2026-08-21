<?php
/**
 * forgot_password.php — Khôi phục mật khẩu Admin Panel (Giao diện 3 bước OTP)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu đã đăng nhập trước đó, chuyển hướng ngay về Admin
if (isset($_SESSION['admin_user']['email']) && !empty($_SESSION['admin_user']['email'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | VNPT Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .forgot-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3), 0 8px 10px -6px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .forgot-header {
            background: linear-gradient(135deg, #0d6efd, #1d4ed8);
            color: #fff;
            padding: 28px 24px;
            text-align: center;
        }
        .forgot-header .brand-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 12px;
        }
        .forgot-body { padding: 32px 28px; }
        .step-pill-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .step-pill {
            width: 32px; height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            transition: all .3s ease;
        }
        .step-pill.active {
            background: #0d6efd;
            width: 48px;
        }
        .otp-input-box {
            letter-spacing: 0.5em;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }
        .btn-primary-gradient {
            background: linear-gradient(135deg, #0d6efd, #1d4ed8);
            border: none;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
        }
        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }
    </style>
</head>
<body>

<div class="forgot-card">
    <div class="forgot-header">
        <div class="brand-icon"><i class="fa-solid fa-key"></i></div>
        <h5 class="fw-bold mb-1">Khôi Phục Mật Khẩu</h5>
        <p class="small mb-0 opacity-75">VNPT Admin Panel — Hệ thống Quản trị</p>
    </div>

    <div class="forgot-body">
        <!-- Step Indicators -->
        <div class="step-pill-container">
            <div class="step-pill active" id="pillStep1"></div>
            <div class="step-pill" id="pillStep2"></div>
            <div class="step-pill" id="pillStep3"></div>
        </div>

        <!-- Alert Message Container -->
        <div id="alertBox" class="alert d-none shadow-sm border-0 mb-3" style="border-radius: 10px;"></div>

        <!-- ── BƯỚC 1: NHẬP EMAIL ────────────────────────────────────────── -->
        <div id="step1">
            <div class="text-center mb-4">
                <h6 class="fw-bold text-dark mb-1">Bước 1: Nhập Email xác nhận</h6>
                <p class="text-muted small mb-0">Nhập email tài khoản nhân viên của bạn để nhận mã xác thực OTP 6 số.</p>
            </div>
            <form id="formSendOtp" onsubmit="event.preventDefault(); handleSendOtp();">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Địa chỉ Email Admin</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" id="emailInput" class="form-control border-start-0" placeholder="admin@vnpt.vn" required>
                    </div>
                </div>
                <button type="submit" id="btnSendOtp" class="btn btn-primary btn-primary-gradient w-100 mb-3">
                    <i class="fa-solid fa-paper-plane me-1"></i> Gửi Mã OTP Xác Thực
                </button>
            </form>
        </div>

        <!-- ── BƯỚC 2: NHẬP MÃ OTP ───────────────────────────────────────── -->
        <div id="step2" class="d-none">
            <div class="text-center mb-4">
                <h6 class="fw-bold text-dark mb-1">Bước 2: Nhập Mã OTP 6 Số</h6>
                <p class="text-muted small mb-0">Mã OTP đã được tạo cho email: <strong id="displayEmail" class="text-primary"></strong></p>
            </div>
            <form id="formVerifyOtp" onsubmit="event.preventDefault(); handleVerifyOtp();">
                <div class="mb-3">
                    <input type="text" id="otpInput" class="form-control otp-input-box font-monospace" maxlength="6" placeholder="000000" required>
                </div>
                <button type="submit" id="btnVerifyOtp" class="btn btn-primary btn-primary-gradient w-100 mb-2">
                    <i class="fa-solid fa-shield-check me-1"></i> Xác Nhận Mã OTP
                </button>
                <div class="text-center">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none" onclick="handleSendOtp()">
                        <i class="fa-solid fa-rotate me-1"></i>Gửi lại mã OTP mới
                    </button>
                </div>
            </form>
        </div>

        <!-- ── BƯỚC 3: ĐẶT MẬT KHẨU MỚI ──────────────────────────────────── -->
        <div id="step3" class="d-none">
            <div class="text-center mb-4">
                <h6 class="fw-bold text-dark mb-1">Bước 3: Đặt Mật Khẩu Mới</h6>
                <p class="text-muted small mb-0">Vui lòng tạo mật khẩu mới an toàn cho tài khoản của bạn.</p>
            </div>
            <form id="formResetPass" onsubmit="event.preventDefault(); handleResetPass();">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-secondary">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" id="newPassInput" class="form-control border-start-0" placeholder="••••••••" minlength="6" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-secondary">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" id="confirmPassInput" class="form-control border-start-0" placeholder="••••••••" minlength="6" required>
                    </div>
                </div>
                <button type="submit" id="btnResetPass" class="btn btn-success w-100 fw-bold py-2 mb-3" style="border-radius: 8px;">
                    <i class="fa-solid fa-check-circle me-1"></i> Lưu Mật Khẩu Mới
                </button>
            </form>
        </div>

        <hr class="my-4 opacity-10">

        <div class="text-center">
            <a href="login.php" class="text-decoration-none small text-secondary fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
            </a>
        </div>
    </div>
</div>

<script>
let currentEmail = '';
let currentOtp = '';

function showAlert(msg, type = 'danger') {
    const box = document.getElementById('alertBox');
    box.className = `alert alert-${type} shadow-sm border-0 mb-3`;
    box.innerHTML = msg;
    box.classList.remove('d-none');
}

function hideAlert() {
    document.getElementById('alertBox').classList.add('d-none');
}

function updateSteps(stepNumber) {
    document.getElementById('pillStep1').className = stepNumber >= 1 ? 'step-pill active' : 'step-pill';
    document.getElementById('pillStep2').className = stepNumber >= 2 ? 'step-pill active' : 'step-pill';
    document.getElementById('pillStep3').className = stepNumber >= 3 ? 'step-pill active' : 'step-pill';

    document.getElementById('step1').classList.toggle('d-none', stepNumber !== 1);
    document.getElementById('step2').classList.toggle('d-none', stepNumber !== 2);
    document.getElementById('step3').classList.toggle('d-none', stepNumber !== 3);
}

// 1. Gửi OTP
async function handleSendOtp() {
    hideAlert();
    const email = document.getElementById('emailInput').value.trim();
    if (!email) {
        showAlert('Vui lòng nhập Email.');
        return;
    }

    const btn = document.getElementById('btnSendOtp');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang khởi tạo OTP...';

    try {
        const res = await fetch('../backend/api/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'send_otp', email: email, type: 'admin' })
        });
        const data = await res.json();

        if (data.success) {
            currentEmail = email;
            document.getElementById('displayEmail').textContent = email;
            const otpNotice = data.otp ? `<br><small class="d-block mt-1 opacity-75">💡 Mã OTP xác thực môi trường Localhost: <strong class="fs-6 font-monospace text-dark" style="letter-spacing: 2px;">${data.otp}</strong></small>` : '';
            showAlert(data.message + otpNotice, 'info');
            updateSteps(2);
            document.getElementById('otpInput').value = '';
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (e) {
        showAlert('Lỗi kết nối máy chủ: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Gửi Mã OTP Xác Thực';
    }
}

// 2. Xác nhận OTP
async function handleVerifyOtp() {
    hideAlert();
    const otp = document.getElementById('otpInput').value.trim();
    if (!otp || otp.length !== 6) {
        showAlert('Vui lòng nhập đủ 6 số OTP.');
        return;
    }

    const btn = document.getElementById('btnVerifyOtp');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang xác minh...';

    try {
        const res = await fetch('../backend/api/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'verify_otp', email: currentEmail, otp: otp })
        });
        const data = await res.json();

        if (data.success) {
            currentOtp = otp;
            showAlert(data.message, 'success');
            updateSteps(3);
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (e) {
        showAlert('Lỗi kết nối máy chủ: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-shield-check me-1"></i> Xác Nhận Mã OTP';
    }
}

// 3. Đổi Mật Khẩu
async function handleResetPass() {
    hideAlert();
    const newPass = document.getElementById('newPassInput').value;
    const confirmPass = document.getElementById('confirmPassInput').value;

    if (newPass !== confirmPass) {
        showAlert('Mật khẩu xác nhận không trùng khớp.');
        return;
    }

    const btn = document.getElementById('btnResetPass');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang lưu mật khẩu...';

    try {
        const res = await fetch('../backend/api/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reset_password',
                email: currentEmail,
                otp: currentOtp,
                new_password: newPass
            })
        });
        const data = await res.json();

        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1800);
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (e) {
        showAlert('Lỗi kết nối máy chủ: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> Lưu Mật Khẩu Mới';
    }
}
</script>
</body>
</html>
