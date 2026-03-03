<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Patient extends Model
{
    protected $fillable = [
        'chronic_conditions',
        'allergies',
        'surgical_history',
        'family_medical_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ];
    // Relación uno a uno inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodType()
    {
        return $this->belongsTo(BloodType::class);
    }
}
