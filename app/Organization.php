<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
* @property  int creator_id
*/

class Organization extends Model
{
    use SoftDeletes;
    
     /******* Properties *******/

    protected $hidden = [
        'api_token', 'deleted_at', 'pivot'
    ];
    protected $fillable = [ 
        'title', 
        'country',
        'city',
        'creator_id'
    ];
    
    public static function boot()
    {
        parent::boot();

        static::creating(function(self $model)
        {
            if(\Auth::id()){
                $model->creator_id = \Auth::id();
           }
        });
    }
    
     /******* Relations *******/

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id')->withDefault();
    }

    public function creator()
    {
        return $this->user();
    }

    public function vacancies()
    {
    	return $this->hasMany('App\Vacancy');
    }

    /******* CRUD Functions *******/

     /******* Static Functions *******/

    public static function getOrganizationList(Request $request)
    {
        $organizations = User::all();
        $all = $organizations->count();
        $active = count($organizations->where('deleted_at', '=', null)->all());        
        $softDelete = \App\Organization::onlyTrashed()->count();
        return $organization = collect(['active' =>  $active, 'softDelete' => $softDelete, 'all' => $all]);
    }
}
