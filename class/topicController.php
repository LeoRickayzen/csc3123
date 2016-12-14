<?php
	class TopicController{

		public function getTopicById($id)
		{
			$themetopic = R::findOne('theme_topic', 'topic_id = "' . $id . '"');

			$topic = R::findOne('topic', "id ='" . $id . "'");
			$topic->themeId = $themetopic->theme_id;
			return $topic;
		}

		public function getTopicByTheme($themeid)
		{
            $topicIDs = R::findAll('theme_topic', 'theme_id = "' . $themeid . '"');
            $topics = [];
            foreach($topicIDs as $topicID)
            {
                $topic = R::findOne('topic', 'id = "' . $topicID->topic_id . '"');
                $topics[] = $topic;
            }
            return $topics;
		}

		public function newTopic($title, $description, $supervisorid, $themeid)
		{
			$topic = R::dispense('topic');
			$topic->title = $title;
			$topic->description = $description;
			$topic->supervisor = R::findOne('user', 'id = "' . $supervisorid . '"');
			$theme = R::findOne("theme", "id = '" . $themeid . "'");	
			$theme->sharedTopic[] = $topic;
            $topic->sharedTheme[] = $theme;
            R::store($theme);
            R::store($topic);
		}
	}
?>