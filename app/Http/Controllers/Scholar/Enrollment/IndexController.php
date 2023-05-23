<?php

namespace App\Http\Controllers\Scholar\Enrollment;

use Hashids\Hashids;
use App\Models\Scholar;
use App\Models\ScholarEnrollment;
use App\Models\ScholarEnrollmentList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Traits\EnrollmentTrait;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\Scholar\EvaluationResource;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\Scholar\Sub\EnrollmentResource;
use App\Http\Resources\Scholar\Sub\EnrolledResource;

class IndexController extends Controller
{
    use EnrollmentTrait, UploadTrait;

    public function index(Request $request){
        $keyword = $request->keyword;
        if($request->type == 'enrolled'){
            $data = ScholarEnrollment::with('scholar.profile','user.profile','level','semester.semester','semester.school.school')
            ->orderBy('created_at','DESC')
            ->paginate($request->counts)
            ->withQueryString();
            return EnrolledResource::collection($data);
        }
        if($request->search){
            if($keyword != ''){
                $data = Scholar::with('profile')->with('status','program')->with('enrollments')->with('education.school.school','education.course','education.level')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->whereHas('profile',function ($query) use ($keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                        ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                        ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                    });
                })->take(5)->get();
                return EvaluationResource::collection($data);
            }
        }else{
            return inertia('Modules/Enrollments/Index');
        }
    }

    public function store(EnrollmentRequest $request){
        $data = \DB::transaction(function () use ($request){
            $hashids = new Hashids('krad',10);
            $scholar_id = $hashids->decode($request->scholar_id);

            $this->newFinancialGroup($request);
            
            $attachments = [
                'grades' => [],
                'enrollments' => $this->enrollment($request)
            ];
            
            $data = ScholarEnrollment::create(array_merge($request->all(),['scholar_id' => $scholar_id[0],'attachment' => json_encode($attachments), 'added_by' => \Auth::user()->id]));
            $this->level($request);
            $this->createList($data->id,$request);
            $data = ScholarEnrollment::find($data->id);
            return $data;
        });

        return back()->with([
            'message' => 'Scholar enrolled successfully. Thanks',
            'data' =>  new EnrollmentResource($data),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function show($id){
        $data = ScholarEnrollmentList::where('enrollment_id',$id)->get();
        return DefaultResource::collection($data);
    }
}
