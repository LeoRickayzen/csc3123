<?php
/**
 * A class that contains code to implement a contact page
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 *
 */
    class Allocate extends Siteaction
    {
/**
 * Handle various allocation actions
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */

        public function handle($context)
        {
            $getAllocation = new Route('all', 'GET');
            $allocateTopic = new Route('topic', 'POST');
            $allocateSupervisorsGet = new Route('supervisors/', 'GET');
            $allocateSupervisorsPost = new Route('supervisors', 'POST');
            $allocateModuleLeaderGet = new Route('moduleleader', 'GET');
            $allocateModuleLeaderPost = new Route('moduleleader', 'POST');
            $rest = $context->rest();
            $path = Route::routeBuilder($rest);            

            if($getAllocation->isEqual($rest, $_SERVER))
            {
                $context->local()->addval('students', $this->getAllocations($context));
                return 'moduleLeaderViews/allocator.twig';
            }
            
            if($allocateTopic->isEqual($rest, $_SERVER))
            {
                $this->allocateTopic($context);
            }
            
            if($allocateSupervisorsGet->isEqual($rest, $_SERVER))
            {
                $theme = substr($path, 12, strlen($path));
                return $this->getAllocateSupervisors($context, $theme);
            }

            if($allocateModuleLeaderGet->isEqual($rest, $_SERVER))
            {
                return $this->getModuleLeaders($context);
            }

            if($allocateModuleLeaderPost->isEqual($rest, $_SERVER))
            {
                return $this->postModuleLeaders($context);
            }
            
            if($allocateSupervisorsPost->isEqual($rest, $_SERVER))
            {
                return $this->postSupervisorAllocation($context);
            }
            return 'error/404.twig';
        }
/**
* Get the page where the supervisors can be allocate to individual students
*
* @param    $context    The context site variable
* @param    $themename  The themename for which supervisors are being allocated for
*
* @return   string  the allocation view
*/
        public function getAllocateSupervisors($context, $themename)
        {
            if($context->hasTL() && $context->user()->hasTheme($themename, $context->user()->id))
            {

                $userController = new UserController();                
                $context->local()->addval('students', $userController->getStudentsByTheme($themename));
                $context->local()->addval('supervisors', $userController->getAllSupervisors());
            }
            else
            {
                return "error/403.twig";
            }
            return 'themeLeaderViews/allocation.twig';
        }
/**
* Allocate a supervisor to a student via a post form, divert back to the theme view
*
* @param    $context    The context variable
* 
* @return   none
*/
        public function postSupervisorAllocation($context)
        {
            $userids = array_keys($_POST);
            $userController = new UserController();            
            foreach($userids as $userid)
            {
                $userController->allocateSupervisor($userid, $_POST[$userid]);
            }
            $context->divert('/theme', FALSE, '', FALSE);
        }
/**
* Allocate a topic to a student, divert back to all or an error page once submitted 
* 
* @param    $context    The context variable
*
* @return   string  return the error page if the form data is incomplete
*/
        public function allocateTopic($context)
        {
            $fdt = $context->formdata();
            if($fdt->haspost('topicStudent'))
            {
                $formins = explode(',', $fdt->mustpost('topicStudent'));
                $topicid = $formins[0]; 
                $studentid = $formins[1];
                $userController = new UserController();
                $userController->allocateTopic($studentid, $topicid);
                $context->divert('/allocate/all', FALSE, '', FALSE);
            }
            else
            {
                return 'error/form.twig';
            }
        }
/**
* Create and return the view that allows module leaders to assign theme leaders
*
* @param    $context    The context variable
*
* @return   string  The template which handles allocating a theme leader to a theme
*/
        public function getModuleLeaders($context)
        {
            $userController = new UserController();
            $themeController = new ThemeController();
            $themeLeaders = $userController->getAllTL();
            $supervisors = $userController->getAllSupervisors();
            $themes = $themeController->getAllThemes();
            foreach($themes as $theme)
            {
                if($theme->leader_id == NULL){
                    $theme->email = "no leader";
                }else{
                    $theme->email = $userController->getByID($theme->leader_id)->email;
                }
            }
            $context->local()->addval('themeLeaders', $themeLeaders);
            $context->local()->addval('themes', $themes);
            return 'moduleLeaderViews/themeLeaderAllocation.twig';
        }
/**
* allocate a theme leader to a theme
*
* @param    $context    The context variable
*
* @return   string  The error template if an error is returned
*/
        public function postModuleLeaders($context)
        {
            $userController = new UserController();
            if($context->formdata()->haspost('userid') && $context->formdata()->haspost('themeid')){
                $userid = $context->formdata()->mustpost('userid');
                $themeid = $context->formdata()->mustpost('themeid');
                if($userid === 'none'){
                    $userController->revokeLeader($themeid);
                }else{
                    $userController->assignLeader($userid, $themeid);
                }
                return $this->getModuleLeaders($context);
            }else{
                return 'error/form.twig';
            }
        }
/**
* get all the allocations
*
* @param    $context    The context variable
*
* @return   object  The object containing all the users
*/        
        public function getAllocations($context)
        {
            $userController = new UserController();
            return $userController->getAllStudentsWithAllocations();
        }
    }
?>