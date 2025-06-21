<?php
namespace App\Repositories;

interface StudentRepositoriesInterface {
public function create($request);
public function Auth($credintals);
    // public function Add_expierd_token($token);
    // public function Refresh_token($token);
    // public function Add_refresh_token($token);
    // public function get_refresh_token_user($refresh_token);
    public function findStudent($student);
    public function StudentSchoolStage($student,$school_stage);
    public function get_school_stage();
public function SchoolSubjects($stage);
public function StudentSchoolSubjects($student,$subjects);
public function Student_profile($student);
public function get_university_stage();
public function UniversityStage($student,$university_stage_id);
public function UniversitySubjects($stage);
public function Student_university_subjects($student,$subjects);
public function ShowTeacherAvailable($student);
public function TeacherRating($student,$teacher);
public function TeacherFollowing($student,$teacher,$RecieveNotification);
public function ShowTeacherProfile($teacher);
public function SubjectReservation($student,$teacher,$subject,$request);
public function GetAvailableTimeTeacher($teacher);
public function GetStages_Subjecs_Teacher($teacher);
public function GetReservation($student);
public function GetLessonse($student);
public function ShowSlider();
public function Report($student,$lesson);
public function Payment($student,$teacher,$request);

}
