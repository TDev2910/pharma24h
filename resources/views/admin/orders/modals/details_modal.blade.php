<!-- Modal Chi tiết đơn hàng -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nội dung sẽ được thêm bằng JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>         
                <a id="printInvoiceBtn" href="#" target="_blank" class="btn btn-success">In hóa đơn</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // CSRF cho ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Biến toàn cục trong scope modal
        let currentOrderId = null;

        // Mở modal xem chi tiết
        $(document).on('click', '.view-order-detail', function(e) {
            e.preventDefault();
            currentOrderId = $(this).data('id');

            $('#orderDetailModal .modal-body').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Đang tải dữ liệu...</p></div>');
            $('#orderDetailModal').modal('show');
            loadOrderDetails(currentOrderId);

            // Cập nhật link in hóa đơn theo currentOrderId
            var invoiceUrl = '{{ route("admin.orders.invoice", ["order" => ":id"]) }}'.replace(':id', currentOrderId);
            $('#printInvoiceBtn').attr('href', invoiceUrl);
        });

        // Tải chi tiết đơn hàng
        function loadOrderDetails(orderId) {
            $.ajax({
                url: '{{ route("admin.orders.show", ["order" => ":id"]) }}'.replace(':id', orderId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var order = response.order;
                        var items = response.items;
                        $('#orderDetailModalLabel').text('Chi tiết đơn hàng #' + order.order_code);
                        var html = buildOrderDetailHTML(order, items);
                        $('#orderDetailModal .modal-body').html(html);
                    } else {
                        $('#orderDetailModal .modal-body').html('<div class="alert alert-danger">Không thể tải dữ liệu đơn hàng. Vui lòng thử lại!</div>');
                    }
                },
                error: function() {
                    $('#orderDetailModal .modal-body').html('<div class="alert alert-danger">Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau!</div>');
                }
            });
        }

        

        // Helpers hiển thị chi tiết
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            var date = new Date(dateString);
            return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes();
        }

        function formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('vi-VN') + 'đ';
        } 

        function getStatusBadge(status) {
            var badgeClass = 'bg-secondary';
            var statusText = 'Không xác định';
            switch((status || '').toString().toLowerCase()) {
                case 'pending':
                case 'new':
                    badgeClass = 'bg-warning text-dark';
                    statusText = 'Đang chờ xử lý';
                    break;
                case 'processing':
                    badgeClass = 'bg-primary';
                    statusText = 'Đang xử lý';
                    break;
                case 'completed':
                    badgeClass = 'bg-success';
                    statusText = 'Hoàn thành';
                    break;
                case 'cancelled':
                    badgeClass = 'bg-danger';
                    statusText = 'Đã hủy';
                    break;
            }
            return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
        }
        
        //hiển thị trạng thái thanh toán
        function getPaymentStatusBadge(status) {
            var badgeClass = 'bg-secondary';
            var statusText = 'Không xác định';
            switch(status) {
                case 'pending':
                case 'unpaid':
                    badgeClass = 'bg-warning text-dark';
                    statusText = 'Chưa thanh toán';
                    break;
                case 'paid':
                    badgeClass = 'bg-success';
                    statusText = 'Đã thanh toán';
                    break;
                case 'failed':
                    badgeClass = 'bg-danger';
                    statusText = 'Thanh toán thất bại';
                    break;
                case 'refunded':
                    badgeClass = 'bg-info';
                    statusText = 'Đã hoàn tiền';
                    break;
                case 'cancelled':
                    badgeClass = 'bg-danger';
                    statusText = 'Đơn hàng đã bị hủy';
                    break;
            }
            return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
        }

        function getDeliveryMethodText(method) {
            switch(method) {
                case 'shipping':
                    return 'Giao hàng tận nơi';
                case 'pickup':
                    return 'Nhận tại cửa hàng';
                default:
                    return method || 'Không xác định';
            }
        }

        function buildOrderDetailHTML(order, items) {
            var html = '';
            html += '<div class="row mb-4">';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin đơn hàng</h6>';
            html += '<p class="mb-1"><strong>Mã đơn hàng:</strong> ' + order.order_code + '</p>';
            html += '<p class="mb-1"><strong>Ngày đặt:</strong> ' + formatDate(order.created_at) + '</p>';
            var statusBadge = getStatusBadge(order.order_status);
            html += '<p class="mb-1"><strong>Trạng thái:</strong> ' + statusBadge + '</p>';
            // Nếu đơn hàng bị hủy thì hiển thị rõ, không hiển thị trạng thái thanh toán
            if ((order.order_status || '').toLowerCase() === 'cancelled') {
                html += '<p class="mb-1"><strong>Trạng thái thanh toán:</strong> <span class="badge bg-danger">Đơn hàng đã bị hủy</span></p>';
            } else {
                var paymentStatusBadge = getPaymentStatusBadge(order.payment_status);
                html += '<p class="mb-1"><strong>Trạng thái thanh toán:</strong> ' + paymentStatusBadge + '</p>';
            }
            html += '<p class="mb-0"><strong>Ghi chú:</strong> ' + (order.note || 'Không có') + '</p>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin khách hàng</h6>';
            html += '<p class="mb-1"><strong>Tên khách hàng:</strong> ' + (order.customer_name || 'N/A') + '</p>';
            html += '<p class="mb-1"><strong>Số điện thoại:</strong> ' + (order.customer_phone || 'N/A') + '</p>';
            html += '<p class="mb-1"><strong>Email:</strong> ' + (order.customer_email || 'N/A') + '</p>';
            if (order.delivery_method === 'shipping') {
                var fullAddress = [order.shipping_address, order.ward, order.district, order.province].filter(Boolean).join(', ');
                html += '<p class="mb-0"><strong>Địa chỉ giao hàng:</strong> ' + (fullAddress || 'N/A') + '</p>';
            } else if (order.delivery_method === 'pickup') {
                html += '<p class="mb-0"><strong>Địa điểm nhận hàng:</strong> ' + (order.pickup_location || 'N/A') + '</p>';
            }
            html += '</div>';
            html += '</div>';

            html += '<h6 class="fw-bold mb-3">Danh sách sản phẩm</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered table-striped">';
            html += '<thead class="table-light">';
            html += '<tr><th>STT</th><th>Tên sản phẩm</th><th class="text-center">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr>';
            html += '</thead><tbody>';
            if (items && items.length > 0) {
                items.forEach(function(item, index) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td>' + item.product_name + '</td>';
                    html += '<td class="text-center">' + item.quantity + '</td>';
                    html += '<td class="text-end">' + formatCurrency(item.price) + '</td>';
                    html += '<td class="text-end">' + formatCurrency(item.subtotal) + '</td>';
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="5" class="text-center">Không có sản phẩm nào</td></tr>';
            }
            html += '</tbody><tfoot>';
            var subtotal = 0;
            if (items && items.length > 0) {
                subtotal = items.reduce(function(sum, item) { return sum + parseFloat(item.subtotal); }, 0);
            }
            html += '<tr><td colspan="4" class="text-end fw-bold">Tạm tính:</td><td class="text-end">' + formatCurrency(subtotal) + '</td></tr>';
            html += '<tr><td colspan="4" class="text-end fw-bold">Tổng cộng:</td><td class="text-end fw-bold">' + formatCurrency(order.total_amount) + '</td></tr>';
            html += '</tfoot></table></div>';

            html += '<div class="row mt-4">';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin thanh toán</h6>';
            html += '<p class="mb-1"><strong>Phương thức:</strong> ' + getPaymentMethodText(order.payment_method) + '</p>';
            if ((order.order_status || '').toLowerCase() === 'cancelled') {
                html += '<p class="mb-1"><strong>Trạng thái:</strong> <span class="badge bg-danger">Đơn hàng đã bị hủy</span></p>';
            } else {
                html += '<p class="mb-1"><strong>Trạng thái:</strong> ' + getPaymentStatusBadge(order.payment_status) + '</p>';
            }
            if (order.transaction_id) html += '<p class="mb-0"><strong>Mã giao dịch:</strong> ' + order.transaction_id + '</p>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin vận chuyển</h6>';
            html += '<p class="mb-1"><strong>Phương thức:</strong> ' + getDeliveryMethodText(order.delivery_method) + '</p>';
            if (order.delivery_method === 'shipping') {
                html += '<p class="mb-0"><strong>Địa chỉ:</strong> ' + ([order.shipping_address, order.ward, order.district, order.province].filter(Boolean).join(', ') || 'N/A') + '</p>';
            } else if (order.delivery_method === 'pickup') {
                html += '<p class="mb-0"><strong>Địa điểm nhận hàng:</strong> ' + (order.pickup_location || 'N/A') + '</p>';
            }
            html += '</div></div>';
            return html;
        }
        function showToast(message){
            const toastId = 'toast-' + Date.now();
            const toastHtml = '<div id="'+toastId+'" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000"><div class="d-flex"><div class="toast-body">'+ message +'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>';
            $('body').append(toastHtml);
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            setTimeout(function(){ $('#'+toastId).remove(); }, 2500);
        }

    });
</script>
@endpush

