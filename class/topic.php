<?php
/**
 * A class that contains code to handle any requests for the student choices page
 *
 * @author Leo Rickayzen <l.rickayzen1@ncl.ac.uk>
 * @copyright 2012-2013 Newcastle University
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

        public function handle($context)
        {

            $rest = $context->rest();

            $path = Route::routeBuilder($rest);

            $topicPostRoute = new Route('', 'POST');
            $topicSpecificRoute = new Route('/', 'GET');

            //if route is /topic and it's post, set the topic choice for the user
            if($topicPostRoute->isEqual($context->rest(), $_SERVER)){
                return $this->postTopic($context);
                //add the students topic choice
                //redirect
            }
            
            if($topicSpecificRoute->isEqual($context->rest(), $_SERVER)){
                $topic = substr($path, 7, sizeof($path));
                //return twig about specific topic
            }
            return "oops.twig";

        }

        public function postTopic($context){
            
            $topicController = new TopicController();
            
            if($context->hasTL() || $context->hasSupervisor() || $context->hasAdmin() || $context->hasML()){

                $fdt = $context->formdata();

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
                }

                $themeid = $fdt->mustpost('theme');

                $topicController->newTopic($title, $description, $supervisorid, $themeid);

            }
            if($context->hasStudent()){
                $fdt = $context->formdata();

                $topics = array_keys($_POST);

                foreach($topics as $topic){
                    $topicObj = $topicController->getTopicById($topic);

                    $choiceNo = $_POST[$topic];

                    $context->user()->userChoose($topicObj, $choiceNo);
                }
            }
            return 'test3.twig';
        }

    }
?>