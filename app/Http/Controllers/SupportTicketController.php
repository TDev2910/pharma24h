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
    public function index(Request $request)
    {
        $user = Auth::user();

        $tickets = SupportTicket::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Giữ lại tham số search/page trên URL khi phân trang

        // Phân định View theo Role
        $view = ($user && $user->isAdmin())
            ? 'Admin/Tickets/Index'
            : 'Staff/Tickets/Index';

        // Trả về Inertia
        return Inertia::render($view, [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'status']),
        ]);
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

    /**
     * TẠO MỚI TICKET
     */
    public function store(Request $request)
    {
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
            'user_id'   => Auth::id() ?? null,
        ]);

        // Redirect back kèm Flash Message
        return redirect()->back()->with('success', 'Gửi yêu cầu hỗ trợ thành công!');
    }

    /**
     * CHI TIẾT TICKET
     */
    public function show($id)
    {
        // Eager load 'responder' để hiển thị ai là người trả lời
        $ticket = SupportTicket::with(['responder', 'user'])->findOrFail($id);

        $user = Auth::user();
        $view = ($user && $user->isAdmin())
            ? 'Admin/Tickets/Show'
            : 'Staff/Tickets/Show';

        return Inertia::render($view, [
            'ticket' => $ticket
        ]);
    }

    /**
     * TRẢ LỜI TICKET
     * Thay đổi: Không trả JSON, dùng Redirect để refresh UI
     */
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|min:5']);

        $ticket = SupportTicket::findOrFail($id);

        // Update Database
        $ticket->update([
            'admin_reply'  => $request->message,
            'responded_by' => Auth::id(),
            'responded_at' => now(),
            'status'       => 'replied'
        ]);

        // Gửi Email
        try {
            $this->emailService->sendTicketReply($ticket, $request->message);
            $message = 'Đã gửi phản hồi và email thông báo thành công!';
        } catch (\Exception $e) {
            // Vẫn báo thành công nhưng cảnh báo lỗi mail (tùy nghiệp vụ)
            $message = 'Đã lưu phản hồi, nhưng lỗi gửi email: ' . $e->getMessage();
        }

        // QUAN TRỌNG: Redirect back để Inertia tự load lại data mới nhất
        return redirect()->back()->with('success', $message);
    }
}
