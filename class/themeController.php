<?php
	class ThemeController{
		
		public function getAllThemes(){
			return R::findAll("theme");
		}

		public function getTheme($theme){
			return R::findOne('theme', 'name="' . $theme . '"');
		}

		public function newTheme($name, $leader){
			$theme = R::dispense('theme');
            $theme->name = $name;
            $theme->leader = $leader;
            R::store($theme);
		}

	}
?>