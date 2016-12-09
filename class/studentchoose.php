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

            $path = Route::routeBuilder($rest);

            $themeRoute = new Route('theme', 'GET');
            $themeSpecificRoute = new Route('theme/', 'GET');
            $themePostRoute = new Route('theme', 'POST');
            $topicChoiceRoute = new Route('topic', 'POST');
            $topicSpecificRoute = new Route('topic/', 'GET');

            //get all the areas
            if($themeRoute->isEqual($context->rest(), $_SERVER)){
                return $this->getThemes($context);
            }

            if($themePostRoute->isEqual($context->rest(), $_SERVER)){
                $this->postTheme($context);
                return "test3.twig";
            }

            if($themeSpecificRoute->isEqual($context->rest(), $_SERVER)){
                return $this->getTheme($context);
                //return twig with topics within that specific area
            }

            //if route is /topic and it's post, set the topic choice for the user
            if($topicChoiceRoute->isEqual($context->rest(), $_SERVER)){
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

        public function getThemes($context){
            $themes = R::findAll("theme");

            $context->local()->addval('themes', $themes);

            return 'studentViews/themes.twig';
        }

        public function getTheme($context){
            $path = Route::routeBuilder($context->rest());
            
            $theme = substr($path, 6, strlen($path));
            
            $themeObj = R::findOne('theme', 'name="' . $theme . '"');
            
            $topicIDs = R::findAll('theme_topic', 'theme_id = "' . $themeObj->id . '"');

            $topics = [];

            //Debugger::write($topicIDs);

            foreach($topicIDs as $topicID){
                $topic = R::findOne('topic', 'id = "' . $topicID->topic_id . '"');
                $topics[] = $topic;
            }

            $context->local()->addval('topics', $topics);

            $context->local()->addval('topicChoices', $context->user()->userChoices());

            return 'studentViews/topics.twig';
        }

        public function postTopic($context){
            if($context->hasTL() || $context->hasSupervisor() ||$context->hasAdmin()){
                $fdt = $context->formdata();
                
                $topic = R::dispense('topic');

                $topic->title = $fdt->mustpost('title');
                
                $topic->description = $fdt->mustpost('description');
                
                if($context->hasSupervisor()){
                    $topic->supervisor = $context->user();
                }else{
                    if($fdt->haspost('supervisorid')){
                        $topic->supervisor = R::findOne("user", "id = '" . $fdt->mustpost('supervisorid') . "'");
                    }
                }

                $theme = R::findOne("theme", "id = '" . $fdt->mustpost('themeid') . "'");
                
                $theme->sharedTopic[] = $topic;
                $topic->sharedTheme[] = $theme;

                R::store($theme);
                R::store($topic);
            }
            if($context->hasStudent()){
                $fdt = $context->formdata();

                $topic = R::findOne('topic', "id ='" . $fdt->mustpost('topicid') . "'");

                $choiceNo = $fdt->mustpost('choiceNo');

                $context->user()->userChoose($topic, $choiceNo);
                return 'test1.twig';
            }
            return 'test3.twig';
        }

        public function postTheme($context){

            $fdt = $context->formdata();
            
            $name = $fdt->mustpost('name');
            
            $theme = R::dispense('theme');
            $theme->name = $name;
            $theme->leader = $context->user();
            
            $id = R::store($theme);
        }

    }
?>