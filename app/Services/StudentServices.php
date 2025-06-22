<?php
namespace App\Services;

use App\Models\RefreshToken;
use App\Models\Students;
use App\Repositories\StudentRepositoriesInterface;
use App\Repositories\TokenRepositories;
use App\Repositories\TokenRepositoriesInterface;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class StudentServices{
    protected $student_repositories_interface;
    protected $token_repositories_interface;
    public function __construct(StudentRepositoriesInterface $student_repositories_interface , TokenRepositoriesInterface $token_repositories_interface)
    {
    $this->student_repositories_interface=$student_repositories_interface;
    $this->token_repositories_interface=$token_repositories_interface;
    }
        public function SendOtp($request){

        $otp = rand(100000, 999999);
        $localPhone=$request->input('phoneNumber');
        $internalPhone='963'. substr($localPhone,1);
        cache()->put('otp_' . $internalPhone, $otp, now()->addMinute(10));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer $2b$10$Keu6Oo_BM8yD1fpJtfvRIuTFjKFBkQU85HKRHXbcrA.SlNYjhDzOm'
        ])->post('http://localhost:21465/api/test_api/send-message',[
            'phone' => $internalPhone,
            'message' => "Your verification code is: $otp",
        ]);
        return $response;
    }
    public function VerifyOtp($request){
        $localPhone = $request->input('phoneNumber');
        $internalPhone = '963' . substr($localPhone, 1);
        $cachedOtp = cache('otp_' . $internalPhone);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return ["message"=>"code otp unvaliad or expiered",'verify'=>false];
        }
        $user=$this->student_repositories_interface->create($request);
        $user->is_profile_completed=false;
        $user->save();
        $token = $user->createToken('authToken')->plainTextToken;
        $this->token_repositories_interface->Add_expierd_token($token);
       $refresh_token= $this->token_repositories_interface->Add_refresh_token($token);
        // cache()->forget('otp_' . $internalPhone);
        return ["message"=>"code verify successfully",'verify'=>true,"token"=>$token,"refresh_token"=>$refresh_token];
    }
    public function refresh_token($request)
    {
        $refresh_token = $request->refresh_token;
        $refresh = $this->token_repositories_interface->Refresh_token($refresh_token);
        if (!$refresh) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }
        $user=$this->token_repositories_interface->get_refresh_token_user($refresh_token);
        $plainTextToken = $user->createToken('authToken')->plainTextToken;
        $this->token_repositories_interface->Add_expierd_token($plainTextToken);
        return response()->json(['message' => 'token refresh successfully', 'refresh_token' => $refresh_token, 'plainTextToken' => $plainTextToken]);
    }
    public function Choose_school_stage($School_stage_id){
        $student=Auth::guard('student')->user()->id;
     return $this->student_repositories_interface->StudentSchoolStage($student, $School_stage_id);
    }
    public function School_stage(){
        return $this->student_repositories_interface->get_school_stage();
    }
    public function School_stage_subjects($school_stage){
        $array=[];
         $result=$this->student_repositories_interface->SchoolSubjects($school_stage);
        if($result!=null){
            foreach($result as $subject){
                if($subject->subject_cover_image!=null){
                    $imageUrl=asset('storage/'. $subject->subject_cover_image);
                    $array[]=['subject'=>$subject,'imageUrl'=>$imageUrl];
                }
                else{
                    $array[] = ['subject' => $subject, 'imageUrl' => null];
                }
            }
            return $array;
        }
        return null;
    }
    public function Student_subject($school_subjects){
        $student = Auth::guard('student')->user()->id;
        return $this->student_repositories_interface->StudentSchoolSubjects($student, $school_subjects);
    }
    public function Profile_complate($request,$data){
        $student_id = Auth::guard('student')->user()->id;
        $student = Students::where('id', '=', $student_id)->first();
        if($request->hasFile('image')){
            $originalName=$request->file('image')->getClientOriginalName();
            $path=$request->file('image')->storeAs('students/images',$originalName,'public');
            $data['image']=$path;
        }
        if(!empty($data['password'])){
            $data['password']=Hash::make($data['password']);
        }
        $student->update($data);
        $student->fresh();
        $student->is_profile_completed=true;
        $student->save();
        $imageUrl= asset('storage/' . $student->image);
        return [$student,$imageUrl];
    }
    public function Student_profile(){
    $student=Auth::guard('student')->user();
    $profile=$this->student_repositories_interface->Student_profile($student->id);
    return $profile;
    }
    public function University_stage(){
        return $this->student_repositories_interface->get_university_stage();
    }
    public function Choose_university_stage($university_stage_id){
        $student = Auth::guard('student')->user()->id;
       return  $this->student_repositories_interface->UniversityStage($student, $university_stage_id);
    }
    public function get_university_stage_subjects($university_stage){
        $array=[];
        $result=$this->student_repositories_interface->UniversitySubjects($university_stage);
        if($result!=null){
            foreach ($result as $subject) {
                if ($subject->subject_cover_image != null) {
                    $imageUrl = asset('storage/' . $subject->subject_cover_image);
                    $array[] = ['subject'=>$subject, 'imageUrl'=>$imageUrl];
                } else {
                    $array[] = ['subject'=>$subject,'imageUrl'=> null];
                }
            }
            return $array;
        }
        return null;
    }
    public function Student_university_subjects($subjects){
        $student=Auth::guard('student')->user()->id;
        return $this->student_repositories_interface->Student_university_subjects($student,$subjects);
    }
}
