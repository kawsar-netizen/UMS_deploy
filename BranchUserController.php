<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrUserSub;
use App\HdUserSub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchUserController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }


    
    public function index()
    {
      return view('index');
    }


          public function search(Request $request){

        $conditionSql = "";

         $req_id = $request->req_id;
         $branch_code = $request->branch_code;
         $module_name = $request->module_name;
         $request_type_name = $request->request_type_name;
         $status = $request->search_status;
         $request_from = $request->request_from;
         $it_maker_search = $request->it_maker_search;
         $it_checker_search = $request->it_checker_search;


         // start search request id
         if (!empty($req_id)){

          $user_id = Auth::user()->id;
          if(Auth::user()->role == '1' or Auth::user()->role == '5' or ((Auth::user()->role == '9' or Auth::user()->role == '10')  && Auth::user()->division_name !='Internal Control Compliance Division') ){



            
              $req_sql = " and ((r_id.req_id like '%$req_id%' )  and (r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id )) ";

            }elseif(Auth::user()->role == '1' or Auth::user()->role == '5' or ((Auth::user()->role == '9' or Auth::user()->role == '10')  && Auth::user()->division_name =='Internal Control Compliance Division')){

              $req_sql = "and (r_id.req_id like '%$req_id%' )  ";

            }elseif(Auth::user()->role == '2' ){  //ho  maker

              $req_sql = " and (r_id.req_id like '%$req_id%' )   ";
              
            }elseif(Auth::user()->role == '6' ){ //it checker

              $req_sql = " and (r_id.req_id like '%$req_id%' )   ";

            }elseif(Auth::user()->role == '8' ){ // ho authorizer

              $req_sql = " and ((r_id.req_id like '%$req_id%' )  and ((r_id.ho_div_role_status=8 and r_id.br_maker=$user_id)  or  (sys.system_id=6 and r_id.request_type_id=33 and r_id.action_status_br_checker=1)
              or r_id.br_checker_assign_manual_id=$user_id )) ";

            }else{
              $req_sql = " and r_id.req_id LIKE '%$req_id%'  "; 
            }

           
         }else{
          $req_sql ="";
         }

         // start branch sql

         // branch sql 
          if(!empty($branch_code)){

            $user_id = Auth::user()->id;
            if(Auth::user()->role=='11' || Auth::user()->role=='12'){
                $branchSql = " and (r_id.branch_code=$branch_code ) ";

            }elseif(Auth::user()->role=='2' ){  // it maker

              $branchSql = " and  (r_id.branch_code=$branch_code   and  (r_id.action_status_br_checker=1  or r_id.br_maker=$user_id  or r_id.ho_maker=$user_id or r_id.br_checker_assign_manual_id=$user_id ) or (r_id.ho_div_role_status=8 and r_id.action_status_ho_maker=8))  ";

            }elseif(Auth::user()->role=='6' ){  // it Checker

              $branchSql = " and  (r_id.branch_code=$branch_code    and (r_id.action_status_ho_maker='4'  or r_id.br_maker=$user_id  or r_id.br_checker_assign_manual_id=$user_id  ))   ";

            }elseif(Auth::user()->role=='8' ){  // HO Authorizer

              $branchSql = " and  (r_id.branch_code=$branch_code   and ((r_id.ho_div_role_status=8 and r_id.br_maker=$user_id)  or  (sys.system_id=6 and r_id.request_type_id=33 and r_id.action_status_br_checker=1)
              or r_id.br_checker_assign_manual_id=$user_id ))  ";

            }

            

          }else{
            $branchSql = ""; 
            }
            
            // start module 
          if(!empty($module_name)){

              $user_id = Auth::user()->id;
              
              if( Auth::user()->role=='11' || Auth::user()->role=='12'){
                
                $module_sql = " and (rt.system_id ='$module_name') ";

              }elseif(Auth::user()->role=='2'){ // it maker
                
                $module_sql = " and (rt.system_id ='$module_name'  and  (r_id.action_status_br_checker=1  or r_id.br_maker=$user_id  or r_id.ho_maker=$user_id or r_id.br_checker_assign_manual_id=$user_id ) or (r_id.ho_div_role_status=8 and r_id.action_status_ho_maker=8))  ";

              }elseif(Auth::user()->role=='6'){  // it checker
                
                $module_sql = " and (rt.system_id ='$module_name'   and (r_id.action_status_ho_maker='4'  or r_id.br_maker=$user_id  or r_id.br_checker_assign_manual_id=$user_id  ))  ";

              }elseif(Auth::user()->role=='8'){ // ho authorizer
                
                $module_sql = " and (rt.system_id ='$module_name'  and ((r_id.ho_div_role_status=8 and r_id.br_maker=$user_id)  or  (sys.system_id=6 and r_id.request_type_id=33 and r_id.action_status_br_checker=1)
                or r_id.br_checker_assign_manual_id=$user_id ))  ";

              }elseif(Auth::user()->role=='5' || Auth::user()->role=='10'){ //branch checker or ho div checker
               
                $module_sql = " and ((r_id.br_authorizer=$user_id or r_id.br_maker=$user_id) and rt.system_id ='$module_name')  ";
              
              }elseif(Auth::user()->role=='1' || Auth::user()->role=='9'){
               
                $module_sql = " and (r_id.br_maker=$user_id and rt.system_id ='$module_name')  ";
              
              }
          }else{

            $module_sql = "";

          }
          
          

          //request type name
          if(!empty($request_type_name)){

            $user_id = Auth::user()->id;
            
            if(Auth::user()->role=='11' || Auth::user()->role=='12'){ // Super Admin, Admin
                
              $request_type_sql = " and (rt.request_type_name like '%$request_type_name%') ";

            }elseif(Auth::user()->role=='2' ){  // it maker
                
              $request_type_sql = " and (rt.request_type_name like '%$request_type_name%'  and  (r_id.action_status_br_checker=1  or r_id.br_maker=$user_id  or r_id.ho_maker=$user_id or r_id.br_checker_assign_manual_id=$user_id ) or (r_id.ho_div_role_status=8 and r_id.action_status_ho_maker=8))  ";

            }elseif( Auth::user()->role=='6' ){ // it checker
                
              $request_type_sql = " and (rt.request_type_name like '%$request_type_name%' and (r_id.action_status_ho_maker='4'  or r_id.br_maker=$user_id  or r_id.br_checker_assign_manual_id=$user_id  ))   ";

            }elseif( Auth::user()->role=='8' ){ // ho authorizer
                
              $request_type_sql = " and (rt.request_type_name like '%$request_type_name%'  and ((r_id.ho_div_role_status=8 and r_id.br_maker=$user_id)  or  (sys.system_id=6 and r_id.request_type_id=33 and r_id.action_status_br_checker=1)
              or r_id.br_checker_assign_manual_id=$user_id )) ";

            }elseif(Auth::user()->role=='5' || Auth::user()->role=='10'){ //branch checker or ho div checker
             
              $request_type_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id ) and rt.request_type_name like '%$request_type_name%') ";
            
            }elseif(Auth::user()->role=='1' || Auth::user()->role=='9'){
             
              $request_type_sql = " and (r_id.br_maker=$user_id and rt.request_type_name like '%$request_type_name%')  ";
            
            }

          }else{
            $request_type_sql ="";
          }

          
          if(!empty($request_from)){
            
            $user_id = $request_from;
            
            
            if(Auth::user()->role=='11' || Auth::user()->role=='12'){
                $request_from_sql = " and (r_id.br_maker=$user_id ) ";

            }elseif(Auth::user()->role=='2' ){  // it maker

              $request_from_sql = " and ((r_id.br_maker=$user_id )   and  (r_id.action_status_br_checker=1    or r_id.ho_maker=$user_id ) or (r_id.ho_div_role_status=8 and r_id.action_status_ho_maker=8))  ";

            }elseif(Auth::user()->role=='6' ){  // it Checker

              $request_from_sql = " and ((r_id.br_maker=$user_id )   and (r_id.action_status_ho_maker='4'  ) )  ";

            }elseif(Auth::user()->role=='8' ){  // HO Authorizer

              $request_from_sql = " and (r_id.br_maker=$user_id ) ";

            }else{
              $request_from_sql = " and (r_id.br_maker=$user_id ) ";
            }
            
          }else{

            $request_from_sql="";

          }

          
          if(!empty($it_maker_search)){
            
            $user_id = $it_maker_search;
            
            $it_maker_sql = " and (r_id.ho_maker=$user_id ) ";
            
          }else{

            $it_maker_sql="";

          }


          if(!empty($it_checker_search)){
            
            $user_id = $it_checker_search;
            
            $it_checker_sql = " and (r_id.ho_checker=$user_id ) ";
            
          }else{

            $it_checker_sql="";

          }

          //status
          if(!empty($status) ){
          
             $user_id = Auth::user()->id;
            if(Auth::user()->role=='2' || Auth::user()->role=='6' || Auth::user()->role=='8' || Auth::user()->role=='11' || Auth::user()->role=='12'){
                
                if($status=='10'){ // initiate

                  $status_sql = " and (r_id.status<>'7' and r_id.status='0'  and ((r_id.action_status_br_checker='1' or r_id.action_status_br_checker IS  NULL or r_id.action_status_br_checker='') and (r_id.action_status_ho_maker IS NULL or r_id.action_status_ho_maker<>'3' ) )) ";

                }elseif($status=='1'){ // processing

                  $status_sql = " and  (r_id.status<>'7'  and r_id.status='0'   and (r_id.action_status_ho_maker<>'' and r_id.action_status_ho_maker<>8) and ( r_id.action_status_ho_maker='3' or r_id.action_status_ho_maker<>'4')  and (r_id.action_status_ho_checker IS NULL or r_id.action_status_ho_checker=''))";
                  
                }elseif($status=='5'){ // Waiting For Authorization

                  $status_sql = " and  (r_id.status<>'7'  and (r_id.status='0' or r_id.status='2' or r_id.status='3' or r_id.status='4' )  and  (r_id.action_status_br_checker IS NOT NULL and r_id.action_status_ho_maker='4' ) and (r_id.action_status_ho_checker IS NULL or r_id.action_status_ho_checker='') )";

                }elseif($status=='2'){ // Complete

                  $status_sql = " and (r_id.status='2' and r_id.action_status_ho_checker='5' )";

                }elseif($status=='3'){ // On hold

                  $status_sql = " and (r_id.status='3' and r_id.action_status_ho_checker='5') ";

                }elseif($status=='4'){ // cancel

                  $status_sql = " and  (r_id.status='7' and r_id.action_status='7') ";

                }elseif($status=='6'){ // Decline

                  $status_sql = " and r_id.status='6'  ";

                }
              // $status_sql = " and rt.request_type_name like '%$request_type_name%' ";

            }elseif(Auth::user()->role=='5' || Auth::user()->role=='10'){ //branch checker or ho div checker
              
              if($status=='10'){ // initiate

                $status_sql = "  and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id  )  and r_id.status<>'7' and r_id.status='0'  and (r_id.action_status_br_checker='1' or r_id.action_status_br_checker IS  NULL or r_id.action_status_br_checker='') and (r_id.action_status_ho_maker IS NULL  or r_id.action_status_ho_maker<>'3')) ";
                
              }elseif($status=='1'){ // processing
                $branch_code = Auth::user()->branch;

                $status_sql = " and  (r_id.status<>'7'  and r_id.status='0' and  ( r_id.action_status_ho_maker='3' or r_id.action_status_ho_maker<>'4')  and r_id.action_status_ho_checker IS NULL  and r_id.branch_code='$branch_code') ";

              }elseif($status=='5'){ // Waiting For Authorization

                $status_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id ) and  r_id.status<>'7'  and (r_id.status='0' or r_id.status='2' or r_id.status='3' or r_id.status='4' )  and  (r_id.action_status_br_checker IS NOT NULL and r_id.action_status_ho_maker='4' ) and (r_id.action_status_ho_checker IS NULL or r_id.action_status_ho_checker='')) ";

              }elseif($status=='2'){ // Complete

                $status_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id )  and r_id.status='2' and r_id.action_status_ho_checker='5' ) ";

              }elseif($status=='3'){ // On hold

                $status_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id ) and r_id.status='3' and r_id.action_status_ho_checker='5')  ";

              }elseif($status=='4'){ // cancel

                $status_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id )  and  r_id.status='7' and r_id.action_status='7') ";

              }elseif($status=='6'){ // Decline

                $status_sql = " and ((r_id.br_authorizer='$user_id' or r_id.br_maker=$user_id )  and r_id.status='6')  ";

              }

              // $status_sql = " and (r_id.br_maker='$user_id' or r_id.br_checker='$user_id') and rt.request_type_name like '%$request_type_name%' ";
            
            }elseif(Auth::user()->role=='1' || Auth::user()->role=='9'){

              if($status=='10'){ // initiate

                $status_sql = "   and ((r_id.br_maker=$user_id )  and r_id.status<>'7' and r_id.status='0'  and (r_id.action_status_br_checker='1' or r_id.action_status_br_checker IS  NULL or r_id.action_status_br_checker='') and (r_id.action_status_ho_maker IS NULL or r_id.action_status_ho_maker<>'3')) ";

              }elseif($status=='1'){ // processing

                $branch_code = Auth::user()->branch;
                $status_sql = " and  (r_id.status<>'7'  and r_id.status='0'  and  ( r_id.action_status_ho_maker='3' or r_id.action_status_ho_maker<>'4')  and r_id.action_status_ho_checker IS NULL and r_id.branch_code='$branch_code') ";

              }elseif($status=='5'){ // Waiting For Authorization

                $status_sql = " and ((r_id.br_maker=$user_id ) and  r_id.status<>'7'  and (r_id.status='0' or r_id.status='2' or r_id.status='3' or r_id.status='4' )  and  (r_id.action_status_br_checker IS NOT NULL and r_id.action_status_ho_maker='4' ) and (r_id.action_status_ho_checker IS NULL or r_id.action_status_ho_checker='')) ";

              }elseif($status=='2'){ // Complete

                $status_sql = " and ((r_id.br_maker=$user_id )  and r_id.status='2' and r_id.action_status_ho_checker='5')  ";

              }elseif($status=='3'){ // On hold

                $status_sql = " and ((r_id.br_maker=$user_id ) and r_id.status='3' and r_id.action_status_ho_checker='5')  ";

              }elseif($status=='4'){ // cancel

                $status_sql = " and ((r_id.br_maker=$user_id )  and  r_id.status='7' and r_id.action_status='7') ";

              }elseif($status=='6'){ // Decline

                $status_sql = " and ((r_id.br_maker=$user_id )  and r_id.status='6')  ";

              }

              // $status_sql = " and r_id.br_maker='$user_id' and rt.request_type_name like '%$request_type_name%'  ";
            
            }

          }else{
            $status_sql = "";
          }
            
          

          $conditionSql = $req_sql . $branchSql . $module_sql . $request_type_sql . $status_sql .  $request_from_sql . $it_maker_sql . $it_checker_sql;
        

         $requests = DB::table('request_id as r_id')
         ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
         ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
         ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
         ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
         ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

         ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
           'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
            'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
              'r_id.br_checker_recheck_reason','r_id.ho_authorize_status', 'r_id.pk_for_sub_br',
             'branch_code','br_checker','ho_maker', 'ho_checker_comment',
           'ho_checker', 'r_id.entry_date', 'r_id.br_checker_sts_update_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'r_id.ho_maker_remarks', 'u.name as branch_maker_name','r_id.ho_authorizer'])
         ->whereRaw("r_id.sl != '' $conditionSql")
         ->orderBy('r_id.sl', 'desc')
         ->paginate(60);
         
         
         //return $requests;

        
        

        return view('index', [
            'requests'=>$requests
        ]);
       
     
    }

         public function parameter_list_details(Request $request){

         if ($request->ajax()) {

            try {

                $sl =  $request->sl;
                $request_id =  $request->request_id;
                $system_id =  $request->system_id;


                 $request_array = [];



           $requests = DB::select(DB::raw(" SELECT
       r_id.[sl],
       [req_id],
       r_id.[status],
       r_id.[action_status],
       r_id.[action_status_br_checker],
       r_id.[br_checker_assign_manual_id],
       r_id.[br_authorizer],
       r_id.[recheck_status],
       r_id.[ho_chkr_aprove_sts_update_date],
       r_id.[ho_checker_comment],
       r_id.[ho_decliner],
       r_id.[br_checker_sts_update_date],
       [branch_code],
       [br_maker],
       [br_checker],
       [ho_maker],
       [ho_checker],
       r_id.[entry_date],
       r.[sys_id],
       r.[para_id],
       r.[value],
       sys.[para_name],
       sys.[para_type],
       s.[system_name],
        sys.[system_id],

        r_id.[request_type_id],
    rt.[request_type_name],
    rt.[system_id] as rt_system_id,
    r_id.[request_type_value],
    r_id.[created_user_id],
    r_id.[created_password],
    r_id.[ho_maker_remarks],
    r_id.[ho_authorizer],
    r_id.[ho_authorize_status],
    r_id.[canceled_by],
    r_id.[cancel_reason],
    r_id.[rechecker],
    r_id.[br_checker_recheck_reason],
    r_id.[pk_for_sub_br],
    r_id.[pk_for_sub_br_checker],
    r_id.[req_maker_emp_id],
    u.[ip_phone],

        sys.[para_type],
       u.[name] as branch_maker_name, 
       dl.[log_req_maker_role_id] as user_role,
       dl.[log_req_checker_role_id] as user_role_checker,
       dl.[log_division] as division_name,
       u.[division_id],
      
       u.[user_id],
       u.[br_pk_id]

    FROM
       [dbo].[request_id] as r_id 
       left join
          request as r 
          on r.request_id = r_id.req_id 
       left join
          [sys_parameters] as sys 
          on r.para_id = sys.para_id 
       left join
          [systems] as s 
          on s.id = r.sys_id 
       left join
          [users] as u 


          on r_id.br_maker = u.id

        left join 
        [request_type] rt

        on r_id.request_type_id = rt.id

        left join designation_log dl
        on dl.log_reguest_id=r_id.req_id

         where r_id.sl='$sl' and r_id.req_id='$request_id' and  rt.[system_id]='$system_id'

            "));

         

      foreach($requests as $request){

        $request_array[$request->req_id] = [

          "request_id" => $request->sl,
          "req_id" => $request->req_id,
          "para_id" => $request->para_id,
          "br_maker" => $request->br_maker,
           "entry_date" => $request->entry_date,
          "request_type_system_id" => $request->rt_system_id,

          "system_id" => $request->sys_id,
          "request_type_id" => $request->request_type_id,
          "ho_checker_comment" => $request->ho_checker_comment,
          "ho_decliner" => $request->ho_decliner,
          "br_checker_sts_update_date" => $request->br_checker_sts_update_date,
          
         
          "status" => $request->status,
          "action_status" => $request->action_status,
          "action_status_br_checker" => $request->action_status_br_checker,
          "br_checker_assign_manual_id" => $request->br_checker_assign_manual_id,
          "br_authorizer" => $request->br_authorizer,
          "rechecker" => $request->rechecker,
          "br_checker_recheck_reason" => $request->br_checker_recheck_reason,
          "br_pk_id" => $request->br_pk_id,
          "pk_for_sub_br" => $request->pk_for_sub_br,
          "pk_for_sub_br_checker" => $request->pk_for_sub_br_checker,

          "ho_authorizer" => $request->ho_authorizer,
          "ho_authorize_status" => $request->ho_authorize_status,

          "user_name" => $request->branch_maker_name,
          "br_maker_domain_id" => $request->user_id,
          "br_checker" => $request->br_checker,
          "recheck_status" => $request->recheck_status,

          "ho_maker" => $request->ho_maker,
          "ho_checker" => $request->ho_checker,
          "branch_code" => $request->branch_code,
          "system_name" => $request->system_name,
          "input_value" => $request->system_name,
          "request_type_name" => $request->request_type_name,
          "request_type_value" => $request->request_type_value,
          "created_user_id" => $request->created_user_id,
          "created_password" => $request->created_password,
          "ho_maker_remarks" => $request->ho_maker_remarks,
          "user_role_id" => $request->user_role,
          "user_role_checker" => $request->user_role_checker,
          "division_id" => $request->division_id,
          "division_name" => $request->division_name,
          "canceled_by" => $request->canceled_by,
          "cancel_reason" => $request->cancel_reason,
          "ip_phone" => $request->ip_phone,
          "req_maker_emp_id" => $request->req_maker_emp_id,

          "ho_chkr_aprove_sts_update_date"=>$request->ho_chkr_aprove_sts_update_date,

          "operation_name" => [],
          "para_list" => [

          ],
          "request_type" => [],
        ];


      }
      
   
      foreach($requests as $request){

        
            array_push($request_array[$request->req_id]["para_list"],array(
                                        $request->para_id,
                                       $request->para_name,
                                        $request->value,
                                       $request->para_type));

              array_push($request_array[$request->req_id]["operation_name"], urldecode($request->para_name));



        
      }
      
      foreach($requests as $request){
        // $request_array[$request->req_id]["final_operation_name"] = implode(",", $request_array[$request->req_id]["operation_name"]);

        $request_array[$request->req_id]["final_operation_name"] = implode(",", $request_array[$request->req_id]["operation_name"]);

       
      }
      
      
            
         //  return $request_array;
      

           
                $view =  view('single_fetch_parameter_list',[
            'requests'=>$request_array
        ])->render();

                return response()->json(['html' => $view]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }


    }


    public function showReqForm()
    {
        
        $role = Auth::user()->role;

        if ($role=='1' || $role=='5') {
          
           $systemList = DB::table('systems')
              ->where('id','!=','1013')
              ->where('sys_status','!=',0)
              ->get();
              
        }else{

         $systemList = DB::table('systems')
              ->where('sys_status','!=',0)
              ->get();
        }

        $request_data = DB::select(DB::raw("SELECT  rt.[id]
      ,rt.[system_id]
      ,rt.[request_type_name]
      ,rt.[status]
      ,rt.[create_date]
      ,rt.[show_input_field]

      ,sys.[system_name]
  FROM [request_type] rt left join systems sys on rt.[system_id] = sys.id"));



        $system_parameters = DB::table('sys_parameters')
             ->join('systems','sys_parameters.system_id','=','systems.id')
             ->where('user_role', $role)
             ->get();
       


         return view('branch_user.new_index_dynamic',[
            'systemList'=>$systemList,
            'system_parameters'=>$system_parameters,
            'request_data'=>$request_data,
            
        ]);




       // elseif(Auth::user()->role == 2)
       // {
       //    return view('head_user.new_index_for_hduser');
       // }

       // elseif(Auth::user()->role == 3)
       // {
       //    //return view('maker.index');
       //    return redirect('/maker');
       // }

       // else
       // {
        
       //  //return view('checker.index');
       //   return redirect('/checker');
       // }


      
    }


    public function backDash()
    {


       
      $role = Auth::user()->role;
      
      $branch_code = Auth::user()->branch;
      // dd($role);

      // start role=1 (branch maker)



      if ($role == 1) 
      {

        $br_maker_user_id = Auth::user()->id;

       $request_array = [];




          $logic = "u.[role]='$role' and u.[branch]='$branch_code' and r_id.br_maker='$br_maker_user_id'";

           $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
                    'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.ho_authorize_status', 'r_id.pk_for_sub_br',
                      'branch_code','br_checker','ho_maker', 'ho_checker_comment',
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'r_id.ho_maker_remarks', 'u.name as branch_maker_name','r_id.ho_authorizer'   ])

                  ->whereRaw($logic)
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);




      
      
      
      
       // return $request_array;
      
      
      
      
        // hasan code end

      
       $user_id = Auth::user()->id;
      // $requests = DB::select(DB::raw("SELECT *, users.name as users_table_name, br_user_subs.id as br_user_subs_id FROM br_user_subs LEFT JOIN users on  br_user_subs.br_authorizer= users.id  WHERE br_user_subs.user_id = '$user_id' order by br_user_subs.id desc"));

         return view('index',[
            'requests'=>$requests
        ]);
      }

      // end role=1 (branch maker)

//-----------------------------------

      //start   role=2 (HO maker)

       elseif(Auth::user()->role == 2)
       {
        
         $request_array = [];

          $checker_user_id = Auth::user()->id;

         $user_id = Auth::user()->id;




              $logic = "(r_id.action_status_br_checker=1  or r_id.br_maker=$user_id  or r_id.ho_maker=$user_id or r_id.br_checker_assign_manual_id=$user_id ) or (r_id.ho_div_role_status=8 and r_id.action_status_ho_maker=8)";

           $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
                    'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.ho_authorize_status', 'r_id.pk_for_sub_br',
                      'branch_code','br_checker','ho_maker', 'ho_checker_comment',
                    'ho_checker', 
                    'r_id.entry_date',
                    'r_id.br_checker_sts_update_date',
                     'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'r_id.ho_maker_remarks', 'u.name as branch_maker_name','r_id.ho_authorizer'   ])

                  ->whereRaw($logic)
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);




      
      
      
        //return $request_array;
      
      
      
      
        // hasan code end

      
       $user_id = Auth::user()->id;
      // $requests = DB::select(DB::raw("SELECT *, users.name as users_table_name, br_user_subs.id as br_user_subs_id FROM br_user_subs LEFT JOIN users on  br_user_subs.br_authorizer= users.id  WHERE br_user_subs.user_id = '$user_id' order by br_user_subs.id desc"));

         return view('index',[
            'requests'=>$requests
        ]);

       }

        //end   role=2 (HO maker)

       //------------------------

       //start   role=8 (HO Authorize)

       elseif(Auth::user()->role == 8 && Auth::user()->division_name=='Operations Division')
       {
        
         $request_array = [];

          $checker_user_id = Auth::user()->id;

         


            $logic = "(r_id.ho_div_role_status=8 and r_id.br_maker=$checker_user_id)  or  (sys.system_id=6 and r_id.request_type_id=33 and r_id.action_status_br_checker=1)
        or r_id.br_checker_assign_manual_id=$checker_user_id";

           $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
                    'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.ho_authorize_status', 'r_id.pk_for_sub_br',
                      'branch_code','br_checker','ho_maker', 'ho_checker_comment',
                    'ho_checker', 
                    'r_id.entry_date',
                    'r_id.br_checker_sts_update_date',
                     'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'u.name as branch_maker_name'   ])

                  ->whereRaw($logic)
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);

                


      
      
      
      //  return $request_array;
      


      
       $user_id = Auth::user()->id;
      

         return view('index',[
            'requests'=>$requests
        ]);

       }

        //end   role=8 (HO Authorizer)


       //start role=9 (HO Div Maker)

       elseif(Auth::user()->role==9){

            $hodm_id = Auth::user()->id;

                     $request_array = [];

          $checker_user_id = Auth::user()->id;

         
          


            $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
                    'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.pk_for_sub_br',
                      'branch_code','br_checker','ho_maker', 'ho_checker_comment',
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'u.name as branch_maker_name'   ])

                  ->where('r_id.ho_div_maker_id',"$hodm_id")
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);


      
      
      
       // return $request_array;
      


      
       $user_id = Auth::user()->id;
      

         return view('index',[
            'requests'=>$requests
        ]);


       }

       //end if role=9 (HO Div Maker)


       //start role = 10 (HO Div Checker)

       elseif(Auth::user()->role == 10){

           

                     $request_array = [];

          $my_user_id = Auth::user()->id;

         
          



             $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker',
                    'r_id.br_maker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.pk_for_sub_br',
                      'branch_code','br_checker','ho_maker', 'ho_checker_comment',
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'u.name as branch_maker_name'   ])

                  ->where('r_id.br_checker_assign_manual_id',"$my_user_id")->orWhere('r_id.br_maker',"$my_user_id")
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);




      
      
       // return $request_array;
      


      
       $user_id = Auth::user()->id;
      

         return view('index',[
            'requests'=>$requests
        ]);

       }

       //end if role = 10 (HO Div Checker)

       elseif(Auth::user()->role == 3)
       {
        // return view('index3');
        $requests = DB::table('br_user_subs')->orderBy('id','DESC')->get();

         return view('index',[
            'requests'=>$requests
        ]);
       }


       //start role=5 (branch Checker)

       elseif(Auth::user()->role == 5)
       {

         $role = Auth::user()->role;
         $branch_code = Auth::user()->branch;

        
        $request_array = [];

          $checker_user_id = Auth::user()->id;

          


          $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker',
                       'r_id.br_checker_recheck_reason','r_id.pk_for_sub_br',
                      'branch_code','br_maker','br_checker','ho_maker', 
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' , 'u.name as branch_maker_name'   ])

                  ->where('r_id.br_authorizer',"$checker_user_id")->orWhere('r_id.br_maker',"$checker_user_id")
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);



      
      
      
        // return $requests;
      
      
      
      
        // hasan code end

      
       $user_id = Auth::user()->id;
      // $requests = DB::select(DB::raw("SELECT *, users.name as users_table_name, br_user_subs.id as br_user_subs_id FROM br_user_subs LEFT JOIN users on  br_user_subs.br_authorizer= users.id  WHERE br_user_subs.user_id = '$user_id' order by br_user_subs.id desc"));

         return view('index',[
            'requests'=>$requests
        ]);


       }  

       //end role=5 (branch checker)

       //-------------------------

       //start role=6 (HO checker)

       elseif(Auth::user()->role == 6)
       {
         $request_array = [];

          $checker_user_id = Auth::user()->id;
          $user_id = Auth::user()->id;

  
      $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')

                  ->select(['r_id.sl','r_id.req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker', 'r_id.recheck_status',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 'r_id.canceled_by', 'r_id.rechecker', 'r_id.br_checker_recheck_reason',
                      'branch_code','br_maker','br_checker','ho_maker', 'ho_checker_comment', 
                    'ho_checker',
                     'r_id.entry_date',
                     'r_id.br_checker_sts_update_date',
                      'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' ,'r_id.pk_for_sub_br','u.name as branch_maker_name'])

                  ->where('r_id.action_status_ho_maker',4)
                  ->orWhere('r_id.br_maker',"$user_id")
                  ->orWhere('r_id.br_checker_assign_manual_id',"$user_id")
                  // ->orWhere('r_id.status','!=',2)
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);
                       
      // return $requests;
    
        // hasan code end

      
       $user_id = Auth::user()->id;
        
      

         return view('index',[
            'requests'=>$requests
        ]);
        
       }


       //--------------

        //end role=6 (HO checker)



        //start role=11 (Super Admin)

       elseif(Auth::user()->role == 11)
       {
         $request_array = [];

          $checker_user_id = Auth::user()->id;
          $user_id = Auth::user()->id;

    



    $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')
                  ->select(['r_id.sl','req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 
                      'branch_code','br_maker','br_checker','ho_maker', 'ho_checker_comment', 
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' ,'r_id.pk_for_sub_br','u.name as branch_maker_name'   ])


                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);


      
       $user_id = Auth::user()->id;
    

         return view('index',[
            'requests'=>$requests
        ]);
        
       }


       //--------------

        //end role=11 (Super Admin)




               //start role=12 ( Admin)

       elseif(Auth::user()->role == 12)
       {
         $request_array = [];

          $checker_user_id = Auth::user()->id;
          $user_id = Auth::user()->id;

    
  


            $requests = DB::table('request_id as r_id')
                  ->leftJoin('request as r', 'r.request_id', '=' , 'r_id.req_id')
                  ->leftJoin('sys_parameters as sys', 'r.para_id', '=' , 'sys.para_id')
                  ->leftJoin('systems as s', 's.id', '=' , 'r.sys_id')
                  ->leftJoin('users as u', 'r_id.br_maker', '=' , 'u.id')
                  ->leftJoin('request_type as rt', 'r_id.request_type_id', '=' , 'rt.id')
                  ->select(['r_id.sl','req_id', 'r_id.status', 'r_id.action_status', 'r_id.action_status_br_checker', 'r_id.action_status_ho_maker', 'r_id.action_status_ho_checker',
                     'r_id.br_checker_assign_manual_id',  'r_id.br_authorizer', 
                      'branch_code','br_maker','br_checker','ho_maker', 'ho_checker_comment', 
                    'ho_checker', 'r_id.entry_date', 'r.sys_id', 'r.para_id', 'r.value', 'sys.para_name', 'sys.para_type', 's.system_name', 'sys.system_id','r_id.request_type_id', 'rt.request_type_name', 'rt.system_id as rt_system_id', 'r_id.request_type_value' ,'r_id.pk_for_sub_br','u.name as branch_maker_name'   ])

                  ->where('r_id.br_authorizer','!=',"''")
                  ->orderBy('r_id.sl', 'desc')
                  ->paginate(60);


    
      
      
        // return $request_array;
      
        // hasan code end

      
       $user_id = Auth::user()->id;
    

         return view('index',[
            'requests'=>$requests
        ]);
        
       }


       //End Admin

       else
       {

      
           
         return view('index4');

       }



    } // end backdash function 


    function system_edit_data(Request $request){

         if ($request->ajax()) {
            try {

                $id =  $request->id;

            

                $single_fetch_data = DB::table('systems')
            ->where('id', $id)
            ->first();

                $view = view('single_fetch_system_data', compact('single_fetch_data'))->render();

                return response()->json(['html' => $view]);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }


    } //end system_edit_data function


    function update_system(Request $request){

        if ($request->ajax()) {
            try {

                $today_date = date('Y-m-d');

                $hidden_id =  $request->hidden_id;
                $system_id =  $request->system_id;
                $system_name =  $request->system_name;
                $status =  $request->status;

                //$single_blog = Blog::find($row_id);
                $single_blog = DB::table('systems')->where('id',$hidden_id)->update(
                  [
                    'system_name'=>$system_name,
                    'sys_id'=>$system_id,
                    'sys_status'=>$status,
                    'entry_by'=>Auth::user()->id,
                    'entry_date'=>$today_date,

                  ]
                );

                

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'This request is not ajax !';
        }

    }// end update system function
    
public function delete_system(Request $request){
      
      $id = $request->id;
      
      DB::table('systems')->where('id',$id)->update([
        "sys_status"=>0
      ]);

    } // end function delete_system

    function system_para_edit_data(Request $request){

        if ($request->ajax()) {
            try {

                $id =  $request->id;

            

                $single_fetch_data = DB::table('sys_parameters')
            ->where('para_id', $id)
            ->first();

                $view = view('single_fetch_system_para_data', compact('single_fetch_data'))->render();

                return response()->json(['html' => $view]);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }

    } //end system_para_edit_data function


    function update_system_parameter(Request $request){

        if ($request->ajax()) {
            try {

                $today_date = date('Y-m-d');

                $hidden_id =  $request->hidden_id;
                $system_name =  $request->system_name;
                $para_type =  $request->para_type;
                $para_name =  $request->para_name;
                $user_role =  $request->user_role;

                //$single_blog = Blog::find($row_id);
                $single_blog = DB::table('sys_parameters')->where('para_id',$hidden_id)->update(
                  [

                    'system_id'=>$system_name,
                    'para_name'=>$para_name,
                    'para_type'=>$para_type,
                    'entry_date'=>$today_date,
                    'user_role'=>$user_role,

                  ]
                );

                

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'This request is not ajax !';
        }

    } // end update_system_parameter function




  public function delete_system_parameter(Request $request){
      $id = $request->id;
      $data = DB::table('sys_parameters')->where('para_id',$id)->update(
        [

          'para_status'=>0,
          
        ]);

    } // end function delete_system_parameter 


    function request_type_hide(Request $request){


            if ($request->ajax()) {
            try {
                $system_id =  $request->system_id;
               
          $system_data =  DB::select(DB::raw("SELECT  [id]
      
  FROM [request_type] where system_id='$system_id' and status='1' "))[0];
           
         
           return $system_data->id;     

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'This request is not ajax !';
        }

    }



        function request_type_parameter_show_hide(Request $request){

         if ($request->ajax()) {
            try {

               

                $id =  $request->id;
                $system_id =  $request->system_id;
                // $requestTypeName =  $request->requestTypeName;
               
          $system_data =  DB::select(DB::raw("SELECT [id],[system_id],[show_input_field]
      
  FROM [request_type] where id='$id' "))[0];
           
         $id = $system_data->id;
         $system_id = $system_data->system_id;
         $show_status = $system_data->show_input_field;

         $system_data2 =  DB::select(DB::raw("SELECT [id],[system_id],[show_input_field]
      
  FROM [request_type] where system_id='$system_id'  and show_input_field='1'  "))[0];

         // 
         $input_id_val=$system_data2->id;
      //print "$input_id_val--$id";
        if($input_id_val!=$id )
        {
           $show_input=0;
           $input_id=$input_id_val;
        }
        else
        {
             $show_input=1;
              $input_id=NULL;
        }
        $arr = array('id' => "$id", 'system_id' =>"$system_id", 'show_status' =>"$show_status",'input_show'=>$show_input,"input_id"=>$input_id);
       
       //echo "<pre>";
       //print_r($arr);
       //die;

            return json_encode($arr);    

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            echo 'This request is not ajax !';
        }

    }


    // transfer

     public function system_user_id_map(Request $request){
    
       $system_data = DB::select(DB::raw("SELECT *
     FROM [systems] where id in(1,1002,1004,1005,1009,1010,1012,1013,1014,1015,1021)"));


        $my_user_id =  Auth::user()->id;
        $system_get_data = DB::select(DB::raw("SELECT su.id, sys.system_name,su.sys_user_id, su.entry_date from system_user_id su left join systems sys on su.sys_id=sys.id  where [user]='$my_user_id'"));
                                              
      return view('user_security.system_user_id_map', compact('system_data','system_get_data'));
    }

      // Coding start by Kawsar Khan

     public function system_user_id_map_new(Request $request){
  
    $system_data = DB::table('system_domain')->where('domain_status','Non-domain')->get();

        $my_user_id =  Auth::user()->id;
        $system_get_data = DB::select(DB::raw("SELECT su.id, sys.system_name,su.sys_user_id, su.entry_date from system_user_id su left join systems sys on su.sys_id=sys.id  where [user]='$my_user_id'"));
                                              
      return view('user_security.system_user_id_map', compact('system_data','system_get_data'));
    }
    
     // Coding end by Kawsar Khan

    //


    public function system_user_id_map_insert(Request $request){
      $system_id = $request->system_id;
      $system_user_id = $request->system_user_id;
      $user_id = Auth::user()->id;

     $get_system_user_id_count = DB::table('system_user_id')->where('sys_id',$system_id)->where('user',$user_id)->count();

      if($get_system_user_id_count>0){
        echo '0';
      }else{

           $data = DB::table('system_user_id')->insert([

          'sys_id'=>$system_id,
          'sys_user_id'=>$system_user_id,
          'user'=>Auth::user()->id,
          'entry_date'=>date('Y-m-d')
        ]);

          echo '1'; 
      }



      
    } // system_user_id_map_insert


    public function system_user_id_edit_data(Request $request){

        if ($request->ajax()) {


            try {

                $id =  $request->id;
                
               $get_data = DB::table('system_user_id')->where('id', $id)->first();

             
            
               $view = view('single_page_fetch_system_user_id_edit_data', compact('get_data'))->render();

                return response()->json(['html' => $view]);
                

            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }

    }


    public function update_system_user_id(Request $request){

        if ($request->ajax()) {


            try {

                $id =  $request->hidden_id;
                $edit_system_user_id =  $request->edit_system_user_id;
                
               $update_data = DB::table('system_user_id')->where('id', $id)->update(['sys_user_id'=>$edit_system_user_id]);

             if ($update_data) {
               echo "1";
             }
            
                
                

            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }

    }



    public function system_user_id_delete_data(Request $request){

          if ($request->ajax()) {


            try {

                $id =  $request->id;
               
                
               $delete_data = DB::table('system_user_id')->where('id', $id)->delete();

             if ($delete_data) {
               echo "1";
             }
            
                
                

            } catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }

    }


//get system user id value

     public function get_sys_user_id_val(Request $request){

          if ($request->ajax()) {


            try {

                $id =  $request->system_id;
               $user=Auth::user()->id;
                
               $data = DB::table('system_user_id')->where('sys_id', $id)->where('user', $user)->first();

             if ($data) {
               print $data->sys_user_id;
             }
             else
             {
              print "";
             }
            
                
                

            } 
            catch (\Exception $e) {
                echo $e->getMessage();
            }


        } else {
            echo 'This request is not ajax !';
        }

    }




    public function branch_cheker_usr_list(){

       return view('user_security.branch_checker_req_list');
    }


    public function operations_special_role(Request $request){

        return view('user_security.operations_special_role');
    }


}
