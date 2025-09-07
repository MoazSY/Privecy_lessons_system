<?php

namespace App\Repositories;


use App\Models\Admin;
use App\Models\Payment_transaction;
use App\Models\School_stage;
use App\Models\School_subjects;
use App\Models\Teacher;
use App\Models\University_stage;
use App\Models\University_subjects;
use App\Models\Student_card_charging;
use App\Models\Students;
use App\Models\Report_proccess;
use App\Models\Report;
use App\Models\Lesson_session;
use App\Notifications\CashAccept;
use App\Notifications\teacherProfileProccess;
use App\Repositories\AdminRepositoriesInterface ;
use App\Notifications\proccessReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

 class AdminRepositories implements AdminRepositoriesInterface{



    public function AddSchool_stage($request)
    {
        if($request->specialize==true){
            $school_stage = School_stage::create([
                'className' => $request->className,
                'school_stage' => $request->school_stage,
                'semester' => $request->semester,
                'specialize' => $request->specialize,
                'secondary_school_branch' => $request->secondary_school_branch,
                'vocational_type' => $request->vocational_type
            ]);
        }else{
            $school_stage=School_stage::create([
                'className'=>$request->className,
                'school_stage'=>$request->school_stage,
                'semester'=>$request->semester,
                'specialize'=>null,
                'secondary_school_branch'=>null,
                'vocational_type'=>null
            ]);
        }

        return $school_stage;
    }
    public function AddSchool_subjects($school_stage, $request,$imagepath)
    {

        if($imagepath!=null){
            if($request->has('about_subject')){
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'about_subject' => $request->about_subject,
                    'subject_cover_image' => $imagepath,
                    'school_stage_id' => $school_stage->id
                ]);
            }else{
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'subject_cover_image' => $imagepath,
                    'school_stage_id' => $school_stage->id
                ]);
            }
        }else{
            if($request->has('about_subject')){
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'about_subject' => $request->about_subject,
                    'school_stage_id' => $school_stage->id
                ]);
            }else{
                $school_subjects = School_subjects::create([
                    'name_subject' => $request->name_subject,
                    'school_stage_id' => $school_stage->id
                ]);
            }
        }
            return $school_subjects;
    }
    public function AddUniversity_stage($request,$specialize)
    {
        if($specialize==true){
            $university_stage = University_stage::create([
                'university_type' => $request->university_type,
                'university_branch' => $request->university_branch,
                'college_name' => $request->college_name,
                'study_year' => $request->study_year,
                'specialize' => $request->specialize,
                'specialize_name' => $request->specialize_name,
                'semester' => $request->semester
            ]);
        }else{
            $university_stage = University_stage::create([
                'university_type' => $request->university_type,
                'university_branch' => $request->university_branch,
                'college_name' => $request->college_name,
                'study_year' => $request->study_year,
                'specialize' => false,
                'specialize_name' => null,
                'semester' => $request->semester
            ]);
        }
    return $university_stage;
    }
    public function AddUniversity_subject($university_stage, $request,$imagepath)
    {
        if($imagepath!=null){
            $university_subjects = University_subjects::create([
                'university_stage_id' => $university_stage->id,
                'subject_name' => $request->subject_name,
                'about_subject' => $request->about_subject,
                'subject_cover_image' => $request->subject_cover_image
            ]);
        }
        $university_subjects=University_subjects::create([
            'university_stage_id'=> $university_stage->id,
            'subject_name'=>$request->subject_name,
            'about_subject'=>$request->about_subject,
        ]);
        return $university_subjects;
    }
    public function create($request,$imagepath){
        if($request->filled('SuperAdmin')){
            $superAdmin=$request->input('SuperAdmin');
        }
        else{
            $superAdmin=false;
        }
        if($imagepath!=null){
            $admin=Admin::create([
                'firstName'=>$request->firstName,
                'lastName'=>$request->lastName,
                'phoneNumber'=>$request->phoneNumber,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'image'=>$imagepath,
                'birthdate'=>$request->birthdate,
                'gender'=>$request->gender,
                'bankAccount'=>$request->bankAccount,
                'SuperAdmin'=>$superAdmin
            ]);
        }
        else{
            $admin = Admin::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birthdate' => $request->birthdate,
                'gender' => $request->gender,
                'bankAccount' => $request->bankAccount,
                'SuperAdmin'=>$superAdmin
            ]);
        }
        return $admin;
    }
    public function Admin_profile($admin_id){
        $admin=Admin::findOrFail($admin_id);
        return $admin;
    }
    public function proccess_teacher_account($teacher, $request)
    {
        $admin_id=Auth::guard('admin')->user()->id;
        $admin=Admin::findOrFail($admin_id);
        $teacher = Teacher::findOrFail($teacher->id);
        if ($request->state == 'approve') {
            $admin->TeacherAccount()->syncWithoutDetaching([
                $teacher->id => [
                    'state' => $request->state,
                    'cause_of_reject' => null,
                ]
            ]);
            $teacher->notify(new teacherProfileProccess($admin,['status'=>$request->state,'reject_cause'=>null]));
            }
             else{
                $admin->TeacherAccount()->syncWithoutDetaching([
                    $teacher->id => [
                        'state' => $request->state,
                        'cause_of_reject' => $request->cause_of_reject,
                    ]
                ]);
            $teacher->notify(new teacherProfileProccess($admin,['status'=>$request->state,'reject_cause'=>null]));

        }

        //notify teacher

        return ;
    }
    public function charging_card($admin_id,$request){
        $admin=Admin::findOrFail($admin_id);
        $admin->Card_charging()->attach([$request->student_id=>['card_charging'=>$request->card_charging,'charging_time'=>now()]]);
        $chargingCards=Student_card_charging::where('admin_id','=',$admin_id)->orderBy('charging_time','desc')->first();
        $student=Students::findOrFail($request->student_id);
        $student->CardValue+=$request->card_charging;
        $admin->CardValue+=$request->card_charging;
        $student->save();
        $admin->save();
        return $chargingCards;
    }
    public function delivery_cash_teacher($admin_id,$request){
        $admin=Admin::findOrFail($admin_id);
        $teacher=Teacher::findOrFail($request->teacher_id);
        if($teacher->CardValue<$request->cash_value){
            return 'cash_larger_card';
        }
        if($admin->CardValue<$request->cash_value){
            return "Not_enought_cash";
        }
        $admin->Delivery_cash_teacher()->attach([$request->teacher_id=>['cash_value'=>$request->cash_value,'delivery_time'=>Carbon::now()]]);
        // $cash=$admin->Delivery_cash_teacher()->orderBy('delivery_time','desc')->first();
            $cash = $admin->Delivery_cash_teacher()
                ->withPivot('id') // تضمين الـ ID من الجدول الوسيط
                ->orderBy('delivery_cash_teacher.delivery_time', 'desc')
                ->first();
        $teacher->notify(new CashAccept($admin,$cash));
        return $cash;
    }
    public function teacher_for_delivery($admin_id){
    $admin=Admin::findOrFail($admin_id);
    $teacherNotPay=Payment_transaction::where('admin_id','=',$admin_id)->where('admin_payout_teacher','=',false)->whereHas('S_or_G_lesson.lesson_session')->get();

    $pay = $teacherNotPay->filter(function ($teacher_N_Pay) {

        $lessonSession = $teacher_N_Pay->S_or_G_lesson->lesson_session->first();
        if ($lessonSession) {
        $end_time = $lessonSession->end_time;
        } else {
        return false;
        }
        $end_time = Carbon::parse($end_time);
        $now = Carbon::now();
         return $now >= $end_time->copy()->addMinutes(15);
        // return true;
    })->map(function ($teacher_N_Pay) {
        $session = $teacher_N_Pay->S_or_G_lesson->lesson_session->first();
        $teacher_N_Pay->session = $session;
        $teacher_N_Pay->teacher_duration = $session->calculateTeacherDuration();
        $teacher_N_Pay->report=$session->Report()->whereDoesntHave('Report_proccess')->get()->map(function($s){
            if($s->reference_report_path!=null){
                $fileUrl=asset('storage/'.$s->reference_report_path);
                return ['report'=>$s,'fileUrl'=>$fileUrl];
            }
                return ['report'=>$s,'fileUrl'=>null];

        });
        $teacher_N_Pay->teacher=$session->teacher;
        $teacher_N_Pay->student=$session->student;

        return $teacher_N_Pay;
    });

    if($pay->isEmpty()){
        return null;
    }
    return $pay;
    }

    public function proccess_report($data,$report,$admin_id){
        $admin=Admin::findOrFail($admin_id);
        $report=Report::FindOrFail($report->id);
        $teacher=Teacher::findOrFail($report->session->teacher->id);
       $proccess= $report->Report_proccess()->create([
            'admin_id'=>$admin->id,
            'proccess_method'=>$data['proccess_method'],
            'block_type'=>$data['block_type']??null,
            'block_duaration_value'=>$data['block_duaration_value']??null,
            'disscount_percentage_value'=>$data['disscount_percentage_value']??null,
            'response_time'=>now()
        ]);
        $report->state='Resolved';
        $report->save();
        if($data['proccess_method']=='block'){
            $block_duaration_value=$data['block_duaration_value'];
            $block_type=$data['block_type'];
            if($block_type=='hour'){
                $teacher->blocked_until =now()->addHours($block_duaration_value);
                $teacher->save();
            }elseif($block_type=='day'){
            $teacher->blocked_until =now()->addDays($block_duaration_value);
            $teacher->save();
            }
            elseif($block_type=='week'){
            $teacher->blocked_until =now()->addDays(7*$block_duaration_value);
            $teacher->save();
            }else{}
        $teacher->notify(new proccessReport($report,$proccess));

        }
        if($data['proccess_method']=='disscount'){
            $disscount_percentage_value=$data['disscount_percentage_value'];
            $payment_transaction=$report->session->S_or_G_lesson->payments->first();
            if($payment_transaction->disscount_percentage!=null){
               $payment_transaction->disscount_percentage+=$disscount_percentage_value;
                $payment_transaction->save();
            }
            else{
               $payment_transaction->disscount_percentage=$disscount_percentage_value;
                $payment_transaction->save();
            }
        $teacher->notify(new proccessReport($report,$proccess));
        }
        if($data['proccess_method']=='warning'){
        $teacher->notify(new proccessReport($report,$proccess));
        }
        else{

        }
        return $proccess;
    }

    public function transform_money($admin_id,$session){
        $admin=Admin::findOrFail($admin_id);
        $session=Lesson_session::findOrFail($session->id);
        if($session->end_time>now()){
            return 'session_not_end';
        }
        if ($session->hasUnprocessedReports()) {
        return 'process_reports_before_transfer';
        }
        $teacher=Teacher::findOrFail($session->teacher_id);
        $payment=$session->S_or_G_lesson->payments->first();
        $transformValue=$payment->amount-$payment->commission_value-($payment->disscount_percentage ?? 0)*($payment->amount-$payment->commission_value);
        $teacher->CardValue+=$transformValue;
        $teacher->save();
        $payment->admin_payout_teacher=true;
        $payment->teacher_amount_final=$transformValue;
        $payment->save();
        return $transformValue;
    }


    public function show_commisions($request)
    {

        $showType = $request->input('show_type');
        $payment = 0;

        if ($showType == 'daily') {
            $payment = Payment_transaction::whereDate('payment_transaction_time', today())
                ->where('admin_payout_teacher', true)
                ->sum('commission_value');

        } elseif ($showType == 'specefic_day') {
            $payment = Payment_transaction::whereDate('payment_transaction_time', $request->specefic_day)
                ->where('admin_payout_teacher', true)
                ->sum('commission_value');

        } elseif ($showType == 'monthly') {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $payment = Payment_transaction::whereBetween('payment_transaction_time', [$startOfMonth, $endOfMonth])
            ->where('admin_payout_teacher', true)
            ->sum('commission_value');

        } elseif ($showType == 'specefic_month') {

            $payment = Payment_transaction::whereYear('payment_transaction_time', $request->year)
                ->whereMonth('payment_transaction_time', $request->month)
                ->where('admin_payout_teacher', true)
                ->sum('commission_value');

        } elseif ($showType == 'total') {
            $payment = Payment_transaction::where('admin_payout_teacher', true)
                ->sum('commission_value');

        } else {
        }
        return [$payment,$showType];

}

}
