<?php
	class ThemeController{		
		public function getAllThemes()
		{
			return R::findAll("theme");
		}

		public function getTheme($theme)
		{
			return R::findOne('theme', 'name="' . $theme . '"');
		}

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