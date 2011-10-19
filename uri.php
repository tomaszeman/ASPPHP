<?php
/*
* TERMS OF USE
* This code is free for anyone to download and use/edit. The code is not guaranteed to work 
* properly in all use cases (or at all really). It was developed with a loose set of use cases in 
* mind, and it meets all of those. If for some reason you find a case where the code does not 
* function as you would like, feel free to modify it for your own purposes.
*/
/**
* Uri class
* This class contains functions to construct and handle a Uri piece by piece. It can be very helpful for 
* cases where you may want to generate a long Uri but then modify only small parts of it. 
* 
* @author BriceOlion
* @package gen_lib
*/
class Uri
{

   /**#@+
   * @access protected
   * @var string
   */
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * protocol is http   
   */
   protected $protocol; //HTTP, HTTPS, FTP

   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * hostname is video.google.co.uk
   */
   protected $hostname; 
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * path is /videoplay
   */
   protected $path;   
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * fragment is 00h02m30s
   */
   protected $fragment;   
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * subdomain is video
   * (not currently used)
   */
   protected $subdomain;
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * topLevelDomain is uk
   */
   protected $topLevelDomain;
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * secondLevelDomain is co.uk
   */
   protected $secondLevelDomain;
      
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * port is 80
   */
   protected $port; 
   /**#@-*/
   
   /** 
   * In the Uri: http://video.google.co.uk:80/videoplay?docid=-7246927612831078230&hl=en#00h02m30s
   * parameters are docid, hl   
   * @var array
   */
   protected $parameters;
   
   /**#@+
   * @access protected
   */
   /**
   * Build the Uri object. The parts that are required are the minimum pieces required to form a valid Uri. 
   * 
   * @param string $protocol
   * @param string $hostname
   * @param string $path optional
   * @param string $parameters optional
   * @param string $fragement optional
   * @param string $port optional
   */
   public function __construct($protocol, $hostname, $path=NULL, $parameters=NULL, $fragment=NULL, $port=NULL){
      $this->parameters = array();
      $parameters = (is_array($parameters) ? $parameters : $this->parseQueryString($parameters));
      $this->setProtocol($protocol);
      $this->setHostname($hostname);
      $this->setPath($path);
      $this->addParameters($parameters);
      $this->setFragment($fragment);
      $this->setPort($port);
   }

   /**
   *Static method that will use the PHP $_SERVER superglobal to try to construct the 
   *Uri that the code is currently running from. 
   * 
   **Note if you are using the Zend Framework, or some other framework that implements 
   *the Front Controller pattern, the Uri::getCurrentUri() function may not return the 
   *result you are expecting, make sure to test before depending on this function.
   *
   * @static
   * @return Uri   
   */
   public static function getCurrentUri(){
      $host = $_SERVER['SERVER_NAME'];
      //$protocol = isset($_SERVER['HTTPS']) ? "https" : "http";
		$protocol = "http";
      $path = $_SERVER['PHP_SELF'];
      $queryString = @$_SERVER['QUERY_STRING'];
      $url = new Uri($protocol, $host, $path, $queryString);
      return $url;
   }

	public static function parse($url)
	{
		$parts= Uri::explodeUrl($url);
		$url = new Uri(
			array_key_exists("scheme", $parts) ? $parts["scheme"] : "http", 
			$parts["host"], 
			array_key_exists("path", $parts) ? $parts["path"] : null, 
			array_key_exists("query", $parts) ? $parts["query"] : null, 
			array_key_exists("fragment", $parts) ? $parts["fragment"] : null, 
			array_key_exists("port", $parts) ? $parts["port"] : null);
		return $url;
   }	

	private static function explodeUrl($url) {
		$r  = "(?:([a-z0-9+-._]+)://)?";
		$r .= "(?:";
		$r .=   "(?:((?:[a-z0-9-._~!$&'()*+,;=:]|%[0-9a-f]{2})*)@)?";
		$r .=   "(?:\[((?:[a-z0-9:])*)\])?";
		$r .=   "((?:[a-z0-9-._~!$&'()*+,;=]|%[0-9a-f]{2})*)";
		$r .=   "(?::(\d*))?";
		$r .=   "(/(?:[a-z0-9-._~!$&'()*+,;=:@/]|%[0-9a-f]{2})*)?";
		$r .=   "|";
		$r .=   "(/?";
		$r .=     "(?:[a-z0-9-._~!$&'()*+,;=:@]|%[0-9a-f]{2})+";
		$r .=     "(?:[a-z0-9-._~!$&'()*+,;=:@\/]|%[0-9a-f]{2})*";
		$r .=    ")?";
		$r .= ")";
		$r .= "(?:\?((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9a-f]{2})*))?";
		$r .= "(?:#((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9a-f]{2})*))?";
		preg_match("`$r`i", $url, $match);
		$parts = array(
				"scheme"=>'',
				"userinfo"=>'',
				"authority"=>'',
				"host"=> '',
				"port"=>'',
				"path"=>'',
				"query"=>'',
				"fragment"=>'');
		switch (count ($match)) {
			case 10: $parts['fragment'] = $match[9];
			case 9: $parts['query'] = $match[8];
			case 8: $parts['path'] =  $match[7];
			case 7: $parts['path'] =  $match[6] . $parts['path'];
			case 6: $parts['port'] =  $match[5];
			case 5: $parts['host'] =  $match[3]?"[".$match[3]."]":$match[4];
			case 4: $parts['userinfo'] =  $match[2];
			case 3: $parts['scheme'] =  $match[1];
		}
		$parts['authority'] = ($parts['userinfo']?$parts['userinfo']."@":"").
			$parts['host'].
			($parts['port']?":".$parts['port']:"");
		return $parts;
	}
	

