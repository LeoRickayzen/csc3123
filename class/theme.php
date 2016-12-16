<?php
/**
 * A class that contains code to handle any requests for the student choices page
 *
 * @author Leo Rickayzen <l.rickayzen1@ncl.ac.uk>
 *
 */
/**
 * Support / or /home
 */
    class Theme extends Siteaction
    {
/**
 * Handle hall routes throug the rought object
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
        public function handle($context)
        {
            $rest = $context->rest();
            $path = Route::routeBuilder($rest);
            $themeRoute = new Route('', 'GET');
            $themePostRoute = new Route('', 'POST');
            $themeSpecificRoute = new Route('/', 'GET');
            $editTheme = new Route('edit', 'POST');
            if ($themeRoute->isEqual($context->rest(), $_SERVER))
            {
                return $this->getThemes($context);
            }
            if ($themePostRoute->isEqual($context->rest(), $_SERVER))
            {
                $this->postTheme($context);
            }
            if ($themeSpecificRoute->isEqual($context->rest(), $_SERVER))
            {
                return $this->getTheme($context);
            }
            return 'error/404.twig';
        }
/**
* Get the all the themes and return a twig view of them all
* 
* @param object     $context    The context object for the site
*
* @return string template name
*/
        public function getThemes($context)
        {
            $themeController = new ThemeController();
            $themes = $themeController->getAllThemes();
            $context->local()->addval('themes', $themes);            
            $context->local()->addval('topicChoices', $context->user()->userChoices());            
            if ($context->hasStudent())
            {
                return 'studentViews/themes.twig';
            }
            if ($context->hasML())
            {
                return 'moduleLeaderViews/themes.twig';
            }
            if ($context->hasTL())
            {
                return 'themeLeaderViews/themes.twig';
            }
            if ($context->hasSupervisor())
            {
                return 'supervisorViews/themes.twig';
            }
            return 'error/403.twig';
        }

/**
* get a specific theme through a rest route theme/{themename}
* 
* @param object     $context    The context object for the site
*
* @return string template name
*/
        public function getTheme($context)
        {
            $themeController = new ThemeController();
            $topicController = new TopicController();
            $theme = Route::routeBuilder($context->rest());
            $themeObj = $themeController->getTheme($theme);
            if ($themeObj == null)
            {
                return 'error/404.twig';
            }
            $context->local()->addval('theme', $themeObj->id);
            $topics = $topicController->getTopicByTheme($themeObj->id);
            $context->local()->addval('topics', $topics);
            $context->local()->addval('topicChoices', $context->user()->userChoices());
            if ($context->hasStudent())
            {   
                return 'studentViews/topics.twig';
            }
            if ($context->hasML())
            {
                return 'moduleLeaderViews/topics.twig';
            }
            if ($context->hasSupervisor() || $context->hasTL())
            {
                if ($context->hasSupervisor())
                {
                    return 'supervisorViews/topics.twig';
                }
                if ($context->hasTL())
                {
                    if($context->user()->hasTheme($theme, $context->user()->id))
                    {
                        return 'themeLeaderViews/topics.twig';
                    }
                    else
                    {
                        return 'themeLeaderViews/topicExplore.twig';
                    }
                }
            }
            else
            {
                return 'error/403.twig';
            }
        }
/**
* Create a new theme
*
* @param object     $context    The context object for this site
*
* @return void
*/
        public function postTheme($context)
        {
            $themeController = new ThemeController();
            $fdt = $context->formdata();            
            if ($fdt->haspost('name') && $fdt->haspost('TLemail'))
            {      
                $name = $fdt->mustpost('name');
                $leader = $fdt->mustpost('TLemail');
            }
            else
            {
                return 'error/form.twig';
            }
            $themeController->newTheme($name, $leader);
            Debugger::write('130');
            $context->divert('/home', FALSE, '', FALSE);
        }
    }
?>