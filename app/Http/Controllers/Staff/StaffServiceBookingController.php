<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Notifications\ServiceBookingStatusUpdated;

class StaffServiceBookingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Staff/OrderServices');
    }

    public function apiIndex(Request $request)
    {
        //lấy dữ liệu từ db và relationship của service và user
        // Lấy dữ liệu từ db và relationship của service và user
        $query = ServiceBooking::with(['service', 'user']);

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Lọc trạng thái thanh toán
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo ngày
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('booking_date', '<=', $request->date_to);
        }

        // Tìm kiếm theo tên khách hàng hoặc SĐT
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Sắp xếp theo ngày đặt mới nhất
        $query->orderBy('created_at', 'desc');

        // Phân trang
        $perPage = $request->get('per_page', 10);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'from' => $bookings->firstItem(),
                'to' => $bookings->lastItem()
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $booking = ServiceBooking::with(['service', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    //xác nhận lịch đặt dịch vụ
    public function confirm($id)
    {
        $booking = ServiceBooking::findOrFail($id);
        $oldStatus = $booking->status; // Lưu status cũ

        // Kiểm tra có thể xác nhận không
        if (!$booking->canConfirm()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xác nhận lịch hẹn này'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'confirmed']);

        // Gửi notification cho user nếu status thay đổi
        if ($oldStatus !== $booking->status && $booking->user) {
            $booking->user->notify(new ServiceBookingStatusUpdated($booking, $oldStatus, $booking->status));
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận lịch hẹn thành công',
            'data' => $booking->fresh(['service', 'user'])
        ]);
    }

    //hủy lịch đặt dịch vụ
    public function cancel($id)
    {
        $booking = ServiceBooking::findOrFail($id);
        $oldStatus = $booking->status; // Lưu status cũ

        // Kiểm tra có thể hủy không
        if (!$booking->canCancel()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy lịch hẹn này'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'cancelled']);

        // Gửi notification cho user nếu status thay đổi
        if ($oldStatus !== $booking->status && $booking->user) {
            $booking->user->notify(new ServiceBookingStatusUpdated($booking, $oldStatus, $booking->status));
        }

        return response()->json([
            'success' => true,
            'message' => 'Hủy lịch hẹn thành công',
            'data' => $booking->fresh(['service', 'user'])
        ]);
    }

    //cập nhật trạng thái thanh toán
    public function markAsPaid($id)
    {
        $booking = ServiceBooking::findOrFail($id);

        // Kiểm tra đã thanh toán chưa
        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn đã được thanh toán'
            ], 422);
        }

        // Cập nhật trạng thái thanh toán
        $booking->update(['payment_status' => 'paid']);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thanh toán thành công',
            'data' => $booking->fresh(['service', 'user'])
        ]);
    }

    //hoàn thành dịch vụ
    public function complete($id)
    {
        $booking = ServiceBooking::findOrFail($id);
        $oldStatus = $booking->status; // Lưu status cũ

        // Kiểm tra có thể hoàn thành không
        if (!$booking->canComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hoàn thành dịch vụ. Cần thanh toán trước.'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'completed']);

        // Gửi notification cho user nếu status thay đổi
        if ($oldStatus !== $booking->status && $booking->user) {
            $booking->user->notify(new ServiceBookingStatusUpdated($booking, $oldStatus, $booking->status));
        }

        return response()->json([
            'success' => true,
            'message' => 'Hoàn thành dịch vụ thành công',
            'data' => $booking->fresh(['service', 'user'])
        ]);
    }

    //lấy lịch cần gọi điện xác nhận
    public function needConfirmation(Request $request)
    {
        $date = $request->get('date', today());

        $bookings = ServiceBooking::needConfirmation()
            ->with('service')
            ->whereDate('booking_date', $date)
            ->orderBy('booking_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    //
    public function needPayment(Request $request)
    {
        $date = $request->get('date', today());

        $bookings = ServiceBooking::needPayment()
            ->with('service')
            ->whereDate('booking_date', $date)
            ->orderBy('booking_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}
