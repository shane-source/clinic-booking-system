@extends('layout')
@section('title','Admin Dashboard')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
  <h2 class="section-title" style="border:none;margin:0">Admin Dashboard</h2>
  <button class="btn btn-primary" onclick="openModal('clinicModal')">+ Add Clinic</button>
</div>

<!-- Stats -->
<div class="grid-4" style="margin-bottom:32px;">
  <div class="stat-card">
    <div class="stat-num" style="color:#2563eb">{{ $appointments->count() }}</div>
    <div class="stat-label">Total Appointments</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" style="color:#f59e0b">{{ $appointments->where('status','pending')->count() }}</div>
    <div class="stat-label">Pending</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" style="color:#10b981">{{ $appointments->where('status','confirmed')->count() }}</div>
    <div class="stat-label">Confirmed</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" style="color:#8b5cf6">{{ $clinics->count() }}</div>
    <div class="stat-label">Clinics</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" style="color:#ec4899">{{ $doctors->count() }}</div>
    <div class="stat-label">Doctors</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" style="color:#06b6d4">{{ $users->count() }}</div>
    <div class="stat-label">Users</div>
  </div>
</div>

<!-- Tabs -->
<div class="tabs">
  <button class="tab active" onclick="showTab(this,'appointments')">📋 Appointments</button>
  <button class="tab" onclick="showTab(this,'clinics')">🏥 Clinics</button>
  <button class="tab" onclick="showTab(this,'doctors')">🩺 Doctors</button>
  <button class="tab" onclick="showTab(this,'users')">👥 Users</button>
</div>

