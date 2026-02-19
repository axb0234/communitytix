@extends('public.layout')
@section('title', 'Events - ' . $tenant->name)

@section('content')
<section class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold">Events</h1>
        <p class="lead">Upcoming events from {{ $tenant->name }}</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h3 class="fw-bold mb-4">Upcoming Events</h3>
        @if($upcomingEvents->count())
        <div class="row g-4">
            @foreach($upcomingEvents as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100">
                    @if($event->flyer_path)
                        <img src="{{ route('storage.file', $event->flyer_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height:220px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $event->isTicketed() ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $event->isTicketed() ? 'Ticketed' : 'Free RSVP' }}
                            </span>
                        </div>
                        <h5 class="card-title fw-bold">{{ $event->title }}</h5>
                        <p class="text-muted mb-1"><i class="fas fa-calendar me-2"></i>{{ $event->start_at->format('D, M j, Y \a\t g:i A') }}</p>
                        @if($event->location)<p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}</p>@endif
                        @if($event->short_description)<p class="small">{{ Str::limit($event->short_description, 100) }}</p>@endif
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $upcomingEvents->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">No upcoming events right now. Check back soon!</p>
        </div>
        @endif

        @if($pastEvents->count())
        <h3 class="fw-bold mb-4 mt-5">Past Events</h3>
        <div class="row g-4">
            @foreach($pastEvents as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 opacity-75">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $event->title }}</h5>
                        <p class="text-muted"><i class="fas fa-calendar me-2"></i>{{ $event->start_at->format('M j, Y') }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline-secondary btn-sm w-100">View</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
