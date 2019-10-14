<?php

namespace App;

//use Tymon\JWTAuth\Contracts\JWTSubject;
use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable, Softdeletes;
    
    /******* Properties *******/
    
    protected $fillable = [ 
        'email', 
        'password',
        'first_name',
        'last_name',
        'country',
        'city',
        'phone',
        'role',
    ];
    
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'deleted_at', 'pivot'
    ];
    
    /******* Relations *******/
    
    public function vacancies()
        {
        return $this->belongsToMany(Vacancy::class);
        }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'creator_id');
    }     

        /******* CRUD Functions *******/

   
    public function edit($fields)
    {
        $this->fill($fields); 
        
        $this->save();
    }

//     public function generatePassword($password)//********for edit func for renew pass**********not need instead updatePassword func
//     {
//         if($password != null)
//         {
//             $this->password = Hash::make($password);
//             $this->save();
//         }
//     }

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }
    
    /******* Getters *******/

    public static function getRoleList(Request $request)
    {
       $users = User::all();
       $worker = User::where('role','worker')->get()->count();
       $employer = User::where('role','employer')->get()->count();
       $admin = User::where('role','admin')->get()->count();
        
        return $user = collect(['worker' =>  $worker, 'employer' => $employer, 'admin' => $admin]);
    }

    
    public static function getSearchList(Request $request)
    {
        $search = $request->get('search');        
        $search = $search ? '%' . $search . '%' : null;
       
        return User::where('first_name', 'LIKE', '%'.$search.'%')
            ->orWhere('last_name', 'LIKE', '%'.$search.'%')
            ->orWhere('country', 'LIKE', '%'.$search.'%')
            ->orWhere('city', 'LIKE', '%'.$search.'%')->get();
    }

        /*** JWT ***/

    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    // public function getJWTCustomClaims()
    // {
    //     return [];
    // }

    /*** END-JWT ***/
    
    
    
}
