export const defaultFormState = {
    ma_dich_vu: '',
    nhom_hang_id: null,
    ten_dich_vu: '',
    doctor_id: null,
    hinh_thuc: null,
    thoi_gian_thuc_hien: null,
    gia_dich_vu: 0,
    trang_thai: 'kich_hoat',
    mo_ta: '',
    ghi_chu: '',
    image: null
}

//dữ liệu mặc định
export const SERVICE_TYPES = [
    { label: 'Tại phòng khám', value: 'tai_phong_kham' },
    { label: 'Tại nhà khách', value: 'tai_nha_khach' },
]

//trạng thái mặc định
export const STATUS_OPTIONS = [
    { label: 'Kích hoạt', value: 'kich_hoat' },
]