<?php
	class Route
	{
		private $path;
		private $requestType;
		private $withVariable;
		private $length;
		
		function __construct($path, $requestType){
			$this->path = $path;
			$this->requestType = $requestType;
			if(substr($path, -1) === '/'){
				$this->withVariable = true;
			}else{
				$this->withVariable = false;
			}
			$this->length = strlen($path);
		}

		public function isEqual($rest, $requests){
			$path = $this->routeBuilder($rest);
			Debugger::write(substr($path, 0, $this->length) . ' ' . $this->path);
			if($this->withVariable){
				if($this->requestType($requests) === $this->requestType){
                	return TRUE;
            	}else{
            		return FALSE;
            	}
			}else{
				if(strlen($path) == $this->length && $this->requestType($requests) === $this->requestType){
                	return TRUE;
            	}else{
            		return FALSE;
            	}
			}
		}

		private function requestType($requests){
            $methods = array('GET', 'PUT', 'POST', 'DELETE');

            for($i = 0; $i < sizeof($methods); $i++){
                if($requests['REQUEST_METHOD'] === $methods[$i]){
                    return $methods[$i];
                }
            }
        }

        public static function routeBuilder($rest){
            $path = "";
            for($i = 0; $i < sizeof($rest); $i = $i + 1){
                if($i == sizeof($rest)-1){
                    $path = $path . $rest[$i];
                }else{    
                    $path = $path . $rest[$i] . '/';
                }
            }
            return $path;
        }
	}
?>