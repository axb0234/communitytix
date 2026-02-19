@extends('admin.layout')
@section('page-title', 'Blog Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
        <select name="status" class="form-select form-select-sm" style="width:120px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </form>
    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Post</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td><a href="{{ route('admin.blog.edit', $post) }}">{{ $post->title }}</a></td>
                    <td>{{ $post->author->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span></td>
                    <td>{{ $post->published_at?->format('M j, Y') ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No blog posts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $posts->links() }}</div>
@endsection
