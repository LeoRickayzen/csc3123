<?php
/**
 * A class that contains code to handle any requests for topics pages
 *
 * @author Leo Rickayzen <l.rickayzen1@ncl.ac.uk>
 *
 */
/**
 * Support / or /home
 */
    class Topic extends Siteaction
    {
/**
 * Handle home operations /
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        /**
         *
         *
         *
         */
        public function handle($context)
        {
            $rest = $context->rest();
            $path = Route::routeBuilder($rest);
            $topicPostRoute = new Route('', 'POST');           
            $topicSpecificRoute = new Route('/', 'GET');
            if($topicPostRoute->isEqual($context->rest(), $_SERVER))
            {                
                return $this->postTopic($context);           
            }            
            if($topicSpecificRoute->isEqual($context->rest(), $_SERVER)){
                $topic = substr($path, 7, sizeof($path));
            }
        }

        public function postTopic($context)
        {            
            $topicController = new TopicController();            
            if($context->hasTL() || $context->hasSupervisor() || $context->hasAdmin() || $context->hasML())
            {
                $fdt = $context->formdata();
                if($fdt->haspost('title') && $fdt->haspost('description'))
                {
                    $title = $fdt->mustpost('title');
                    $description = $fdt->mustpost('description');
                    if($context->hasSupervisor())
                    {    
                        $supervisorid = $context->user()->getId();
                    }
                    else
                    {  
                        if($fdt->haspost('supervisorid'))
                        { 
                            $supervisorid = $fdt->mustpost('supervisorid');
                        }
                        else
                        {    
                            //return 'error/404.twig';
                        }
                    }
                    $themeid = $fdt->mustpost('theme');
                    $topicController->newTopic($title, $description, $supervisorid, $themeid);
                }
            }
            if($context->hasStudent())
            {
                $fdt = $context->formdata();
                $topics = array_keys($_POST);
                foreach($topics as $topic)
                {
                    $topicObj = $topicController->getTopicById($topic);
                    if($topicObj == null)
                    {
                        //return 'error/404.twig';
                    }
                    $choiceNo = $_POST[$topic];
                    $context->user()->userChoose($topicObj, $choiceNo);
                }
            }
            $context->divert('/theme', FALSE, '', FALSE);
        }

    }
?>