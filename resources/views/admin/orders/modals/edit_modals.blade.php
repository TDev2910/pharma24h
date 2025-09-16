<div class="modal fade" id="orderEditModal" tabindex="-1" aria-labelledby="orderEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderEditModalLabel">Chỉnh sửa đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nội dung sẽ được render bằng JS giống details + form chỉnh sửa -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <div class="dropdown d-inline-block me-2">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="editUpdateStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Cập nhật trạng thái
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="editUpdateStatusDropdown">
                        <li><a class="dropdown-item update-status-edit" href="#" data-status="pending">Đang chờ xử lý</a></li>
                        <li><a class="dropdown-item update-status-edit" href="#" data-status="processing">Đang xử lý</a></li>
                        <li><a class="dropdown-item update-status-edit" href="#" data-status="completed">Hoàn thành</a></li>
                        <li><a class="dropdown-item update-status-edit" href="#" data-status="cancelled">Hủy đơn hàng</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-success print-invoice-btn">In hóa đơn</button>
                <button type="button" class="btn btn-primary ms-auto" id="saveOrderInfoBtn">Lưu thông tin</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function(){
        // Thiết lập CSRF cho ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let editingOrderId = null;

        // Mở modal edit và nạp chi tiết đơn (giống modal details)
        $(document).on('click', '.edit-order', function(e){
            e.preventDefault();
            editingOrderId = $(this).data('id');
            if(!editingOrderId) return;

            $('#orderEditModal .modal-body').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Đang tải dữ liệu...</p></div>');
            $('#orderEditModal').modal('show');

            loadEditOrderDetails(editingOrderId);
        });

        function loadEditOrderDetails(orderId){
            $.ajax({
                url: '{{ route("admin.orders.show", ["order" => ":id"]) }}'.replace(':id', orderId),
                type: 'GET',
                dataType: 'json'
            }).done(function(res){
                if(res.success){
                    const order = res.order;
                    const items = res.items;
                    $('#orderEditModalLabel').text('Chỉnh sửa đơn hàng #' + order.order_code);
                    const detailHtml = buildOrderDetailHTML(order, items);
                    const formHtml = buildEditFormHTML(order);
                    $('#orderEditModal .modal-body').html(formHtml + '<hr class="my-3">' + detailHtml);
                } else {
                    $('#orderEditModal .modal-body').html('<div class="alert alert-danger">Không thể tải dữ liệu đơn hàng. Vui lòng thử lại!</div>');
                }
            }).fail(function(){
                $('#orderEditModal .modal-body').html('<div class="alert alert-danger">Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau!</div>');
            });
        }

        // Chọn trạng thái trong dropdown để cập nhật và lưu
        $(document).on('click', '.update-status-edit', function(e){
            e.preventDefault();
            if(!editingOrderId) return alert('Không thể xác định đơn hàng cần cập nhật!');
            const newStatus = $(this).data('status');
            
            // Thực hiện cập nhật trực tiếp, không hiển thị confirm
            $('#orderEditModal .modal-body').append('<div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75"><div class="spinner-border text-primary"></div></div>');
            $.ajax({
                url: '{{ route("admin.orders.update-status", ["order" => ":id"]) }}'.replace(':id', editingOrderId),
                type: 'POST',
                dataType: 'json',
                data: { status: newStatus, _token: '{{ csrf_token() }}' }
            }).done(function(res){
                $('#orderEditModal .modal-body > div:last-child').remove();
                if(res.success){
                    const $row = $('tr[data-order-id="' + editingOrderId + '"]');
                    $row.find('td').eq(3).html(getStatusBadge(newStatus));
                    loadEditOrderDetails(editingOrderId);
                } else {
                    showToast(res.message || 'Cập nhật thất bại');
                }
            }).fail(function(xhr){
                $('#orderEditModal .modal-body > div:last-child').remove();
                showToast(xhr.responseJSON?.message || 'Có lỗi xảy ra');
            });
        });

        // Lưu các trường thông tin khách hàng/đơn
        $(document).on('click', '#saveOrderInfoBtn', function(){
            if(!editingOrderId) return;
            const payload = {
                customer_name: $('#editCustomerName').val(),
                customer_email: $('#editCustomerEmail').val(),
                customer_phone: $('#editCustomerPhone').val(),
                shipping_address: $('#editShippingAddress').val(),
                province: $('#editProvince').val(),
                district: $('#editDistrict').val(),
                ward: $('#editWard').val(),
                pickup_location: $('#editPickupLocation').val(),
                note: $('#editNote').val(),
                delivery_method: $('#editDeliveryMethod').val(),
                payment_method: $('#editPaymentMethod').val(),
                _method: 'PATCH',
                _token: '{{ csrf_token() }}'
            };

            $('#saveOrderInfoBtn').prop('disabled', true);
            $.ajax({
                url: '{{ url("admin/orders") }}/' + editingOrderId,
                type: 'POST',
                dataType: 'json',
                data: payload
            }).done(function(res){
                if(res.success){
                    alert('Đã lưu thông tin đơn hàng');
                    // Cập nhật một số ô trên bảng nếu cần
                    const $row = $('tr[data-order-id="' + editingOrderId + '"]');
                    $row.find('td').eq(2).text(payload.customer_name || $row.find('td').eq(2).text());
                    // Làm mới lại nội dung chi tiết phía dưới
                    loadEditOrderDetails(editingOrderId);
                } else {
                    alert(res.message || 'Lưu thất bại');
                }
            }).fail(function(xhr){
                alert(xhr.responseJSON?.message || 'Có lỗi xảy ra');
            }).always(function(){
                $('#saveOrderInfoBtn').prop('disabled', false);
            });
        });

        // Helpers dùng chung (bản rút gọn giống details)
        function formatDate(dateString){
            if(!dateString) return 'N/A';
            const d = new Date(dateString);
            return d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes();
        }
        function formatCurrency(amount){
            return parseFloat(amount).toLocaleString('vi-VN') + 'đ';
        }
        function getStatusBadge(status){
            const s = (status||'').toLowerCase();
            if(s==='pending' || s==='new') return '<span class="badge bg-warning text-dark">Đang chờ xử lý</span>';
            if(s==='processing') return '<span class="badge bg-primary">Đang xử lý</span>';
            if(s==='completed') return '<span class="badge bg-success">Hoàn thành</span>';
            if(s==='cancelled') return '<span class="badge bg-danger">Đã hủy</span>';
            return '<span class="badge bg-secondary">Khác</span>';
        }

        // Hiển thị trạng thái thanh toán
        function getPaymentStatusBadge(status){
            let badgeClass = 'bg-secondary', text = 'Không xác định';
            switch(status){
                case 'pending':
                case 'unpaid':
                    badgeClass='bg-warning text-dark'; text='Chưa thanh toán'; break;
                case 'paid': badgeClass='bg-success'; text='Đã thanh toán'; break;
                case 'failed': badgeClass='bg-danger'; text='Thanh toán thất bại'; break;
                case 'refunded': badgeClass='bg-info'; text='Đã hoàn tiền'; break;
                case 'cancelled': badgeClass='bg-danger'; text='Đơn hàng đã bị hủy'; break;
            }
            return '<span class="badge ' + badgeClass + '">' + text + '</span>';
        }
        
        function getDeliveryMethodText(method){
            switch(method){
                case 'shipping': return 'Giao hàng tận nơi';
                case 'pickup': return 'Nhận tại cửa hàng';
                default: return method || 'Không xác định';
            }
        }

        function buildOrderDetailHTML(order, items){
            let html = '';
            html += '<div class="row mb-4">';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin đơn hàng</h6>';
            html += '<p class="mb-1"><strong>Mã đơn hàng:</strong> ' + order.order_code + '</p>';
            html += '<p class="mb-1"><strong>Ngày đặt:</strong> ' + formatDate(order.created_at) + '</p>';
            html += '<p class="mb-1"><strong>Trạng thái:</strong> ' + getStatusBadge(order.order_status) + '</p>';
            html += '<p class="mb-1"><strong>Trạng thái thanh toán:</strong> ' + getPaymentStatusBadge(order.payment_status) + '</p>';
            html += '<p class="mb-0"><strong>Ghi chú:</strong> ' + (order.note || 'Không có') + '</p>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin khách hàng</h6>';
            html += '<p class="mb-1"><strong>Tên khách hàng:</strong> ' + (order.customer_name || 'N/A') + '</p>';
            html += '<p class="mb-1"><strong>Số điện thoại:</strong> ' + (order.customer_phone || 'N/A') + '</p>';
            html += '<p class="mb-1"><strong>Email:</strong> ' + (order.customer_email || 'N/A') + '</p>';
            if(order.delivery_method === 'shipping'){
                const addr = [order.shipping_address, order.ward, order.district, order.province].filter(Boolean).join(', ');
                html += '<p class="mb-0"><strong>Địa chỉ giao hàng:</strong> ' + (addr || 'N/A') + '</p>';
            }else if(order.delivery_method === 'pickup'){
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
            if(items && items.length > 0){
                items.forEach(function(item, index){
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
            let subtotal = 0;
            if(items && items.length > 0){
                subtotal = items.reduce(function(sum, item){ return sum + parseFloat(item.subtotal); }, 0);
            }
            html += '<tr><td colspan="4" class="text-end fw-bold">Tạm tính:</td><td class="text-end">' + formatCurrency(subtotal) + '</td></tr>';
            html += '<tr><td colspan="4" class="text-end fw-bold">Tổng cộng:</td><td class="text-end fw-bold">' + formatCurrency(order.total_amount) + '</td></tr>';
            html += '</tfoot></table></div>';

            html += '<div class="row mt-4">';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin thanh toán</h6>';
            html += '<p class="mb-1"><strong>Phương thức:</strong> ' + getPaymentMethodText(order.payment_method) + '</p>';
            html += '<p class="mb-1"><strong>Trạng thái:</strong> ' + getPaymentStatusBadge(order.payment_status) + '</p>';
            if(order.transaction_id){ html += '<p class="mb-0"><strong>Mã giao dịch:</strong> ' + order.transaction_id + '</p>'; }
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h6 class="fw-bold">Thông tin vận chuyển</h6>';
            html += '<p class="mb-1"><strong>Phương thức:</strong> ' + getDeliveryMethodText(order.delivery_method) + '</p>';
            if(order.delivery_method === 'shipping'){
                const addr2 = [order.shipping_address, order.ward, order.district, order.province].filter(Boolean).join(', ');
                html += '<p class="mb-0"><strong>Địa chỉ:</strong> ' + (addr2 || 'N/A') + '</p>';
            } else if(order.delivery_method === 'pickup'){
                html += '<p class="mb-0"><strong>Địa điểm nhận hàng:</strong> ' + (order.pickup_location || 'N/A') + '</p>';
            }
            html += '</div></div>';

            return html;
        }

        function buildEditFormHTML(order){
            let html = '';
            html += '<div class="row g-3">';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Họ tên</label>';
            html += '<input type="text" class="form-control" id="editCustomerName" value="' + (order.customer_name || '') + '">';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Email</label>';
            html += '<input type="email" class="form-control" id="editCustomerEmail" value="' + (order.customer_email || '') + '">';
            html += '</div>';

            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Số điện thoại</label>';
            html += '<input type="text" class="form-control" id="editCustomerPhone" value="' + (order.customer_phone || '') + '">';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Phương thức thanh toán</label>';
            html += '<select class="form-select" id="editPaymentMethod">' +
                    buildOptions(order.payment_method, {cash:'Tiền mặt',transfer:'Chuyển khoản',vnpay:'VNPay',momo:'Ví MoMo',zalopay:'ZaloPay'}) + '</select>';
            html += '</div>';

            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Địa chỉ giao hàng</label>';
            html += '<input type="text" class="form-control" id="editShippingAddress" value="' + (order.shipping_address || '') + '">';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Tỉnh/Thành</label>';
            html += '<input type="text" class="form-control" id="editProvince" value="' + (order.province || '') + '">';
            html += '</div>';

            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Quận/Huyện</label>';
            html += '<input type="text" class="form-control" id="editDistrict" value="' + (order.district || '') + '">';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Phường/Xã</label>';
            html += '<input type="text" class="form-control" id="editWard" value="' + (order.ward || '') + '">';
            html += '</div>';

            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Phương thức nhận hàng</label>';
            html += '<select class="form-select" id="editDeliveryMethod">' +
                    buildOptions(order.delivery_method, {shipping:'Giao hàng tận nơi',pickup:'Nhận tại cửa hàng'}) + '</select>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<label class="form-label small fw-bold">Địa điểm nhận (nếu nhận tại cửa hàng)</label>';
            html += '<input type="text" class="form-control" id="editPickupLocation" value="' + (order.pickup_location || '') + '">';
            html += '</div>';

            html += '<div class="col-12">';
            html += '<label class="form-label small fw-bold">Ghi chú</label>';
            html += '<textarea class="form-control" id="editNote" rows="2">' + (order.note || '') + '</textarea>';
            html += '</div>';

            html += '</div>'; // row
            return html;
        }

        function buildOptions(selected, map){
            const cur = (selected || '').toString().toLowerCase();
            return Object.keys(map).map(function(key){
                const sel = (cur === key) ? ' selected' : '';
                return '<option value="' + key + '"' + sel + '>' + map[key] + '</option>';
            }).join('');
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

