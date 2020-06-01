<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    public $fillable = ['DirectorIdentificationNumber', 'Name', 'Designation', 'DateofAppointment', 'company_id'];
}
