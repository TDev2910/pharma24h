# Firebase OTP - Tài liệu cấu hình và triển khai

## 📋 Mục lục
1. [Tại sao sử dụng Firebase OTP?](#tại-sao-sử-dụng-firebase-otp)
2. [Cấu hình Firebase trong dự án](#cấu-hình-firebase-trong-dự-án)
3. [Các file đảm nhận chức năng OTP](#các-file-đảm-nhận-chức-năng-otp)
4. [Luồng hoạt động](#luồng-hoạt-động)
5. [Cấu hình chi tiết](#cấu-hình-chi-tiết)

---

## 🎯 Tại sao sử dụng Firebase OTP?

### 1. **Xác thực số điện thoại an toàn**
- Firebase Authentication cung cấp dịch vụ xác thực số điện thoại qua SMS OTP
- Đảm bảo người dùng sở hữu số điện thoại họ đăng ký
- Bảo mật cao với mã OTP 6 số, có thời hạn 1-2 phút

### 2. **Tích hợp dễ dàng**
- Không cần tự xây dựng hệ thống gửi SMS
- Firebase xử lý việc gửi SMS tự động
- Hỗ trợ nhiều quốc gia, bao gồm Việt Nam (+84)

### 3. **Trong dự án hiện tại**
Firebase OTP được sử dụng cho chức năng **Quên mật khẩu** qua số điện thoại:
- Người dùng quên mật khẩu có thể chọn xác thực qua Email hoặc Số điện thoại
- Nếu chọn số điện thoại, hệ thống sẽ gửi OTP qua Firebase
- Sau khi xác thực thành công, người dùng có thể đặt lại mật khẩu

### 4. **Lợi ích**
- ✅ Không cần tích hợp nhà cung cấp SMS riêng
- ✅ Tự động xử lý rate limiting và bảo mật
- ✅ Hỗ trợ reCAPTCHA để chống spam
- ✅ Miễn phí với hạn mức nhất định (Blaze plan)

---

## ⚙️ Cấu hình Firebase trong dự án

### 1. **Cấu hình Firebase (File chính)**

**File:** `resources/js/library/firebase.js`

```javascript
const firebaseConfig = {
  apiKey: "AIzaSyBCOzuBfeEwhs1Ybnn2Q9hFoPf2NnSDKuE",
  authDomain: "pharma24h-f0cd2.firebaseapp.com",
  projectId: "pharma24h-f0cd2",
  storageBucket: "pharma24h-f0cd2.firebasestorage.app",
  messagingSenderId: "989050282805",
  appId: "1:989050282805:web:cecb5ec012be833b67227c",
  measurementId: "G-21D5BKCZX2"
};
```

**Cách lấy thông tin cấu hình:**
1. Truy cập [Firebase Console](https://console.firebase.google.com/)
2. Chọn project `pharma24h-f0cd2`
3. Vào **Project Settings** > **General**
4. Scroll xuống phần **Your apps** > chọn Web app
5. Copy các giá trị trong `firebaseConfig`

### 2. **Cài đặt package**

**File:** `package.json`

```json
{
  "dependencies": {
    "firebase": "^12.4.0"
  }
}
```

**Cài đặt:**
```bash
npm install firebase
```

### 3. **Import trong ứng dụng**

**File:** `resources/js/app.js`

```javascript
// Import Firebase modules
import './library/firebase'
import './library/firebasePhoneAuth'
```

---

## 📁 Các file đảm nhận chức năng OTP

### **Frontend (JavaScript/Vue)**

#### 1. **`resources/js/library/firebase.js`**
- **Chức năng:** Khởi tạo Firebase App và Authentication
- **Nhiệm vụ:**
  - Cấu hình Firebase với credentials
  - Export `auth` object để sử dụng trong các module khác

#### 2. **`resources/js/library/firebasePhoneAuth.js`**
- **Chức năng:** Service chính xử lý OTP qua số điện thoại
- **Các phương thức chính:**
  - `initRecaptcha(containerId)` - Khởi tạo reCAPTCHA verifier
  - `sendOTP(phoneNumber)` - Gửi mã OTP đến số điện thoại
  - `verifyOTP(otpCode)` - Xác thực mã OTP
  - `formatPhoneNumber(phoneNumber)` - Format số điện thoại Việt Nam (+84)
  - `checkRateLimit()` - Kiểm tra giới hạn số lần gửi
  - `isOTPExpired()` - Kiểm tra OTP hết hạn

**Đặc điểm:**
- Rate limiting: Tối đa 3 lần trong 1 phút
- OTP expiry: 2 phút
- Retry logic: Tự động retry 3 lần khi gặp lỗi 503
- Lưu confirmationResult vào localStorage để khôi phục sau reload

#### 3. **`resources/views/auth/passwords/email.blade.php`**
- **Chức năng:** Form quên mật khẩu (Email/Phone)
- **Nhiệm vụ:**
  - Hiển thị form cho phép chọn Email hoặc Số điện thoại
  - Xử lý submit form cho số điện thoại
  - Gọi `firebasePhoneAuth.sendOTP()` để gửi OTP
  - Redirect đến trang verify OTP

#### 4. **`resources/views/auth/passwords/verify-phone.blade.php`**
- **Chức năng:** Form nhập và xác thực mã OTP
- **Nhiệm vụ:**
  - Hiển thị 6 input box để nhập mã OTP
  - Gọi `firebasePhoneAuth.verifyOTP()` để xác thực
  - Gửi uid và idToken lên backend sau khi xác thực thành công
  - Xử lý resend OTP
  - Hiển thị countdown timer

### **Backend (PHP/Laravel)**

#### 5. **`app/Http/Controllers/Auth/ForgotPasswordController.php`**
- **Chức năng:** Controller xử lý quên mật khẩu
- **Các phương thức liên quan Firebase OTP:**

  **a. `showPhoneVerifyForm(Request $request)`**
  - Hiển thị form nhập OTP
  
  **b. `verifyPhoneOtp(Request $request)`**
  - Nhận uid và idToken từ frontend (nếu OTP đúng)
  - Nhận otp từ frontend (nếu OTP sai) để đếm attempts
  - Lưu thông tin xác thực vào session
  - Rate limiting: Tối đa 3 lần nhập sai
  
  **c. `savePhoneToSession(Request $request)`**
  - Lưu số điện thoại vào session trước khi redirect
  - Rate limiting: Tối đa 3 lần trong 1 giờ
  
  **d. `resetPhoneOtpAttempts(Request $request)`**
  - Reset số lần thử khi gửi lại OTP
  
  **e. `handlePhoneVerification(Request $request)`**
  - Xử lý xác thực phone từ Firebase (AJAX)
  - Lưu uid, idToken vào session

#### 6. **`routes/web.php`**
- **Chức năng:** Định nghĩa routes cho chức năng OTP
- **Routes liên quan:**
  ```php
  Route::prefix('password')->name('password.')->group(function () {
      Route::get('/verify-phone', [ForgotPasswordController::class, 'showPhoneVerifyForm'])->name('verify.phone');
      Route::post('/verify-phone', [ForgotPasswordController::class, 'verifyPhoneOtp'])->name('verify.phone.post');
      Route::post('/auth/phone-verify', [ForgotPasswordController::class, 'handlePhoneVerification'])->name('phone.verify');
      Route::post('/save-phone', [ForgotPasswordController::class, 'savePhoneToSession'])->name('save.phone');
      Route::post('/reset-phone-otp-attempts', [ForgotPasswordController::class, 'resetPhoneOtpAttempts'])->name('phone.otp.reset.attempts');
  });
  ```

---

## 🔄 Luồng hoạt động

### **Luồng gửi OTP:**

```
1. User vào trang quên mật khẩu (/password/reset)
   ↓
2. User chọn tab "Số điện thoại"
   ↓
3. User nhập số điện thoại và click "Gửi mã xác thực"
   ↓
4. Frontend gọi firebasePhoneAuth.initRecaptcha()
   ↓
5. Frontend gọi firebasePhoneAuth.sendOTP(phoneNumber)
   ↓
6. Firebase gửi SMS OTP đến số điện thoại
   ↓
7. Frontend lưu confirmationResult vào localStorage
   ↓
8. Redirect đến /password/verify-phone?phone=+84xxxxxxxxx
```

### **Luồng xác thực OTP:**

```
1. User nhập 6 số OTP vào form
   ↓
2. Frontend gọi firebasePhoneAuth.verifyOTP(otpCode)
   ↓
3. Firebase xác thực mã OTP
   ↓
4a. Nếu OTP ĐÚNG:
    - Firebase trả về user object với uid
    - Frontend lấy idToken từ user.getIdToken()
    - Frontend gửi POST /password/verify-phone với uid và idToken
    - Backend lưu vào session và redirect đến /password/reset-password
    
4b. Nếu OTP SAI:
    - Firebase trả về lỗi
    - Frontend gửi POST /password/verify-phone với otp (để đếm attempts)
    - Backend tăng số lần thử, nếu >= 3 lần thì yêu cầu gửi lại OTP
```

---

## 🔧 Cấu hình chi tiết

### **1. Cấu hình Firebase Console**

#### **Bước 1: Bật Phone Authentication**
1. Vào Firebase Console > Authentication
2. Chọn tab **Sign-in method**
3. Bật **Phone** provider
4. Cấu hình reCAPTCHA (Invisible hoặc reCAPTCHA v2)

#### **Bước 2: Cấu hình reCAPTCHA**
- **Invisible reCAPTCHA:** Tự động xử lý, không hiển thị checkbox
- **reCAPTCHA v2:** Hiển thị checkbox "I'm not a robot"

**Trong code hiện tại:** Sử dụng Invisible reCAPTCHA
```javascript
this.recaptchaVerifier = new RecaptchaVerifier(auth, containerId, {
    'size': 'invisible',
    'callback': (response) => {
        // reCAPTCHA solved
    }
});
```

#### **Bước 3: Cấu hình Quota (nếu cần)**
- Firebase Free plan: 10,000 SMS/tháng
- Nếu vượt quá, cần upgrade lên Blaze plan (pay-as-you-go)

### **2. Format số điện thoại**

**Quy tắc format trong dự án:**
- Input: `0901645269` hoặc `901645269`
- Format: `+84901645269` (bỏ số 0 đầu, thêm +84)
- Validation: Phải bắt đầu bằng 3, 5, 7, 8, hoặc 9

**Code xử lý:**
```javascript
formatPhoneNumber(phoneNumber) {
    const cleaned = phoneNumber.replace(/\D/g, '');
    const last9Digits = cleaned.slice(-9);
    
    if (last9Digits.length !== 9) {
        throw new Error('Số điện thoại không hợp lệ');
    }
    
    const firstDigit = last9Digits[0];
    if (!['3', '5', '7', '8', '9'].includes(firstDigit)) {
        throw new Error('Số điện thoại không hợp lệ');
    }
    
    return '+84' + last9Digits;
}
```

### **3. Rate Limiting**

**Frontend (firebasePhoneAuth.js):**
- Tối đa 3 lần gửi OTP trong 1 phút
- Lưu trong localStorage

**Backend (ForgotPasswordController.php):**
- Tối đa 3 lần nhập sai OTP
- Tối đa 3 lần gửi OTP trong 1 giờ
- Lưu trong Cache (Redis/File)

### **4. OTP Expiry**

- **Thời gian hiệu lực:** 2 phút (theo Firebase thực tế là 1-2 phút)
- **Lưu ý:** Firebase tự động expire OTP sau 1-2 phút, không cần check thủ công

### **5. Error Handling**

**Các lỗi thường gặp:**

| Mã lỗi | Mô tả | Xử lý |
|--------|-------|-------|
| `auth/invalid-phone-number` | Số điện thoại không hợp lệ | Hiển thị lỗi, yêu cầu nhập lại |
| `auth/too-many-requests` | Quá nhiều yêu cầu | Hiển thị thông báo đợi |
| `auth/invalid-verification-code` | Mã OTP không đúng | Đếm attempts, hiển thị số lần thử còn lại |
| `auth/code-expired` | Mã OTP hết hạn | Yêu cầu gửi lại OTP |
| `auth/quota-exceeded` | Vượt quá hạn mức SMS | Thông báo lỗi, đề xuất dùng Email |
| `auth/error-code:-39` hoặc `503` | Firebase service tạm thời không khả dụng | Retry tự động 3 lần |

---

## 📝 Ghi chú quan trọng

### **1. Bảo mật**
- ✅ Không lưu OTP code trong database
- ✅ Sử dụng session để lưu trạng thái xác thực
- ✅ Verify idToken từ Firebase trước khi cho phép reset password
- ✅ Rate limiting để chống spam

### **2. User Experience**
- ✅ Auto-focus vào input đầu tiên
- ✅ Auto-submit khi nhập đủ 6 số
- ✅ Hiển thị countdown timer
- ✅ Hỗ trợ paste OTP từ clipboard
- ✅ Hiển thị số lần thử còn lại

### **3. Khôi phục sau reload**
- ✅ Lưu `verificationId` vào localStorage
- ✅ Khôi phục `confirmationResult` khi reload trang
- ✅ Cho phép verify OTP ngay cả sau khi reload

### **4. Tối ưu**
- ✅ Retry logic cho lỗi 503
- ✅ Exponential backoff khi retry
- ✅ Clear reCAPTCHA sau khi sử dụng
- ✅ Cleanup localStorage khi xác thực thành công

---

## 🚀 Cách test

### **1. Test gửi OTP:**
```
1. Vào /password/reset
2. Chọn tab "Số điện thoại"
3. Nhập số điện thoại hợp lệ (VD: 0901645269)
4. Click "Gửi mã xác thực"
5. Kiểm tra SMS nhận được
```

### **2. Test xác thực OTP:**
```
1. Nhập mã OTP 6 số từ SMS
2. Kiểm tra auto-submit khi nhập đủ 6 số
3. Kiểm tra redirect đến /password/reset-password khi thành công
```

### **3. Test error cases:**
```
- Nhập OTP sai 3 lần → Yêu cầu gửi lại OTP
- Đợi quá 2 phút → OTP hết hạn
- Gửi OTP quá nhiều lần → Rate limit
```

---

## 📚 Tài liệu tham khảo

- [Firebase Phone Authentication](https://firebase.google.com/docs/auth/web/phone-auth)
- [Firebase Auth API Reference](https://firebase.google.com/docs/reference/js/auth)
- [reCAPTCHA Configuration](https://firebase.google.com/docs/auth/web/phone-auth#recaptcha-verification)

---

**Cập nhật lần cuối:** 2025-01-XX
**Người tạo:** AI Assistant
**Dự án:** Pharma24h - Hệ thống quản lý nhà thuốc

