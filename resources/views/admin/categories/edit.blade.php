@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Sửa nhóm hàng</h4>
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên nhóm</label>
            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
        </div>
        <div class="mb-3">
            <label for="parent_id" class="form-label">Nhóm cha</label>
            <select name="parent_id" class="form-select">
                <option value="">Chọn nhóm hàng</option>
                @foreach($parents as $id => $name)
                    {{-- Skip current category to prevent self-reference --}}
                    @if($id != $category->id)
                        <option value="{{ $id }}" {{ $category->parent_id == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Bỏ qua</a>
    </form>
    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
        @csrf @method('DELETE')
        <button class="btn btn-danger mt-2" onclick="return confirm('Xóa nhóm này?')">Xóa</button>
    </form>
</div>
@endsection