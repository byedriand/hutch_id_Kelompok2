<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipPdf extends Model
{
    use HasFactory;

    protected $table = 'arsip_pdfs';

    protected $fillable = [
        'filename',
        'path',
        'size',
    ];
}
