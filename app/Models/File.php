<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['document_id', 'file_path', 'file_url', 'mime_type', 'ia_resume', 'status'])]
class File extends Model
{
    /**
     * O documento associado ao arquivo
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
