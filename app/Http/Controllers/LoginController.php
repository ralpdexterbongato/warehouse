<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Storage;
use App\User;
class LoginController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsAdmin',['except'=>['loginpage','loginSubmit','logoutAccount']]);
    }
    public function loginpage()
    {
      return view('Warehouse.login-page');
    }
    public function loginSubmit(Request $request)
    {
      $credentials = array('Username' =>$request->Username,'password'=>$request->Password );
      if (Auth::attempt($credentials)) {
        return redirect('/');
      }else
      {
        return redirect()->back();
      }
    }
    public function GetRegister()
    {
      return view('Warehouse.Register');
    }
    public function registrationStore(Request $request)
    {
      $this->RegistrationValidation($request);
      if ($request->hasFile('Signature')) {
        $picname=$request->Signature->hashName();
        Storage::putFile('public\signatures',$request->Signature);
        $userDB= new User;
        $userDB->Fname=$request->Fname;
        $userDB->Lname=$request->Lname;
        $userDB->Role=$request->Role;
        $userDB->Position=$request->Position;
        $userDB->Username=$request->Username;
        $userDB->Password=bcrypt($request->Password);
        $userDB->Signature=$picname;
        $userDB->save();
          return redirect('/');

      }

    }
    public function RegistrationValidation($request)
    {
     $this->validate($request,[
       'Fname'=>'required|max:30',
       'Lname'=>'required|max:30',
       'Role'=>'required',
       'Position'=>'required',
       'Username'=>'required|max:30|unique:users',
       'Password'=>'confirmed',
       'Signature'=>'required',
     ]);
    }
    public function logoutAccount()
    {
      Auth::logout();
      return redirect('/login');
    }
}
