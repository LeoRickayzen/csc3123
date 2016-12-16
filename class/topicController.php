<?php
/**
 * A class that controls topic aspects of the database,
 * I wanted to put this and the other controller classes in a controller directory, but couldn't figure out how to configure the classpath in php so that it still worked, If you know how, please write in the feedback?
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 *
 */
	class TopicController{
/**
* get a topic by it's id
*
* @param 	$id 	the id of the topic
*
* @return 	Object 	the topic object
*/
		public function getTopicById($id)
		{
			$themetopic = R::findOne('theme_topic', 'topic_id = "' . $id . '"');

			$topic = R::findOne('topic', "id ='" . $id . "'");
			$topic->themeId = $themetopic->theme_id;
			return $topic;
		}
/**
* get a topics by the id of the theme(s) they belong to
*
* @param 	$themeid 	the id of the theme that the topic belongs to
*
* @return 	Object 		a list of the topic objects that belong to a certian theme
*/
		public function getTopicByTheme($themeid)
		{
            $topicIDs = R::findAll('theme_topic', 'theme_id = "' . $themeid . '"');
            $topics = [];
            foreach ($topicIDs as $topicID)
            {
                $topic = R::findOne('topic', 'id = "' . $topicID->topic_id . '"');
                $topics[] = $topic;
            }
            return $topics;
		}
/**
* insert a new topic
*
* @param 	$title 			the string value of the topic title
* @param 	$description 	the string value of the description
* @param 	$supervisorid 	the value of the supervisor of that topic
* @param 	$themeid 		the value of the theme id that the topic belongs to
*
*/
		public function newTopic($title, $description, $themeid)
		{
			$topic = R::dispense('topic');
			$topic->title = $title;
			$topic->description = $description;
			$theme = R::findOne("theme", "id = '" . $themeid . "'");	
			$theme->sharedTopic[] = $topic;
            $topic->sharedTheme[] = $theme;
            R::store($theme);
            R::store($topic);
		}
/**
* edit a topic
*
* @param 	$topicid	the id of the theme
* @param 	$topicdescription 	the description of the theme
*
* @return 	void
*/
		public function editTopic($topicid, $topicdescription)
		{
			$topic = R::findOne('topic', 'id = "' . $topicid . '"');
			if($topic != null){
				$topic->description = $topicdescription;
				R::store($topic);
			}
		}
	}
?>