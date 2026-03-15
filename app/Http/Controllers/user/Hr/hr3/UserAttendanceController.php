<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{

    public function scanView()
    {
        return view('hr.hr3.attendance_scan');
    }

    public function verify(Request $request)
    {

        if (!Auth::check()) {
            return $this->handleResponse($request,false,'Unauthorized',401);
        }

        $user = Auth::user();

        $employee = Employee::where('user_id',$user->id)->first();

        if(!$employee){
            return $this->handleResponse($request,false,'Employee record not found.',404);
        }

        $today = now()->format('l');

        $assignedShift = Shift::where('employee_id',$employee->employee_id)
            ->where('day_of_week',$today)
            ->where('is_active',1)
            ->first();

        if(!$assignedShift){
            return $this->handleResponse(
                $request,
                false,
                "Access Denied: No assigned shift for today ($today).",
                403
            );
        }

        $existingLog = AttendanceLog::where('employee_id',$employee->employee_id)
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | PREVENT DOUBLE SHIFT
        |--------------------------------------------------------------------------
        */

        if(!$existingLog){

            $alreadyCompleted = AttendanceLog::where('employee_id',$employee->employee_id)
                ->whereDate('clock_in',now()->toDateString())
                ->whereNotNull('clock_out')
                ->exists();

            if($alreadyCompleted){
                return $this->handleResponse(
                    $request,
                    false,
                    'You already completed your shift today.',
                    422
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOKEN VALIDATION
        |--------------------------------------------------------------------------
        */

        $tokenValue = null;

        if(!$existingLog){

            $rawToken = $request->input('token');

            if($rawToken === 'DASHBOARD_BUTTON'){
                $tokenValue = 'DIRECT_CLOCK_'.now()->timestamp;
            }else{

                $tokenValue = str_contains($rawToken ?? '','/')
                    ? collect(explode('/',$rawToken))->last()
                    : $rawToken;

                if(!$tokenValue){
                    return $this->handleResponse($request,false,'QR Token required',400);
                }

                $validToken = Cache::pull("attendance_token_$tokenValue");

                if(!$validToken){
                    return $this->handleResponse($request,false,'QR expired or invalid.',422);
                }
            }

        }else{
            $tokenValue = $existingLog->qr_token;
        }


        try{

            $now = now();


            /*
            |--------------------------------------------------------------------------
            | CLOCK OUT
            |--------------------------------------------------------------------------
            */

            if($existingLog){

                $clockIn = Carbon::parse($existingLog->clock_in);
                $clockOut = $now;

                $scheduledStart = Carbon::parse(
                    $clockIn->format('Y-m-d').' '.$assignedShift->start_time
                );

                $scheduledEnd = Carbon::parse(
                    $clockIn->format('Y-m-d').' '.$assignedShift->end_time
                );

                if($scheduledEnd->lte($scheduledStart)){
                    $scheduledEnd->addDay();
                }

                /*
                |--------------------------------------------------------------------------
                | WORKED HOURS (SECOND PRECISION)
                |--------------------------------------------------------------------------
                */

                $workedSeconds = $clockIn->diffInSeconds($clockOut);
                $workedHours = round($workedSeconds / 3600,4);

                /*
                |--------------------------------------------------------------------------
                | SCHEDULED HOURS
                |--------------------------------------------------------------------------
                */

                $scheduledSeconds = $scheduledStart->diffInSeconds($scheduledEnd);

                /*
                |--------------------------------------------------------------------------
                | OVERTIME
                |--------------------------------------------------------------------------
                */

                $overtimeSeconds = max(0,$workedSeconds - $scheduledSeconds);
                $overtimeHours = round($overtimeSeconds / 3600,4);

                /*
                |--------------------------------------------------------------------------
                | NIGHT DIFFERENTIAL (22:00 - 06:00)
                |--------------------------------------------------------------------------
                */

                $nightSeconds = 0;

                $periodStart = $clockIn->copy();
                $periodEnd = $clockOut->copy();

                while($periodStart < $periodEnd){

                    $nightStart = $periodStart->copy()->setTime(22,0,0);
                    $nightEnd = $periodStart->copy()->addDay()->setTime(6,0,0);

                    $start = $periodStart->copy()->max($nightStart);
                    $end = $periodEnd->copy()->min($nightEnd);

                    if($end > $start){
                        $nightSeconds += $start->diffInSeconds($end);
                    }

                    $periodStart->addDay()->startOfDay();
                }

                $nightHours = round($nightSeconds / 3600,4);


                /*
                |--------------------------------------------------------------------------
                | UPDATE LOG
                |--------------------------------------------------------------------------
                */

                $existingLog->update([
                    'clock_out' => $clockOut,
                    'worked_hours' => $workedHours,
                    'overtime_hours' => $overtimeHours,
                    'night_diff_hours' => $nightHours
                ]);


                return $this->handleResponse(
                    $request,
                    true,
                    'Clock-out recorded successfully!'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLOCK IN
            |--------------------------------------------------------------------------
            */

            $scheduledStart = Carbon::parse(
                $now->format('Y-m-d').' '.$assignedShift->start_time
            );

            $status = $now->gt($scheduledStart->copy()->addMinutes(15))
                ? 'late'
                : 'on-time';


            $employee->load('position');

            AttendanceLog::create([

                'employee_id' => $employee->employee_id,
                'department_id' => $employee->department_id,
                'specialization' => $employee->position->specialization_name ?? $employee->specialization,
                'position_title' => $employee->position->position_title ?? 'Unassigned',
                'shift_name' => $assignedShift->shift_name,
                'qr_token' => $tokenValue,
                'clock_in' => $now,
                'device_fingerprint' => md5($request->userAgent() ?? ''),
                'status' => $status
            ]);


            return $this->handleResponse(
                $request,
                true,
                "Clock-in successful! You are marked as $status."
            );

        }
        catch(\Exception $e){

            return $this->handleResponse(
                $request,
                false,
                'Server Error: '.$e->getMessage(),
                500
            );
        }
    }



    private function handleResponse(Request $request,bool $success,string $message,int $status=200)
    {

        if($request->expectsJson()){
            return response()->json([
                'success'=>$success,
                'message'=>$message
            ],$status);
        }

        if($request->isMethod('post')){

            return $success
                ? redirect()->route('user.attendance.success')->with('status',$message)
                : redirect()->back()->with('error',$message);
        }

        return $success
            ? redirect()->route('user.attendance.success')->with('status',$message)
            : redirect()->route('user.attendance.scan')->with('error',$message);
    }



    public function success()
    {
        return redirect()
            ->route('hr.dashboard')
            ->with('success','Attendance logged successfully!');
    }
}