@extends('public.layout')
@section('title', $post->title . ' - ' . $tenant->name)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($post->featured_image)
                    <img src="{{ route('storage.local', $post->featured_image) }}" class="img-fluid rounded mb-4" alt="{{ $post->title }}">
                @endif
                <h1 class="fw-bold mb-3">{{ $post->title }}</h1>
                <div class="text-muted mb-4">
                    <i class="fas fa-clock me-1"></i>{{ $post->published_at->format('F j, Y') }}
                    @if($post->author) <span class="ms-3"><i class="fas fa-user me-1"></i>{{ $post->author->name }}</span> @endif
                </div>
                <div class="blog-content">{!! $post->body_html !!}</div>
                <hr class="my-4">
                <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Blog</a>
            </div>
        </div>
    </div>
</section>
@endsection
