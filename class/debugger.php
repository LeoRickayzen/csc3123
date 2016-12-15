<?php
/**
* A utility class that prints output to a text file, useful for debugging
*
* @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
*/
	class Debugger{
		/**
		* writes a string to a debugging file, static for ease, not to be used in production so OOP not a priority
		*
		* @param 	$string 	a string to write to the text file
		*
		* @return 	none
		*/
		public static function write($string){
			$file = fopen("debugger.txt", "w" );
			fwrite($file, $string);
			fclose($file);
		}
	}
?>