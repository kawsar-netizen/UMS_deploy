<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrUserSub;
use App\HdUserSub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UBSunlockController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  //UBS function here......

  public function ubsUnlock()
  {
    $user_id = Auth::user()->id;
    $user_get_data = DB::select("select sys_user_id from system_user_id inner join users on users.id=system_user_id.[user] where sys_id='1' and users.id=$user_id");

    return view('ubs_unlock.ubs_index', ['name' => $user_get_data[0]->sys_user_id]);
  }

  public function ubsUnlock_Store(Request $request)
  {
    $req_name = $request->req_name;
    $currentTimestamp = date('Y-m-d H:i:s');
    $user_id = Auth::user()->id;
    $branch_code = Auth::user()->branch;

    $data_get = DB::table('ubs_unlock_request')->where('req_name', '=', $req_name)->where('status', '=', 0)->get();
    if($data_get->count() > 0) {
      return redirect()->back()->with('message','Unlock Request Already Exists!!');
    } else {
      $ok =  DB::table('ubs_unlock_request')->insert([
        'req_name' => $request->req_name,
        'status'   => '0',
        'br_code' =>$branch_code,
        'maker_user_id' => $user_id,
        'entry_date' => $currentTimestamp,
      ]);
  
      if($ok == true){
        return redirect()->back()->with('message','Unlock Request Sent successfully!!');
      }
      else{
        return redirect()->back()->with('message','Something went wrong. Please try letter!!');
      }
    }
  }
  public function authorizeList(){
    $authorize_get_data = DB::table('ubs_unlock_request')->get();
   return view('ubs_unlock.authorize_list',compact('authorize_get_data'));
  }
}