   /**
   * Define the toString magic function for this object to be an alias for getUri
   * @see function getUri
   * @return string
   */
   public function __toString(){
		return $this->toString();
   }
   
   /**
   * Reconstitute the individual pieces of the Uri into a string.
   * @return string
   */
   public function toString(){
      $url = "";
	if(!empty($this->protocol))
		$url .= $this->protocol."://";
      $url .= $this->hostname;
      $url .= $this->path;
      if(count($this->parameters) > 0){
         $url .= "?";
         $count = 0;
         foreach($this->parameters as $key => $val){
            $count ++;
            $url .= "$key=$val".($count == count($this->parameters) ? "" : "&");
         }      
      }
      if($this->fragment){
         $url .= "#$this->fragment";
      }
      return $url;
   }
   
   /**
   *Set the protocol for the url. This function will accept any string, meaning that it isn't
   *limited to only accepting http or https as protocols (could use FTP for instance). 
   * 
   *@see Uri::$protocol
   *@param string $protocol
   */
   public function setProtocol($protocol){
      $this->protocol = $protocol;
   }

   /**
   *Set the hostname for the Uri
   *@see Uri::$hostname
   *@param string $host
   *@todo: break the hostname down into other info: subdomain, topLevelDomain, secondLevelDomain
   */
   public function setHostname($host){
      $this->hostname = $host;      
   }

   /**
   *Set the path for the Uri
   *@see Uri::$path 
   *@param string $path
   */
   public function setPath($path){
      $this->path = $path;
   }

   /**
   *Return the full path from the Uri
   *@see Uri::$path
   *@return string
   */
   public function getPath(){
      return $this->path;
   }
   
   /**
   * Set the port number for the Uri 
   * @param mixed $port This will work as a string or a number
   */
   public function setPort($port){
      $this->port = is_numeric($port) ? $port : NULL;
   }

   /**
   * Set the fragment for the Uri 
   * @see Uri::$fragment
   * @param string $fragment
   */
   public function setFragment($fragment){
      $this->fragment = $fragment;
   }
   
   /** 
   * Add a value to the internal parameters array. Parameters will 
   * be displayed in the url as $param=$val&$param=$val......
   *
   *@see Uri::$parameters
   *@param string $param The index of the internal array. Because of this, repeating a parameter will overwrite the previous value.
   *@param string $val Value of the parameter
   */
   public function addParameter($param, $val){
      $this->parameters[$param] = $val;
   }
   
   /**
   *Takes an array of name/value pairs and passes them to the addParameter function. 
   *
   *@see function addParameter 
   *@param array $params The array should be built as an associative array: array("param"=>"val");
   */
   public function addParameters($params){
      if(is_array($params)){
         foreach($params as $key=>$val){
            $this->parameters[$key] = $val;
         }
      }
   }

   /**
   *Get the array of parameters.
   *@see Uri::$parameters
   *@return array
   */
   public function getParameters(){
      return $this->parameters;
   }

   /**
   *Remove a parameter from the Uri. This is done by unsetting the element of the internal parameter array
   *whose index is "$param".
   *@see Uri::$parameters
   *@param string $param name of parameter to be removed
   */
   public function removeParameter($param){
      if(isset($this->parameters[$param])){
         unset($this->parameters[$param]);
      }
   }
   
   /**
   *Remove all parameters from the Uri. 
   *
   *@see Uri::$parameters
   *@param string $param name of parameter to be removed
   */
   public function removeParameters(){
      $this->parameters = array();
   }
   /**#@-*/

   /**
   *Internal function to handle breaking the querystring of a url into pieces and adding them to 
   *the parameters array. 
   * 
   *@param string $string
   *@access private
   */
   private function parseQueryString($string){
      $params = array();
      $pairs = explode("&", $string);
      foreach($pairs as $pair){
         $param = explode("=",$pair);
         if(isset($param[0],$param[1])){
            $params[$param[0]] = $param[1];
         }
      }
      return $params;
   }

}
?>