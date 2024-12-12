<?php

namespace App\Services;

use App\Models\DeliveryMaster;

class PostDeliveryService
{
    public function uniqueCheckMobile($mobile)
    {
        return DeliveryMaster::where('CustomerMobile',$mobile)->exists();
    }

    public function uniqueCheckChassis($chassis)
    {
        return DeliveryMaster::where('FrameNo',$chassis)->exists();
    }
}