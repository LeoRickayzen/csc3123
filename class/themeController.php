<?php
/**
 * A class that controls theme aspects of the database,
 * I wanted to put this and the other controller classes in a controller directory, but couldn't figure out how to configure the classpath in php so that it still worked, If you know how, please write in the feedback?
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 *
 */
	class ThemeController{
/**
* get all the themes from the database
*
* @return 	Object 		A list of all the theme bean objects
*/		
		public function getAllThemes()
		{
			return R::findAll("theme");
		}
/**
* get a theme object by it's name
*
* @param 	$theme 		the string value of the theme name
*
* @return   Object 		the theme object with that theme name
*/
		public function getTheme($theme)
		{
			return R::findOne('theme', 'name="' . $theme . '"');
		}
/**
* create a new theme
*
* @param 	$name 	the name of the new theme
* @param 	$leaderid 	the id of the theme leader
*
* @return 	none
*/
		public function newTheme($name, $leaderid)
		{
			$leader = R::findOne('user', 'id = "' . $leaderid . '"');
			$theme = R::dispense('theme');
            $theme->name = $name;
            $theme->sharedLeader = $leader;
            $leader->sharedTheme = $theme;
            R::store($theme);
            R::store($leader);
		}
	}
?>