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
                $area = substr($path, 7, sizeof($path));
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

            return 'themes.twig';
        }

        public function getTheme($context){
            $path = Route::routeBuilder($context->route());
            $area = substr($path, 7, sizeof($path));
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

                $topic->ownTheme[] = $theme;

                R::store($topic);
            }
            if($context->hasStudent()){
                $fdt = $context->formdata();

                $topic = R::findOne('topic', "id ='" . $fdt->mustpost('topicid') . "'");

                $topic->students[] = $context->user();

                R::store($topic);
            }
            return 'test3.twig';
        }

        public function postTheme($context){

            if($context->hasTL()){

            }else{

            }

            $fdt = $context->formdata();
            
            $name = $fdt->mustpost('name');
            
            $theme = R::dispense('theme');
            $theme->name = $name;
            $theme->leader = $context->user();
            
            $id = R::store($theme);
        }

    }
?>