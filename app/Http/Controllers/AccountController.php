<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Storage;
use App\User;
use App\MIRSMaster;
use App\MCTMaster;
class AccountController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsAdmin',['except'=>['loginpage','MyMCTHistoryandSearch','MyMIRSHistoryandSearch','ShowMyHistoryPage','loginSubmit','logoutAccount']]);
    }
    public function loginpage()
    {
      return view('Warehouse.Account.login-page');
    }
    public function loginSubmit(Request $request)
    {
      $credentials = array('Username' =>$request->Username,'password'=>$request->Password );
      if (Auth::attempt($credentials)) {
        if (Auth::user()->IsActive==null)
        {
          Auth::logout();
        }
        return redirect('/');
      }else
      {
        return redirect()->back();
      }
    }
    public function GetRegister()
    {
      return view('Warehouse.Account.Register');
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
    public function GMAccountsList()
    {
      return view('Warehouse.Account.ManageAccounts');
    }
    public function ManagerAccountsList()
    {
      return view('Warehouse.Account.ManageAccountsManagers');
    }
    public function AdminAccountslist()
    {
      return view('Warehouse.Account.ManageAccountsAdmins');
    }
    public function otheraccountslist()
    {
      return view('Warehouse.Account.ManageAccountsOther');
    }

    //vue js
    public function getallGMAccounts()
    {
      return User::orderBy('id','DESC')->where('Role','2')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username']);
    }
    public function getallManagers()
    {
      return User::orderBy('id','DESC')->where('Role','0')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username']);
    }
    public function getallAdmin()
    {
      return User::orderBy('id','DESC')->where('Role','1')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username']);
    }
    public function getOtherAccounts()
    {
      return User::orderBy('id','DESC')->where('Role','3')->orWhere('Role', '4')->orWhere('Role','5')->orWhere('Role','6')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username']);
    }
    public function ShowMyHistoryPage(Request $request)
    {
      return view('Warehouse.Account.MyHistory');
    }
    public function MyMIRSHistoryandSearch(Request $request)
    {
      return MIRSMaster::where('Preparedby',$request->Preparedby)->where('MIRSDate','LIKE',$request->YearMonth.'%')->paginate(1,['MIRSNo','MIRSDate','Preparedby','Recommendedby','RecommendSignature','Approvedby','ApproveSignature','Purpose','IfDeclined','ApprovalReplacerSignature']);
    }
    public function MyMCTHistoryandSearch(Request $request)
    {
      return MCTMaster::where('Receivedby',$request->Receivedby)->where('MCTDate','LIKE',$request->YearMonth.'%')->paginate(1,['MCTNo','MCTDate','Particulars','AddressTo','Issuedby','Receivedby','IssuedbySignature']);
    }
}
