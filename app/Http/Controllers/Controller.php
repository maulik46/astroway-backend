<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function __construct(Request $request)
    {

        // $request->setHeaders(array(
        //     'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2xvY2FsaG9zdC9hcGkvbG9naW5BcHBVc2VyIiwiaWF0IjoxNzEzNzg0MjQyLCJleHAiOjE3MTQwMDAyNDIsIm5iZiI6MTcxMzc4NDI0MiwianRpIjoidnR4T2RiQTEyT1B3OWdJRSIsInN1YiI6IjczMCIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.VfMl3Wijbjh9p7ro81VYszZKweTm3Ew7LPDpL9q_6wQ',
        //     'Content-Type' => 'application/json',
        //     'Cookie' => 'PHPSESSID=ruv0gkvctbtfnergrhqc61oo5p'
        //   ));


        // header('Content-Type:application/json');
        // header('Accept:application/json');
        // header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');

    }


}
