<?php
namespace ModuleUserGuide\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'user_guide_modules';
    protected $fillable = ['name'];
    public function userGuides()
    {
        return $this->hasMany(UserGuide::class);
    }

}
