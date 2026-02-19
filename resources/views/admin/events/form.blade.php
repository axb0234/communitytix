@extends('admin.layout')
@section('page-title', $event ? 'Edit Event' : 'New Event')

@section('content')
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Event Details</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $event ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf
            @if($event) @method('PUT') @endif

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title', $event->title ?? '') }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Event Type *</label>
                    <select name="event_type" class="form-select" required>
                        <option value="FREE" {{ old('event_type', $event->event_type ?? '') === 'FREE' ? 'selected' : '' }}>Free RSVP</option>
                        <option value="TICKETED" {{ old('event_type', $event->event_type ?? '') === 'TICKETED' ? 'selected' : '' }}>Ticketed</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start Date/Time *</label>
                    <input type="datetime-local" name="start_at" class="form-control" required value="{{ old('start_at', $event ? $event->start_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">End Date/Time</label>
                    <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', $event?->end_at?->format('Y-m-d\TH:i') ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status', $event->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $event->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $event->location ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="location_address" class="form-control" value="{{ old('location_address', $event->location_address ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Short Description</label>
                    <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $event->short_description ?? '') }}</textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">RSVP Capacity</label>
                    <input type="number" name="rsvp_capacity" class="form-control" min="1" value="{{ old('rsvp_capacity', $event->rsvp_capacity ?? '') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Flyer / Poster</label>
                    <input type="file" name="flyer_file" class="form-control" accept="image/*,.pdf">
                    @if($event?->flyer_path)<small class="text-muted">Current: {{ basename($event->flyer_path) }}</small>@endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Description</label>
                <textarea name="body_html" class="wysiwyg-editor">{{ old('body_html', $event->body_html ?? '') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $event ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@section('wysiwyg', true)

@if($event)
{{-- Ticket Types --}}
@if($event->event_type === 'TICKETED')
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Ticket Types</h5></div>
    <div class="card-body">
        @if($event->ticketTypes->count())
        <table class="table table-sm">
            <thead><tr><th>Name</th><th>Price</th><th>Capacity</th><th>Sold</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($event->ticketTypes as $tt)
                <tr>
                    <td>{{ $tt->name }}</td>
                    <td>{{ number_format($tt->price, 2) }}</td>
                    <td>{{ $tt->capacity ?? 'Unlimited' }}</td>
                    <td>{{ $tt->sold_count }}</td>
                    <td><span class="badge bg-{{ $tt->active ? 'success' : 'secondary' }}">{{ $tt->active ? 'Yes' : 'No' }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.events.ticket-types.destroy', [$event, $tt]) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <form method="POST" action="{{ route('admin.events.ticket-types.store', $event) }}" class="mt-3">
            @csrf
            <h6>Add Ticket Type</h6>
            <div class="row g-2 align-items-end">
                <div class="col-md-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control form-control-sm" required></div>
                <div class="col-md-2"><label class="form-label">Price</label><input type="number" name="price" class="form-control form-control-sm" step="0.01" min="0" required></div>
                <div class="col-md-2"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control form-control-sm" min="1"></div>
                <div class="col-md-2"><label class="form-label">Sort</label><input type="number" name="sort_order" class="form-control form-control-sm" value="0"></div>
                <div class="col-md-3"><button type="submit" class="btn btn-sm btn-success w-100"><i class="fas fa-plus me-1"></i>Add</button></div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Event Images --}}
<div class="card">
    <div class="card-header"><h5 class="mb-0">Event Images</h5></div>
    <div class="card-body">
        @if($event->images->count())
        <div class="row g-2 mb-3">
            @foreach($event->images as $img)
            <div class="col-4 col-md-3 position-relative">
                <img src="{{ route('storage.file', $img->image_path) }}" class="img-thumbnail" style="height:120px;object-fit:cover;width:100%;">
                <form method="POST" action="{{ route('admin.events.images.destroy', [$event, $img]) }}" class="position-absolute top-0 end-0">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.events.images.store', $event) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-5"><label class="form-label">Image</label><input type="file" name="image" class="form-control form-control-sm" accept="image/*" required></div>
                <div class="col-md-4"><label class="form-label">Caption</label><input type="text" name="caption" class="form-control form-control-sm"></div>
                <div class="col-md-3"><button type="submit" class="btn btn-sm btn-success w-100"><i class="fas fa-upload me-1"></i>Upload</button></div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
