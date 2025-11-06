<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceBookingController extends Controller
{
    /**
     * Tạo booking mới (khách hàng đặt lịch)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'booking_date' => 'required|date_format:Y-m-d|after:today',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $service = Service::findOrFail($request->service_id);

            $booking = ServiceBooking::create([
                'service_id' => $request->service_id,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'price' => $service->gia_dich_vu,
                'payment_method' => 'pay_at_pharmacy',
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch thành công!',
                'booking' => $booking->load('service')
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}

