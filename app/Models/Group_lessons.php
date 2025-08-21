<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group_lessons extends Model
{
    //


     public function payments()
    {
        return $this->morphMany(Payment_transaction::class, 'S_or_G_lesson');
    }
}