<!-- APPOINTMENTS TAB -->
<div id="tab-appointments">
  <div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Clinic</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($appointments as $appt)
            <tr>
              <td>
                <p style="color:#f1f5f9;font-weight:600">{{ $appt->patient->name ?? '—' }}</p>
                <p style="color:#64748b;font-size:12px">{{ $appt->patient->email ?? '' }}</p>
              </td>
              <td style="color:#f1f5f9">{{ $appt->doctor->user->name ?? '—' }}</td>
              <td style="color:#94a3b8">{{ $appt->clinic->name ?? '—' }}</td>
              <td style="color:#94a3b8">
                {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
              </td>
              <td style="color:#94a3b8">
                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
              </td>
              <td><span class="badge badge-{{ $appt->status }}">{{ $appt->status }}</span></td>
              <td>
                @if($appt->status === 'pending')
                  <form method="POST" action="/admin/appointments/{{ $appt->id }}/confirm"
                        style="display:inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Confirm</button>
                  </form>
                  <form method="POST" action="/admin/appointments/{{ $appt->id }}/cancel"
                        style="display:inline">
                    @csrf
                    <button class="btn btn-danger btn-sm">Cancel</button>
                  </form>
                @else
                  <span style="color:#334155;font-size:12px">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;color:#64748b;padding:40px">
              No appointments yet
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CLINICS TAB -->
<div id="tab-clinics" style="display:none">
  <div class="grid-3">
    @forelse($clinics as $clinic)
      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">{{ $clinic->name }}</p>
            <p class="card-sub">{{ $clinic->city }}</p>
          </div>
          <span class="badge" style="background:#06402022;color:#10b981;border:1px solid #10b98144">
            Active
          </span>
        </div>
        <p style="color:#64748b;font-size:13px;margin-bottom:6px">📍 {{ $clinic->address }}</p>
        @if($clinic->phone)
          <p style="color:#64748b;font-size:13px;margin-bottom:6px">📞 {{ $clinic->phone }}</p>
        @endif
        @if($clinic->email)
          <p style="color:#64748b;font-size:13px;margin-bottom:12px">✉️ {{ $clinic->email }}</p>
        @endif
        <form method="POST" action="/admin/clinics/{{ $clinic->id }}/delete"
              onsubmit="return confirm('Delete this clinic?')">
          @csrf
          <button class="btn btn-danger btn-sm" style="width:100%">Delete Clinic</button>
        </form>
      </div>
    @empty
      <div class="empty" style="grid-column:1/-1">
        <h3>No clinics yet</h3>
        <p style="margin-bottom:16px">Add your first clinic to get started</p>
        <button class="btn btn-primary" onclick="openModal('clinicModal')">+ Add Clinic</button>
      </div>
    @endforelse
  </div>
</div>

<!-- DOCTORS TAB -->
<div id="tab-doctors" style="display:none">
  <div class="grid-3">
    @forelse($doctors as $doctor)
      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">{{ $doctor->user->name ?? 'Doctor' }}</p>
            <p class="card-sub">{{ $doctor->specialty }}</p>
          </div>
          <span class="badge" style="background:#1e1b4b22;color:#818cf8;border:1px solid #818cf844">
            Doctor
          </span>
        </div>
        <p style="color:#64748b;font-size:13px;margin-bottom:4px">
          🏥 {{ $doctor->clinic->name ?? 'No clinic' }}
        </p>
        <p style="color:#64748b;font-size:13px">
          ✉️ {{ $doctor->user->email ?? '' }}
        </p>
      </div>
    @empty
      <div class="empty" style="grid-column:1/-1">
        <h3>No doctors yet</h3>
        <p>Doctors are created when users are assigned the doctor role</p>
      </div>
    @endforelse
  </div>
</div>

<!-- USERS TAB -->
<div id="tab-users" style="display:none">
  <div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Change Role</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td style="color:#f1f5f9;font-weight:600">{{ $user->name }}</td>
              <td style="color:#94a3b8">{{ $user->email }}</td>
              <td>
                <span class="badge badge-{{ $user->roles->first()?->name === 'admin' ? 'confirmed' : ($user->roles->first()?->name === 'doctor' ? 'completed' : 'pending') }}">
                  {{ $user->roles->first()?->name ?? 'no role' }}
                </span>
              </td>
              <td style="color:#64748b;font-size:13px">
                {{ $user->created_at->format('M d, Y') }}
              </td>
              <td>
                @if($user->id !== Auth::id())
                  <form method="POST" action="/admin/users/{{ $user->id }}/role"
                        style="display:flex;gap:6px;align-items:center;">
                    @csrf
                    <select name="role" class="form-input"
                            style="padding:6px 10px;font-size:13px;width:auto;">
                      <option value="patient"  {{ $user->hasRole('patient')  ? 'selected' : '' }}>Patient</option>
                      <option value="doctor"   {{ $user->hasRole('doctor')   ? 'selected' : '' }}>Doctor</option>
                      <option value="admin"    {{ $user->hasRole('admin')    ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button class="btn btn-primary btn-sm">Save</button>
                  </form>
                @else
                  <span style="color:#334155;font-size:12px">You</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" style="text-align:center;color:#64748b;padding:40px">
              No users found
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD CLINIC MODAL -->
<div class="modal-overlay" id="clinicModal">
  <div class="modal">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3 class="modal-title" style="margin:0">Add New Clinic</h3>
      <button class="modal-close" onclick="closeModal('clinicModal')">✕</button>
    </div>
    <form method="POST" action="/admin/clinics">
      @csrf
      <div class="form-group">
        <label class="form-label">Clinic Name *</label>
        <input type="text" name="name" class="form-input" placeholder="City Medical Center" required/>
      </div>
      <div class="form-group">
        <label class="form-label">Address *</label>
        <input type="text" name="address" class="form-input" placeholder="123 Main Street" required/>
      </div>
      <div class="form-group">
        <label class="form-label">City *</label>
        <input type="text" name="city" class="form-input" placeholder="Nairobi" required/>
      </div>
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-input" placeholder="+254 700 000 000"/>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-input" placeholder="clinic@email.com"/>
      </div>
      <div style="display:flex;gap:10px;margin-top:8px;">
        <button type="button" class="btn btn-ghost" style="flex:1"
                onclick="closeModal('clinicModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" style="flex:1">Add Clinic</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function showTab(el, name) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    ['appointments','clinics','doctors','users'].forEach(t => {
      document.getElementById('tab-'+t).style.display = t===name ? 'block' : 'none';
    });
  }
  function openModal(id)  { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  // Close modal on overlay click
  document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
      if (e.target === this) closeModal(this.id);
    });
  });
</script>
@endsection
