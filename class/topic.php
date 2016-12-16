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
 * Handle all url paths to this route
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
            $topicEditRoute = new Route('edit', 'POST');
            if ($topicPostRoute->isEqual($context->rest(), $_SERVER))
            {                
                return $this->postTopic($context);           
            }            
            if ($topicSpecificRoute->isEqual($context->rest(), $_SERVER))
            {
                $topic = substr($path, 7, sizeof($path));
            }
            if ($topicEditRoute->isEqual($context->rest(), $_SERVER))
            {
                return $this->editTopic($context);
            }
            return 'error/404.twig';
        }
/**
* Create a new topic via a post form, redirect the page back to the themes index
*
* @param object     $context    The context object for the site
*
* @return void
*/
        public function postTopic($context)
        {            
            $topicController = new TopicController();            
            if ($context->hasTL() || $context->hasSupervisor() || $context->hasAdmin() || $context->hasML())
            {
                $fdt = $context->formdata();
                if ($fdt->haspost('title') && $fdt->haspost('description'))
                {
                    $title = $fdt->mustpost('title');
                    $description = $fdt->mustpost('description');
                    $themeid = $fdt->mustpost('theme');
                    $topicController->newTopic($title, $description, $themeid);
                }
            }
            if($context->hasStudent())
            {
                $fdt = $context->formdata();
                $topics = array_keys($_POST);
                foreach ($topics as $topic)
                {
                    $topicObj = $topicController->getTopicById($topic);
                    if ($topicObj == null)
                    {
                        return 'error/form.twig';
                    }
                    $choiceNo = $_POST[$topic];
                    $context->user()->userChoose($topicObj, $choiceNo);
                }
            }
            $context->divert('/theme', FALSE, '', FALSE);
        }

/**
* edit a topic
*
* @param object     $context    The context object for this site
*
* @return void
*/
        public function editTopic($context)
        {
            $fdt = $context->formdata();
            if($context->hasML())
            {
                if ($fdt->haspost('topicid') && $fdt->haspost('topicdesc'))
                {
                    $topicid = $fdt->mustpost('topicid');
                    $topicdesc = $fdt->mustpost('topicdesc');
                    $topicController = new TopicController();
                    $topicController->editTopic($topicid, $topicdesc);
                    $context->divert('/theme', FALSE, '', FALSE);
                }
                else
                {
                    return 'error/form.twig';
                }
            }else{
                return 'error/403.twig';
            }
        }

    }
?>