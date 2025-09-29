@extends('layouts.admin')
@section('title','Nhập hàng')

@section('content')
  <!-- Main content area -->
  <div class="row g-3">
    <!-- Products table - Left side (main content) -->
    <div class="col-lg-8">
      @include('admin.products.Nhaphang.Import.partials.toolbar')
      @include('admin.products.Nhaphang.Import.partials.items-table')
    </div>
    
    <!-- Form details - Right sidebar -->
    <div class="col-lg-4">
      @include('admin.products.Nhaphang.Import.partials.summary-panel')
    </div>
  </div>
</div>
@include('admin.products.Nhaphang.Import.partials.modal-payment')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Hanghoa/Nhaphang/import.css') }}">
<style>
  .summary-card {
    position: sticky;
    top: 1rem;
  }
  
  #importItemsBody tr:hover {
    background-color: #f8f9fa;
  }
  
  .table th {
    font-weight: 600;
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle Excel file selection button
    const fileSelectBtn = document.querySelector('.btn-primary.btn-lg');
    if (fileSelectBtn) {
      fileSelectBtn.addEventListener('click', function() {
        // Create file input element
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.xls,.xlsx';
        fileInput.style.display = 'none';
        document.body.appendChild(fileInput);
        
        // Handle file selection
        fileInput.addEventListener('change', function(e) {
          if (e.target.files.length > 0) {
            const file = e.target.files[0];
            console.log('Selected file:', file.name);
          }
          document.body.removeChild(fileInput);
        });
      });
    }
    
    // Set up search box shortcut (F3)
    document.addEventListener('keydown', function(e) {
      if (e.key === 'F3') {
        e.preventDefault();
        document.querySelector('input[placeholder*="F3"]')?.focus();
      }
    });
  });
</script>
@endpush
@endsection