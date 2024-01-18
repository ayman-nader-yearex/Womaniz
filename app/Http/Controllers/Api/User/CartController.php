<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AddProductCartValidation;
use App\Http\Resources\Api\CartResource;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function cartDetails(){
        $user = auth()->user()->load(['carts.product','appliedcoupon']);
        $totalSub = 0 ;
        $tax  = 14;
        $shipping = count($user->carts) > 0 ?  20 : 0 ;
        $discount = $user->appliedcoupon ? $user->appliedcoupon->discount : 0  ;
        $user->carts->each(function ($cart) use (&$total , &$totalSub) {
            $cart->price = $cart->quantity * $cart->product->price; // Assuming there's a 'price' column in your 'products' table
            $cart->price_after_sale = $cart->quantity * ( $cart->product->price -  $cart->product->price_after_sale);
            //sum cart final
            $totalSub += $cart->price_after_sale;
            //sum cart final
        });
        $data = [
            'total' =>  round(( ($totalSub - ($totalSub * $discount / 100))  + ( $totalSub * $tax / 100 ) + $shipping ) * 2) / 2 ,
            'totalSub' => $totalSub ,
            'discount' => $discount ,
            'details' => CartResource::collection($user->carts) ,
            'vat' => $tax ,
            'shipping' => $shipping ,
        ];
        return $this->sendResponse($data);
    }

    public function minusQuantity($cartId){
        $cart = Cart::find($cartId);
        if(!$cart){
            return $this->sendResponse(['error' => __('messages.Cart item is not found')],'fail' , 404);
        }
        if($cart->quantity == 1){
            return $this->sendResponse(['error' => __('messages.Quantity cant be less than one')], 'fail' , 400);
        }
        $cart->update([
            'quantity' => $cart->quantity - 1,
        ]);
        return $this->cartDetails();
    }

    public function plusQuantity($cartId){
        $cart = Cart::find($cartId);
        if(!$cart){
            return $this->sendResponse(['error' => __('messages.Cart item is not found')],'fail' , 404);
        }
        $cart->update([
            'quantity' => $cart->quantity + 1,
        ]);
        return $this->cartDetails();
    }

    public function remove($cartId){
        $cart = Cart::find($cartId);
        if(!$cart){
            return $this->sendResponse(['error' => __('messages.Cart item is not found')],'fail' , 404);
        }
        $cart->delete();
        return $this->cartDetails();
    }

    public function add(AddProductCartValidation $request){
        $data = $request->validated();
        $user  =  auth()->user();
        $product = Product::where([
            'id' => $data['product_id'],
        ]);
        if(!$product){
            return $this->sendResponse(['error' => __('messages.Product is not found')],'fail',404);
        }
        Cart::firstorcreate([
            'product_id' =>  $data['product_id'] ,
            'user_id' => auth()->user()->id ,
        ]);
        return $this->cartDetails();
    }

}
