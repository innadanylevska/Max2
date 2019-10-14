<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\User;
use App\Organization;
use App\Vacancy;
use App\Filters\UserFilters;
use App\Http\Resources\UserResourceCollection;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{   
    public function index(Request $request)
    {
        $this->authorize('index',  User::class);

        return new UserResourceCollection(User::getSearchList($request));
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function indexStats(Request $request)
    {
       $this->authorize('indexStats', User::class);
       $user = User::getRoleList($request);
       
       return response()->json(['success' => true, 'data' => $user], 200);   
    }

    public function show(User $user, Vacancy $vacancy)
    {
       $this->authorize('show',  User::class);
        
       return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', User::class);
        $user = \Auth::user();
        $data = $request->validated();
        $user->update($request->except('password', 'role'), $data);
        
        return new UserResource($user);

    }
    
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $currentUser = \Auth::user()->id();//auth()->user()->id;
        if (Hash::check($request->get('password'), $currentUser->password)) {
            $currentUser->update(['password' => Hash::make($request->get('new_password'))]);
            return response()->json(['success' => true, 'message' => 'Password updated']);
        }

        return response()->json(['success' => false, 'error' => 'Old password is incorrect']);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);
        $user->delete();

        return response()->json(['success' => true], 200);
    }
}
