<?php
	class UserController{
		
		public function getAllSupervisors()
        {
            getByRole('Supervisor');
		}

        public function getByRole($role){
            $nameid = R::findOne('rolename', 'name = "' . $role . '"');
            $userids = R::findAll('role', 'rolename_id = "' . $nameid->id . '"');
            $users = [];
            foreach($userids as $userid)
            {
                $user = R::findOne('user', 'id = "' . $userid->user_id . '"');
                $users[] = $user;
            }
            return $users;
        }

        public function assignLeader($userid, $themeid){
            $user = R::findOne('user', 'id = "' . $userid . '"');
            if($user->isTL()){
                $theme = R::findOne('theme', 'id = "' . $themeid . '"');
                $theme->leader_id = $user->id;
                R::store($theme);
            }else{
                return false;
            }
        }

		public function getStudentsByTheme($themename)
        {
			$theme = R::findOne('theme', 'name = "' . $themename . '"');
            $topicThemes = R::findAll('theme_topic', 'theme_id = "' . $theme->id . '"');
            $topics = [];
            foreach($topicThemes as $topicTheme){          
                $topics[] = R::findOne('topic', 'id = "' . $topicTheme->topic_id . '"'); 
            }                   
            $students = [];
            foreach($topics as $topic)
            {
                Debugger::write(json_encode($topic));
                $user = R::findOne('user', 'allocated_topic = "' . $topic->id . '"');
                if($user != null)
                {
                    $students[] = $user;
                }
            }
            return $students;
		}

        public function allocateSupervisor($userid, $supervisorid)
        {
            $user = R::findOne('user', 'id = "' . $userid . '"');
            $user->allocateSupervisor($supervisorid);
        }

        public function allocateTopic($studentid, $topicid)
        {
            $student = R::findOne('user', 'id = "' . $studentid . '"');
            $topic = R::findOne('topic', 'id = "' . $topicid . '"');
            $student->allocateTopic($topic);
        }

        public function getAllStudents()
        {
            $studentRoles = R::findAll('role', 'rolename_id = "3"');
            $students = [];        
            foreach($studentRoles as $studentRole)
            {
                $id = $studentRole->user_id;   
                $student = R::findOne('user', 'id = "' . $id . '"');                
                $students[] = $student;            
            }
            return $students;
        }

        public function getAllStudentsWithAllocations()
        {
            $students = $this->getAllStudents();            
            $studentsWA = [];
            foreach($students as $student)
            {               
                $student->ownChoices = $student->getStudentChoices();                
                $studentsWA[] = $student;
            
            }            
            return $students;
        }
	}
?>