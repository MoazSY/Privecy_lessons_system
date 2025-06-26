<?php
namespace App\Services;

use App\Models\Students;
use App\Models\Teacher;
use App\Repositories\StudentRepositoriesInterface;
use App\Repositories\TeacherRepositoriesInterface;
use App\Repositories\TokenRepositoriesInterface;
use Illuminate\Support\Facades\Http;

class OtpCodeServices{
    protected $student_repositories_interface;
    protected $teacher_repositories_interface;
    protected $token_repositories_interface;
    public function __construct(StudentRepositoriesInterface $student_repositories_interface ,TeacherRepositoriesInterface $teacher_repositories_interface,
    TokenRepositoriesInterface $token_repositories_interface)
    {
        $this->student_repositories_interface=$student_repositories_interface;
        $this->teacher_repositories_interface=$teacher_repositories_interface;
        $this->token_repositories_interface=$token_repositories_interface;
    }
    public function SendOtp($request)
    {

        $otp = rand(100000, 999999);
        $localPhone = $request->input('phoneNumber');
        $user = $request->input('user');
        $array = ['otp' => $otp, 'user' => $user];
        $internalPhone = '963' . substr($localPhone, 1);
        cache()->put('otp_' . $internalPhone, $array, now()->addMinute(10));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer $2b$10$Keu6Oo_BM8yD1fpJtfvRIuTFjKFBkQU85HKRHXbcrA.SlNYjhDzOm'
        ])->post('http://localhost:21465/api/test_api/send-message', [
            'phone' => $internalPhone,
            'message' => "Your verification code is: $otp",
        ]);
        return $response;
    }
    public function VerifyOtp($request)
    {
        $localPhone = $request->input('phoneNumber');
        $internalPhone = '963' . substr($localPhone, 1);
        $cachedOtp = cache('otp_' . $internalPhone);
        $otp = $cachedOtp['otp'];
        if (!$otp || $otp != $request->otp) {
            return ["message" => "code otp unvaliad or expiered", 'verify' => false];
        }
        if ($cachedOtp['user'] == 'student') {
            $student= Students::where('phoneNumber', '=', $localPhone)->first();
            if($student){
                if($student->School_stage()->exists()||$student->University_stage()->exists()){
                    $userStatus="Stage_register";
                }else{
                    $userStatus='Stage_not_register';
                }
                $user=$student;
            }else{
                $user = $this->student_repositories_interface->create($request);
                $user->is_profile_completed = false;
                $user->save();
                $userStatus='user_new';
            }
        }
        elseif ($cachedOtp['user'] == 'teacher') {
            $teacher=Teacher::where('phoneNumber','=',$localPhone)->first();
            if($teacher){
                if($teacher->School_stage()->exists()||$teacher->University_stage()->exists()){
                    $userStatus= 'Stage_register';
                }else{
                    $userStatus = 'Stage_not_register';
                }
                $user=$teacher;
            }else{
         $user = $this->teacher_repositories_interface->create($request);
                $userStatus = 'user_new';
            }
        }else{
            return null;
        }
        $token = $user->createToken('authToken')->plainTextToken;
        $this->token_repositories_interface->Add_expierd_token($token);
        $refresh_token = $this->token_repositories_interface->Add_refresh_token($token);
        cache()->forget('otp_' . $internalPhone);
        return ["message" => "code verify successfully", 'verify' => true, "token" => $token, "refresh_token" => $refresh_token,'user_status'=>$userStatus];
    }
    public function refresh_token($request)
    {
        $refresh_token = $request->refresh_token;
        $refresh = $this->token_repositories_interface->Refresh_token($refresh_token);
        if (!$refresh) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }
        $user = $this->token_repositories_interface->get_refresh_token_user($refresh_token);
        $plainTextToken = $user->createToken('authToken')->plainTextToken;
        $this->token_repositories_interface->Add_expierd_token($plainTextToken);
        return response()->json(['message' => 'token refresh successfully', 'refresh_token' => $refresh_token, 'plainTextToken' => $plainTextToken]);
    }
}
