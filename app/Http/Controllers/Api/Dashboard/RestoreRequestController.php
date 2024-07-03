<?php
namespace App\Http\Controllers\Api\Dashboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AccountRestoreRequestChangeStatusValidation;
use App\Http\Resources\Dashboard\RestoreAccountRequestResource;
use App\Http\Resources\Dashboard\RoleResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\RestoreAccountRequest;


class RestoreRequestController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('permission:spin-game-information', ['only' => ['spinInformation']]);
        $this->middleware('permission:spin-game-information-update', ['only' => ['spinInformationUpdate']]);
    }

    public function index(){
        $requests = RestoreAccountRequest::with(['user' => function($q){
            $q->withTrashed();
        }])->latest();
        if(request()->has('search')){
            $requests = $requests->where(function($q){
                $q->where('email', 'like', '%'.request('search').'%')
                ->orwhereHas('user', function($q){
                    $q->where('name',  'like', '%'.request('search').'%')
                      ->orwhere('phone',  'like', '%'.request('search').'%')
                      ->orwhere('birthdate', 'like', '%'.request('search').'%')
                      ->orwhere('gender', 'like', '%'.request('search').'%')
                      ->orwhere('id',  'like', '%'.request('search').'%');
                });
            });
        }
        if(request()->has('date')){
            $requests->where('created_at', 'like', '%'.request('date').'%');
        }
        $requests =  $requests->simplepaginate();
        return $this->sendResponse(resource_collection(RestoreAccountRequestResource::collection($requests)));
    }

    public function changeStatus(AccountRestoreRequestChangeStatusValidation $request , RestoreAccountRequest $restoreAccountRequest){
        $data = $request->validated();
        if($restoreAccountRequest->status == 'pending'){
            $statusMappingArray = [
                0 => 'rejected',
                1 => 'accepted'
            ];
            $restoreAccountRequest->update([
                'status' =>  $statusMappingArray[$data['status']],
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);
        }
        return $this->sendResponse([]);
    }

}
