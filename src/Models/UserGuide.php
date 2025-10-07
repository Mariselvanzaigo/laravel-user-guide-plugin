<?php

namespace LaravelUserGuide\Models;

use Illuminate\Database\Eloquent\Model;

class UserGuide extends Model {
    protected \ = ['module_id','name','description','files','urls'];
    protected \ = [
        'files' => 'array',
        'urls' => 'array'
    ];

    public function module() {
        return \->belongsTo(Module::class);
    }
}
