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
      $this->middleware('IsAdmin',['except'=>['getSelectedRoleAndSearch','loginpage','sendsms','getCurrentAssigned','UpdateManagerTakePlace','getActiveManager','toManagerTakePlacePage','fetchDataofSelectedUser','loginSubmit','logoutAccount']]);
    }
    public function loginpage()
    {
      return view('Warehouse.Account.login-page');
    }
    public function loginSubmit(Request $request)
    {
      if ($request->Username==null || $request->Password==null)
      {
        return ['message'=>'fields are required'];
      }
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
    public function AccountsList()
    {
      return view('Warehouse.Account.ManageAccounts');
    }
    //vue js
    public function getallManagers()
    {
      return User::where('Role', '0')->where('IsActive', '0')->get(['id','FullName']);
    }
    public function getSelectedRoleAndSearch(Request $request)
    {
      if ($request->Role=='')
      {
        return User::orderBy('id','DESC')->where('FullName','LIKE','%'.$request->FullName.'%')->paginate(15,['id','FullName','Username','Signature','IsActive','Mobile']);
      }else
      {
        return User::orderBy('id','DESC')->where('Role', $request->Role)->where('FullName', 'LIKE','%'.$request->FullName.'%')->paginate(15,['id','FullName','Username','Signature','IsActive','Mobile']);
      }
    }
    public function fetchDataofSelectedUser($id)
    {
      return User::where('id',$id)->get(['id','FullName','Signature','Position','Role','Username','IsActive','Manager','Mobile']);
    }
    public function updateUser(Request $request,$id)
    {
      $this->handleUpdateUserValidation($request,$id);
      $validateUniqueName=User::where('FullName',$request->FullName)->where('id','!=',$id)->get(['id']);
      if (!empty($validateUniqueName[0]))
      {
        return response()->json(['error'=>$request->FullName.' has already been taken']);
      }
      $validateUniqueUN=User::where('Username', $request->Username)->where('id','!=',$id)->get(['id']);
      if (!empty($validateUniqueUN[0]))
      {
        return response()->json(['error'=>'Username has already been taken']);
      }
      $fileName = $this->saveSignatureImage($request);
      $rightPosition=$this->determinePosition($request);

      $userDB= User::find($id);
      $userDB->FullName=$request->FullName;
      $userDB->Role=$request->Role;
      $userDB->Position = $rightPosition;
      $userDB->Mobile=$request->Mobile;
      if (($request->Role!=0) && ($request->Role!=2))
      {
        $userDB->Manager=$request->Manager;
      }else
      {
        $userDB->Manager=null;
      }

      $userDB->Username=$request->Username;
      if ($request->Password!=null)
      {
        $userDB->Password=bcrypt($request->Password);
      }
      if ($fileName)
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
    public function determinePosition($request)
    {
      if (($request->Position!=null)&&($request->Role==0))
      {
        return $request->Position;
      }elseif($request->Role==0)
      {
        return 'Manager';
      }elseif($request->Role==1)
      {
        return 'Admin';
      }elseif($request->Role==2)
      {
        return 'General Manager';
      }elseif($request->Role==3)
      {
        return 'Warehouse Assistant';
      }elseif($request->Role==4)
      {
        return 'Warehouse-Head';
      }elseif($request->Role==5)
      {
        return 'Senior Auditor';
      }elseif($request->Role==6)
      {
        return 'Stock clerk';
      }elseif($request->Role==7)
      {
        return 'Budget Officer';
      }elseif($request->Role==8)
      {
        return 'Requisitioner';
      }
    }
    public function saveSignatureImage($request)
    {
      $imageData = $request->get('Signature');
      if($request->Signature)
      {
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        Image::make($request->get('Signature'))->save(public_path('/storage/signatures/').$fileName);
        return $fileName;
      }
    }
    public function handleUpdateUserValidation($request,$id)
    {
      if ($request->Role=='0'||$request->Role=='2')
      {
        $this->validate($request,[
          'FullName'=>'required|max:30',
          'Role'=>'required',
          'Username'=>'required|max:30',
          'Mobile'=>'max:11',
          'Password'=>'confirmed',
          'IsActive'=>'max:1',
        ]);
      }else
      {
        $this->validate($request,[
          'FullName'=>'required|max:30',
          'Role'=>'required',
          'Username'=>'required|max:30',
          'Mobile'=>'max:11',
          'Password'=>'confirmed',
          'Manager'=>'required',
          'IsActive'=>'max:1'
        ]);
      }
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
      $this->handleSaveManagerValidation($request);
      $fileName = $this->uploadSignatureImage($request);
      $userDB=new User;
      $userDB->FullName=$request->FullName;
      $userDB->Username=$request->Username;
      $userDB->Mobile=$request->Mobile;
      $userDB->Role='0';
      $userDB->Position=$request->Position;
      $userDB->Password=bcrypt($request->Password);
      $userDB->Signature=$fileName;
      $userDB->LastOnline=Carbon::now();
      $userDB->save();
    }
    public function uploadSignatureImage($request)
    {
      $imageData = $request->get('Signature');
      $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
      Image::make($request->get('Signature'))->save(public_path('/storage/signatures/').$fileName);
      return $fileName;
    }
    public function handleSaveManagerValidation($request)
    {
      $this->validate($request,[
        'FullName'=>'required|max:25',
        'Username'=>'required|unique:users',
        'Mobile'=>'max:11',
        'Position'=>'required|max:50',
        'Password'=>'required|confirmed',
        'Signature'=>'required',
      ]);
    }
    public function SaveNewUser(Request $request)
    {
      $this->handleValidationNewUser($request);
      $fileName = $this->saveSignatureImage($request);
      $rightPosition = $this->handleNewUserPositionDetermine($request);
      
      $userDB=new User;
      $userDB->FullName=$request->FullName;
      $userDB->Username=$request->Username;
      $userDB->Mobile=$request->Mobile;
      $userDB->Role=$request->Role;
      $userDB->LastOnline=Carbon::now();
      $userDB->Position = 'position';
      if ($request->Role!=2)
      {
        $userDB->Manager=$request->Manager;
      }
      $userDB->Password=bcrypt($request->Password);
      $userDB->Signature=$fileName;
      $userDB->save();
    }
    public function handleNewUserPositionDetermine($request)
    {
      if($request->Role==1)
      {
        return 'Admin';
      }elseif($request->Role==2)
      {
        return 'General Manager';
      }elseif($request->Role==3)
      {
        return 'Warehouse Assistant';
      }elseif($request->Role==4)
      {
        return 'Warehouse-Head';
      }elseif($request->Role==5)
      {
        return 'Senior Auditor';
      }elseif($request->Role==6)
      {
        return 'Stock clerk';
      }elseif($request->Role==7)
      {
        return 'Budget Officer';
      }elseif($request->Role==8)
      {
        return 'Requisitioner';
      }
    }
    public function handleValidationNewUser($request)
    {
      if ($request->Role!=2)
      {
        $this->validate($request,[
          'FullName'=>'required|max:25|unique:users',
          'Username'=>'required|unique:users',
          'Mobile'=>'max:11',
          'Role'=>'required',
          'Manager'=>'required',
          'Password'=>'required|confirmed',
          'Signature'=>'required'
        ]);
      }else
      {
        $this->validate($request,[
          'FullName'=>'required|max:25|unique:users',
          'Username'=>'required',
          'Mobile'=>'max:11',
          'Role'=>'required',
          'Password'=>'required|confirmed',
          'Signature'=>'required'
        ]);
      }
    }
}
