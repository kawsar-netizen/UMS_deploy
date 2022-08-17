<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use  Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('m', function(){
	$data = [
		'name' => 'hasan'
	];

	try{
		Mail::send(['text' => 'mail'], $data, function($message){
			$message->to('halimkhanfeni7@gmail.com', '=VSL')->subject('HH');
			// $message->to('ataur.rahman@dhakabank.com.bd', '=VSL')->subject('Check');
			$message->from('user.id@dhakabank.com.bd', 'DBL');
		});
		return "done";
	}catch(\Exception $e){
		return $e->getMessage();
	}

});*/


Route::get('make', function(){

	echo Hash::make("Dbl#2029");

});

// Route::get('ps', function(){

// 	if (Hash::check("Ums@1234", '$2y$10$mb5AwjVhavbEG1PrjPfGs.ERXFRdf3JZCE8S28igAmzfDX3ipkwJG')) {
            
//             return 'success';
//        }

// });

Route::get('/', function () {
	if (Auth::check()) {
		
		 return back();

	}else{
		 return view('auth.login');
	}
  
});

Route::get('index', function () {
	
    return view('index');
});


// Route::get('db', function(){
// 	$data = DB::table('users')->get();

// 	echo "<pre>";

// 	print_r($data);
// });

Auth::routes(['register'=>false]);





//for 404
Route::get('/checkUser','HomeController@checkUser')->name('check');

//password reset
Route::get('/resetPassword', 'HomeController@resetPassword')->name('reset-password');
Route::post('/resetPasswordSuccess', 'HomeController@saveResetPassword')->name('save-reset-password');


Route::get('/dashboard1', 'HomeController@dashboard')->name('branchUser')->middleware('branchUser');

Route::get('/dashboard2', 'HomeController@dashboard')->name('headUser')->middleware('headUser');

Route::get('/dashboard5', 'HomeController@dashboard')->name('branchChecker')->middleware('branchChecker');

Route::get('/dashboard6', 'HomeController@dashboard')->name('headChecker')->middleware('headChecker');

Route::get('/dashboard8', 'HomeController@dashboard')->name('headAuth')->middleware('headAuth');


Route::get('/dashboard9', 'HomeController@dashboard')->name('ho_div_maker')->middleware('ho_div_maker');

Route::get('/dashboard10', 'HomeController@dashboard')->name('ho_div_checker')->middleware('ho_div_checker');

Route::get('/dashboard11', 'HomeController@dashboard')->name('superadmin')->middleware('superadmin');
Route::get('/dashboard12', 'HomeController@dashboard')->name('admin')->middleware('admin');




//back to user dashboard
Route::get('/dashboard', 'HomeController@dash')->name('dash');


Route::get('/user_request_form', 'BranchUserController@showReqForm')->name('bruser-index')->middleware('without_admin_superadmin');
Route::post('request_type_hide', 'BranchUserController@request_type_hide');
Route::post('request_type_parameter_show_hide', 'BranchUserController@request_type_parameter_show_hide');




//save data from branch user request submission
Route::post('/save', 'BrUserSubController@saveData')->name('br-user');


//ajax try
Route::post('/saveSubmissionData', 'BrUserSubController@ajaxTry')->name('my-data');





############################ Designation ##############################################
Route::get('designation', 'DesignationController@index')->middleware('operations-special-role');
Route::post('/designation_edit_data', 'DesignationController@designation_edit_data');
Route::post('/update_designation_title', 'DesignationController@update_designation_title');
Route::post('/degingation_submit', 'DesignationController@degingation_submit');
############################ Designation ##############################################


############################ Division ##############################################
Route::get('division', 'DivisionController@index')->middleware('operations-special-role');
Route::post('/division_edit_data', 'DivisionController@division_edit_data');
Route::post('/update_division_title', 'DivisionController@update_division_title');
Route::post('/division_submit', 'DivisionController@division_submit');
############################ Division ##############################################




############################ Branch ##############################################
Route::get('add-branch', 'BranchController@index')->middleware('operations-special-role');
Route::get('/branch_edit/{id}', 'BranchController@branch_edit')->middleware('operations-special-role');

Route::post('/branch_update', 'BranchController@branch_update');
Route::post('/sub_branch_update', 'BranchController@sub_branch_update');
Route::post('/branch_submit', 'BranchController@branch_submit');
Route::post('/sub_branch_submit', 'BranchController@sub_branch_submit');
############################ Branch ##############################################



//view head user dashboard and save data from head office user request submission


//back to user dashboard
Route::get('/user-request-list', 'BranchUserController@backDash')->name('back-to-br-user');


Route::get('/search-table-info', 'BranchUserController@search')->name('search');



// Route::get('/home', 'HomeController@index')->name('home');

