<?php
namespace App\Http\Controllers;
 
use App\Models\Appointment;
use Illuminate\Http\Request;
 
class AppointmentController extends Controller
{
    // GET /api/appointments
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user', 'clinic']);
 
        if ($request->user()->hasRole('patient')) {
            $query->where('patient_id', $request->user()->id);
        }
 
        if ($request->user()->hasRole('doctor')) {
            $query->whereHas('doctor', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }
 
        return $query->latest()->paginate(20);
    }
 
    // POST /api/appointments
    public function store(Request $request)
    {
        $request->validate([
            'clinic_id'        => 'required|exists:clinics,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes'            => 'nullable|string|max:1000',
        ]);
 
        $appointment = Appointment::create([
            ...$request->validated(),
            'patient_id' => auth()->id(),
            'status'     => 'pending',
        ]);
 
        return response()->json(
            $appointment->load('doctor.user', 'clinic'),
            201
        );
    }
 
    // GET /api/appointments/{id}
    public function show(Appointment $appointment)
    {
        return response()->json(
            $appointment->load('patient', 'doctor.user', 'clinic')
        );
    }
 
    // PATCH /api/appointments/{id}/confirm
    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);
        return response()->json($appointment);
    }
 
    // PATCH /api/appointments/{id}/cancel
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return response()->json($appointment);
    }
 
    // DELETE /api/appointments/{id}
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json(null, 204);
    }
}
