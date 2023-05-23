<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Resources\LogsResource;
use App\Http\Resources\StaffResource;
use App\Http\Requests\StaffRequest;

class StaffController extends Controller
{
    public function index(Request $request){
        if($request->search){
            $data = StaffResource::collection(
                User::query()
                ->with('profile.agency')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->whereHas('profile',function ($query) use ($keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', "%{$keyword}%")
                        ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', "%{$keyword}%");
                    })->orWhere(function ($query) use ($keyword) {
                        $query->where('username', 'LIKE', "%{$keyword}%")->whereNotIn('role',['Scholar','Administrator']);
                    });
                })
                ->whereNotIn('role',['Scholar','Administrator'])
                ->get()
            );
            return $data;
        }else{
            return inertia('Modules/Staffs/Index');
        }
    }

    public function store(StaffRequest $request){
        $data = \DB::transaction(function () use ($request){
            $password = rand(1000000000,9999999999);
            $data = User::create(array_merge($request->all(), ['password' => bcrypt($password), 'temp_password' => $password]));
            $data->profile()->create($request->all());
            $id = $data->id;
            if($request->img != ''){
                $data = $request->img;
                $img = explode(',', $data);
                $ini =substr($img[0], 11);
                $type = explode(';', $ini);
                if($type[0] == 'png'){
                    $image = str_replace('data:image/png;base64,', '', $data);
                }else{
                    $image = str_replace('data:image/jpeg;base64,', '', $data);
                }
                $image = str_replace(' ', '+', $image);
                $imageName = $request->username.'.'.$type[0];
                
                if(\File::put(public_path('images/avatars'). '/' . $imageName, base64_decode($image))){
                    $data = User::findOrFail($id);
                    $data->avatar = $imageName;
                    $data->save();
                }
            }

            return $data;
        });

        return back()->with([
            'message' => 'Staff created successfully. Thanks',
            'data' => new StaffResource($data),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function show(Request $request){
        if($request->staff == 'logs'){
            $data = Log::lists();
            return LogsResource::collection($data);
        }
        // $data = new StaffResource(User::findOrFail($request->staff));
        // return inertia('Modules/Staffs/Profile',['user' => $data]);
    }

    public function update(Request $request)
    {   
        $data = \DB::transaction(function () use ($request){
            $user = User::findOrFail($request->id);
            if($request->type == 'token'){
                $user = User::findOrFail($request->id);
                $user->tokens()->delete();
                $token = $user->createToken('kradworkz')->plainTextToken;
                return $token;
            }else if($request->type == 'revoke'){
                $user = User::findOrFail($request->id);
                $user->tokens()->delete();
                return [
                    'data' => '',
                    'message' => 'User API Key revoked. Thanks',
                    'type' => 'bx-mail-send'
                ];
            }else if($request->type === 'password'){
                $user->update($request->except('img','editable','type'));
            }else if($request->type === 'verify'){
                $user->verify();

                return [
                    'data' => '',
                    'message' => 'User verification successfully send. Thanks',
                    'type' => 'bx-mail-send'
                ];
            }else{
                $data = User::findOrFail($request->id);
                $data->update($request->except('img','editable'));
                $profile = UserProfile::where('user_id',$request->id)->first();
                $profile->update($request->except('email','role','is_active','img','editable'));
                ($request->img != '') ? $data = $data->image($request->all()) : '';
                $data = User::findOrFail($request->id);
                return [
                    'data' => $data,
                    'message' => 'User updated successfully. Thanks',
                    'type' => 'bxs-check-circle'
                ];
            }
        });
        
        if($request->editable){
            return back()->with([
                'message' => $data['message'],
                'data' => ($data['data'] != '') ? new StaffResource($data['data']) : '',
                'type' => $data['type']
            ]);
        }else{
            return $data;
        }

    }
}
