<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Hash;
class MyAccountSettings extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function MyAccountSettingsPage()
  {
    return view('Warehouse.Account.MyAccountSettings');
  }
  public function FetchMyData()
  {
    return Auth::user();
  }
  public function updateContact(Request $request)
  {
    User::where('id', Auth::user()->id)->update(['Mobile'=>$request->NewMobile]);
  }
  public function updateUserName(Request $request)
  {
    $NullIfAvailable=User::where('Username', $request->NewUserName)->value('id');
    if ($NullIfAvailable==null)
    {
      User::where('id', Auth::user()->id)->update(['Username'=>$request->NewUserName]);
    }else
    {
      return ['error'=>'Username is already taken'];
    }
  }
  public function changeMyPassword(Request $request)
  {
    if ($request->Password!=$request->Password_confirmation)
    {
      return ['error'=>'New pass did not match'];
    }
   $user = User::find(auth()->user()->id);
   if(!Hash::check($request->currentPass, $user->password))
   {
     return ['error'=>'Please retype your current password'];
   }else
   {
     User::where('id', Auth::user()->id)->update(['Password'=>bcrypt($request->Password)]);
   }
  }
}
