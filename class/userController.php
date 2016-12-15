<?php
/**
 * A class that controls user aspects of the database,
 * I wanted to put this and the other controller classes in a controller directory, but couldn't figure out how to configure the classpath in php so that it still worked, If you know how, please write in the feedback?
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 *
 */
	class UserController{
/**
* Find a user by their user id
*
* @param    $id    the id of the user your trying to find 
*
* @return   Object  a user bean
*/	
        public function getByID($id)
        {
            return R::findOne('user', 'id = "' . $id . '"');
        }
/**
* Find all the users that are supervisors
*
* @param    none
*
* @return   Object  some user beans
*/
		public function getAllSupervisors()
        {
            return $this->getByRole('Supervisor');
		}
/**
* Find all the users that are theme leaders
*
* @param    none
*
* @return   Object  some user beans
*/
        pubLic function getAllTL()
        {
            return $this->getByRole('ThemeLeader');
        }
/**
* get a user by the rolename NOT id, as id could change with respect to name
*
* @param    $role  string value of the role name
*
* @return   $users  a list of users associated with that role
*/
        public function getByRole($role)
        {
            $nameid = R::findOne('rolename', 'name = "' . $role . '"');
            $userids = R::findAll('role', 'rolename_id = "' . $nameid->id . '" && rolecontext_id = "2"');
            $users = [];
            foreach($userids as $userid)
            {
                $user = R::findOne('user', 'id = "' . $userid->user_id . '"');
                $users[] = $user;
            }
            return $users;
        }
/**
* assign a leader to a theme
*
* @param    $userid     the id of the user being assigned to a module
* @param    $themeid    the id of the module the user is being assigned to
*
* @return   Boolean   if the user being referenced doesn't have the role theme leader, or exist
*/
        public function assignLeader($userid, $themeid)
        {
            $user = R::findOne('user', 'id = "' . $userid . '"');
            if($user->isTL() || $user == NULL){
                $theme = R::findOne('theme', 'id = "' . $themeid . '"');
                $theme->leader_id = $user->id;
                R::store($theme);
            }else{
                return false;
            }
        }
/**
* revoke the leadership of a module
*
* @param    $themeid    the id of the theme for which the themeleader is being removed
*
* @return   none    Doesn't return a value      
*/
        public function revokeLeader($themeid)
        {
            $theme = R::findOne('theme', 'id = "' . $themeid . '"');
            $theme->leader_id = NULL;
            R::store($theme);
        }
/**
* get a student by the themename of the associated topic that they have been allocated
*
* @param    $themename      the themename
*
* @return   Array      the student beans that have topics within this theme
*/
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
/**
* allocate a supervisor to a student
*
* @param    $userid           the id of the user being allocated a supervisor
* @param    $supervisorid     the id of the supervisor being allocated to a student
*
* @return   none
*/
        public function allocateSupervisor($userid, $supervisorid)
        {
            $user = R::findOne('user', 'id = "' . $userid . '"');
            $user->allocateSupervisor($supervisorid);
        }
/**
* allocate a topic to a student
*
* @param    $studentid     The id of the student being allocated a topic
* @param    $topicid       The id of the topic being allocated to the student
*
* @return   none
*/
        public function allocateTopic($studentid, $topicid)
        {
            $student = R::findOne('user', 'id = "' . $studentid . '"');
            $topic = R::findOne('topic', 'id = "' . $topicid . '"');
            $student->allocateTopic($topic);
        }
/**
* get all the students 
*
* @param    none
*
* @return   Object      All of the users that are students
*/
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
/**
* get all the students objects, with a field containing all of their allocations
*
* @param    none
*
* @return   Object      A list of all the students
*/
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