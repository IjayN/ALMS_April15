<?php

namespace App\Http\Controllers;

use App\Department;
use App\ForcedLeave;
use App\LeaveDays;
use App\Reason;
use App\Type;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\LeaveApplication;
use App\Visitors;
use DB;
use Auth;
use Avatar;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use App\Mail\SendMailable;
use Mail;
use Illuminate\Support\Facades\Hash;


/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $leaveDays;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sidebar()
    {
        return view('sidebar');
    }
    public function navbar()
    {
        return view('navbar');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employees()
    {
        $employee = User::all();
        return view('employees', compact('employee'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add_member()
    {
        $employee = User::all();
        $type = Type::all();
        $department = Department::all();
        return view('member_signup', compact('employee','type','department'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage_members()
    {
        $employee = User::all();
        return view('manage_members', compact('employee'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function member_edit($id)
    {
        $employee = User::where('id', $id)->first();
        $type = Type::all();
        $department = Department::all();
        return view('member_edit', compact('employee','type','department'));
    }

    public function edit_dept($id)
    {
        $employee = User::where('id', $id)->get();
        $type = Type::all();
        $department = Department::all();
        return view('edit_dept', compact('employee','type','department'));


    }
    public function edit_department(Request $request, $id)
    {

        User::where('id', $id)->update([

          'department_id' => $request->department_id,
            'type_id' => $request->type_id

        ]);

        return redirect()->back();
    }




    public function pw_reset($id)
    {
        $row = DB::table('users')
            ->where('id',$id)
            ->update([
                'active' => 0,
                'password' => Hash::make('123456')
            ]);

          // $row = DB::table('users')->updateOrInsert(['active'=>0],['password'=>Hash::make('123456')])->where('id',$id);

          // return redirect()->intended('manage_members');
          // return Redirect::back()->with('msg', 'The Message');
            return redirect()->back()->with('success', ['Password Reset successfully']);

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit_member(Request $request, $id)
    {

        User::where('id', $id)->update([
            'employee_no'=>$request->employee_no,
            'name' => $request->name,
            'email' => $request->email,
            'nat_id' => $request->nat_id,
            'phone_no'=>$request->phone_no,
            'NSSF' => $request->NSSF,
            'NHIF' => $request->NHIF,
            'KRA_Pin' => $request->KRA_Pin,
            'category'=> $request->category
        ]);

        return redirect()->route('manage-members');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_member($id){
        $member = User::find($id)->delete();
        return redirect()->back();
    }

    public function pendingRequests()
    {
        $userType = auth()->user()->type->name;
        if ($userType == 'PM'){
            $department_id = auth()->user()->department->id;
            $leave = LeaveApplication::where('department_id',$department_id)->where('releiver_approval','=','approved')->where('PM','=','pending')->orderBy('created_at','desc')->get();
            return view('pending_request',compact('leave'));
        }elseif ($userType == 'HOD'){
            $department_id = auth()->user()->department->id;
            $leave = LeaveApplication::where('department_id',$department_id)->where('releiver_approval','=','approved')->where('PM','=','approved')->where('HOD','=','pending')->orderBy('created_at','desc')->get();
            return view('pending_request',compact('leave'));
        } elseif ($userType == 'HR') {
            $leave = LeaveApplication::where('HOD','!=','pending')->where('HR','=','pending')->orderBy('created_at','desc')->get();
            return view('pending_request',compact('leave'));
        } if ( $userType == 'MD') {
        $leave = LeaveApplication::where('HR','!=','pending')->where('MD','=','pending')->where('usertype_id','=',2)->where('usertype_id','=',3)->orderBy('created_at','desc')->get();
        return view('pending_request',compact('leave'));
    } else {
        $leave =  LeaveApplication::where('reliever', auth()->user()->id)->orderBy('id', 'desc')->get();
        return view('pending_request',compact('leave'));
    }
    }

    public function pendingRequestDetails($id)
    {
        $leave = LeaveApplication::find($id);
        $reliever_id = $leave->reliever;
        $reliever = User::where('id',$reliever_id)->first();
        $leaveDays = LeaveDays::where('user_id',$leave->user_id)->first();
        $reason = Reason::find($id);
        return view('leave_details',compact('leave','reliever','leaveDays','reason'));
    }
    public function pendingReliverDetails($id)
    {
        $leave = LeaveApplication::find($id);
        $reliever_id = $leave->reliever;
        $reliever = User::where('id',$reliever_id)->first();
        $leaveDays = LeaveDays::where('user_id',$leave->user_id)->first();
        $reason = Reason::find($id);
        return view('reliever_details',compact('leave','reliever','leaveDays','reason'));
    }

    public function relieverRequest()
    {
        $leave =  LeaveApplication::where('reliever', auth()->user()->id)->where('releiver_approval','=','pending')->orderBy('id', 'desc')->get();

        return view('reliever_request',compact('leave'));

    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function communication()
    {
        $employee = User::all();
        return view('communication', compact('employee'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function apply_leave()
    {
        $reliever =  LeaveApplication::where('reliever', auth()->user()->id)->orderBy('id', 'desc')->get();

        $user = auth()->user();
        $employee = User::where('department_id', $user->department_id)->where('id', '!=', $user->id)
            ->select(['name', 'id'])->get();
        return view('applyleave', compact('employee'));

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function leave_history()
    {
        $leave = LeaveApplication::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        return view('leave_history', compact('leave'));

    }

    public function leaveReqeust()
    {
        $usertype = auth()->user()->type->name;
        if($usertype == 'PM') {
            $department_id = auth()->user()->department->id;
            $leave = LeaveApplication::where('department_id',$department_id)->orderBy('created_at','desc')->get();
            return view('all_request', compact('leave'));
        }
        elseif($usertype == 'HOD') {
            $department_id = auth()->user()->department->id;
            $leave = LeaveApplication::where('department_id',$department_id)->orderBy('created_at','desc')->get();
            return view('all_request', compact('leave'));

        }else {
            $leave = LeaveApplication::all();
            return view('all_request', compact('leave'));
        }

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visitors()
    {
        $visitors = Visitors::with('visit', 'visit.employee')->orderBy('id', 'desc')->get();
        return view('visitors', compact('visitors'));
    }

    public function allVisits()
    {
        $visitors = Visitors::with('visit', 'visit.employee')->orderBy('id', 'desc')->get();
        return view('visitors', compact('visitors'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $id = Auth::user()->id;
        $employee = User::find($id);
        return view('profile', compact('employee'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function account()
    {
        $employee = User::all();
        return view('account', compact('employee'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function compLeave()
    {
        $employee = User::orderBy('id','asc')->get();
        return view('comp_leave',compact('employee'));
    }

    public function compDetails($id)
    {
        $employee = User::find($id);
        return view('comp_details',compact('employee'));

    }
    public function add_employee(Request $request)
    {

        $this->validate(request(), [
            'employee_no'=> 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'nat_id' => 'required|unique:users',
            'NSSF' => 'required',
            'NHIF' => 'required',
            'KRA_Pin' => 'required|unique:users',
            'phone_no' => 'required',
            'category'=>'required'

        ]);
        $usertype = auth()->user()->type->name;
        if ( $usertype == 'MD' | $usertype == 'Admin') {

            // getting department of a relationship
            $department = new Department();
            $department->id = $request->department;
            //getting type data of arelationship
            $type = new Type();
            $type->id = $request->type;



            $user = new User([
                'employee_no'=>$request->employee_no,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('123456'),
                'department_id' => $request->department,
                'nat_id' => $request->nat_id,
                'NSSF' => $request->NSSF,
                'NHIF' => $request->NHIF,
                'KRA_Pin' => $request->KRA_Pin,
                'type_id' => $request->type,
                'phone_no' => $request->phone_no,
                'category'=> $request->category
            ]);
            $department->user()->save($user);
            $user->department()->associate($department);
            //      trying to save type
            $type->user()->save($user);
            $user->type()->associate($type);


            $user->save();

            $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
            Storage::put('avatars/'.$user->id.'/avatar.png', (string) $avatar);


            // Mail::to($user->email)->send(new SendMailable($initialPass));

            Session::flash('message','member added successful');
            return redirect()->back();
        } else {
            Session::flash('danger','you are not allowed to add member');
            return redirect()->back();
        }



//
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id=null)
    {
        $usertype = auth()->user()->type->name;
        if ( $usertype == 'MD' | $usertype == 'Admin') {
            $id = $request->input('id');
            $employee = User::find($id);

            $employee->status = $request->status;

            $employee->save();
            return redirect()->back();
        } else {
            session()->flash('danger','you are not allowed to update status');
            return redirect()->back();
        }



    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applied_leave(Request $request){

        $id = Auth::user()->id;
        $type_id = Auth::user()->type_id;



        $this->leaveDays = LeaveDays::where('user_id', $id)->first();


        if ($this->leaveDays == null) {
            $this->leaveDays = Auth::user()->leaveDays()->create([
                'annualDays' => config('app_settings.annualLeaveDays'),
                'daysRemaining' => 0,
                'year' => date('Y', strtotime(Carbon::now())),
                'daysGone' => 0,
            ]);
        }

        if ($this->leaveDays->daysRemaining < 0) {
            session()->flash('danger','you have exhausted all your leave days');
        }
        /*
        * Check if user has active forced Leaves
        */

        $forcedLeaves = ForcedLeave::where('user_id', $id)->where('active', true)->get();

        if ($forcedLeaves->count() > 0) {
            \session()->flash('message','You currently have a forced leave, if you think this is a mistake talk to your hr');
            return redirect()->back();

        }


        $format = 'Y/m/d';
        $beginday = Carbon::parse($request->startDate)->format($format);
        $lastday=Carbon::parse($request->endDate)->format($format);


        $begin=strtotime($beginday);
        $end=strtotime($lastday);
        if($begin>$end){
            Session::flash('message','startdate is in the future!');
            return redirect()->back();
        }else{
            $no_days=0;
            $weekends=0;
            while($begin<=$end){
                $no_days++; // no of days in the given interval
                $what_day=date("N",$begin);
                if($what_day>5) { // 6 and 7 are weekend days
                    $weekends++;
                };
                $begin+=86400; // +1 day
            };

            $working_days=$no_days-$weekends;
        }
        if ($working_days > 0) {
            $annualLeaveDays = $this->leaveDays->annualDays;
            $daysGone = $this->leaveDays->daysGone;
            $newDaysGone = $this->leaveDays->daysGone + $working_days;

            $daysRemaining = $annualLeaveDays - $daysGone;
            $newDaysRemaining = $annualLeaveDays - $newDaysGone;


            if ($working_days > $daysRemaining) {
                Session::flash('danger','Request not successful, you only have '.$daysRemaining.' leave days remaining');
                return redirect()->back();
            }

            if (Auth::user()->id == (int)$request->reliever ){
                Session::flash('message','you cannot be a reliever');
                return redirect()->back();
            } else {

              // $row = DB::table('leave_application')->where('id',$id)->update([
              //         'usertype_id' => 6,
              //     ]);


                $leave = Auth::user()->leave()->create([
                    'type' => $request->type,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'no_of_relievers' => $request->no_of_relievers,
                    'leave_days' => $working_days,
                    'department_id' => Auth::user()->department->id,
                    'usertype_id' => $type_id
                ]);
                /*
             * Update Leave days
             */
                $this->leaveDays->update([
                    'daysGone' => $newDaysGone,
                    'daysRemaining' => $newDaysRemaining
                ]);


                return redirect()->route('home');

            }


        } else {
            Session::flash('message','invalid appliction');
            return redirect()->back();
        }


    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
      public function choose_relievers()
      {
        $reliever =  LeaveApplication::where('reliever', auth()->user()->id)->orderBy('id', 'desc')->get();

        $user = auth()->user();
        $employee = User::where('department_id', $user->department_id)->where('id', '!=', $user->id)
            ->select(['name', 'id'])->get();
        return view('choose_relievers', compact('employee'));

      }

      public function multiple_relievers()
      {
        $reliever =  LeaveApplication::where('reliever', auth()->user()->id)->orderBy('id', 'desc')->get();

      }

    public function relieverApprove(Request $request, $id)
    {

        $user = auth()->user();
        $application = LeaveApplication::where('id', $id)->where('reliever', $user->id)->first();
        if ($application == null) {
            Session::flash('danger','No leave with that ID, Permission denied');
            return redirect()->route('home');
        }

        /*
         * Accept Request
         */
        $accepted = $request->reliever;
        $application->update([
            'releiver_approval' => $accepted
        ]);

        Session::flash('message','Leave request accepted');
        return redirect()->route('reliever_request');

    }
    public function relieverReject(Request $request, $id)
    {
        $validate = $request->validate([
            'reason' => 'required'
        ]);
        $application = LeaveApplication::where('id',$id)->first();
        if ($application == null) {
            Session::flash('danger','no leave with that id');
            return redirect()->back();
        }
        $application->update([
            'releiver_approval' => 'rejected'
        ]);
        $application->reason()->update([
            'reliever' => $request->get('reason', 'no reason')
        ]);
        Session::flash('message','Leave request rejected');
        return redirect()->route('reliever_request');

    }

    public function leaveApproval(Request $request, $id)
    {
        $type = auth()->user()->type->name;
        if ($type == 'PM') {
            $user = auth()->user();
            $application = LeaveApplication::where('id', $id)->first();


            if ($application == null) {
                Session::flash('danger','No leave with that ID, Permission denied');
                return redirect()->route('home');
            }
            /*
        * Accept Request
        */

            $application->update([
                'PM' => 'approved'
            ]);
            Session::flash('message','Leave request approved');
            return redirect()->route('pending');

        }
        elseif ($type == 'HOD') {
            $user = auth()->user();
            $application = LeaveApplication::where('id', $id)->first();


            if ($application == null) {
                Session::flash('danger','No leave with that ID, Permission denied');
                return redirect()->route('home');
            }
            /*
        * Accept Request
        */

            $application->update([
                'HOD' => 'approved'
            ]);
            Session::flash('message','Leave request approved');
            return redirect()->route('pending');

        }
        elseif ($type =='HR') {
            $user = auth()->user();
            $application = LeaveApplication::where('id', $id)->first();


            if ($application == null) {
                Session::flash('danger','No leave with that ID, Permission denied');
                return redirect()->route('home');
            }
            /*
        * Accept Request
        */

            $application->update([
                'HR' => 'approved'
            ]);
            Session::flash('message','Leave request approved');
            return redirect()->route('pending');

        }
        elseif ($type == 'MD') {
            $user = auth()->user();
            $application = LeaveApplication::where('id', $id)->first();


            if ($application == null) {
                Session::flash('danger','No leave with that ID, Permission denied');
                return redirect()->route('home');
            }
            /*
        * Accept Request
        */

            $application->update([
                'MD' => 'approved'
            ]);
            Session::flash('message','Leave request approved');
            return redirect()->route('pending');
        }

    }

    public function leaveReject(Request $request, $id)
    {
        $type = auth()->user()->type->name;
        if ($type == 'PM'){
            $validate = $request->validate([
                'reason' => 'required'
            ]);
            $application = LeaveApplication::where('id',$id)->first();
            if ($application == null) {
                Session::flash('danger','no leave with that id');
                return redirect()->back();
            }
            $application->update([
                'PM' => 'rejected'
            ]);
            $application->reason()->update([
                'hr' => $request->get('reason', 'no reason')
            ]);
            Session::flash('message','Leave request rejected');
            return redirect()->route('pending');
        }
        elseif ($type == 'HOD'){
            $validate = $request->validate([
                'reason' => 'required'
            ]);
            $application = LeaveApplication::where('id',$id)->first();
            if ($application == null) {
                Session::flash('danger','no leave with that id');
                return redirect()->back();
            }
            $application->update([
                'HOD' => 'rejected'
            ]);
            $application->reason()->update([
                'hod' => $request->get('reason', 'no reason')
            ]);
            Session::flash('message','Leave request rejected');
            return redirect()->route('pending');
        }
        elseif ($type == 'HR') {
            $request->validate([
                'reason' => 'required'
            ]);
            $application = LeaveApplication::where('id',$id)->first();

            if ($application == null) {
                Session::flash('danger','no leave with that id');
                return redirect()->back();
            }
            $application->update([
                'HR' => 'rejected'
            ]);

            $application->reason()->update([
                'hr' => $request->get('reason', '')
            ]);
            Session::flash('message','Leave request rejected');
            return redirect()->route('pending');

        }
        elseif ($type =='MD') {
            $validate = $request->validate([
                'reason' => 'required'
            ]);

            $application = LeaveApplication::where('id',$id)->first();

            if ($application == null) {
                Session::flash('danger','no leave with that id');
                return redirect()->back();
            }
            $application->update([
                'MD' => 'rejected'
            ]);

            $application->reason()->update([
                'md' => $request->get('reason', 'md')
            ]);
            Session::flash('message','Leave request rejected');
            return redirect()->route('pending');

        }
    }

}
