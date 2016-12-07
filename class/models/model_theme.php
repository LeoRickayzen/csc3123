<?php
/**
 * A model class for the RedBean object theme
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for area beans
 */
    class Model_Theme extends RedBean_SimpleModel
    {
    	public function getAllThemes(){
    		$themes = R::findAll("theme");
    		return $themes;
    	}

    	public function getTheme($theme){
    		$themes = R::findOne("theme", "name = '" . $theme . "'");
    		return $themes;
    	}

        public function makeTheme($themeName, $themeLeader){
            $theme = R::dispense('theme');
            $theme->name = $themeName;
            $theme->leader = $themeLeader;
            R::store($theme);
        }

        
    }
?>