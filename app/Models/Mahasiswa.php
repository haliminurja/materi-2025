<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DateTimeInterface;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mahasiswa extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'mahasiswa'; // Set nama tabel
    protected $primaryKey = 'nim'; // Set primary key sebagai NIM
    public $incrementing = false;  // Non-increment karena NIM adalah string
    protected $keyType = 'string'; // Tipe data primary key
    protected $fillable = ['nim', 'nama', 'jenis_kelamin', 'password','email','otp'];

    protected $hidden = ['password']; // Sembunyikan password dalam response
    public $timestamps = true; // Mengaktifkan created_at & updated_at

    /**
     * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
