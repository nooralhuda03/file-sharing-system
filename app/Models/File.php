<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'user_id',
        'file_name',
        'file_path',
        'size',
        'visibility'
    ];

 
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeMyFiles($query)
{
    return $query->where('user_id', auth()->id());
}
}