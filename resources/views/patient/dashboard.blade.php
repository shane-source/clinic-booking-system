@extends('layout')
@section('title','My Appointments')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
  <div>
    <h2 class="section-title" style="border:none;margin:0">My Appointments</h2>
    <p style="color:#64748b;font-size:14px;margin-top:4px;">
      {{ $appointments->count() }} appointment{{ $appointments->count() !== 1 ? 's' : '' }} total
    </p>
  </div>
  <a href="/book" class="btn btn-primary">+ Book Appointment</a>
</div>

@if($appointments->isEmpty())
  <div class="empty">
    <h3>No appointments yet</h3>
    <p style="margin-bottom:20px">Book your first appointment with a doctor</p>
    <a href="/book" class="btn btn-primary">Book Now</a>
  </div>
@else
  <div class="grid-3">
    @foreach($appointments as $appt)
      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">{{ $appt->doctor->user->name ?? 'Doctor' }}</p>
            <p class="card-sub">{{ $appt->clinic->name ?? 'Clinic' }}</p>
          </div>
          <span class="badge badge-{{ $appt->status }}">{{ $appt->status }}</span>
        </div>

        <div style="display:flex;justify-content:space-between;color:#94a3b8;font-size:13px;margin-bottom:12px;">
          <span>📅 {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</span>
          <span>⏰ {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</span>
        </div>

        @if($appt->notes)
          <p style="color:#64748b;font-size:13px;margin-bottom:12px;
                    background:#0f172a;border-radius:6px;padding:8px 12px;">
            {{ $appt->notes }}
          </p>
        @endif

        @if($appt->status === 'pending' || $appt->status === 'confirmed')
          <form method="POST" action="/appointments/{{ $appt->id }}/cancel"
                onsubmit="return confirm('Cancel this appointment?')">
            @csrf
            <button class="btn btn-danger btn-sm" style="width:100%">Cancel</button>
          </form>
        @endif
      </div>
    @endforeach
  </div>
@endif

@endsection