Route::post('user_authorize', 'BrUserSubController@user_authorize')->name('user_authorize');

Route::post('assign_person', 'BrUserSubController@assign_person');


Route::post('branch_checker_authorize', 'BranchCheckerController@branch_checker_authorize');
Route::post('branch_checker_authorize_all', 'BranchCheckerController@branch_checker_authorize_all');



Route::post('branch_checker_decline', 'BranchCheckerController@branch_checker_decline');
Route::post('branch_checker_decline_all', 'BranchCheckerController@branch_checker_decline_all');



Route::post('cancel_reason_submit', 'BrUserSubController@cancel_reason_submit');

Route::post('ho_maker_accept', 'HdUserSubController@ho_maker_accept');
Route::post('ho_maker_change_status', 'HdUserSubController@ho_maker_change_status');

Route::post('assign_person_url', 'HdUserSubController@assign_person_url');
Route::post('my_branch_assgin_person', 'HdUserSubController@my_branch_assgin_person');
Route::post('ho_authorize_submit', 'HdUserSubController@ho_authorize_submit');
Route::post('ho_authorize_decline_submit', 'HdUserSubController@ho_authorize_decline_submit');


Route::post('ho_release_submit', 'HdUserSubController@ho_release_submit');
Route::post('ho_release_authorize', 'BranchCheckerController@ho_release_authorize');


Route::post('ho_maker_change_status_submit', 'HdUserSubController@ho_maker_change_status_submit')->name('ho_maker_change_status_submit');

Route::post('ho_checker_approved', 'HeadCheckerController@ho_checker_approved');
Route::post('it_checker_approve_all', 'HeadCheckerController@it_checker_approve_all');



Route::post('ho_checker_decline', 'HeadCheckerController@ho_checker_decline');
Route::post('it_checker_decline_all', 'HeadCheckerController@it_checker_decline_all');

Route::post('branch_maker_edit', 'BrUserSubController@branch_maker_edit');

Route::post('ho_chkr_decline_comment_submit', 'HeadCheckerController@ho_chkr_decline_comment_submit');



Route::get('system_form', 'parameterSetupController@system_form')->middleware('system_param_req_auth');
Route::post('system_submit', 'parameterSetupController@system_submit');

Route::get('system_parameter', 'parameterSetupController@system_parameter')->middleware('system_param_req_auth');
Route::post('system_parameter_submit', 'parameterSetupController@system_parameter_submit');



// delete system parameter
Route::post('/delete_system_parameter', 'BranchUserController@delete_system_parameter');

Route::get('/request_type', 'parameterSetupController@request_type')->middleware('system_param_req_auth');
Route::post('/request_type_edit_data', 'parameterSetupController@request_type_edit_data');
Route::post('/update_request_type', 'parameterSetupController@update_request_type');



Route::post('/request_type_submit', 'parameterSetupController@request_type_submit');
Route::get('/approve_system', 'parameterSetupController@approve_system');


// user and security, menu and others
Route::get('/menu_add', 'UserSecurityController@menu_add');
Route::post('/menu_add_data_submit', 'UserSecurityController@menu_add_data_submit');

Route::post('/existing_role_edit', 'UserSecurityController@existing_role_edit');

Route::get('/create_a_new_user', 'UserSecurityController@create_a_new_user');
Route::post('/new_user_data_submit', 'UserSecurityController@new_user_data_submit');
Route::post('/user_edit_data', 'UserSecurityController@user_edit_data');



Route::get('/edit_profile', 'UserSecurityController@edit_profile')->middleware('without_admin_superadmin');
Route::post('/find-sub-branch', 'UserSecurityController@findSubBranch');
Route::post('/update_profile', 'UserSecurityController@update_profile');


//parameter list details ajax
Route::post('/parameter_list_details', 'BranchUserController@parameter_list_details');

Route::post('/system_edit_data', 'BranchUserController@system_edit_data');
Route::post('/update_system', 'BranchUserController@update_system');

//system delete route here.... by kawsar
Route::post('/delete_system', 'BranchUserController@delete_system');

Route::post('/system_para_edit_data', 'BranchUserController@system_para_edit_data');
Route::post('/update_system_parameter', 'BranchUserController@update_system_parameter');

//System domain route here by Kawsar(start)

Route::get('system_domain','SystemDomainController@system_domain')->name('systemDomain');
Route::post('system_domain','SystemDomainController@system_domain_store')->name('systemDomainStore');
Route::get('system_domain/edit/{id}','SystemDomainController@system_domainedit')->name('systemDomainedit');
Route::post('system_domian/update/{id}','SystemDomainController@system_domianupdate')->name('systemDomainupdate');

// UBS user unlock route
Route::get('ubs','UBSunlockController@ubsUnlock')->name('UBSunlock');
Route::post('ubs','UBSunlockController@ubsUnlock_Store')->name('UBSunlockStore');

