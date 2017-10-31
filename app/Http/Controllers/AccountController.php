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
      // $text = 'ok its working now confirmed';
      // $number = '09105717885';
      // chdir('c:/xampp/htdocs/gnokii');
      // exec('echo '.$text.' | gnokii --sendsms '.$number);
      $tobenotify = array('Requisitioner' =>'ggaaaaaaa');
      $tobenotify=(object)$tobenotify;
      $job=(new NewApprovedMIRSJob($tobenotify))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
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
      return User::orderBy('id','DESC')->where('Role','2')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username','Mobile']);
    }
    public function getallManagers()
    {
      return User::orderBy('id','DESC')->where('Role','0')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username','Mobile']);
    }
    public function getallAdmin()
    {
      return User::orderBy('id','DESC')->where('Role','1')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username','Mobile']);
    }
    public function getOtherAccounts()
    {
      return User::orderBy('id','DESC')->where('Role','3')->orWhere('Role', '4')->orWhere('Role','5')->orWhere('Role','6')->orWhere('Role','7')->orWhere('Role','8')->paginate(5,['id','Fname','Lname','Signature','IsActive','Username','Mobile']);
    }
    public function ShowMyHistoryPage(Request $request)
    {
      $ActiveNames=User::whereNotNull('IsActive')->get(['Fname','Lname']);
      return view('Warehouse.Account.MyHistory',compact('ActiveNames'));
    }
    public function MyMIRSHistoryandSearch(Request $request)
    {
      return MIRSMaster::orderBy('MIRSNo','DESC')->where('Preparedby',$request->Preparedby)->where('MIRSDate','LIKE',$request->YearMonth.'%')->paginate(10,['MIRSNo','MIRSDate','Preparedby','Recommendedby','RecommendSignature','Approvedby','ApproveSignature','Purpose','IfDeclined','ApprovalReplacerSignature','ManagerReplacerSignature']);
    }
    public function MyMCTHistoryandSearch(Request $request)
    {
      return MCTMaster::orderBy('MCTNo','DESC')->where('Receivedby',$request->Receivedby)->where('MCTDate','LIKE',$request->YearMonth.'%')->paginate(10,['MCTNo','MCTDate','Particulars','AddressTo','Receivedby','ReceivedbySignature','IfDeclined']);
    }
    public function MyMRTHistoryandSearch(Request $request)
    {
      return MRTMaster::orderBy('id','DESC')->where('Returnedby',$request->Returnedby)->where('ReturnDate','LIKE',$request->YearMonth.'%')->paginate(10,['MRTNo','ReturnDate','Particulars','AddressTo','Returnedby','ReturnedbySignature','IfDeclined']);
    }
    public function MyMRHistoryandSearch(Request $request)
    {
      return MRMaster::orderBy('MRNo','DESC')->where('Receivedby',$request->Receivedby)->where('MRDate','LIKE',$request->YearMonth.'%')->paginate(10,['MRNo','MRDate','Supplier','Receivedby','ReceivedbySignature','Recommendedby','RecommendedbySignature','GeneralManager','GeneralManagerSignature'
      ,'IfDeclined']);
    }
    public function MyRVHistoryandSearch(Request $request)
    {
      return RVMaster::orderBy('RVNo','DESC')->where('Requisitioner',$request->Receivedby)->where('RVDate','LIKE',$request->YearMonth.'%')->paginate(10,['RVNo','RVDate','Purpose','Requisitioner','Recommendedby','RecommendedbySignature','GeneralManager','GeneralManagerSignature','BudgetOfficer'
      ,'ManagerReplacerSignature','BudgetOfficerSignature','IfDeclined']);
    }
    public function fetchDataofSelectedUser($id)
    {
      return User::where('id',$id)->get(['id','Fname','Lname','Signature','Position','Role','Username','IsActive','Manager','Mobile']);
    }
    public function updateUser(Request $request,$id)
    {
      $this->validate($request,[
        'Fname'=>'required|max:30',
        'Lname'=>'required|max:30',
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
      $validateUniqueName=User::where('Fname',$request->Fname)->where('Lname', $request->Lname)->where('id','!=',$id)->get(['id']);
      if (!empty($validateUniqueName[0]))
      {
        return response()->json(['error'=>$request->Fname.' '.$request->Lname.' has already been taken']);
      }
      $validateUniqueUN=User::where('Username', $request->Username)->where('id','!=',$id)->get(['id']);
        if (!empty($validateUN[0]))
        {
          return response()->json(['error'=>'Username has already been taken']);
        }
        $userDB= User::find($id);
        $userDB->Fname=$request->Fname;
        $userDB->Lname=$request->Lname;
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
        return User::where('Role',0)->whereNotNull('IsActive')->get(['id','Fname','Lname']);
    }
    public function getCurrentAssigned()
    {
      return User::whereNotNull('IfApproveReplacer')->take(1)->get(['Fname','Lname']);
    }


    // Create Manager Account
    public function SaveManagerAcc(Request $request)
    {
      $this->validate($request,[
        'Fname'=>'required|max:25',
        'Lname'=>'required|max:25',
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
      $userDB->Fname=$request->Fname;
      $userDB->Lname=$request->Lname;
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
        'Fname'=>'required|max:25',
        'Lname'=>'required|max:25',
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
      $userDB->Fname=$request->Fname;
      $userDB->Lname=$request->Lname;
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
