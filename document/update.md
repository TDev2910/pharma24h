# README — Ràng buộc duy nhất cho Mã hàng, Mã vạch, Số đăng ký

## 🎯 Mục tiêu
- Trong hệ thống, mỗi sản phẩm chỉ có một:
  - **Mã hàng** (`ma_hang`)
  - **Mã vạch** (`ma_vach`)
  - **Số đăng ký** (`so_dang_ky`)
- Khi trùng, hiển thị thông báo lỗi **ngay tại ô nhập**:
  - Mã hàng: *"Mã hàng bạn nhập đang trùng với một sản phẩm khác."*
  - Mã vạch: *"Mã vạch bạn nhập đang trùng với một sản phẩm khác."*
  - Số đăng ký: *"Số đăng ký bạn nhập đang trùng với một sản phẩm khác."*

---

## 1️⃣ Database (Migration)
```php
Schema::table('medicines', function (Blueprint $t) {
    $t->string('ma_hang')->nullable()->unique()->change();
    $t->string('ma_vach')->nullable()->unique()->change();
    $t->string('so_dang_ky')->nullable()->unique()->change();
});
```
> `nullable()->unique()` cho phép nhiều giá trị NULL, nhưng cấm trùng khi có giá trị.

---

## 2️⃣ FormRequest (Validation)
File: `app/Http/Requests/MedicineUpsertRequest.php`
```php
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicineUpsertRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ma_hang'    => $this->normalize($this->ma_hang),
            'ma_vach'    => $this->normalize($this->ma_vach),
            'so_dang_ky' => $this->normalize($this->so_dang_ky, upper: true),
        ]);
    }

    private function normalize($v, bool $upper = false) {
        if ($v === null) return null;
        $s = preg_replace('/\s+/', '', (string)$v); // bỏ khoảng trắng
        return $upper ? mb_strtoupper($s, 'UTF-8') : $s;
    }

    public function rules(): array
    {
        $id = $this->route('medicine')?->id;

        return [
            'ten_thuoc' => ['required','string','max:255'],

            'ma_hang' => [
                'nullable','string','max:64',
                Rule::unique('medicines','ma_hang')->ignore($id)->whereNotNull('ma_hang'),
            ],
            'ma_vach' => [
                'nullable','string','max:64',
                Rule::unique('medicines','ma_vach')->ignore($id)->whereNotNull('ma_vach'),
            ],
            'so_dang_ky' => [
                'nullable','string','max:64',
                Rule::unique('medicines','so_dang_ky')->ignore($id)->whereNotNull('so_dang_ky'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_hang.unique'    => 'Mã hàng bạn nhập đang trùng với một sản phẩm khác.',
            'ma_vach.unique'    => 'Mã vạch bạn nhập đang trùng với một sản phẩm khác.',
            'so_dang_ky.unique' => 'Số đăng ký bạn nhập đang trùng với một sản phẩm khác.',
        ];
    }
}
```

---

## 3️⃣ Controller
```php
public function store(MedicineUpsertRequest $req)
{
    Medicine::create($req->validated());
    return back()->with('ok','Đã thêm thuốc');
}

public function update(MedicineUpsertRequest $req, Medicine $medicine)
{
    $medicine->update($req->validated());
    return back()->with('ok','Đã cập nhật thuốc');
}
```

---

## 4️⃣ View (Blade)
```blade
{{-- Mã hàng --}}
<input name="ma_hang" value="{{ old('ma_hang', $medicine->ma_hang ?? '') }}"
       class="form-control @error('ma_hang') is-invalid @enderror">
@error('ma_hang') <div class="invalid-feedback">{{ $message }}</div> @enderror

{{-- Mã vạch --}}
<input name="ma_vach" value="{{ old('ma_vach', $medicine->ma_vach ?? '') }}"
       class="form-control @error('ma_vach') is-invalid @enderror">
@error('ma_vach') <div class="invalid-feedback">{{ $message }}</div> @enderror

{{-- Số đăng ký --}}
<input name="so_dang_ky" value="{{ old('so_dang_ky', $medicine->so_dang_ky ?? '') }}"
       class="form-control @error('so_dang_ky') is-invalid @enderror">
@error('so_dang_ky') <div class="invalid-feedback">{{ $message }}</div> @enderror
```

---

## ✅ Kết quả
- Nếu nhập trùng **Mã hàng**, **Mã vạch**, hoặc **Số đăng ký**:
  - Form **không lưu**.
  - Lỗi hiển thị ngay tại ô nhập, đúng thông điệp yêu cầu.
- DB luôn đảm bảo **không thể tồn tại bản ghi trùng**, kể cả khi nhiều request đồng thời.

---

## 💡 Best Practices
- Luôn normalize dữ liệu trước khi validate (bỏ khoảng trắng, chuẩn uppercase).  
- Unique index ở DB để tránh race condition.  
- Khi update, dùng `ignore($id)` để không báo lỗi cho chính bản ghi đang sửa.  
- Nếu multi-tenant: thêm `->where('store_id', auth()->user()->store_id)` trong Rule để ràng buộc theo cửa hàng.
