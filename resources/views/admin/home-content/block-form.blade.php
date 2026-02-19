@extends('admin.layout')
@section('page-title', $block ? 'Edit Content Block' : 'Add Content Block')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $block ? route('admin.home-content.blocks.update', $block) : route('admin.home-content.blocks.store') }}">
            @csrf
            @if($block) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $block->title ?? '') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Content (HTML) *</label>
                <textarea name="body_html" class="form-control" rows="6" required>{{ old('body_html', $block->body_html ?? '') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Icon (Font Awesome class, e.g. "fas fa-heart")</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $block->icon ?? '') }}">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $block->sort_order ?? 0) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" class="form-check-input" {{ old('active', $block->active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $block ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.home-content.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
