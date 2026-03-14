<?php

namespace App\Http\Controllers\ZKTeco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZKTecoController extends Controller
{

    public function getrequest()
    {
        info('ICLOCK getrequest', request()->all());
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }



    public function cdata(Request $request)
    {
        info('ICLOCK  cdata all', [$request->all()]);
        info('ICLOCK  cdata ', [$request->getContent()]);
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    public function getCdata()
    {
        info('ICLOCK getCdata', request()->all());
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
        info('ICLOCK ping', request()->all());
        return response("OK", 200);
    }

}
