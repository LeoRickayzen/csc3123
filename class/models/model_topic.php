<?php
/**
 * A model class for the RedBean object topic
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for topic beans
 */
    class Model_Topic extends RedBean_SimpleModel
    {
    	public function getAllTopics(){
    		$topics = R::findAll("topic");
    		return $topics;
    	}

    	public function getTopic($topic){
    		$topics = R::findOne("topic", "name = '" . $topic . "'");
    		return $topics;
    	}
   
    }
?>