Route::get('authorize','UBSunlockController@authorizeList')->name('AuthorizeList');

//System domain route here by Kawsar(end)


//Self Registration Route

Route::get('/selfreg', 'SelfRegController@selfreg');
Route::post('/find-user-role', 'SelfRegController@find_user_role');


Route::post('self_reg_submit', 'SelfRegController@self_reg_submit');




Route::get('branch_cheker_usr_list', 'BranchUserController@branch_cheker_usr_list')->middleware('requested_role_auth');

Route::get('operations-special-role', 'BranchUserController@operations_special_role')->middleware('operations-special-role');

Route::post('rtgs_special_role_approved', 'SelfRegController@rtgs_special_role_approved');
Route::post('special_role_decline', 'SelfRegController@special_role_decline');


Route::get('system-user-id-map', 'BranchUserController@system_user_id_map');
Route::post('system_user_id_map_insert', 'BranchUserController@system_user_id_map_insert');
Route::post('system_user_id_edit_data', 'BranchUserController@system_user_id_edit_data');
Route::post('update_system_user_id', 'BranchUserController@update_system_user_id');
Route::post('system_user_id_delete_data', 'BranchUserController@system_user_id_delete_data');
Route::post('get_sys_user_id_val', 'BranchUserController@get_sys_user_id_val');

// Coding start by kawsar

Route::get('system-user-id-map-new', 'BranchUserController@system_user_id_map_new')->name('system-user-id-map-new');
Route::post('system_user_id_map_insert_new', 'BranchUserController@system_user_id_map_insert_new')->name('system-user-id-map-insert-new');

// Coding end by kawsar


Route::post('sub_branch_show', 'SelfRegController@sub_branch_show');


Route::post('branch_checker_request_list_approved', 'SelfRegController@branch_checker_request_list_approved');

Route::post('branch_checker_request_list_decline', 'SelfRegController@branch_checker_request_list_decline');


Route::post('ho_div_checker_authorize', 'HODIVController@ho_div_checker_authorize');


Route::get('audit_sheet_form', 'AuditSheetController@audit_sheet_form')->middleware('check_audit_auth_user');
Route::post('audit_sheet_form_submit', 'AuditSheetController@audit_sheet_form_submit');
Route::get('audit_sheet', 'AuditSheetController@audit_sheet')->middleware('check_audit_auth_user');

//find branch code
Route::post('get_branch_code', 'AuditSheetController@getBranchCode');
Route::post('get_sub_branch', 'AuditSheetController@get_sub_branch');

Route::post('authorize_audit_sheet', 'AuditSheetController@authorize_audit_sheet');
Route::post('delete_audit_sheet', 'AuditSheetController@delete_audit_sheet');

// dynamic pdf
Route::get('/dynamic_pdf', 'DynamicPDFController@index');

Route::get('/dynamic_pdf/pdf/{id}', 'DynamicPDFController@pdf');

//mail integration

Route::get('/email', 'EmailController@create');
Route::post('/email', 'EmailController@sendEmail')->name('send.email');

Route::post('get_data_from_ad', 'SelfRegController@get_data_from_ad');


Route::post('search-with-system-and-status', 'TableFilter@get_data_with_system_and_status')->name('search_with_system_and_status');

// report

Route::get('date_wise_report', 'ReportController@date_wise_report');

Route::post('date_wise_report_data_table', 'ReportController@date_wise_report_data_table');

Route::get('user_wise_report', 'ReportController@user_wise_report');
Route::get('user_report_data', 'ReportController@user_report_data');

Route::get('activities_report', 'ReportController@activities_report');


Route::get('activities_report_data_table', 'ReportController@activities_report_data_table');


Route::get('system_wise_report', 'ReportController@system_wise_report');
Route::get('system_wise_report_data_table', 'ReportController@system_wise_report_data_table');

Route::get('status_wise_report', 'ReportController@status_wise_report');
Route::post('status_wise_report_data_table', 'ReportController@status_wise_report_data_table');



Route::get('just_user_wise_report', 'ReportController@just_user_wise_report');



Route::get('request_id', 'ReportController@request_id');

Route::get('request_report_data_table', 'ReportController@request_report_data_table');

Route::get('user_audit_log_report', 'ReportController@user_audit_log_report');

Route::get('single_user_report', 'ReportController@single_user_report')->middleware('single_user_report_auth');

Route::get('single_user_report_get_data', 'ReportController@single_user_report_get_data')->middleware('single_user_report_auth');


Route::get('audit_sheet_report', 'ReportController@audit_sheet_report')->middleware('single_user_report_auth');
Route::get('audit_sheet_report_data', 'ReportController@audit_sheet_report_data')->middleware('single_user_report_auth');




