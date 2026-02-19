@extends('admin.layout')
@section('page-title', $item ? 'Edit Carousel Item' : 'Add Carousel Item')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $item ? route('admin.home-content.carousel.update', $item) : route('admin.home-content.carousel.store') }}" enctype="multipart/form-data">
            @csrf
            @if($item) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Image {{ $item ? '' : '*' }}</label>
                <input type="file" name="image" class="form-control" accept="image/*" {{ $item ? '' : 'required' }}>
                @if($item)<img src="{{ route('storage.file', $item->image_path) }}" class="mt-2 img-thumbnail" style="max-height:100px;">@endif
            </div>
            <div class="mb-3">
                <label class="form-label">Caption</label>
                <input type="text" name="caption" class="form-control" value="{{ old('caption', $item->caption ?? '') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $item->subtitle ?? '') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Link URL</label>
                <input type="url" name="link_url" class="form-control" value="{{ old('link_url', $item->link_url ?? '') }}">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" class="form-check-input" {{ old('active', $item->active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $item ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.home-content.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
