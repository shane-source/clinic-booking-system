@extends('layout')
@section('title','Doctor Dashboard')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
  <div>
    <h2 class="section-title" style="border:none;margin:0">
      🩺 Doctor Dashboard
    </h2>
    <p style="color:#64748b;font-size:14px;margin-top:4px;">
      Welcome, {{ Auth::user()->name }}
      @if($doctor)
        — {{ $doctor->specialty }}
      @endif
    </p>
  </div>
</div>

@if(!$doctor)
  <div class="card" style="border-color:#f59e0b;background:#78350f22;">
    <p style="color:#f59e0b;font-weight:700;margin-bottom:6px;">⚠️ No doctor profile found</p>
    <p style="color:#94a3b8;font-size:14px;">
      Ask the admin to create a doctor profile linked to your account.
    </p>
  </div>
@else

  <!-- Doctor Info Card -->
  <div class="card" style="margin-bottom:28px;border-color:#334155;">
    <div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
      <div style="background:#2563eb22;border-radius:50%;width:64px;height:64px;
                  display:flex;align-items:center;justify-content:center;font-size:28px;">
        🩺
      </div>
      <div style="flex:1;">
        <p style="color:#f1f5f9;font-size:20px;font-weight:800;margin:0">
          {{ Auth::user()->name }}
        </p>
        <p style="color:#60a5fa;font-size:14px;margin:4px 0 2px">
          {{ $doctor->specialty }}
        </p>
        <p style="color:#64748b;font-size:13px;">
          🏥 {{ $doctor->clinic->name ?? 'No clinic assigned' }}
        </p>
      </div>
      <div style="text-align:right;">
        <p style="color:#64748b;font-size:13px;">Total Appointments</p>
        <p style="color:#f1f5f9;font-size:32px;font-weight:800;margin:0">
          {{ $appointments->count() }}
        </p>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="grid-4" style="margin-bottom:28px;">
    @php
      $pending   = $appointments->where('status','pending')->count();
      $confirmed = $appointments->where('status','confirmed')->count();
      $cancelled = $appointments->where('status','cancelled')->count();
      $today     = $appointments->filter(function($a) {
                     return \Carbon\Carbon::parse($a->appointment_date)->isToday();
                   })->count();
    @endphp
    <div class="stat-card" style="border-color:#f59e0b44;">
      <div class="stat-num" style="color:#f59e0b">{{ $pending }}</div>
      <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card" style="border-color:#10b98144;">
      <div class="stat-num" style="color:#10b981">{{ $confirmed }}</div>
      <div class="stat-label">Confirmed</div>
    </div>
    <div class="stat-card" style="border-color:#ef444444;">
      <div class="stat-num" style="color:#ef4444">{{ $cancelled }}</div>
      <div class="stat-label">Cancelled</div>
    </div>
    <div class="stat-card" style="border-color:#60a5fa44;">
      <div class="stat-num" style="color:#60a5fa">{{ $today }}</div>
      <div class="stat-label">Today</div>
    </div>
  </div>

  <!-- Filter Tabs -->
  <div class="tabs">
    <button class="tab active" onclick="filterTab(this,'all')">
      All ({{ $appointments->count() }})
    </button>
    <button class="tab" onclick="filterTab(this,'pending')">
      Pending ({{ $pending }})
    </button>
    <button class="tab" onclick="filterTab(this,'confirmed')">
      Confirmed ({{ $confirmed }})
    </button>
    <button class="tab" onclick="filterTab(this,'cancelled')">
      Cancelled ({{ $cancelled }})
    </button>
  </div>

  @if($appointments->isEmpty())
    <div class="empty">
      <h3>No appointments yet</h3>
      <p>Patients will appear here when they book with you</p>
    </div>
  @else
    <div id="appointmentsList">
      @foreach($appointments as $appt)
        <div class="card appt-row" data-status="{{ $appt->status }}"
             style="margin-bottom:12px;">
          <div style="display:flex;justify-content:space-between;
                      align-items:flex-start;flex-wrap:wrap;gap:12px;">

            <!-- Patient Info -->
            <div style="display:flex;gap:14px;align-items:center;">
              <div style="background:#334155;border-radius:50%;width:44px;height:44px;
                          display:flex;align-items:center;justify-content:center;
                          font-size:18px;flex-shrink:0;">
                👤
              </div>
              <div>
                <p style="color:#f1f5f9;font-weight:700;font-size:16px;margin:0">
                  {{ $appt->patient->name ?? 'Patient' }}
                </p>
                <p style="color:#64748b;font-size:13px;margin:3px 0 0">
                  {{ $appt->patient->email ?? '' }}
                </p>
                @if($appt->notes)
                  <p style="color:#94a3b8;font-size:13px;margin:6px 0 0;
                             background:#0f172a;border-radius:6px;padding:6px 10px;
                             border-left:3px solid #334155;">
                    📝 {{ $appt->notes }}
                  </p>
                @endif
              </div>
            </div>

            <!-- Date, Time, Status, Actions -->
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
              <span class="badge badge-{{ $appt->status }}">{{ $appt->status }}</span>
              <p style="color:#94a3b8;font-size:13px;margin:0;text-align:right;">
                📅 {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
              </p>
              <p style="color:#94a3b8;font-size:13px;margin:0;text-align:right;">
                ⏰ {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
              </p>
              @if(\Carbon\Carbon::parse($appt->appointment_date)->isToday())
                <span style="background:#1e3a8a;color:#93c5fd;border-radius:20px;
                             padding:2px 10px;font-size:11px;font-weight:700;">
                  TODAY
                </span>
              @endif
            </div>
          </div>

          <!-- Action Buttons -->
          @if($appt->status === 'pending')
            <div style="display:flex;gap:10px;margin-top:16px;
                        padding-top:16px;border-top:1px solid #334155;">
              <form method="POST" action="/doctor/appointments/{{ $appt->id }}/confirm"
                    style="flex:1">
                @csrf
                <button class="btn btn-success" style="width:100%">
                  ✓ Confirm Appointment
                </button>
              </form>
              <form method="POST" action="/doctor/appointments/{{ $appt->id }}/cancel"
                    style="flex:1"
                    onsubmit="return confirm('Cancel this appointment?')">
                @csrf
                <button class="btn btn-danger" style="width:100%">
                  ✗ Cancel Appointment
                </button>
              </form>
            </div>
          @elseif($appt->status === 'confirmed')
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #334155;">
              <p style="color:#10b981;font-size:13px;">
                ✅ Confirmed — patient has been notified
              </p>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endif

@endif

@endsection

@section('scripts')
<script>
  function filterTab(el, status) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.appt-row').forEach(row => {
      row.style.display = (status === 'all' || row.dataset.status === status)
        ? 'block' : 'none';
    });
  }
</script>
@endsection