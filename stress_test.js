import http from "k6/http";
import { sleep, check } from "k6";

export const options = {
    vus: 1,
    duration: "30s",
};

export default function () {
    const loginPageUrl = "https://healthviet.com/login";
    const baseHeaders = {
        "User-Agent":
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    };

    // BƯỚC 1: Lấy Token và QUAN TRỌNG NHẤT LÀ Inertia-Version hiện tại của Production
    const getRes = http.get(loginPageUrl, { headers: baseHeaders });

    const xsrfToken = getRes.cookies["XSRF-TOKEN"]
        ? getRes.cookies["XSRF-TOKEN"][0].value
        : "";
    // Tự động bóc tách mã Version mà Production đang dùng
    const inertiaVersion =
        getRes.headers["X-Inertia-Version"] ||
        getRes.headers["x-inertia-version"] ||
        "";

    // BƯỚC 2: Thực hiện đăng nhập kèm theo đúng mã Version
    const postPayload = JSON.stringify({
        email: "admin@example.com", // Hãy dùng 1 tài khoản thật của bạn
        password: "password",
        remember: false,
    });

    const postParams = {
        headers: {
            ...baseHeaders,
            "Content-Type": "application/json",
            "X-Inertia": "true",
            "X-Inertia-Version": inertiaVersion, // Gửi đúng version server đang có (Fix lỗi 409)
            "X-XSRF-TOKEN": decodeURIComponent(xsrfToken),
        },
    };

    const res = http.post(loginPageUrl, postPayload, postParams);

    // Kiểm tra và Log lỗi nếu có
    if (res.status !== 200 && res.status !== 302 && res.status !== 303) {
        console.warn(
            `[Iteration ${__ITER}] Mã lỗi: ${res.status}. Nếu là 429, hãy nghỉ tay 1 phút rồi chạy lại nhé!`,
        );
    }

    check(res, {
        "Status OK (200/302/303)": (r) => [200, 302, 303].includes(r.status),
        "Thong qua Cloudflare": (r) => r.headers["Cf-Ray"] !== undefined,
    });

    // Thêm thời gian nghỉ lâu hơn một chút (3-5 giây) để tránh bị server chặn IP (429)
    sleep(3);
}
