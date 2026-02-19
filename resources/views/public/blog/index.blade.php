@extends('public.layout')
@section('title', 'Blog - ' . $tenant->name)

@section('content')
<section class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">Blog</h1>
        <p class="lead">News and updates from {{ $tenant->name }}</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($posts->count())
        <div class="row g-4">
            @foreach($posts as $post)
            <div class="col-md-4">
                <div class="card card-hover h-100">
                    @if($post->featured_image)
                        <img src="{{ route('storage.file', $post->featured_image) }}" class="card-img-top" alt="{{ $post->title }}" style="height:200px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $post->published_at->format('M j, Y') }}</small>
                        <h5 class="card-title mt-2 fw-bold">{{ $post->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($post->excerpt ?? strip_tags($post->body_html), 150) }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $posts->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
            <p class="text-muted">No blog posts yet. Check back soon!</p>
        </div>
        @endif
    </div>
</section>
@endsection
