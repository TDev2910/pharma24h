@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Danh sách nhóm hàng</h3>
        <ul>
            @foreach($categories as $cat)
                <li>
                    {{ $cat->name }}
                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-primary">Chỉnh sửa</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection