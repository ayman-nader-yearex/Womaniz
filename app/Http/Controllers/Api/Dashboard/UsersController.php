<?php
namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminDeleteValidation;
use App\Http\Requests\Dashboard\AdminFormValidation;
use App\Http\Requests\Dashboard\AdminSearchValidation;
use App\Http\Resources\Dashboard\AdminResource;
use App\Http\Resources\Dashboard\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    use ApiResponseTrait;
    public function index(){
        $users = User::with('country','city')->latest()->simplepaginate();
        return $this->sendResponse(resource_collection(UserResource::collection($users)));
    }

    public function show(User $user){
        $user->load('country','city');
        return $this->sendResponse(new UserResource($user));
    }

    public function store(AdminFormValidation $request){
        $data = $request->validated();
        if(isset($data['image'])){
            $data['image'] = FileHelper::upload_file('admins', $data['image']);
        }
        $data['password'] = Hash::make($data['password']);
        unset($data['jobs']);
        Admin::create($data);
        return $this->sendResponse([], 'success' , 200);
    }

    public function update(AdminFormValidation $request, Admin $admin){
        $data = $request->validated();
        if(isset($data['image'])){
            $data['image'] = FileHelper::update_file('admins', $data['image'], $admin->image );
        }
        $data['password'] = Hash::make($data['password']);
        unset($data['jobs']);
        $admin->update($data);
        return $this->sendResponse([], 'success' , 200);
    }

    public function delete(AdminDeleteValidation $request){
        $data = $request->validated();
        Admin::whereIn('id',$data['ids'])->delete();
        return $this->sendResponse([], 'success' , 200);
    }

    public function search(){

        $admins = Admin::where('country_id', auth()->user()->country_id);

        if(request('search')){
            $admins = $admins->where(function($q){
                $q->where('name', 'like', '%'.request('search').'%')
                ->orwhere('email', 'like', '%'.request('search').'%')
                ->orwhere('birthdate', 'like', '%'.request('search').'%')
                ->orwhere('address', 'like', '%'.request('search').'%')
                ->orwhere('phone', 'like', '%'.request('search').'%')
                ->orwhere('status', 'like', '%'.request('search').'%');
            });
        }

        if(request()->has('status')){
            $admins = $admins->where('status' ,request('status'));
        }

        $admins = $admins->simplePaginate();
        return $this->sendResponse(resource_collection(AdminResource::collection($admins)));
    }

}




