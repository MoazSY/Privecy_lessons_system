<?php
namespace App\Services;


use App\Models\Admin;
use App\Models\Students;
use App\Models\Teacher;
use App\Repositories\AdminRepositoriesInterface;
use App\Repositories\TokenRepositoriesInterface;
use Illuminate\Support\Facades\Hash;

class AdminServices{
protected $admin_repositories_interface;
protected $token_repositories_interface;
public function __construct(AdminRepositoriesInterface $admin_repositories_interface,TokenRepositoriesInterface $token_repositories_interface)
{
    $this->admin_repositories_interface=$admin_repositories_interface;
    $this->token_repositories_interface=$token_repositories_interface;
}
public function Add_school_stage($request){
   $result= $this->admin_repositories_interface->AddSchool_stage($request);
   return $result;
}
public function Add_school_subject($request, $school_stage){
         if($request->hasFile('subject_cover_image')){
            $originalName = $request->file('subject_cover_image')->getClientOriginalName();
            $imagepath = $request->file('subject_cover_image')->storeAs('school/subjects/images', $originalName, 'public');
            $result = $this->admin_repositories_interface->AddSchool_subjects($school_stage, $request, $imagepath);
            }
            else{
            $result = $this->admin_repositories_interface->AddSchool_subjects($school_stage, $request, null);
        }
        return $result;
}
public function Add_university_stage($request){
    if($request['specialize']==true){
      $result=  $this->admin_repositories_interface->AddUniversity_stage($request,true);
    }
    else{
    $result =  $this->admin_repositories_interface->AddUniversity_stage($request, false);
        }
     return $result;
}
public function Add_university_subject($request,$university_stage){
    if($request->hasFile('subject_cover_image')){
            $originalName = $request->file('subject_cover_image')->getClientOriginalName();
            $imagepath = $request->file('subject_cover_image')->storeAs('university/subjects/images', $originalName, 'public');
            $result=$this->admin_repositories_interface->AddUniversity_subject($university_stage,$request,$imagepath);
        }
        else{
        $result = $this->admin_repositories_interface->AddUniversity_subject($university_stage, $request, null);
        }
        return $result;
}
public function Regester($request){
    if($request->hasFile('image')){
        $image=$request->file('image')->getClientOriginalName();
        $imagepath=$request->file('image')->storeAs('admin/images',$image,'public');
        $admin=$this->admin_repositories_interface->create($request,$imagepath);
            $token = $admin->createToken('authToken')->plainTextToken;
            $this->token_repositories_interface->Add_expierd_token($token);
            $refresh_token = $this->token_repositories_interface->Add_refresh_token($token);
            $imageUrl=asset('storage/' . $imagepath);
    }
    else{
        $admin=$this->admin_repositories_interface->create($request,null);
        $imageUrl=null;
    }
    return ['admin'=>$admin,'token'=>$token,'refresh_token'=>$refresh_token,'imageUrl'=>$imageUrl];
}
public function login($request){
        $credentials = $request->only('email', 'password');
        $userType=[
            \App\Models\Admin::class,
            \App\Models\Students::class,
            \App\Models\Teacher::class
        ];
        foreach ($userType as $type) {
            $user = $type::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('authToken')->plainTextToken;
                $this->token_repositories_interface->Add_expierd_token($token);
                $refresh_token = $this->token_repositories_interface->Add_refresh_token($token);

                return response()->json([
                    'token' => $token,
                    'refresh_token'=>$refresh_token,
                    'user_type' => class_basename($type),
                    'user' => $user,
                ]);
            }
        }
        return response()->json(['message'=>'invalid input data']);
}
    public function Logout($request){
        $accessToken = $request->user()?->currentAccessToken();
        if($accessToken){
            $user = $accessToken->tokenable;

            if ($user instanceof \App\Models\Students) {
                $user = Students::findOrFail($user->id);
                $user->refreshTokens()->delete();
            } elseif ($user instanceof \App\Models\Admin) {
                $user = Admin::findOrFail($user->id);
                $user->refreshTokens()->delete();
            } else {
                $user = Teacher::findOrFail($user->id);
                $user->refreshTokens()->delete();
            }
            $request->user()->tokens()->delete();
            return response()->json(['message'=>'logout successfully','user'=>$user]);
        }
        return response()->json(['message'=>'user token unavailable']);

    }
}
