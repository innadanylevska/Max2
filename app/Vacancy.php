<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @property  int organization_id
 */
class Vacancy extends Model
{ 
    use SoftDeletes;

    /******* Properties *******/
    
    protected $fillable = [ 
        'vacancy_name',           
        'workers_amount',
        'organization_id',
        'salary',
    ];    
    
    protected $hidden = [
        'api_token', 'deleted_at', 'pivot'
    ];

    protected $casts = [
        'workers_booked' => 'boolean',
        'status' => 'string',
    ];
    
    protected $appends = [
        'status',
        'workers_booked',
    ];   
        
     /******* Relations *******/
    
    public function workers()
    {
    	return $this->belongsToMany('App\User', 'user_vacancy', 'user_id', 'vacancy_id');
    }

     public function organization()
    {
    	return $this->belongsTo('App\Organization')->default();
    }
    
     /******* Getters *******/
    
    public function getWorkersBookedAttribute()
    {        
        $workers_booked = $this->workers()->count();
        
        return $workers_booked;
       
    }

    public function getStatusAttribute()
    {
        if($this->shouldShowStatusActive()){
            return 'active';
        }
        return 'closed';
        
    }

    /******* Conditions Functions *******/

    public function shouldShowStatusActive()
    {
        return $this->workers_booked < $this->workers_amount;
    }

    public function shouldShowStatusClosed()
    {
        return $this->workers_booked >= $this->workers_amount;
    }

    public function shouldShowOnlyActive($only_active)
    {
        return $this->$only_active == true;
    }
    
     /******* Static Functions *******/
    
    public static function getVacancyList(Request $request)
    {
        $vacancies = Vacancy::all();
        $all = $vacancies->count();
        $active = $vacancies->filter(function ($value){
            return $value->shouldShowStatusActive();
        })->count();
        $closed = $all - $active;
        $vacancy = collect(['active' =>  $active, 'closed' => $closed, 'all' => $all]);
        
        return $vacancy;
            
    }

//     public static function getIndexList(Request $request)
//     {
//         $only_active = (bool)$request->input('only_active');
//         $vacancies = \App\Http\Resources\VacancyResourceCollection::make(Vacancy::all());
        
//         return $vacancies = $vacancies->filter(function ($value) use ($only_active) {
//             if ($value->shouldShowOnlyActive($only_active)) {
//                 if ($value->shouldShowStatusActive()){
//                     return $value;
//                 }
//             } else {
//                 return $value;
//             }
//         });
//     }
    public static function getIndexList(Request $request)
    {
        $only_active = $request->input('only_active', null);
        $active = $request->input('true',null) ?? true;
        $all = $request->input('false',null) ?? true;
        $workers_amount = $request->get('workers_amount', 0);
        //$builder = self::query();
        $w = self::query()
        ->leftJoinSub('user_vacancy', function ($join) {
            $join->on( 'user_vacancy.vacancy_id', '=', 'vacancies.id');
        })
        ->select('user_vacancy.vacancy_id', \DB::raw('user_vacancy.vacancy_id, COUNT(user_vacancy.vacancy_id) AS cv'))            
        ->groupBy('user_vacancy.vacancy_id');    
		$builder
        ->select('vacancies.* AS v, w.cv', \DB::raw("IF(vacancies.workers_ammount > w.cv, 'active', 'closed') AS status"));
        if ($only_active) {
        $builder->where('vacancies.workers_ammount', '>', 'w.cv', 'active', 'closed');
        $builder->where('true', $active);            
        } else {
            $builder->orWhere('false', $all);            
        } 
        $results = $builder->get()->appends(\Request::query());

        return $results;       

//     SELECT
//     vacancies.*,
//     w.c,
//     IF(
//         vacancies.workers_amount > w.c,
//         'active',
//         'closed'
//     ) AS
// status
// FROM
//     vacancies   
// LEFT JOIN(
//     SELECT
//         vacancy_id,
//         COUNT(vacancy_id) AS c
//     FROM
//         user_vacancy
//     GROUP BY
//         vacancy_id
// ) AS w
// ON
//     w.vacancy_id = vacancies.id; 
//WHERE vacancies.workers_amount > w.c,
//         'active',
//         'closed' 
    }
    public static function getBook(Request $request)
    {
        $id = \Auth::user()->id;
        $vacancyId = $request->post('vacancy_id');
        $userId = $request->post('user_id');
        $vacancy = Vacancy::find($vacancyId);
        $users = $vacancy->workers;
        foreach ($users as $user){
            if($user->id == $id){
                return response()->json(['success' => false, 'error' => 'User Booked!'], 200);
            }
        }
        return $vacancy->workers()->attach($userId);
    }

    public static function getUnbook(Request $request)
    {
        $vacancyId = $request->get('vacancy_id');
        $userId = $request->post('user_id');
        $vacancy = Vacancy::find($vacancyId);
        return $vacancy->workers()->detach($userId);
    }
    
}
     
