@extends('layout')
@section('title','Doctor Dashboard')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
  <div>
    <h2 class="section-title" style="border:none;margin:0">Doctor Dashboard</h2>
    <p style="color:#64748b;font-size:14px;margin-top:4px;">
      {{ $appointments->count() }} appointment{{ $appointments->count() !== 1 ? 's' : '' }}
    </p>
  </div>
</div>

<!-- Stats -->
<div class="grid-4" style="margin-bottom:28px;">
  @php
    $pending   = $appointments->where('status','pending')->count();
    $confirmed = $appointments->where('status','confirmed')->count();
    $cancelled = $appointments->where('status','cancelled')->count();
    $completed = $appointments->where('status','completed')->count();
  @endphp
  <div class="stat-card"><div class="stat-num" style="color:#f59e0b">{{ $pending }}</div><div class="stat-label">Pending</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#10b981">{{ $confirmed }}</div><div class="stat-label">Confirmed</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#ef4444">{{ $cancelled }}</div><div class="stat-label">Cancelled</div></div>
  <div class="stat-card"><div class="stat-num" style="color:#818cf8">{{ $completed }}</div><div class="stat-label">Completed</div></div>
</div>

@if($appointments->isEmpty())
  <div class="empty">
    <h3>No appointments yet</h3>
    <p>Patients will appear here when they book with you</p>
  </div>
@else
  <!-- Filter tabs -->
  <div class="tabs">
    <button class="tab active" onclick="filterTab(this,'all')">All</button>
    <button class="tab" onclick="filterTab(this,'pending')">Pending</button>
    <button class="tab" onclick="filterTab(this,'confirmed')">Confirmed</button>
    <button class="tab" onclick="filterTab(this,'cancelled')">Cancelled</button>
  </div>

  <div id="appointmentsList">
    @foreach($appointments as $appt)
      <div class="card appt-row" data-status="{{ $appt->status }}"
           style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
          <p class="card-title">{{ $appt->patient->name ?? 'Patient' }}</p>
          <p class="card-sub">{{ $appt->patient->email ?? '' }}</p>
          <p style="color:#94a3b8;font-size:13px;margin-top:6px;">
            📅 {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
            &nbsp;⏰ {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
          </p>
          @if($appt->notes)
            <p style="color:#64748b;font-size:13px;margin-top:4px;">📝 {{ $appt->notes }}</p>
          @endif
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <span class="badge badge-{{ $appt->status }}">{{ $appt->status }}</span>
          @if($appt->status === 'pending')
            <form method="POST" action="/doctor/appointments/{{ $appt->id }}/confirm" style="display:inline">
              @csrf
              <button class="btn btn-success btn-sm">✓ Confirm</button>
            </form>
            <form method="POST" action="/doctor/appointments/{{ $appt->id }}/cancel" style="display:inline"
                  onsubmit="return confirm('Cancel this appointment?')">
              @csrf
              <button class="btn btn-danger btn-sm">✗ Cancel</button>
            </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>
@endif

@endsection
@section('scripts')
<script>
  function filterTab(el, status) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.appt-row').forEach(row => {
      row.style.display = (status === 'all' || row.dataset.status === status) ? 'flex' : 'none';
    });
  }
</script>
@endsection
