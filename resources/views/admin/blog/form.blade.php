@extends('admin.layout')
@section('page-title', $post ? 'Edit Post' : 'New Post')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $post ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data">
            @csrf
            @if($post) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title', $post->title ?? '') }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Content *</label>
                <textarea name="body_html" class="wysiwyg-editor @error('body_html') is-invalid @enderror" required>{{ old('body_html', $post->body_html ?? '') }}</textarea>
                @error('body_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Featured Image</label>
                    <input type="file" name="featured_image_file" class="form-control" accept="image/*">
                    @if($post?->featured_image)
                        <img src="{{ route('storage.local', $post->featured_image) }}" class="mt-2 img-thumbnail" style="max-height:100px;">
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $post ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@section('wysiwyg', true)
@endsection
