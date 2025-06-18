<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $guarded = ['id'];

    // Accessors
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    // Helper method to get formatted phone
    public function getFormattedPhoneAttribute()
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        // Format Indonesian phone number
        if (substr($phone, 0, 1) === '0') {
            return '+62' . substr($phone, 1);
        }
        
        return $this->phone;
    }

    // Helper method to get WhatsApp URL
    public function getWhatsappUrlAttribute()
    {
        $phone = $this->formatted_phone;
        return "https://wa.me/" . str_replace('+', '', $phone);
    }
}