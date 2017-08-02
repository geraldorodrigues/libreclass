<?php

Route::get('/login', function () {
	return view('auth/login');
});

Route::post('auth/in', 'Auth\LoginController@authenticate');
Route::any('auth/out', 'Auth\LoginController@out');

# Route::controller('/censo', 'CensoController');
Route::get('/censo/student', 'CensoController@getStudent');

// Erro ao detectar internet explorer
Route::get('/ie', function () {
	return view("ie");
});

# Route::controller('/classrooms', "ClassroomController");
Route::get('/classrooms', 'ClassroomController@getIndex');
Route::get('/classrooms/campus', 'ClassroomController@getCampus');

Route::get('student', function () {
	return view("students.disciplines");
});

# Route::controller('sync', "SyncController");
Route::get('/sync', 'SyncController@getIndex');
Route::post('/sync/receive', 'SyncController@postReceive');
Route::get('/sync/receive', 'SyncController@getReceive');
Route::get('/sync/error', 'SyncController@getError');

Route::get('logout', function () {
	Auth::logout();
	return Redirect::guest("/");
});

Route::get('help/{rota}', 'HelpController@getIndex');

# Route::controller('/', 'LoginController');
Route::get('/', 'HomeController@index');
Route::post('/', 'LoginController@postIndex');
Route::get('/login', 'LoginController@getLogin')->name('login');
Route::post('/login', 'LoginController@postLogin');
Route::get('/check', 'LoginController@getCheck');
Route::get('/email', 'LoginController@getEmail');
Route::post('/forgot-password', 'LoginController@postForgotPassword');

Route::get('/user/scholar-report', "UsersController@printScholarReport");
Route::post('user/teacher/delete', "UsersController@postUnlink");
Route::post('user/teacher/update-enrollment', "UsersController@updateEnrollment");
Route::get('classes/units/report-unit/{idUnit}', "UnitsController@getReportUnit");

//CourseController
Route::any('/course/save', 'CourseController@save');
Route::any('/course/list', 'CourseController@list');
Route::any('/course/delete', 'CourseController@delete');
# Route::controller('course', "CourseController");
// Route::get('/course', 'CourseController@getIndex');
// Route::post('/course/all-courses', 'CourseController@postAllCourses');
// Route::get('/course/edit', 'CourseController@getEdit');
// Route::post('/course/edit', 'CourseController@postEdit');
// Route::post('/course/period', 'CourseController@postPeriod');
// Route::post('/course/editperiod', 'CourseController@postEditperiod');

//DisciplineController
Route::any('/discipline/save', 'DisciplineController@save');
Route::any('/discipline/list', 'DisciplineController@list');
Route::any('/discipline/list', 'DisciplineController@read');
Route::any('/discipline/delete', 'DisciplineController@delete');
// Route::any('/discipline/discipline', 'DisciplineController@getDiscipline');
// Route::any('/discipline/edit', 'DisciplineController@postEdit');
// Route::any('/discipline/listperiods', 'DisciplineController@postListperiods');
// Route::any('/discipline/ementa', 'DisciplineController@getEmenta');

# Route::controller('classes/lessons', "LessonsController");
Route::get('/classes/lessons', 'LessonsController@getIndex');
Route::get('/classes/lessons/new', 'LessonsController@getNew');
Route::post('/classes/lessons/save', 'LessonsController@postSave');
Route::any('/classes/lessons/frequency', 'LessonsController@anyFrequency');
Route::post('/classes/lessons/delete', 'LessonsController@postDelete');
Route::get('/classes/lessons/info', 'LessonsController@getInfo');
Route::any('/classes/lessons/copy', 'LessonsController@anyCopy');
Route::post('/classes/lessons/list-offers', 'LessonsController@postListOffers');
Route::any('/classes/lessons/delete', 'LessonsController@anyDelete');

# Route::controller('classes/offers', "OffersController");
Route::get('/classes/offers', 'OffersController@getIndex');
Route::get('/classes/offers/user', 'OffersController@getUser');
Route::get('/classes/offers/unit', 'OffersController@getUnit');
Route::post('/classes/offers/teacher', 'OffersController@postTeacher');
Route::post('/classes/offers/status', 'OffersController@postStatus');
Route::get('/classes/offers/students/{offer}', 'OffersController@getStudents');
Route::post('/classes/offers/status-student', 'OffersController@postStatusStudent');
Route::any('/classes/offers/delete-last-unit/{offer}', 'OffersController@anyDeleteLastUnit');

