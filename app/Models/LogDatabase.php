<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDatabase extends Model
{
    use HasFactory;

    protected $table = 'log_database';
    public $timestamps = false;
    protected $fillable = ['method', 'agent', 'ip', 'tanggal', 'list', 'table', 'data', 'id_table'];
}
