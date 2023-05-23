<?php

namespace App\Http\Controllers\Scholar;

use Hashids\Hashids;
use App\Models\Profile;
use App\Models\SchoolSemester;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Scholar\IndexResource;
use App\Http\Resources\Scholar\SearchResource;
use App\Http\Requests\ScholarRequest;
use App\Http\Traits\ScholarsTrait;

class IndexController extends Controller
{
    use ScholarsTrait;

    public $link, $agency;

    public function __construct()
    {
        $this->link = config('app.api_link');
        $this->agency = config('app.agency');
    }

    public function index(Request $request){
        if($request->search == 'benefits'){
            return $this->benefits($request->id,$request->ay,$request->semester);
        }else if($request->search == 'search'){
            $data = SearchResource::collection(
                Profile::with('user')
                ->with('scholar.program','scholar.status')
                ->with('scholar.education.school.school','scholar.education.course')
                ->with('scholar.education.school.semesters.semester')->with('scholar.education.school.term')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                    ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%');
                })
                ->orderBy('lastname','ASC')
                ->paginate(10)
                ->withQueryString()
            );
            return $data;

        }else if($request->search){
            $info = (!empty(json_decode($request->info))) ? json_decode($request->info) : NULL;
            $filter = (!empty(json_decode($request->filter))) ? json_decode($request->filter) : NULL;
    
            $data = IndexResource::collection(
                Profile::
                with('address.region','address.province','address.municipality','address.barangay','user')
                ->with('scholar.program','scholar.benefits.semester.semester','scholar.benefits.benefit','scholar.enrollments.lists')
                ->with('scholar.education.school.school','scholar.education.course')
                ->with('scholar.education.school.semesters.semester')->with('scholar.education.school.term')
                ->when($info->keyword, function ($query, $keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                    ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%');
                })
                ->whereHas('address',function ($query) use ($filter) {
                    if(!empty($filter)){
                        (property_exists($filter, 'region')) ? $query->where('region_code',$filter->region) : '';
                        (property_exists($filter, 'province')) ? $query->where('province_code',$filter->province) : '';
                        (property_exists($filter, 'municipality')) ? $query->where('municipality_code',$filter->municipality) : '';
                        (property_exists($filter, 'barangay')) ? $query->where('barangay_code',$filter->barangay) : '';
                    }
                })
                ->whereHas('scholar',function ($query) use ($filter) {
                    $query->whereHas('education',function ($query) use ($filter) {
                        if(!empty($filter)){
                            (property_exists($filter, 'school')) ? $query->where('school_id',$filter->school) : '';
                            (property_exists($filter, 'course')) ? $query->where('course_id',$filter->course) : '';
                        }
                    });
                })
                ->whereHas('scholar',function ($query) use ($info) {
                    ($info->program == null) ? '' : $query->where('program_id',$info->program);
                    ($info->status == null) ? '' : $query->where('status_id',$info->status);
                    ($info->is_undergrad == 'all') ? '' : $query->where('is_undergrad',$info->is_undergrad);
                    ($info->year == null) ? '' : $query->where('awarded_year',$info->year);
                 })
                ->orderBy('lastname',$info->sorty)
                ->paginate($info->counts)
                ->withQueryString()
            );
            return $data;
        }else{
            return inertia('Modules/Scholars/Index');
        }
    }

    public function store(ScholarRequest $request){
        $data = \DB::transaction(function () use ($request){
            switch($request->editable){
                // case 'single': 
                //     return $this->single($request);
                // break;
                // case 'qualifier': 
                //     return $this->qualifier($request);
                // break;
                case 'course': 
                    return $this->course($request);
                break;
                case 'information': 
                    return $this->information($request);
                break;
                case 'authentication':
                    return $this->authentication($request);
                break;
                // case 'api': 
                //     return $this->api($request);
                // break;
            }
        });
        return back()->with([
            'message' => 'Scholar updated successfully. Thanks',
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function show($data){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($data);
        
        $data = Profile::with('scholar.education.school','scholar.benefits.semester.semester','scholar.benefits.benefit','address.region','address.province','address.municipality','address.barangay')
        ->with('scholar.education.school.semesters.semester')->with('scholar.education.school.term')
        ->where('id',$id)->first();

        $benefits = $this->benefits($id,null,null);

        return inertia('Modules/Scholars/Profile/Index',[
            'user' => new IndexResource($data),
            'benefits' => $benefits
        ]);
    }

    public function benefits($id,$ay,$semester){
        $lists = SchoolSemester::with('semester','benefits.benefit')->with(['benefits' => function($query) use ($id){ 
            $query->where('scholar_id',$id)->where('status_id',56);
        }])
        ->withSum(
            ['benefits' => function($query) use ($id) {
                $query->where('scholar_id', $id)->where('status_id',56);
            }],
            'amount'
        )
        ->whereHas('benefits',function ($query) use ($id) {
            $query->where('scholar_id', $id)->where('status_id',56);
        })
        ->when($ay, function ($query, $ay) {
            $query->where('academic_year',$ay);
        })
        ->when($semester, function ($query, $semester) {
            $query->where('semester_id',$semester);
        })
        ->get();

        return $lists;
    }
}
