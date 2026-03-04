<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
 
class Clinic extends Model
{
    protected $fillable = [
        'name', 'address', 'city',
        'phone', 'email', 'is_active',
    ];
 
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
 
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