# Route::controller('classes', "ClassesController");
Route::get('/classes', 'ClassesController@getIndex');
Route::get('/classes/panel', 'ClassesController@getPanel');
Route::post('/classes/listdisciplines', 'ClassesController@postListdisciplines');
Route::post('/classes/new', 'ClassesController@postNew');
Route::get('/classes/info', 'ClassesController@getInfo');
Route::post('/classes/edit', 'ClassesController@postEdit');
Route::post('/classes/delete', 'ClassesController@postDelete');
Route::post('/classes/change-status', 'ClassesController@postChangeStatus');
Route::any('/classes/list-offers', 'ClassesController@anyListOffers');
Route::post('/classes/list-units/{status?}', 'ClassesController@postListUnits');
Route::post('/classes/block-unit', 'ClassesController@postBlockUnit');
Route::post('/classes/unblock-unit', 'ClassesController@postUnblockUnit');
Route::any('/classes/create-units', 'ClassesController@anyCreateUnits');

# Route::controller('user', "UsersController");
Route::post('/user/search-teacher', 'UsersController@postSearchTeacher');
Route::any('/user/teachers-friends', 'UsersController@anyTeachersFriends');
Route::get('/user/teacher', 'UsersController@getTeacher');
Route::post('/user/teacher', 'UsersController@postTeacher');
Route::get('/user/profile-student', 'UsersController@getProfileStudent');
Route::any('/user/reporter-student-class', 'UsersController@anyReporterStudentClass');
Route::get('/user/reporter-student-offer', 'UsersController@getReporterStudentOffer');
Route::post('/user/profile-student', 'UsersController@postProfileStudent');
Route::post('/user/attestt', 'UsersController@postAttest');
Route::get('/user/profile-teacher', 'UsersController@getProfileTeacher');
Route::post('/user/invite/{id?}', 'UsersController@postInvite');
Route::get('/user/student', 'UsersController@getStudent');
Route::any('/user/find-user/{search}', 'UsersController@anyFindUser');
Route::post('/user/student', 'UsersController@postStudent');
Route::post('/user/unlink', 'UsersController@postUnlink');
Route::get('/user/infouser', 'UsersController@getInfouser');
Route::any('/user/link/{type}/{user}', 'UsersController@anyLink');

# Route::controller('import', "CSVController");
Route::get('/import', 'CSVController@getIndex');
Route::post('/import', 'CSVController@postIndex');
Route::get('/import/confirm-classes', 'CSVController@getConfirmClasses');
Route::get('/import/confirmattends', 'CSVController@getConfirmattends');
Route::post('/import/classwithteacher', 'CSVController@postClasswithteacher');
Route::get('/import/teacher', 'CSVController@getTeacher');
Route::get('/import/offer', 'CSVController@getOffer');
Route::get('/import/confirmoffer', 'CSVController@getConfirmoffer');

# Route::controller('permissions', "PermissionController");
Route::get('/permissions', 'PermissionController@getIndex');
Route::post('/permissions', 'PermissionController@postIndex');
Route::post('/permissions/find', 'PermissionController@postFind');

# Route::controller('lectures/units', "UnitsController");
Route::get('/lectures/units', 'UnitsController@getIndex');
Route::post('/lectures/units/edit', 'UnitsController@postEdit');
Route::get('/lectures/units/new', 'UnitsController@getNew');
Route::get('/lectures/units/student', 'UnitsController@getStudent');
Route::post('/lectures/units/rmstudent', 'UnitsController@postRmstudent');
Route::post('/lectures/units/addstudent', 'UnitsController@postAddstudent');
Route::post('/lectures/units/newunit', 'UnitsController@getNewunit');
Route::post('/lectures/units/reportunitz', 'UnitsController@getReportunitz');
Route::get('/lectures/units/report-unit/{idUnit}', 'UnitsController@getReportUnit');

# Route::controller('bind', "BindController");
Route::any('/bind/link', 'BindController@anyLink');
Route::any('/bind/list', 'BindController@anyList');

Route::get('user/profile', "UsersController@getProfile");
Route::get('user/student', "UsersController@getStudent");
Route::post('user/student', "UsersController@postStudent");


// Route::controller('classes/panel', "ClassesController");


