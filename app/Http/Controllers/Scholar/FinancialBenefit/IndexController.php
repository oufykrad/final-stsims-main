<?php

namespace App\Http\Controllers\Scholar\FinancialBenefit;

use App\Models\User;
use App\Models\Scholar;
use App\Models\BenefitList;
use App\Models\ListPrivilege;
use App\Models\BenefitRelease;
use App\Models\SchoolSemester;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Requests\ReleaseRequest;
use App\Http\Resources\Benefit\ListResource;
use App\Http\Resources\NameResource;
use App\Http\Resources\Scholar\EvaluationResource;
use App\Http\Resources\Scholar\Sub\ReleaseResource;

class IndexController extends Controller
{
    use UploadTrait;

    public function index(Request $request){
        $keyword = $request->keyword;
        if($request->search == 'print'){
            $this->print($request->id);
        }else if($request->search){
            $month = date_parse($request->month)['month'];
            $year = $request->year;
            $data = BenefitRelease::orderBy('created_at','DESC')
            ->when($month, function ($query, $month) {
                $query->whereMonth('created_at',$month);
            })
            ->when($year, function ($query, $year) {
                $query->whereYear('created_at',$year);
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where('batch','LIKE','%'.$keyword.'%');
            })
            ->paginate($request->count)
            ->withQueryString();
            return ReleaseResource::collection($data);
        }else{
            return inertia('Modules/FinancialBenefits/Index');
        }
    }

    public function store(ReleaseRequest $request){
        if($request->type == 'Completed'){
            $data = \DB::transaction(function () use ($request){
                $attachments = $this->release($request);
                $benefit = BenefitList::where('release_id',$request->id)->update(['status_id' => 56]);
                $data = BenefitRelease::where('id',$request->id)->update(['status_id' => 56, 'attachment' => json_encode($attachments)]);
                $data = BenefitRelease::where('id',$request->id)->first();
                return new ReleaseResource($data);
            });
            
            return back()->with([
                'message' => 'Mark as completed. Thanks',
                'data' =>  $data,
                'type' => 'bxs-check-circle'
            ]); 
        }else{
            $data = \DB::transaction(function () use ($request){
                $attachment = [];
                $count = BenefitRelease::whereYear('created_at',now())->count();
                $data = BenefitRelease::create(
                    array_merge($request->all(),[
                        'attachment' => json_encode($attachment),
                        'added_by' => \Auth::user()->id,
                        'batch' => str_pad(($count+1), 5, '0', STR_PAD_LEFT),
                        'status_id' => 55
                    ])
                );
                foreach($request->lists as $list){
                    foreach($list['benefits'] as $benefit){
                        $benefit = BenefitList::where('id',$benefit['id'])->first();
                        $benefit->status_id = 57;
                        $benefit->release_id = $data->id;
                        $benefit->save();
                    }
                }
                return $data;
            });

            return back()->with([
                'message' => 'Released was successfull. Thanks',
                'data' =>  $data,
                'type' => 'bxs-check-circle'
            ]); 
        }
    }

    public function create(Request $request){
        switch($request->type){
            case 'lists':
                return $this->lists();
            break;
            case 'benefits':
                return $this->benefits($request->info);
            break;
            case 'view':
                return $this->view($request->id);
            break;
        }
    }

    public function lists(){
        $date = date("Y").'-'.date("m").'-1';
        $pending = BenefitList::where('status_id',55)->where('month','<=',$date)->groupBy('scholar_id')->pluck('scholar_id');
        $scholars = Scholar::with('profile.user')->whereIn('id',$pending)->get();
        $data = [
            'pending' => $pending,
            'scholars' => NameResource::collection($scholars),
            'month' => date('F', mktime(0, 0, 0, date("m"), 10)),
            'count' => BenefitRelease::whereYear('created_at', '=', date("Y"))->whereMonth('created_at', '=', date("m"))->count()
        ];
        return $data;
    }

    public function benefits($info){
        $scholars = (!empty(json_decode($info))) ? json_decode($info) : NULL;
        $month = date("Y").'-'.date("m").'-1';
        $data = Scholar::with('profile.user')->with('benefits.benefit')->with('program')
        ->withWhereHas('benefits', function ($query) use ($month) {
            $query->where('status_id',55)->where('month','<=',$month);
        })
        ->whereIn('id',$scholars)
        ->get();

        return ListResource::collection($data);
    }

    public function edit($id){
        $data = BenefitRelease::where('id',$id)->first();
        $user = User::with('profile')->where('role','Scholarship Coordinator')->where('is_active',1)->first();

        $array = [
            'benefits' => new ReleaseResource($data),
            'user' => $user
        ];

        $pdf = \PDF::loadView('prints2.fb',$array)->setPaper('a4', 'portrait');
        return $pdf->download('FinancialBenefit.pdf');
        //s
    }

    public function print($id){
        $lists = SchoolSemester::with('semester')
        // ->with(['benefits' => function($query) use ($id){ 
        //     $query->where('scholar_id',$id)->where('status_id',56);
        // }])
        ->withSum(
            ['benefits' => function($query) use ($id) {
                $query->where('scholar_id', $id)->where('status_id',56);
            }],
            'amount'
        )
        ->whereHas('benefits',function ($query) use ($id) {
            $query->where('scholar_id', $id)->where('status_id',56);
        })
        ->get();

        $privileges = ListPrivilege::get();

        foreach($lists as $list){
            foreach($privileges as $privilege){
                
            }
            $arr = [
                'ay' => $list->academic_year.' - '.$list->semester->name,
                'academic_year' => $list->academic_year,
                'semester' => $list->semester->name
            ];
        }

        $array = [
            'lists' => $arr,
            'privileges' => ListPrivilege::pluck('short')
        ];

        return $array;

        $pdf = \PDF::loadView('prints.breakdown',$array)->setPaper('a4', 'landscape');
        return $pdf->download('FinancialBenefitBreakdown.pdf');
        
    }
}
