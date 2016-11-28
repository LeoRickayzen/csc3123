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

            $topicLinks = array("");

            $rest = $context->rest();

            $path = $rest[0];

            if ($rest[0] == 'area'){

            }
            if ($rest[0] == 'topics' & rest[2] == 'area'){
                for($i = 0; $i < $topics.length(); $i++){

                }
            }

        	return 'something.twig'
            }
        }
    }
?>