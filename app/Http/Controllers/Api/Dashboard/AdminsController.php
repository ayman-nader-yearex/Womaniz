<?php
namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdminDeleteValidation;
use App\Http\Requests\Dashboard\AdminFormValidation;
use App\Http\Requests\Dashboard\AdminSearchValidation;
use App\Http\Resources\Dashboard\AdminResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminsController extends Controller
{
    use ApiResponseTrait;
    public function index(){
        $admins = Admin::with('country')->latest()->simplepaginate();
        return $this->sendResponse(resource_collection(AdminResource::collection($admins)));
    }

    public function show(Admin $admin){
        $admin->load('country');
        return $this->sendResponse(new AdminResource($admin));
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

        if(request('status')){
            $admins = $admins->where('status' ,request('status'));
        }

        $admins = $admins->simplePaginate();
        return $this->sendResponse(resource_collection(AdminResource::collection($admins)));
    }

}




