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
if(count($user_get_data)>0){
    return view('ubs_unlock.ubs_index', ['name' => $user_get_data[0]->sys_user_id]);
}else{
    return view('ubs_unlock.ubs_index', ['name' => ""]);
}
   
  }

  public function ubsUnlock_Store(Request $request)
  {
    $req_name = $request->req_name;
    $currentTimestamp = date('Y-m-d H:i:s');
    $user_id = Auth::user()->id;
    $branch_code = Auth::user()->branch;

    $data_get = DB::table('ubs_unlock_request')->where('req_name', '=', $req_name)->where('status', '=', 0)->get();
    if ($data_get->count() > 0) {
      return redirect()->back()->with('message', 'Unlock Request Already Exists!!');
    } else {
      $ok =  DB::table('ubs_unlock_request')->insert([
        'req_name' => $request->req_name,
        'status'   => '0',
        'br_code' => $branch_code,
        'maker_user_id' => $user_id,
        'entry_date' => $currentTimestamp,
      ]);

      if ($ok == true) {
        return redirect()->back()->with('message', 'Unlock Request Sent successfully!!');
      } else {
        return redirect()->back()->with('message', 'Something went wrong. Please try letter!!');
      }
    }
  }

  public function authorizeList()
  {
    $usr_primary_id = Auth::user()->id; //incremental id of user table
    $usr_branch= Auth::user()->branch;
    $usr_role= Auth::user()->role;
    $authorize_get_data = DB::table('ubs_unlock_request')->where('status',0)->where('maker_user_id','!=',$usr_primary_id)->where('br_code','=',$usr_branch)->get();

    return view('ubs_unlock.authorize_list', compact('authorize_get_data'));
  }

  public function changeStatus_authorize($id)
  {
    $user_id = DB::table('ubs_unlock_request')->where('id', $id)->first();
    $userId = $user_id->req_name;
    $responseData = $this->DoLogOutUser($userId);
    $currentTimestamp = date('Y-m-d H:i:s');
    $authUserId = Auth::user()->id;

    DB::table('ubs_unlock_api_log')->insert([
      'date_time' => $currentTimestamp,
      'request_user_id' => $responseData['user_id'],
      "message"    =>  $responseData['message'],
      'description' => $responseData['description'],
      'request' => $responseData['request'],
      'response' => $responseData['response'],
      'request_by' => $authUserId,
    ]);

    if($responseData['is_success'] == 'success') {
        DB::update(DB::RAW('UPDATE ubs_unlock_request SET status = 1 WHERE id = ' . $id));
        return redirect()->back()->with('authorize',$responseData['description']);
    } else {
        return redirect()->back()->with('authorize',$responseData['description']);
    }

  }


  //Pass data for api
  public function DoLogOutUser($userId){
      $inquiry_url="https://uatapi.dhakabank.com.bd/DblUserServices/FubsUserServices.asmx?WSDL";
          $curl = curl_init();
  
          $xml_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dhak="http://dhakabank.com.bd">
     <soapenv:Header/>
     <soapenv:Body>
        <dhak:DoLogOutUser>
           <!--Optional:-->
           <dhak:userId>'.$userId.'</dhak:userId>
        </dhak:DoLogOutUser>
     </soapenv:Body>
  </soapenv:Envelope>';
          curl_setopt_array($curl, array(
            CURLOPT_URL => $inquiry_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $xml_body,
            CURLOPT_HTTPHEADER => array(
              'Content-Type: text/xml',
              "SOAPAction: http://dhakabank.com.bd/DoLogOutUser"
            )
          ));
          $response     = curl_exec($curl);
          curl_close($curl);
          
          $xml           = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
          $xml           = simplexml_load_string($xml);
          $json          = json_encode($xml);
          $responseArray = json_decode($json,true);
          $sBody  = $responseArray['soapBody'];
          $message  = $sBody['DoLogOutUserResponse']['DoLogOutUserResult']['Message'];
          
          if(strtoupper($message)=="FAILURE")
          {
              $result_array = array(
                "is_success" => "failed",
                "user_id"    => $sBody['DoLogOutUserResponse']['DoLogOutUserResult']['UserId'],
                "description"  => $sBody['DoLogOutUserResponse']['DoLogOutUserResult']['Description'],
                "message"    => $message,
                "response"   => $response,
                "request"    => json_encode($xml_body)
              );
          }
          else
          {
              $result_array = array(
                "is_success" => "success",
                "user_id"    => $sBody['DoLogOutUserResponse']['DoLogOutUserResult']['UserId'],
                "description"  => $sBody['DoLogOutUserResponse']['DoLogOutUserResult']['Description'],
                "message"    => $message,
                "response"   => $response,
                "request"    => json_encode($xml_body)
              );
          }
          return $result_array;



        }
  public function changeStatus_decline($id)
  {

    DB::update(DB::RAW('UPDATE ubs_unlock_request SET status = 2 WHERE id = ' . $id));
    return redirect()->back()->with('decline','Status Declined Successfully!!');
  }
}
