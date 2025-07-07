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
use Laravel\Sanctum\PersonalAccessToken;

class StudentServices{
    protected $student_repositories_interface;
    protected $token_repositories_interface;
    public function __construct(StudentRepositoriesInterface $student_repositories_interface , TokenRepositoriesInterface $token_repositories_interface)
    {
    $this->student_repositories_interface=$student_repositories_interface;
    $this->token_repositories_interface=$token_repositories_interface;
    }


    public function Profile_complate($request,$data){
        $student_id = Auth::guard('student')->user()->id;
        $student = Students::where('id', '=', $student_id)->first();
        if($request->hasFile('image')){
            $originalName=$request->file('image')->getClientOriginalName();
            $path=$request->file('image')->storeAs('students/images',$originalName,'public');
            $data['image']=$path;
            $imageUrl = asset('storage/' . $path);
        }else{$imageUrl=null;}
        if(!empty($data['password'])){
            $data['password']=Hash::make($data['password']);
        }
        $student->update($data);
        $student->fresh();
        $student->is_profile_completed=true;
        $student->save();

        return [$student,$imageUrl];
    }
    public function Student_profile(){
    $student=Auth::guard('student')->user();
    $profile=$this->student_repositories_interface->Student_profile($student->id);
    return $profile;
    }



}
