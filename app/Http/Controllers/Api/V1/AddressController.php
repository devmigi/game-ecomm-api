<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AddressResource;
use App\Http\Resources\PincodeResource;
use App\Models\Address;
use App\Models\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AddressController extends ApiController
{


    /**
     * Get all addresses of a user
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $addresses = Cache::rememberForever("users.{$request->user()->id}.address", function () use ($request) {

            return Address::where('user_id', $request->user()->id)->where('deleted', false)->with(['pincode', 'city.state'])->get();
        });

        return $this->sendResponse(AddressResource::collection($addresses), 'user addresses');
    }


    /**
     * API - Remove user Address
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function remove(Request $request){
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $input = $request->all();

        $updated = Address::where('id', $input['address_id'])->where('user_id', $user->id)->update(['deleted' => true]);

        // clear cache
        Cache::forget("users.{$user->id}.address");

        return $this->sendResponse(['updated' => $updated], 'Successfully removed.');
    }



    /**
     * Get Cities of a pincode
     *
     * @return \Illuminate\Http\Response
     */
    public function pincodeCities($pincode)
    {
        $pincodes = Cache::rememberForever("pincodes.$pincode", function () use ($pincode) {
            return Pincode::where('pincode', $pincode)->where('active', true)->with('city.state')->get();
        });

        return $this->sendResponse(PincodeResource::collection($pincodes), 'pincode details');
    }


}
