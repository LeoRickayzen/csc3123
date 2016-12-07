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
    class Studentchoose extends Siteaction
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

            $topics = array("");

            $topicLinks = array("all, ");

            $rest = $context->rest();

            $path = $this->routeBuilder($rest);

            $areaRoute = new Route('area', 'GET');
            $areaSpecificRoute = new Route('area/', 'GET');
            $topicChoiceRoute = new Route('topic', 'POST');
            $topicSpecificRoute = new Route('topic/', 'GET');

            //get all the areas
            if($areaRoute->isEqual($context->rest(), $_SERVER)){
                return "test1.twig";
            }

            if($areaSpecificRoute->isEqual($context->rest(), $_SERVER)){
                $area = substr($path, 7, sizeof($path));
                return "test2.twig";
                //return twig with topics within that specific area
            }

            //if route is /topic and it's post, set the topic choice for the user
            if($topicChoiceRoute->isEqual($context->rest(), $_SERVER)){
                $topicSelection = $_POST['topic'];
                //add the students topic choice
                //redirect
            }
            
            if($topicSpecificRoute->isEqual($context->rest(), $_SERVER)){
                return "test3.twig";
                $topic = substr($path, 7, sizeof($path));
                //return twig about specific topic
            }
            return "oops.twig";

        }

        public function routeBuilder($rest){
            $path = "";
            for($i = 0; $i < sizeof($rest); $i = $i + 1){
                if($i == sizeof($rest)-1){
                    $path = $path . $rest[$i];
                }else{    
                    $path = $path . $rest[$i] . '/';
                }
            }
            return $path;
        }

        public function requestType(){
            $methods = array('GET', 'PUT', 'POST', 'DELETE');

            for($i = 0; $i < sizeof($methods); $i++){
                if($_SERVER['REQUEST_METHOD'] === $methods[$i]){
                    return $methods[$i];
                }
            }
        }

        public function area(){

        }

        public function areaAll(){

        }

        public function areaModule(){

        }
    }
?>