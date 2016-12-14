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

        public function getAllocations($context)
        {
            $userController = new UserController();
            return $userController->getAllStudentsWithAllocations();
        }
    }
?>