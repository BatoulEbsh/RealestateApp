<?php

namespace App\Traits;

use App\Models\Image;
use App\Models\User;

trait Helper
{

    public function saveImage($image, $file): string
    {
        $newImage = time() . $image->getClientOriginalName();
        $image->move("uploads/$file", $newImage);
        return "uploads/$file/" . $newImage;
    }
    public function saveImages($images,$propertyId, $file): void
    {
        foreach ($images as $image){
            $data=new Image();
            $data->fill(
                [
                    'property_id' => $propertyId,
                    'image' => $this->saveImage($image,$file)
                ]
            );
            $data->save();
        }
    }
    public  function howMoney(User $user){
        $wallet = $user->wallet;
        $s=$wallet->walletOperations()->where('type','=',true)->sum('value');
        $s2=$wallet->walletOperations()->where('type','=',false)->sum('value');
        return $s-$s2;

    }
}
