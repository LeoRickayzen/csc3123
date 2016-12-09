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

            $rest = $context->rest();

            $path = Route::routeBuilder();

            $getAllocation = new Route('topic/', 'GET');

            $allocateTopic = new Route('topic', 'POST');

            if($getAllocation->isEqual($rest, $_SERVER)){

            }

            if($allocateTopic->isEqual($rest, $_SERVER)){
                Debugger::write("called");
                $this->allocateTopic($context);
            }
            return 'test3.twig';
        }

        public function allocateTopic($context){
            $fdt = $context->formdata();
            
            $topicid = $fdt->mustpost('topic');
            $studentid = $fdt->mustpost('studentid');

            $student = R::findOne('user', 'id = "' . $studentid . '"');

            $student->chosenTopic = $topidid;

            R::store($student);
        }

        public function getAllocations($context){

        }
    }
?>