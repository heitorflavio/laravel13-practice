<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'name', 'date', 'doctor_name', 'specialty', 'cid10', 'ia_resume', 'type'])]
class Document extends Model
{
    use HasFactory;
    /**
     * O usuário associado ao documento
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Os arquivos associados ao documento
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
