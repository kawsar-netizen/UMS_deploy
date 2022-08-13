<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrUserSub;
use App\HdUserSub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemDomainController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }


  //System user map function here by Kawsar

  public function system_domain()
  {
    $systemDomainGet = DB::table('system_domain')->get();
    $systemData = DB::table('systems')->get();
    return view('system_domain.index', compact('systemDomainGet','systemData'));
  }

  public function system_domain_store(Request $request)
  {
    $request->validate([
      'system_name' => 'required',
      'domain_status' => 'required',
    ], [
      'system_name.required' => 'Please enter system name',
      'domain_status.required' => 'Please enter domain status',
     
    ]);

    // $user_id = Auth::user()->id;
    // $currentTimestamp = date('Y-m-d H:i:s');
    // DB::table('systems')->insert([
    //   'system_name' => $request->system_name,
    //   'sys_id' => $request->sys_id,
    //   'entry_by' => $user_id,
    //   'sys_status' => 0,
    //   'entry_date' => $currentTimestamp
    // ]);
    // return redirect()->back();

    DB::table('system_domain')->insert([
      'system_name' => $request->system_name,
      'domain_status' => $request->domain_status,
    ]);
    return redirect()->back();
  }

  public function system_domainedit($id)
  {
    $systemDomainEdit = DB::table('system_domain')->find($id);

    $systemData = DB::table('systems')->get();

    return view('system_domain.edit', compact('systemDomainEdit','systemData'));
  }

  public function system_domianupdate(Request $request, $id)
  {
    $request->validate([
      'system_name' => 'required',
      'domain_status' => 'required',
    ]);
    $system_id = (int) $id;
    DB::table('system_domain')->where('id', $system_id)->update([
      'system_name' => $request->system_name,
      'domain_status' => $request->domain_status,
    ]);
    return redirect()->route('systemDomain');
  }
}
