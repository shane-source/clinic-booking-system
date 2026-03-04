@extends('layout')
@section('title','Book Appointment')
@section('content')

<div style="max-width:600px;margin:0 auto;">
  <h2 class="section-title">Book an Appointment</h2>

  <div class="card">
    <!-- Step indicator -->
    <div style="display:flex;gap:6px;margin-bottom:24px;" id="stepIndicator">
      <div style="flex:1;height:4px;border-radius:2px;background:#2563eb;" id="dot1"></div>
      <div style="flex:1;height:4px;border-radius:2px;background:#334155;" id="dot2"></div>
      <div style="flex:1;height:4px;border-radius:2px;background:#334155;" id="dot3"></div>
    </div>

    <p id="stepLabel" style="color:#64748b;font-size:13px;margin-bottom:20px;">
      Step 1 of 3 — Select a Clinic
    </p>

    <form method="POST" action="/book" id="bookingForm">
      @csrf
      <input type="hidden" name="clinic_id"  id="clinic_id"/>
      <input type="hidden" name="doctor_id"  id="doctor_id"/>
      <input type="hidden" name="appointment_date" id="date_hidden"/>
      <input type="hidden" name="appointment_time" id="time_hidden"/>
      <input type="hidden" name="notes"      id="notes_hidden"/>

      <!-- STEP 1: CLINICS -->
      <div id="step1">
        <p style="color:#f1f5f9;font-weight:700;margin-bottom:16px;">Choose a clinic:</p>
        @forelse($clinics as $clinic)
          <button type="button" onclick="selectClinic({{ $clinic->id }}, '{{ $clinic->name }}')"
            style="width:100%;background:#0f172a;border:1.5px solid #334155;border-radius:10px;
                   padding:14px 18px;text-align:left;cursor:pointer;margin-bottom:10px;
                   transition:border-color 0.2s;"
            onmouseover="this.style.borderColor='#2563eb'"
            onmouseout="this.style.borderColor='#334155'">
            <p style="color:#f1f5f9;font-weight:700;font-size:15px;margin:0">{{ $clinic->name }}</p>
            <p style="color:#64748b;font-size:13px;margin:4px 0 0">
              {{ $clinic->city }} — {{ $clinic->address }}
            </p>
          </button>
        @empty
          <p style="color:#64748b;text-align:center;padding:20px">
            No clinics available. Ask admin to add clinics first.
          </p>
        @endforelse
      </div>

      <!-- STEP 2: DOCTORS -->
      <div id="step2" style="display:none">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <button type="button" onclick="goStep(1)"
            style="background:none;border:none;color:#60a5fa;cursor:pointer;font-size:14px;">
            ← Back
          </button>
          <p style="color:#f1f5f9;font-weight:700">Choose a doctor:</p>
        </div>
        <div id="doctorsList">
          <p style="color:#64748b">Loading doctors...</p>
        </div>
      </div>

      <!-- STEP 3: DATE & TIME -->
      <div id="step3" style="display:none">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
          <button type="button" onclick="goStep(2)"
            style="background:none;border:none;color:#60a5fa;cursor:pointer;font-size:14px;">
            ← Back
          </button>
          <p style="color:#f1f5f9;font-weight:700">Choose date & time:</p>
        </div>

        <div class="form-group">
          <label class="form-label">Date</label>
          <input type="date" class="form-input" id="dateInput"
                 min="{{ date('Y-m-d') }}" required
                 onchange="document.getElementById('date_hidden').value=this.value"/>
        </div>

        <div class="form-group">
          <label class="form-label">Time</label>
          <input type="time" class="form-input" id="timeInput"
                 onchange="document.getElementById('time_hidden').value=this.value"/>
        </div>

        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <textarea class="form-input" id="notesInput" placeholder="Describe your symptoms..."
                    onchange="document.getElementById('notes_hidden').value=this.value"></textarea>
        </div>

        <!-- Summary -->
        <div id="summary" style="background:#0f172a;border-radius:10px;padding:16px;
                                  margin-bottom:20px;border:1px solid #334155;display:none;">
          <p style="color:#64748b;font-size:12px;font-weight:700;
                    text-transform:uppercase;margin-bottom:10px;">Booking Summary</p>
          <p style="color:#f1f5f9;font-size:14px;margin-bottom:6px;">
            🏥 <span id="sumClinic"></span>
          </p>
          <p style="color:#f1f5f9;font-size:14px;margin-bottom:6px;">
            🩺 <span id="sumDoctor"></span>
          </p>
          <p style="color:#f1f5f9;font-size:14px;">
            📅 <span id="sumDate"></span> at <span id="sumTime"></span>
          </p>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;font-size:16px;"
                onclick="return validateStep3()">
          ✅ Confirm Booking
        </button>
      </div>

    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
  let selectedClinicName = '';
  let selectedDoctorName = '';
  let currentStep = 1;

  function goStep(n) {
    document.getElementById('step1').style.display = n===1?'block':'none';
    document.getElementById('step2').style.display = n===2?'block':'none';
    document.getElementById('step3').style.display = n===3?'block':'none';
    ['dot1','dot2','dot3'].forEach((id,i) => {
      document.getElementById(id).style.background = (i+1)<=n ? '#2563eb' : '#334155';
    });
    const labels = ['','Step 1 of 3 — Select a Clinic',
                       'Step 2 of 3 — Select a Doctor',
                       'Step 3 of 3 — Pick Date & Time'];
    document.getElementById('stepLabel').textContent = labels[n];
    currentStep = n;
  }

  function selectClinic(id, name) {
    document.getElementById('clinic_id').value = id;
    selectedClinicName = name;
    goStep(2);
    loadDoctors(id);
  }

  function loadDoctors(clinicId) {
    document.getElementById('doctorsList').innerHTML = '<p style="color:#64748b">Loading...</p>';
    fetch('/book/doctors?clinic_id=' + clinicId)
    .then(function(r) { return r.text(); })
    .then(function(text) {
      // Strip any leading < or whitespace that Laravel might add
      var clean = text.trim();
      var start = clean.indexOf('[');
      if (start > 0) clean = clean.substring(start);
      var doctors = JSON.parse(clean);
      if (!doctors || doctors.length === 0) {
        document.getElementById('doctorsList').innerHTML =
          '<p style="color:#64748b;text-align:center;padding:20px">No doctors at this clinic.</p>';
        return;
      }
      var html = '';
      doctors.forEach(function(d) {
        html += '<button type="button" onclick="selectDoctor(' + d.id + ', \'' + d.user.name + '\')"' +
          ' style="width:100%;background:#0f172a;border:1.5px solid #334155;border-radius:10px;' +
          'padding:14px 18px;text-align:left;cursor:pointer;margin-bottom:10px;"' +
          ' onmouseover="this.style.borderColor=\'#2563eb\'"' +
          ' onmouseout="this.style.borderColor=\'#334155\'">' +
          '<p style="color:#f1f5f9;font-weight:700;font-size:15px;margin:0">' + d.user.name + '</p>' +
          '<p style="color:#64748b;font-size:13px;margin:4px 0 0">' + d.specialty + '</p>' +
          '</button>';
      });
      document.getElementById('doctorsList').innerHTML = html;
    })
    .catch(function(err) {
      document.getElementById('doctorsList').innerHTML =
        '<p style="color:#ef4444">Error: ' + err.message + '</p>';
    });
  }

  function selectDoctor(id, name) {
    document.getElementById('doctor_id').value = id;
    selectedDoctorName = name;
    goStep(3);
    updateSummary();
  }

  function updateSummary() {
    document.getElementById('sumClinic').textContent = selectedClinicName;
    document.getElementById('sumDoctor').textContent = selectedDoctorName;
    document.getElementById('summary').style.display = 'block';
  }

  function validateStep3() {
    const date = document.getElementById('dateInput').value;
    const time = document.getElementById('timeInput').value;
    const notes = document.getElementById('notesInput').value;
    if (!date) { alert('Please select a date.'); return false; }
    if (!time) { alert('Please select a time.'); return false; }
    document.getElementById('date_hidden').value  = date;
    document.getElementById('time_hidden').value  = time;
    document.getElementById('notes_hidden').value = notes;
    document.getElementById('sumDate').textContent = date;
    document.getElementById('sumTime').textContent = time;
    return true;
  }

  // Update summary when date/time changes
  document.getElementById('dateInput')?.addEventListener('change', function() {
    document.getElementById('sumDate').textContent = this.value;
  });
  document.getElementById('timeInput')?.addEventListener('change', function() {
    document.getElementById('sumTime').textContent = this.value;
  });
</script>
@endsection