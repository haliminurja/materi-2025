<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogError extends Model
{
    use HasFactory;

    protected $table = 'log_error';
    public $timestamps = false;
    protected $fillable = ['method', 'agent', 'ip', 'tanggal', 'path', 'list', 'error'];
}
