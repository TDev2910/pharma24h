<?php

namespace App\Http\Controllers\Admin\OrderServices;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceBookingController extends Controller
{
    public function index()
    {
        //lấy dữ liệu từ db và relationship của service và user
        $query = ServiceBooking::with(['service', 'user']);
        
        //lọc theo trạng thái
        if($request->has('status') && $request->status !== '')
        {
            $query->where('status', $request->status);
        }

        //lọc trạng thái thanh toán
        if ($request->has('payment_status') && $request->payment_status !== '') 
        {
            $query->where('payment_status', $request->payment_status);
        }

        //loc theo ngay
        if ($request->has('date') && $request->date !== '') 
        {
            $query->whereDate('booking_date', $request->date);
        }

        //tìm kiếm theo so dien thoai
        if ($request->has('phone') && $request->phone !== '') {
            $query->where('customer_phone', 'like', '%' . $request->phone . '%');
        }
        // Sắp xếp theo thời gian tạo mới nhất
        $query->orderBy('created_at', 'desc');

        // Pagination 20 items/page
        $bookings = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $bookings
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

        // Kiểm tra có thể xác nhận không
        if (!$booking->canConfirm()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xác nhận lịch hẹn này'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'confirmed']);

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

        // Kiểm tra có thể hủy không
        if (!$booking->canCancel()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy lịch hẹn này'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'cancelled']);

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

        // Kiểm tra có thể hoàn thành không
        if (!$booking->canComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hoàn thành dịch vụ. Cần thanh toán trước.'
            ], 422);
        }

        // Cập nhật trạng thái
        $booking->update(['status' => 'completed']);

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
