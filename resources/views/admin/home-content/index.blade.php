@extends('admin.layout')
@section('page-title', 'Home Page Content')

@section('content')
{{-- Carousel --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Carousel Items</h5>
        <a href="{{ route('admin.home-content.carousel.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add</a>
    </div>
    <div class="card-body p-0 table-responsive-wrap">
        <table class="table table-hover mb-0">
            <thead><tr><th>Image</th><th>Caption</th><th>Order</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($carouselItems as $item)
                <tr>
                    <td><img src="{{ route('storage.local', $item->image_path) }}" style="height:50px;width:80px;object-fit:cover;" class="rounded"></td>
                    <td>{{ $item->caption ?? '-' }}</td>
                    <td>{{ $item->sort_order }}</td>
                    <td><span class="badge bg-{{ $item->active ? 'success' : 'secondary' }}">{{ $item->active ? 'Yes' : 'No' }}</span></td>
                    <td>
                        <a href="{{ route('admin.home-content.carousel.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.home-content.carousel.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No carousel items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Content Blocks --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-th-large me-2"></i>Content Blocks</h5>
        <a href="{{ route('admin.home-content.blocks.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add</a>
    </div>
    <div class="card-body p-0 table-responsive-wrap">
        <table class="table table-hover mb-0">
            <thead><tr><th>Title</th><th>Icon</th><th>Order</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($contentBlocks as $block)
                <tr>
                    <td>{{ $block->title }}</td>
                    <td>@if($block->icon)<i class="{{ $block->icon }}"></i> {{ $block->icon }}@else - @endif</td>
                    <td>{{ $block->sort_order }}</td>
                    <td><span class="badge bg-{{ $block->active ? 'success' : 'secondary' }}">{{ $block->active ? 'Yes' : 'No' }}</span></td>
                    <td>
                        <a href="{{ route('admin.home-content.blocks.edit', $block) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.home-content.blocks.destroy', $block) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No content blocks.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
