<?php
	class UserController{
		
		public function getAllSupervisors(){
			$userids = R::findAll('role', 'rolename_id = "4"');
            $users = [];
            foreach($userids as $userid){
                $user = R::findOne('user', 'id = "' . $userid->user_id . '"');
                $users[] = $user;
            }
            
            return $users;
		}

		public function getStudentsByTheme($themename){
			$theme = R::findOne('theme', 'name = "' . $themename . '"');

            $topics = R::findAll('topic', 'theme_id = "' . $theme->id . '"');
                    
            $students = [];
                
            foreach($topics as $topic)
            {
                $user = R::findOne('user', 'allocated_topic = "' . $topic->id . '"');
                if($user != null)
                {
                    $students[] = $user;
                }
            }
            return $students;
		}
	}
?>