<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 
class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'clinic_id',
        'appointment_date', 'appointment_time',
        'status', 'notes',
    ];
 
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
 
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
 
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}

