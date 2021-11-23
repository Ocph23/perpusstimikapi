<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
                'error' => $error,
                'message'=>$errorMessages
        ];
        return response()->json($response, $code);
    }

    public function errorMessage(\Exception $e)
    {
      try {
        $type =  get_class($e);
        if ($type == 'Illuminate\Database\QueryException') {
            $errorInfo = $e->errorInfo;
            if ($errorInfo && $errorInfo[1]) {
                $code = $errorInfo[1];
                switch ($code) {
                    case 1062:
                        return "Data Sudah Ada !";
                    default:
                        return $errorInfo[2];
                }
            }
        }
        return $e->getMessage();
      } catch (\Throwable $th) {
          return "Terjadi Kesalahan !";
      }
    }
}
