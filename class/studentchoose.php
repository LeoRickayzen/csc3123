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

            $path = $rest[0];

            if ($rest[1] == 'area')
            {
                if($rest[0] == 'all')
                {
                    return 'test1.twg';
                }
                else
                {
                    return 'test2.twg';
                }
            }

        	return 'test3.twig'
            }
        }
    }
?>