<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Anggota;
use App\Http\Controllers\API\BaseController;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;


class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        try {
            $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            if (Auth::attempt([$fieldType => $request->username, 'password' => $request->password])) {
                $authUser = Auth::user();
                $roles = $authUser->getRoleNames();
                $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
                $success['name'] =  $authUser->name;
                $success['roles'] =  $roles;

                return $this->sendResponse($success, 'User signed in');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
            }
        } catch (\Throwable $th) {
            $message = $this->errorMessage($th);
            return $this->sendError($message, [], 400);
        }
    }

    public function signup(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            if($input['email']=='' || $input['email']=='mahasiswa@mail.com' || $input['email']=='stimiksepnop@mail.com' ){
                //throw new Exception("Silahkan Ubah Email Anda di 'SIMAK' Sebelum Mendaftar !");
                $input['email']= $input['username']."@mail.com";
            }
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Error validation', $validator->errors(),400);
            }

           // $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $user->assignRole($input['jenis']);
            $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
            $success['name'] =  $user->name;
            $input['user_id'] = $user->id;
            $input['nama'] =  $user->name;
            $anggota = Anggota::create($input);
            DB::commit();
            return $this->sendResponse($success, 'User created successfully.');
        } catch (\Exception $e) {
            $message = $this->errorMessage($e);
            DB::rollBack();
            return $this->sendError($message, [], 400);
        }
    }


    public function profile(Request $request)
    {
        $userId = Auth::id();
        $user = Anggota::where('user_id', $userId)->first();
        return $this->sendResponse($user, "Success");
    }
}
