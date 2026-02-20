@extends('public.layout')
@section('title', $event->title . ' - ' . $tenant->name)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if($event->flyer_path)
                    <img src="{{ route('storage.local', $event->flyer_path) }}" class="img-fluid rounded mb-4" alt="{{ $event->title }}">
                @endif

                <h1 class="fw-bold">{{ $event->title }}</h1>
                <div class="d-flex flex-wrap gap-3 text-muted my-3">
                    <span><i class="fas fa-calendar me-1"></i>{{ $event->start_at->format('l, F j, Y') }}</span>
                    <span><i class="fas fa-clock me-1"></i>{{ $event->start_at->format('g:i A') }}@if($event->end_at) - {{ $event->end_at->format('g:i A') }}@endif</span>
                    @if($event->location)<span><i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}</span>@endif
                </div>
                @if($event->location_address)
                    <p class="text-muted small"><i class="fas fa-directions me-1"></i>{{ $event->location_address }}</p>
                @endif

                @if($event->body_html)
                    <div class="mt-4">{!! $event->body_html !!}</div>
                @endif

                {{-- Event Images Gallery --}}
                @if($event->images->count())
                <div class="mt-4">
                    <h4 class="fw-bold mb-3">Gallery</h4>
                    <div class="row g-2">
                        @foreach($event->images as $img)
                        <div class="col-6 col-md-4">
                            <img src="{{ route('storage.local', $img->image_path) }}" class="img-fluid rounded" alt="{{ $img->caption }}" style="height:200px;object-fit:cover;width:100%;">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm sticky-top" style="top:80px;">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">
                            @if($event->isTicketed()) Get Tickets @else RSVP @endif
                        </h4>

                        @if($event->isFree())
                        {{-- RSVP Form --}}
                        <form method="POST" action="{{ route('events.rsvp', $event->slug) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="guests" class="form-control" value="{{ old('guests', 1) }}" min="1" max="10">
                            </div>
                            @if($event->rsvp_capacity)
                                <p class="small text-muted">Capacity: {{ $event->rsvp_capacity }} | Reserved: {{ $event->rsvps->sum('guests') }}</p>
                            @endif
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-check me-2"></i>Confirm RSVP</button>
                        </form>

                        @elseif($event->isTicketed())
                        {{-- Ticket Selection --}}
                        @if($event->ticketTypes->count() || $event->isPwyw())
                        <form method="POST" action="{{ route('events.checkout', $event->slug) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>

                            @if($event->ticketTypes->count())
                            <hr>
                            <h6 class="fw-bold mb-3">Select Tickets</h6>
                            @foreach($event->ticketTypes as $i => $tt)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                <div>
                                    <strong>{{ $tt->name }}</strong><br>
                                    <span class="text-primary fw-bold">{{ $tenant->currency }} {{ number_format($tt->price, 2) }}</span>
                                    @if($tt->capacity)<br><small class="text-muted">{{ $tt->available ?? 'Unlimited' }} left</small>@endif
                                </div>
                                <div style="width:80px;">
                                    <input type="hidden" name="tickets[{{ $i }}][ticket_type_id]" value="{{ $tt->id }}">
                                    <input type="number" name="tickets[{{ $i }}][qty]" class="form-control form-control-sm text-center" value="0" min="0" max="{{ $tt->available ?? 20 }}">
                                </div>
                            </div>
                            @endforeach
                            @endif

                            @if($event->isPwyw())
                            <hr>
                            <h6 class="fw-bold mb-3"><i class="fas fa-hand-holding-heart me-1"></i>Pay What You Can</h6>
                            <p class="small text-muted mb-2">Choose a suggested amount or enter your own.</p>
                            <div class="d-flex gap-2 mb-3">
                                @foreach(['pwyw_amount_1', 'pwyw_amount_2', 'pwyw_amount_3'] as $amountField)
                                    @if($event->$amountField)
                                    <button type="button" class="btn btn-outline-primary pwyw-btn" data-amount="{{ $event->$amountField }}">
                                        {{ $tenant->currency }} {{ number_format($event->$amountField, 2) }}
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">{{ $tenant->currency }}</span>
                                <input type="number" name="pwyw_amount" id="pwyw_amount" class="form-control" step="0.01" min="0.01" placeholder="Enter amount" value="{{ old('pwyw_amount') }}">
                            </div>
                            @endif

                            <button type="submit" class="btn btn-accent w-100 mt-2"><i class="fas fa-shopping-cart me-2"></i>Proceed to Payment</button>
                        </form>
                        @else
                        <p class="text-muted">Tickets are not yet available. Check back soon.</p>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Events</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pwyw-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pwyw-btn').forEach(b => b.classList.remove('btn-primary'));
            document.querySelectorAll('.pwyw-btn').forEach(b => b.classList.add('btn-outline-primary'));
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            document.getElementById('pwyw_amount').value = this.dataset.amount;
        });
    });
});
</script>
@endpush
