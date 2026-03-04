<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 
class Doctor extends Model
{
    protected $fillable = [
        'user_id', 'clinic_id',
        'specialty', 'bio', 'is_available',
    ];
 
    // A doctor IS a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
 
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
