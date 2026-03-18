<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

  
    public function files()
    {
        return $this->hasMany(File::class);
    }
    public function scopeMyFolders($query)
{
    return $query->where('user_id', auth()->id());
}
}