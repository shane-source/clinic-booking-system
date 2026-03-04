<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Appointment;

Route::get('/', fn() => redirect('/login'));

// ── AUTH ──────────────────────────────────────────────────
Route::get('/login',  fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'));

Route::post('/login', function (Request $request) {
    $request->validate(['email'=>'required|email','password'=>'required']);
    if (Auth::attempt(['email'=>$request->email,'password'=>$request->password])) {
        $request->session()->regenerate();
        $role = Auth::user()->roles->first()?->name ?? 'patient';
        return redirect($role === 'admin' ? '/admin' : ($role === 'doctor' ? '/doctor' : '/dashboard'));
    }
    return back()->withErrors(['email'=>'Wrong email or password.'])->withInput();
});

Route::post('/register', function (Request $request) {
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
    ]);
    try { $user->assignRole('patient'); } catch (\Exception $e) {}
    Auth::login($user);
    return redirect('/dashboard');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth');

// ── PATIENT ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $appointments = Appointment::with(['doctor.user','clinic'])
            ->where('patient_id', Auth::id())->latest()->get();
        return view('patient.dashboard', compact('appointments'));
    });

    Route::get('/book', function () {
        $clinics = Clinic::where('is_active', true)->get();
        return view('patient.book', compact('clinics'));
    });

    Route::get('/book/doctors', function (Request $request) {
        $doctors = Doctor::with('user')
            ->where('clinic_id', $request->clinic_id)
            ->get();
        $data = $doctors->map(function($d) {
            return [
                'id'        => $d->id,
                'specialty' => $d->specialty,
                'user'      => ['name' => $d->user->name ?? 'Doctor'],
            ];
        });
        return response(json_encode($data->values()), 200)
            ->header('Content-Type', 'application/json')
            ->header('X-Content-Type-Options', 'nosniff');
    });


    Route::post('/book', function (Request $request) {
        $request->validate([
            'clinic_id'        => 'required|exists:clinics,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes'            => 'nullable|string|max:1000',
        ]);
        Appointment::create([
            'patient_id'       => Auth::id(),
            'clinic_id'        => $request->clinic_id,
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);
        return redirect('/dashboard')->with('success','Appointment booked successfully!');
    });

    Route::post('/appointments/{id}/cancel', function ($id) {
        Appointment::where('id',$id)->where('patient_id',Auth::id())->firstOrFail()
            ->update(['status'=>'cancelled']);
        return redirect('/dashboard')->with('success','Appointment cancelled.');
    });

    // ── DOCTOR ────────────────────────────────────────────
    Route::get('/doctor', function () {
        $doctor = Doctor::where('user_id', Auth::id())->first();
        $appointments = $doctor
            ? Appointment::with(['patient','clinic'])
                ->where('doctor_id',$doctor->id)->latest()->get()
            : collect();
        return view('doctor.dashboard', compact('appointments','doctor'));
    });

    Route::post('/doctor/appointments/{id}/confirm', function ($id) {
        Appointment::findOrFail($id)->update(['status'=>'confirmed']);
        return redirect('/doctor')->with('success','Appointment confirmed.');
    });

    Route::post('/doctor/appointments/{id}/cancel', function ($id) {
        Appointment::findOrFail($id)->update(['status'=>'cancelled']);
        return redirect('/doctor')->with('success','Appointment cancelled.');
    });

    // ── ADMIN ─────────────────────────────────────────────
    Route::get('/admin', function () {
        $appointments = Appointment::with(['patient','doctor.user','clinic'])->latest()->get();
        $clinics      = Clinic::all();
        $doctors      = Doctor::with('user','clinic')->get();
        $users        = User::with('roles')->latest()->get();
        return view('admin.dashboard', compact('appointments','clinics','doctors','users'));
    });

    Route::post('/admin/clinics', function (Request $request) {
        $request->validate(['name'=>'required','address'=>'required','city'=>'required']);
        Clinic::create($request->only('name','address','city','phone','email') + ['is_active'=>true]);
        return redirect('/admin')->with('success','Clinic added successfully!');
    });

    Route::post('/admin/clinics/{id}/delete', function ($id) {
        Clinic::findOrFail($id)->delete();
        return redirect('/admin')->with('success','Clinic deleted.');
    });

    Route::post('/admin/appointments/{id}/confirm', function ($id) {
        Appointment::findOrFail($id)->update(['status'=>'confirmed']);
        return redirect('/admin')->with('success','Appointment confirmed.');
    });

    Route::post('/admin/appointments/{id}/cancel', function ($id) {
        Appointment::findOrFail($id)->update(['status'=>'cancelled']);
        return redirect('/admin')->with('success','Appointment cancelled.');
    });

    Route::post('/admin/users/{id}/role', function (Request $request, $id) {
        User::findOrFail($id)->syncRoles([$request->role]);
        return redirect('/admin')->with('success','Role updated successfully.');
    });

});
