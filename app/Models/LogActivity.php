<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $table = 'log_activity';
    public $timestamps = false;
    protected $fillable = [ 'method', 'agent', 'ip', 'tanggal', 'list'];
}
