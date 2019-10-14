<?php

namespace App\Policies;

use App\User;
Use App\Vacancy;
use App\Organization;
use Illuminate\Auth\Access\HandlesAuthorization;

class VacancyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before($user, $ability)
    {
        if ($ability !== 'create'){
            if ($user->role === 'admin') {
                return true;
            }
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
            return true;
       
    }

    public function indexStats(User $user)
    {
        if($user->role === 'admin'){
            return true;
        }
        return false;
    }
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function show(User $user)
    {
        $organization_id = request()->post('organization_id');
        if(($user->role == 'worker' || $user->role == 'admin') || ($user->role == 'employer' && $user->organization->id === $organization_id)){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        $organization_id = request()->post('organization_id');
        if($user->role == 'employer' && $user->organization->id === $organization_id){

            return true;
        }
    }

    /** 
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user)
    {
        
        $organization_id = request()->post('organization_id');
        if($user->role == 'employer' && $user->organization->id === $organization_id){

            return true;
        }
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user)
    {    
        $organization_id = request()->post('organization_id');
        if($user->role == 'employer' && $user->organization->id === $organization_id){

            return true;
        }
    }

    public function book(User $user)
    {

        if($user->role == 'worker'){
            $user_id = request()->post('user_id');

            return $user_id === $user->id;
        }
        return false;
    }

    public function unbook(User $user)
    {
        $organization_id = request()->post('organization_id');        
        if($user->role == 'employer' && $user->organization->id === $organization_id || ($user->role == 'admin' ||( $user->role == 'worker' && $user->id == $user->vacancy->user_id))){

            return true;
        }
        return false;
    }



    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