#Route::controller('disciplines', "DisciplinesController");
Route::get('/disciplines', 'DisciplinesController@getIndex');
Route::post('/disciplines/save', 'DisciplinesController@postSave');
Route::post('/disciplines/delete', 'DisciplinesController@postDelete');
Route::get('/disciplines/discipline', 'DisciplinesController@getDiscipline');
Route::post('/disciplines/edit', 'DisciplinesController@postEdit');
Route::post('/disciplines/listperiods', 'DisciplinesController@postListperiods');
Route::any('/disciplines/list', 'DisciplinesController@anyList');
Route::get('/disciplines/ementa', 'DisciplinesController@getEmenta');

# Route::controller('lectures', "LecturesController");
Route::get('/lectures', 'LecturesController@getIndex');
Route::get('/lectures/finalreport/{offer}', 'LecturesController@getFinalreport');
Route::get('/lectures/frequency/{offer}', 'LecturesController@getFrequency');
Route::post('/lectures/sort', 'LecturesController@postSort');

# Route::controller('avaliable', "AvaliableController");
Route::get('/avaliable', 'AvaliableController@getIndex');
Route::post('/avaliable/postSave', 'AvaliableController@postSave');
Route::get('/avaliable/new', 'AvaliableController@getNew');
Route::post('/avaliable/exam', 'AvaliableController@postExam');
Route::post('/avaliable/exam-descriptive', 'AvaliableController@postExamDescriptive');
Route::get('/avaliable/finalunit/{unit?}', 'AvaliableController@getFinalunit');
Route::post('/avaliable/finalunit/{unit?}', 'AvaliableController@postFinalunit');
Route::post('/avaliable/postFinaldiscipline/{id?}', 'AvaliableController@postFinaldiscipline');
Route::post('/avaliable/offer', 'AvaliableController@postOffer');
Route::get('/avaliable/finaldiscipline/{offer?}', 'AvaliableController@getFinaldiscipline');
Route::get('/avaliable/average-unit/{unit}', 'AvaliableController@getAverageUnit');
Route::get('/avaliable/liststudentsexam/{exam?}', 'AvaliableController@getListstudentsexam');
Route::post('/avaliable/postDelete', 'AvaliableController@postDelete');

# Route::controller('lessons', "LessonsController"); /* anotações de aula */
Route::get('/lessons', 'LessonsController@getIndex');
Route::get('/lessons/new', 'LessonsController@getNew');
Route::post('/lessons/save', 'LessonsController@postSave');
Route::any('/lessons/frequency', 'LessonsController@anyFrequency');
Route::post('/lessons/delete', 'LessonsController@postDelete');
Route::get('/lessons/info', 'LessonsController@getInfo');
Route::any('/lessons/copy', 'LessonsController@anyCopy');
Route::post('/lessons/list-offers', 'LessonsController@postListOffers');
Route::any('/lessons/delete', 'LessonsController@anyDelete');

# Route::controller('attends', "\student\DisciplinesController");
Route::get('/attends', '\student\DisciplinesController@getIndex');
Route::get('/attends/units/{offer}', '\student\DisciplinesController@getUnits');
Route::post('/attends/resume-unit/{unit}', '\student\DisciplinesController@postResumeUnit');

# Route::controller('config', "ConfigController");
Route::get('/config', 'ConfigController@getIndex');
Route::post('/config', 'ConfigController@postIndex');
Route::post('/config/photo', 'ConfigController@postPhoto');
Route::post('/config/birthdate', 'ConfigController@postBirthdate');
Route::post('/config/common', 'ConfigController@postCommon');
Route::post('/config/commonselect', 'ConfigController@postCommonselect');
Route::post('/config/gender', 'ConfigController@postGender');
Route::post('/config/type', 'ConfigController@postType');
Route::post('/config/password', 'ConfigController@postPassword');
Route::post('/config/location', 'ConfigController@postLocation');
Route::post('/config/street', 'ConfigController@postStreet');
Route::post('/config/uee', 'ConfigController@postUee');

# Route::controller('/', 'SocialController');
Route::get('/', 'SocialController@getIndex');
Route::post('/question', 'SocialController@postQuestion');
Route::post('/suggestion', 'SocialController@postSuggestion');

//PeriodController
Route::any('/period/save', 'PeriodController@save');
Route::any('/period/list', 'PeriodController@list');
Route::any('/period/read', 'PeriodController@read');
