<?php

namespace App\Http\Controllers\Scholar\Reimbursement;

use App\Models\Scholar;
use App\Models\BenefitList;
use App\Models\ScholarReimbursement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Resources\Scholar\ReimbursementResource;
use App\Http\Resources\Scholar\Sub\SearchResource;
use App\Http\Resources\DefaultResource;
use App\Http\Requests\ReimbursementRequest;

class IndexController extends Controller
{
    use UploadTrait;

    public function index(Request $request){
        if($request->search == 'lists'){
            $data = ScholarReimbursement::with('scholar.profile','semester.semester','benefit')
            ->when($request->status, function ($query, $status) {
                ($status == 'true') ? $query->where('is_approved',1) :  $query->where('is_approved',0);
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->counts)
            ->withQueryString();
            return ReimbursementResource::collection($data);
        }else if($request->search){
            $keyword = $request->keyword;
            if($keyword != ''){
                $data = Scholar::with('profile')->with('status','program')->with('education.school.semesters.semester')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->whereHas('profile',function ($query) use ($keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                        ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                        ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                    });
                })
                ->take(5)->get();
                return SearchResource::collection($data);
            }
        }else{
            return inertia('Modules/Reimbursements/Index');
        }
    }

    public function store(ReimbursementRequest $request){
        $data = \DB::transaction(function () use ($request){
            $attachments = [
                $this->reimbursement($request)
            ];
            $data = ScholarReimbursement::create(array_merge($request->all(),['attachment' => json_encode($attachments), 'checked_by' => \Auth::user()->id]));
            return new ReimbursementResource($data);
        });
        
        return back()->with([
            'message' => 'Reimbursement successful. Thanks',
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function update(Request $request)
    {   
        $data = \DB::transaction(function () use ($request){
            $data = ScholarReimbursement::findOrFail($request->id);
            $data->update($request->except('editable'));
            if($data){
                BenefitList::create([
                    'amount' => $data->amount,
                    'status_id' => 56,
                    'is_reimbursed' => 1,
                    'scholar_id' => $data->scholar_id,
                    'benefit_id' => $data->benefit_id,
                    'school_semester_id' => $data->school_semester_id,
                    'release_type' => 'Full',
                    'month' => date('Y-m-d'),
                    'attachment' => json_encode([])
                ]);
            }
            return $data;
        });
        
        return back()->with([
            'message' => 'Reimbursement updated successfully. Thanks',
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }
}
