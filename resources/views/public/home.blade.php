@extends('public.layout')
@section('title', $tenant->name . ' - Home')

@section('content')
{{-- Carousel --}}
@if($carouselItems->count())
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        @foreach($carouselItems as $i => $item)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" {!! $i === 0 ? 'class="active"' : '' !!}></button>
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach($carouselItems as $i => $item)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ route('storage.file', $item->image_path) }}" class="d-block w-100" alt="{{ $item->caption }}">
                @if($item->caption)
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="fw-bold">{{ $item->caption }}</h2>
                    @if($item->subtitle)<p>{{ $item->subtitle }}</p>@endif
                </div>
                @endif
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
@else
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">{{ $tenant->name }}</h1>
        @if($tenant->tagline)<p class="lead mt-3">{{ $tenant->tagline }}</p>@endif
        <a href="{{ route('events.index') }}" class="btn btn-primary btn-lg mt-3"><i class="fas fa-calendar-alt me-2"></i>View Events</a>
    </div>
</section>
@endif

{{-- Content Blocks --}}
@if($contentBlocks->count())
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach($contentBlocks as $block)
            <div class="col-md-4">
                <div class="text-center p-4">
                    @if($block->icon)<div class="mb-3"><i class="{{ $block->icon }} fa-3x text-primary"></i></div>@endif
                    <h4 class="fw-bold">{{ $block->title }}</h4>
                    <div class="text-muted">{!! $block->body_html !!}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Upcoming Event --}}
@if($upcomingEvent)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-heading">Upcoming Event</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-hover border-0 shadow">
                    @if($upcomingEvent->flyer_path)
                        <img src="{{ route('storage.file', $upcomingEvent->flyer_path) }}" class="card-img-top" alt="{{ $upcomingEvent->title }}" style="max-height:400px;object-fit:cover;">
                    @endif
                    <div class="card-body p-4">
                        <h3 class="card-title fw-bold">{{ $upcomingEvent->title }}</h3>
                        <p class="text-muted">
                            <i class="fas fa-calendar me-2"></i>{{ $upcomingEvent->start_at->format('D, M j, Y \a\t g:i A') }}
                            @if($upcomingEvent->location)<br><i class="fas fa-map-marker-alt me-2"></i>{{ $upcomingEvent->location }}@endif
                        </p>
                        @if($upcomingEvent->short_description)<p>{{ $upcomingEvent->short_description }}</p>@endif
                        <a href="{{ route('events.show', $upcomingEvent->slug) }}" class="btn btn-primary">
                            @if($upcomingEvent->isTicketed()) Get Tickets @else RSVP Now @endif <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Latest Blog Posts --}}
@if($latestPosts->count())
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-heading">Latest News</h2>
        </div>
        <div class="row g-4">
            @foreach($latestPosts as $post)
            <div class="col-sm-6 col-md-4">
                <div class="card card-hover h-100">
                    @if($post->featured_image)
                        <img src="{{ route('storage.file', $post->featured_image) }}" class="card-img-top" alt="{{ $post->title }}" style="height:200px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $post->published_at->format('M j, Y') }}</small>
                        <h5 class="card-title mt-2 fw-bold">{{ $post->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($post->excerpt ?? strip_tags($post->body_html), 120) }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">View All Posts</a>
        </div>
    </div>
</section>
@endif

{{-- Join CTA --}}
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <h2 class="fw-bold">Join Our Community</h2>
        <p class="lead mt-2">Become a member and stay connected with us.</p>
        <a href="{{ route('members.signup') }}" class="btn btn-primary btn-lg mt-3"><i class="fas fa-user-plus me-2"></i>Sign Up Now</a>
    </div>
</section>
@endsection
