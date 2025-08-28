<!-- Modal Tạo Nhà Cung Cấp -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createSupplierModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Thêm Nhà Cung Cấp Mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createSupplierForm" action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.products.Nhaphang.Suppliers.partials.supplier-form')
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Lưu nhà cung cấp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@include('admin.products.Nhaphang.Suppliers.partials.supplier-modal-scripts')
@endpush
