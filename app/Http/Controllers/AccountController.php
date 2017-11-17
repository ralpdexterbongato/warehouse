<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Storage;
use App\User;
use App\MIRSMaster;
use App\MCTMaster;
use App\MRTMaster;
use App\MRMaster;
use App\RVMaster;
use \Carbon\Carbon;
use Image;
use App\jobs\NewApprovedMIRSJob;
class AccountController extends Controller
{
    public function __construct()
    {
      $this->middleware('IsAdmin',['except'=>['loginpage','sendsms','getCurrentAssigned','UpdateManagerTakePlace','getActiveManager','toManagerTakePlacePage','fetchDataofSelectedUser','MyRVHistoryandSearch','MyMRHistoryandSearch','MyMRTHistoryandSearch','MyMCTHistoryandSearch','MyMIRSHistoryandSearch','ShowMyHistoryPage','loginSubmit','logoutAccount']]);
    }
    public function sendsms()
    {
       return $mirs=MIRSMaster::with('users')->paginate(4);
      // return $mirs=MIRSMaster::with('users')->where('MIRSNo', '17-0001')->get();
    }
    public function loginpage()
    {
      return view('Warehouse.Account.login-page');
    }
    public function loginSubmit(Request $request)
    {
      $this->validate($request,[
        'Username'=>'required',
        'Password'=>'required',
      ]);
      $credentials = array('Username' =>$request->Username,'password'=>$request->Password );
      if (Auth::attempt($credentials)) {
        if (Auth::user()->IsActive==null)
        {
          Auth::logout();
          return ['message'=>'Sorry this account has been deactivated by the admin.'];
        }
        return ['redirect'=>'/'];
      }else
      {
        return ['message'=>'Incorrect username/password.'];
      }
    }
    public function logoutAccount()
    {
      Auth::logout();
      return ['redirect'=>route('login')];
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
      return User::orderBy('id','DESC')->where('Role','2')->paginate(5,['id','FullName','Signature','IsActive','Username','Mobile']);
    }
    public function getallManagers()
    {
      return User::orderBy('id','DESC')->where('Role','0')->paginate(5,['id','FullName','Signature','IsActive','Username','Mobile']);
    }
    public function getallAdmin()
    {
      return User::orderBy('id','DESC')->where('Role','1')->paginate(5,['id','FullName','Signature','IsActive','Username','Mobile']);
    }
    public function getOtherAccounts()
    {
      return User::orderBy('id','DESC')->where('Role','3')->orWhere('Role', '4')->orWhere('Role','5')->orWhere('Role','6')->orWhere('Role','7')->orWhere('Role','8')->paginate(5,['id','FullName','Signature','IsActive','Username','Mobile']);
    }
    public function ShowMyHistoryPage(Request $request)
    {
      $ActiveUser=User::whereNotNull('IsActive')->get(['id','FullName']);
      return view('Warehouse.Account.MyHistory',compact('ActiveUser'));
    }
    public function MyMIRSHistoryandSearch(Request $request)
    {
       $user = User::find($request->PreparedById);
       return $mirshistory = $user->MIRSHistory($request->YearMonth)->paginate(5);

    }
    public function MyMCTHistoryandSearch(Request $request)
    {
      $user = User::find($request->ReceivedById);
      return $mcthistory = $user->MCTHistory($request->YearMonth)->paginate(5);
    }
    public function MyMRTHistoryandSearch(Request $request)
    {
      $user = User::find($request->ReturnedById);
      return $mrthistory = $user->MRTHistory($request->YearMonth)->paginate(5);
    }
    public function MyMRHistoryandSearch(Request $request)
    {
      return MRMaster::orderBy('MRNo','DESC')->where('Receivedby',$request->Receivedby)->where('MRDate','LIKE',$request->YearMonth.'%')->paginate(10,['MRNo','MRDate','Supplier','Receivedby','ReceivedbySignature','Recommendedby','RecommendedbySignature','GeneralManager','GeneralManagerSignature'
      ,'IfDeclined']);
    }
    public function MyRVHistoryandSearch(Request $request)
    {
      $user = User::find($request->Requisitioner);
      return $rvhistory=$user->RVHistory($request->YearMonth)->paginate(5);
    }
    public function fetchDataofSelectedUser($id)
    {
      return User::where('id',$id)->get(['id','FullName','Signature','Position','Role','Username','IsActive','Manager','Mobile']);
    }
    public function updateUser(Request $request,$id)
    {
      $this->validate($request,[
        'FullName'=>'required|max:30',
        'Role'=>'required',
        'Username'=>'required|max:30',
        'Mobile'=>'max:11',
        'Password'=>'confirmed',
        'Manager'=>'numeric',
        'IsActive'=>'max:1',
      ]);
      if ($request->Signature!=null)
      {
        $imageData = $request->get('Signature');
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        Image::make($request->get('Signature'))->save(public_path('/storage/signatures/').$fileName);
      }
      $validateUniqueName=User::where('FullName',$request->FullName)->where('id','!=',$id)->get(['id']);
      if (!empty($validateUniqueName[0]))
      {
        return response()->json(['error'=>$request->FullName.' has already been taken']);
      }
      $validateUniqueUN=User::where('Username', $request->Username)->where('id','!=',$id)->get(['id']);
        if (!empty($validateUN[0]))
        {
          return response()->json(['error'=>'Username has already been taken']);
        }
        $userDB= User::find($id);
        $userDB->FullName=$request->FullName;
        $userDB->Role=$request->Role;
        $userDB->Manager=$request->Manager;
        $userDB->Mobile=$request->Mobile;
        if ($request->Position)
        {
          $userDB->Position=$request->Position;
        }elseif($request->Role==0)
        {
          $userDB->Position='Manager';
        }elseif($request->Role==1)
        {
          $userDB->Position='Admin';
        }elseif($request->Role==2)
        {
          $userDB->Position='General Manager';
        }elseif($request->Role==3)
        {
          $userDB->Position='Warehouse Assistant';
        }elseif($request->Role==4)
        {
          $userDB->Position='Warehouse-Head';
        }elseif($request->Role==5)
        {
          $userDB->Position='Senior Auditor';
        }elseif($request->Role==6)
        {
          $userDB->Position='Stock clerk';
        }elseif($request->Role==7)
        {
          $userDB->Position='Budget Officer';
        }elseif($request->Role==8)
        {
          $userDB->Position='Requisitioner';
        }

        $userDB->Username=$request->Username;
        if ($request->Password!=null)
        {
          $userDB->Password=bcrypt($request->Password);
        }
        if ($request->Signature!=null)
        {
          $userDB->Signature=$fileName;
        }
        if ($request->IsActive==null)
        {
          $userDB->IsActive=null;
        }elseif($request->IsActive!=null)
        {
          $userDB->IsActive='0';
        }
        $userDB->save();
    }
    public function deleteAccount($id)
    {
      User::where('id',$id)->delete();
    }
    public function toManagerTakePlacePage()
    {
      return view('Warehouse.ManagerTakePlaceGM');
    }
    public function UpdateManagerTakePlace(Request $request)
    {
      User::whereNotNull('IfApproveReplacer')->update(['IfApproveReplacer'=>null]);
      if ($request->ManagerID!=null)
      {
        User::where('id', $request->ManagerID)->update(['IfApproveReplacer'=>0]);
      }
    }
    public function getActiveManager()
    {
        return User::where('Role',0)->whereNotNull('IsActive')->get(['id','FullName']);
    }
    public function getCurrentAssigned()
    {
      return User::whereNotNull('IfApproveReplacer')->take(1)->get(['FullName']);
    }


