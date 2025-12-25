<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\EmailSMTP\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    //Danh sách Ticket
    //Hàm này chỉ trả về khung giao diện (View)
    public function index()
    {
        $user = Auth::user();
        // Kiểm tra quyền để trả về đúng View layout
        $view = ($user && $user->isAdmin())
            ? 'Admin/Tickets/Index'
            : 'Staff/Tickets/Index';

        return Inertia::render($view); // Không truyền biến 'tickets' vào đây nữa
    }

    //Hàm API này trả về dữ liệu JSON (Vue sẽ gọi hàm này)
    public function getTickets(Request $request)
    {
        $query = SupportTicket::query();

        // Xử lý tìm kiếm (nếu có)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Xử lý lọc theo trạng thái (nếu có)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp mặc định mới nhất
        $tickets = $query->latest()->paginate(10);

        return response()->json($tickets); // Trả về JSON
    }

    //Xử lý lưu Form (Create - từ trang Contact)
    public function store(Request $request)
    {
        // Validate dữ liệu từ form Vue
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'subject'  => 'required|string',
            'message'  => 'required|string|min:10',
        ]);

        SupportTicket::create([
            'ticket_id' => 'SUP-' . strtoupper(Str::random(6)),
            'full_name' => $validated['fullName'],
            'email'     => $validated['email'],
            'subject'   => $validated['subject'],
            'message'   => $validated['message'],
            'status'    => 'pending',
            'user_id'   => Auth::id() ?? null, // Cho phép khách vãng lai (null)
        ]);

        // Trả về JSON
        return redirect()->back()->with('success', 'Gửi yêu cầu thành công!');
    }

    //chi tiết Ticket
    public function show($id)
    {
        $ticket = SupportTicket::with(['responder', 'user'])->findOrFail($id);

        $user = Auth::user();
        $view = ($user && $user->isAdmin())
            ? 'Admin/Tickets/Show'
            : 'Staff/Tickets/Show';

        return Inertia::render($view, [
            'ticket' => $ticket
        ]);
    }

    // 4. Xử lý Trả lời
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|min:5']);

        $ticket = SupportTicket::findOrFail($id);

        // 1. Cập nhật Database
        $ticket->update([
            'admin_reply' => $request->message,
            'responded_by' => Auth::id(),
            'responded_at' => now(),
            'status' => 'replied'
        ]);

        //Gọi Service gửi Email (Tái sử dụng cấu trúc có sẵn)
        $this->emailService->sendTicketReply($ticket, $request->message);

        //Trả về JSON cho Axios (Modal)
        return response()->json([
            'success' => true,
            'message' => 'Đã gửi phản hồi và email thông báo thành công!'
        ]);
    }
}
