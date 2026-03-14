<?php

namespace App\Http\Controllers\ZKTeco;

use App\Cache\ZKTecoCache;
use App\DTOs\IclockAttendanceDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZKTecoController extends Controller
{

    public function getRequest()
    {
        try {
            if(request()->SN && !blank(request()->SN)){

                $isOnline = ZKTecoCache::findBySerialNumber(request()->SN);
                if($isOnline){
                    ZKTecoCache::deviceHeartbeatStatus(request()->SN);
                }
            }
        } catch (\Throwable $th) {
            Log::error('ICLOCK getRequest error', ['error' => $th->getMessage()]);
        }

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }


    public function cdata(Request $request)
    {

        $raw = trim($request->getContent());

        if (!IclockAttendanceDTO::isSkippablePayload($raw)) {
            $result =IclockAttendanceDTO::fromRaw($raw);
            info('ICLOCK  cdata result', [$result,request()->SN]);
        }


        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    public function getCdata()
    {
        return response(
            "Stamp=9999\n".
            "OpStamp=1\n".
            "ErrorDelay=30\n".
            "Delay=10\n".
            "TransTimes=00:00;23:59\n".
            "TransInterval=1\n".
            "TransFlag=1111111111\n".
            "Realtime=1\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }

    public function ping()
    {
        if(request()->SN && !blank(request()->SN)){
            $isOnline = ZKTecoCache::findBySerialNumber(request()->SN);
            if($isOnline){
                ZKTecoCache::deviceHeartbeatStatus(request()->SN);
            }
        }
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

}
