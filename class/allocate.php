<?php
/**
 * A class that contains code to implement a contact page
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 * @copyright 2012-2016 Newcastle University
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
        }

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
                $context->divert('/home', FALSE, '', FALSE);
            }
            else
            {
                return 'error/404.twig';
            }
        }

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

        public function postModuleLeaders($context)
        {
            Debugger::write('called');
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
                return 'error/403.twig';
            }
        }

        public function getAllocations($context)
        {
            $userController = new UserController();
            return $userController->getAllStudentsWithAllocations();
        }
    }
?>