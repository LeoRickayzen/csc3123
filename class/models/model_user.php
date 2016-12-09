<?php
/**
 * A model class for the RedBean object User
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2013-2014 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for User beans
 */
    class Model_User extends RedBean_SimpleModel
    {
/**
 * @var Array   Key is name of field and the array contains flags for checks
 */
        private static $editfields = array(
            'email'     => array(TRUE),         # array(NOTEMPTY)
        );
/**
 * Check for a role
 *
 * @param string        $contextname    The name of a context...
 * @param string	$rolename       The name of a role....
 *
 * @return object
 */
        public function hasrole($contextname, $rolename)
        {
            $cname = R::findOne('rolecontext', 'name=?', array($contextname));
            $rname = R::findOne('rolename', 'name=?', array($rolename));
            return R::findOne('role', 'rolecontext_id=? and rolename_id=? and user_id=? and start <= UTC_TIMESTAMP() and (end is NULL or end >= UTC_TIMESTAMP())',
                array($cname->getID(), $rname->getID(), $this->bean->getID()));
        }
/**
 * Check for a role
 *
 * @param string	$contextname    The name of a context...
 * @param string	$rolename       The name of a role....
 *
 * @return void
 */
        public function delrole($contextname, $rolename)
        {
            $cname = R::findOne('rolecontext', 'name=?', array($contextname));
            $rname = R::findOne('rolename', 'name=?', array($rolename));
            $bn = R::findOne('role', 'rolecontext_id=? and rolename_id=? and user_id=? and start <= UTC_TIMESTAMP() and (end is NULL or end >= UTC_TIMESTAMP())',
                array($cname->getID(), $rname->getID(), $this->bean->getID()));
            if (is_object($bn))
            {
                R::trash($bn);
            }
        }
/**
 *  Add a role
 *
 * @param string	$contextname    The name of a context...
 * @param string	$rolename       The name of a role....
 * @param string	$otherinfo      Any other info that is to be stored with the role
 * @param string	$start		A datetime
 * @param string	$end		A datetime or ''
 *
 * @return object
 */
        public function addrole($contextname, $rolename, $otherinfo, $start, $end = '')
        {
            $cname = R::findOne('rolecontext', 'name=?', array($contextname));
            if (!is_object($cname))
            {
                Web::getinstance()->bad();
            }
            $rname = R::findOne('rolename', 'name=?', array($rolename));
            if (!is_object($rname))
            {
                Web::getinstance()->bad();
            }
            $this->addrolebybean($cname, $rname, $otherinfo, $start, $end);
        }
/**
 *  Add a role
 *
 * @param object	$context        Contextname
 * @param object	$role           Rolename
 * @param string	$otherinfo      Any other info that is to be stored with the role
 * @param string	$start		A datetime
 * @param string	$end		A datetime or ''
 *
 * @return object
 */
        public function addrolebybean($context, $role, $otherinfo, $start, $end = '')
        {
            $r = R::dispense('role');
            $r->user = $this->bean;
            $r->rolecontext = $context;
            $r->rolename = $role;
            $r->otherinfo = $otherinfo;
            $r->start = $start;
            $r->end = $end === '' ? NULL : $end;
            R::store($r);
        }
/**
 * Get all currently valid roles for this user
 *
 * @param boolean	$all	If TRUE then include expired roles
 *
 * @return array
 */
        public function roles($all = FALSE)
        {
	    if ($all)
	    {
	        return $this->bean->with('order by start,end')->ownRole;
	    }
            return $this->bean->withCondition('start <= UTC_TIMESTAMP() and (end is null or end >= UTC_TIMESTAMP()) order by start, end')->ownRole;
        }
/**
 * Is this user an admin?
 *
 * @return boolean
 */
        public function isadmin()
        {
            return is_object($this->hasrole('Site', 'Admin'));
        }
/**
 * Is this user active?
 *
 * @return boolean
 */
        public function isactive()
        {
            return $this->bean->active;
        }
/**
 * Is this user confirmed?
 *
 * @return boolean
 */
        public function isconfirmed()
        {
            return $this->bean->confirm;
        }
/**
 * Is this user a developer?
 *
 * @return boolean
 */
        public function isdeveloper()
        {
            return is_object($this->hasrole('Site', 'Developer'));
        }
/**
 * Is this user a student?
 *
 * @return boolean
 */
        public function isstudent()
        {
            return is_object($this->hasrole('project', 'Student'));
        }
/**
 * Is this user a module leader??
 *
 * @return boolean
 */
        public function isTL()
        {
            return is_object($this->hasrole('project', 'ThemeLeader'));
        } 
/**
 * Is this user a supervisor?
 *
 * @return boolean
 */
        public function isSupervisor()
        {
            return is_object($this->hasrole('project', 'Supervisor'));
        }

/**
 * Get the users current topic choices,
 * if the user hasn't made any or some, a blank space is passed
 *
 * @return array
 */
        public function userChoices(){
            $id = $this->bean->id;
            $topics = R::find('user_topic', "user_id = '" . $id . "'");
        }
/**
 * User chooses a topic
 * 
 * @return void
 */
        public function userChoose($topic, $choiceNum){
            $user = $this->bean;
            $relationByChoice = R::findOne('userchoice_topic', 'topic_id = "' . $topic->id . '" AND user_id = "' . $user->id . '"');
            $relationByChoiceNum = R::findOne('userchoice_topic', 'choice_num = "' . $choiceNum . '"');
            //if the student hasn't already chosen that topic or allocated that choice number
            if($relationByChoice == NULL && $relationByChoiceNum == NULL){
                $user->link('userchoice_topic', array('choiceNum'=>$choiceNum))->topic = $topic;
                $id = R::store($user);
            }else{
                //if the student hasn't already allocated that choice number, but has already chosen that topic
                if($relationByChoiceNum == NULL){
                    //reallocate the choicen number
                    $relationByChoice->choiceNum = $choiceNum;
                    $id = R::store($relationByChoice);
                }else{
                    //if the student has already allocated that choice number, but the topic hasn't already been chosen
                    $relationByChoiceNum->topicId = $topic->id;
                    $relationByChoiceNum->userId = $user->id;
                    $id = R::store($relationByChoiceNum);
                }
            }
        }

/**
 * Set the user's password
 *
 * @param string	$pw	The password
 *
 * @return void
 */
        public function setpw($pw)
        {
            $this->bean->password = password_hash($pw, PASSWORD_DEFAULT);
            R::store($this->bean);
        }
/**
 * Check a password
 *
 * @param string	$pw The password
 *
 * @return boolean
 */
        public function pwok($pw)
        {
            return password_verify($pw, $this->bean->password);
        }
/**
 * Set the email confirmation flag
 *
 * @return void
 */
        public function doconfirm()
        {
            $this->bean->active = 1;
            $this->bean->confirm = 1;
            R::store($this->bean);
        }
/**
 * Generate a token for this user that can be used as a unique id from a phone.
 *
 * @param string    $device     Currently not used!!
 *
 * @return string
 */
	public function maketoken($device = '')
	{
	    $token = (object)['iss' => Config::SITEURL, 'iat' => idate('U'), 'sub' => $this->bean->getID()];
	    return JWT::encode($token, Context::KEY);
	}
/**
 * Handle an edit form for this user
 *
 * @param object   $context    The context object
 *
 * @return void
 */
        public function edit($context)
        {
            $change = FALSE;
            $error = FALSE;
            $fdt = $context->formdata();
            foreach (self::$editfields as $fld => $flags)
            { // might need more fields for different applications
                $val = $fdt->post($fld, '');
                if ($flags[0] && $val === '')
                { // this is an error as this is a required field
                    $error = TRUE;
                }
                elseif ($val != $this->bean->$fld)
                {
                    $this->bean->$fld = $val;
                    $change = TRUE;
                }
            }
            if ($change)
            {
                R::store($this->bean);
            }
            $pw = $fdt->post('pw', '');
            if ($pw !== '')
            {
                if ($pw == $fdt->post('rpw', ''))
                {
                    $this->setpw($pw); // setting the password will do a store
                }
                else
                {
                    $error = TRUE;
                }
            }
            $uroles = $this->roles();
	    if ($fdt->haspost('exist'))
	    {
                foreach ($_POST['exist'] as $ix => $rid)
                {
                    $rl = $context->load('role', $rid);
                    $start = $_POST['xstart'][$ix];
                    $end = $_POST['xend'][$ix];
                    $other = $_POST['xotherinfo'][$ix];
                    if (strtolower($start) == 'now')
                    {
                        $rl->start = $context->utcnow();
                    }
                    elseif ($start != $rl->start)
                    {
                        $rl->start = $context->utcdate($start);
                    }
                    if (strtolower($end) == 'never' || $end === '')
                    {
                        if ($rl->end !== '')
                        {
                            $rl->end = NULL;
                        }
                    }
                    elseif ($end != $rl->end)
                    {
                         $rl->end = $context->utcdate($end);
                    }
                    if ($other != $rl->otherinfo)
                    {
                        $rl->otherinfo = $other;
                    }
                    R::store($rl);
                }
	    }
            foreach ($_POST['role'] as $ix => $rn)
            {
                $cn = $_POST['context'][$ix];
                if ($rn !== '' && $cn !== '')
                {
                    $end = $_POST['end'][$ix];
                    $start = $_POST['start'][$ix];
                    $this->addrolebybean($context->load('rolecontext', $cn), $context->load('rolename', $rn), $_POST['otherinfo'][$ix],
                        strtolower($start) == 'now' ? $context->utcnow() : $context->utcdate($start),
                        strtolower($end) == 'never' || $end === '' ? '' : $context->utcdate($end)
                    );
                }
            }
            return TRUE;
        }
    }
?>
