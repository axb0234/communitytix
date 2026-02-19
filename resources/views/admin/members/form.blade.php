@extends('admin.layout')
@section('page-title', 'Edit Member')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.members.update', $member) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $member->first_name) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required value="{{ old('last_name', $member->last_name) }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $member->address) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $member->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
            <a href="{{ route('admin.members.show', $member) }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