    // Create Manager Account
    public function SaveManagerAcc(Request $request)
    {
      $this->validate($request,[
        'FullName'=>'required|max:25',
        'Username'=>'required',
        'Mobile'=>'max:11',
        'Position'=>'required|max:50',
        'Password'=>'required|confirmed',
        'Signature'=>'required',
      ]);
      if ($request->Signature!=null)
      {
        $imageData = $request->get('Signature');
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        Image::make($request->get('Signature'))->save(public_path('/storage/signatures/').$fileName);
      }
      $userDB=new User;
      $userDB->FullName=$request->FullName;
      $userDB->Username=$request->Username;
      $userDB->Mobile=$request->Mobile;
      $userDB->Role='0';
      $userDB->Position=$request->Position;
      $userDB->Password=bcrypt($request->Password);
      $userDB->Signature=$fileName;
      $userDB->save();
    }
    public function SaveNewUser(Request $request)
    {
      $this->validate($request,[
        'FullName'=>'required|max:25',
        'Username'=>'required',
        'Mobile'=>'max:11',
        'Role'=>'required',
        'Manager'=>'max:50',
        'Password'=>'required|confirmed',
        'Signature'=>'required',
        'Manager'=>'numeric',
      ]);
      if (($request->Manager==null)&&($request->Role!=2))
      {
        return ['error'=>'The manager is required'];
      }
      if ($request->Signature!=null)
      {
        $imageData = $request->get('Signature');
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        Image::make($request->get('Signature'))->save(public_path('/storage/signatures/').$fileName);
      }
      $userDB=new User;
      $userDB->FullName=$request->FullName;
      $userDB->Username=$request->Username;
      $userDB->Mobile=$request->Mobile;
      $userDB->Role=$request->Role;
      if($request->Role==1)
      {
        $userDB->Position='Admin';
      }elseif($request->Role==2)
      {
        $userDB->Position='General Manager';
      }elseif($request->Role==3)
      {
        $userDB->Position='Warehouse Assistant';
      }elseif($request->Role==4)
      {
        $userDB->Position='Warehouse-Head';
      }elseif($request->Role==5)
      {
        $userDB->Position='Senior Auditor';
      }elseif($request->Role==6)
      {
        $userDB->Position='Stock clerk';
      }elseif($request->Role==7)
      {
        $userDB->Position='Budget Officer';
      }elseif($request->Role==8)
      {
        $userDB->Position='Requisitioner';
      }
      $userDB->Manager=$request->Manager;
      $userDB->Password=bcrypt($request->Password);
      $userDB->Signature=$fileName;
      $userDB->save();
    }
}
