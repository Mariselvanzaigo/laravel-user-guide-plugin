<?php
namespace ModuleUserGuide\Models;

use Illuminate\Database\Eloquent\Model;

class UserGuide extends Model
{
    protected $fillable = ['module_id','name','description','files','urls'];

    protected $casts = [
        'files' => 'array',
        'urls' => 'array',
    ];

    public function module() {
        return $this->belongsTo(Module::class);
    }
}
