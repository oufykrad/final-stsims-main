<?php

namespace App\Http\Middleware;

// use App\Models\College;
// use App\Models\Course;
use App\Models\ListPrivilege;
// use App\Models\ListExpense;
use App\Models\ListProgram;
use App\Models\ListDropdown;
use App\Models\ListAgency;
use App\Models\ListStatus;
use App\Models\LocationRegion;
use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Http\Resources\UserResource;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {   
        if(\Auth::check()){
            $agency_id = (\Auth::user()->profile->agency) ? \Auth::user()->profile->agency->id : '';
            if($agency_id){
                $agency = ListAgency::with('region')->where('id',$agency_id)->first();
            }else{
                $agency = '';
            }
        }else{
            $agency = '';
        }

        return array_merge(parent::share($request), [
            'auth' => (\Auth::check()) ? new UserResource(\Auth::user()) : '',
            'role' => (\Auth::check()) ? \Auth::user()->role : '',
            'agency' => $agency,
            'flash' => [
                'message' => session('message'),
                'datares' => session('data'),
                'type' => session('type')
            ],
            'regions' => LocationRegion::all(),
            'dropdowns' => ListDropdown::all(),
            'programs' => ListProgram::all(),
            'statuses' => ListStatus::all(),
            // 'expenses' => ListExpense::all(),
            'privileges' => ListPrivilege::all(),
            // 'colleges' => College::all(),
            // 'courses' => Course::all(),
            // 'dropdowns' => Dropdown::all(),
            'agencies' => ListAgency::all()
        ]);
    }
}
