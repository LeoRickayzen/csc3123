<?php
/**
 * A class that can be instantiated as a routing object
 * The class takes the path of the route from the base class it is called,
 * and the request method,
 * it can then be evaluated as being equal to the route that the user is accessing, make routing cleaner and more readable.
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 *
 */
	class Route
	{
		//path string
		private $path;
		//type of the request
		private $requestType;
		//does the request have a variable in the url, e.g no variable = 'theme', with variable = 'theme/cryptography'
		private $withVariable;
		//the length of the route
		private $length;
/**
* the constructor takes the desired path and request type, and stores this
*
* @param 	$path 	the string value of the path of this router object
* @param 	$requestType 	the request type of the route
*/
		function __construct($path, $requestType)
		{
			$this->path = $path;
			$this->requestType = $requestType;
			if(substr($path, -1) === '/')
			{
				$this->withVariable = true;
			}
			else
			{
				$this->withVariable = false;
			}
			$this->length = strlen($path);
		}
/**
* this method checks for equality between a called route in the context class and this route
*
* @param 	$rest 		An array of the parts of the url string
* @param 	$request 	A HTTP request Object ?
*
* @return 	Boolean 	The result of the equality evaluation
*/
		public function isEqual($rest, $requests)
		{
			$path = $this->routeBuilder($rest);
			Debugger::write($this->path . ' ' . $path);
			if($this->withVariable)
			{
				if($this->requestType($requests) === $this->requestType)
				{
                	return TRUE;
            	}
            	else
            	{
            		return FALSE;
            	}
			}
			else
			{
				if($this->requestType($requests) === $this->requestType && $path === $this->path)
				{
                	return TRUE;
            	}
            	else
            	{
            		return FALSE;
            	}
			}
		}
/**
* this returns a string value of the request type by checking the server request methods that have been set
*
* @param 	$requests 	The requests, checks for equality amongst these
*
* @return 	string 		The request type as a string value
*/
		private function requestType($requests){
            $methods = array('GET', 'PUT', 'POST', 'DELETE');

            for($i = 0; $i < sizeof($methods); $i++){
                if($requests['REQUEST_METHOD'] === $methods[$i]){
                    return $methods[$i];
                }
            }
        }
/**
* Builds the route from the rest object
* 
* @param 	$rest 	The rest object passed by the server in $context, found in a class extending siteaction
*
* @return 	string 	The request path starting from after the page, e.g for page = 'theme' and route is 'www.webproject.com/theme/allocate/bio-computing', the return value would be 'allocate/'
*/
        public static function routeBuilder($rest)
        {
            $path = "";
            for($i = 0; $i < sizeof($rest); $i = $i + 1)
            {
                if($i == sizeof($rest)-1)
                {
                    $path = $path . $rest[$i];
                }
                else
                {    
                    $path = $path . $rest[$i] . '/';
                }
            }
            return $path;
        }
	}
?>