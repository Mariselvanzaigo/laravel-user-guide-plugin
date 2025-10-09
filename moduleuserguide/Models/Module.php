<?php
namespace ModuleUserGuide\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name'];
    public function userGuides()
    {
        return $this->hasMany(UserGuide::class);
    }

}
