<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Organization;
use App\Vacancy;
use Illuminate\Http\Request;
use App\Http\Requests\VacancyCreateRequest;
use App\Http\Requests\VacancyUpdateRequest;
use App\Http\Requests\BookRequest;
use App\Http\Resources\VacancyResourceCollection;
use App\Http\Resources\VacancyResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class VacancyController extends Controller
{   

    public function book(BookRequest $request)
    {
        $this->authorize('book', Vacancy::class);
        $vacancy = Vacancy::getBook($request);
        
        return response()->json([
            'succes' => true,
        ], 200);
    }
    
    public function unbook(BookRequest $request)
    {
            $this->authorize('unbook', Vacancy::class);            
            $vacancy = Vacancy::getUnbook($request);

            return response()->json([
                'success' => true,
            ], 200);
       
    }

    public function indexStats(Request $request)
    {
        $this->authorize('indexStats', Vacancy::class);
        $vacancy = Vacancy::getVacancyList($request);

        return response()->json(['success' => true, 'data' => $vacancy], 200);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Vacancy::class);
        $vacancies = Vacancy::getIndexList($request);
        
        return new VacancyResourceCollection($vacancies);   
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VacancyCreateRequest $request)
    {
        $this->authorize('store', Vacancy::class);
        $vacancy = Vacancy::create($request->validated());
        
        return new VacancyResource($vacancy);
        
    }
     
    
    public function show(Vacancy $vacancy, User $user)
    {
        
        $this->authorize('show', Vacancy::class);
        $vacancy->load(['workers']);
        $vacancy->workers_booked = count($vacancy->workers()->get());
        
        return new VacancyResource($vacancy);
        
        
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VacancyUpdateRequest $request, Vacancy $vacancy)
    {
        $this->authorize('update', Vacancy::class); 
        $data = $request->validated(); 
        $vacancy->update($request->all(), $data);
        
        return new VacancyResource($vacancy);
        
    }//call to undef user() but work after added to workers()  'id' + add VacancyRequest**********
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vacancy $vacancy)
    {
        
        $this->authorize('delete', Vacancy::class);
        $vacancy->delete();

        return response()->json(['success' => true], 200);
        
    }
}
