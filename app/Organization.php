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

    public static function getShowList(Request $request, Organization $organization)
    {       
        $vacancies = $request->input('vacancies', null);
        $workers = $request->input('workers', null);
        $only_active = $request->input('only_active', null);
        $workers_amount = $request->get('workers_amount', 0);  

        $builder=Vacancy::query();
        $builder->select('vacancies.*', 'w.workers_booked', DB::raw("IF(
            vacancies.workers_amount > w.workers_booked, 
            'active',
            'closed'
            ) AS status"));
        $subQueryWorkers = DB::query()
            ->select('vacancy_id', DB::raw('COUNT(vacancy_id) AS workers_booked'))
            ->from('user_vacancy')
            ->groupBy('vacancy_id');            
        $builder->leftJoinSub($subQueryWorkers,'w', function ($query) use ($organization){
            $query->on('w.vacancy_id', 'vacancies.id')->where('vacancies.organization_id', '=', $organization->id);
            //->where('w.vacancy_id', 'vacancies.organization_id');
        });
        if ($vacancies == '1' && $workers == '1') {
            $builder->whereRaw('vacancies.workers_amount <= w.workers_booked');
        }elseif($vacancies == '2'&& $workers == '1'){
            $builder->whereRaw('vacancies.workers_amount > w.workers_booked');            
        }elseif($vacancies == '3'&& $workers == '1'){
            $builder->whereRaw('vacancies.workers_amount <= w.workers_booked' and 'vacancies.workers_amount > w.workers_booked');
        }elseif($vacancies == '0' && $workers == '0'){
        
        } 
 
        
        $results = $builder->get();

        return $results;  
    }    
}
