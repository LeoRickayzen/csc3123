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
        }

        public function allocateTopic($context)
        {
            $fdt = $context->formdata();

            $formins = explode(',', $fdt->mustpost('topicStudent'));

            $topicid = $formins[0];
            
            $studentid = $formins[1];

            Debugger::write($topicid . ' ' . $studentid);

            $student = R::findOne('user', 'id = "' . $studentid . '"');

            $student->allocatedTopic = $topicid;

            R::store($student);
        }

        public function getAllocations($context)
        {
            $studentsRoles = R::findAll('role', 'rolename_id = "3"');
            
            $students = [];
            
            foreach($studentsRoles as $studentRole)
            {
                
                $id = $studentRole->user_id;
                
                $student = R::findOne('user', 'id = "' . $id . '"');
                
                $choicesids = R::findAll('userchoice_topic', 'user_id = "' . $student->id . '"');
                
                $choices = [];

                for($i = 0; $i < 10; $i++){
                    $choices[$i] = 'no preference';
                }

                foreach($choicesids as $choice)
                {
                    $topic = R::findOne('topic', 'id = "' . $choice->topicId . '"');
                    $choiceNumber = $choice->choice_num;
                    $topic->choiceNumber = $choiceNumber;
                    $choices[$choiceNumber] = $topic;                
                }
                
                $student->ownChoices = $choices;
                
                $students[] = $student;
            
            }
            
            return $students;
        }
    }
?>