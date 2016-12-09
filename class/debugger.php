<?php
	class Debugger{
		public static function write($string){
			$file = fopen("debugger.txt", "w" );
			fwrite($file, $string);
			fclose($file);
		}
	}
?>