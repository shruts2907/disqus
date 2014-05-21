<?php

/**
 * 
 * Spellchecker class which fixes different 
 * spelling mistakes.

 *
 */
class SpellCheck {
    
	/**
	 * to store the words from the file in the array
	 * @var Array
	 */
    private $dict = array();
    
    /**
     * Filepath to dictionary
     * @var string
     */
    private $dict_file = 'big.txt';
    
    
    
    /**
     * Constructor Loads dictionary by default whe the object of class is created
     * 
     */
    public function __construct() {
        $this->dict = $this->extract_words();
    }
    
    /**
     * Retuns dictionary loaded into memory
     * @return Array
     */
    public function get_dictionary() {
        return $this->dict;
    }
	
	/**
     * Extracting the file contents from the file
     * @return Array
     */
    private function extract_words() {
        $handler = fopen($this->dict_file, 'r');
        $contents = fread($handler, filesize($this->dict_file));
        fclose($handler);
        
        return explode("\n", trim($contents));
    }
    
    
    /**
     * 
     * Fix upper/lower case errors
     * @param string $in input string given by user
     * @return string corrected string, or empty string if none is found.
     */
    public function fix_case($in) {
		//array_map conversts all contect of dict array into lowercase using call
		//back function strlower
		//array search gives the index of the dict array by searching it with $in string
    	$key = array_search(strtolower($in), array_map('strtolower', $this->dict));
		
    	if(!$key) {
    		return '';
    	} else {
    		return($this->dict[$key]);
    	}
    }
    
    
    /**
     * Fix repeated letter errors
     * @param string $in
     * @return string corrected string, or empty string if none is found.
     */
    public function fix_repeats($in) {
    	$key = array_search(self::remove_duplicates($in), array_map( 'self::remove_duplicates', $this->dict));
		
    	if(!$key) {
    		return '';
    	} else {
    		return($this->dict[$key]);
    	}
    }

    /**
     * 
     * Removes duplicate characters from a string
     * @param string $in
     */
    public function remove_duplicates($in) {
    	//returns unique array preserving the keys
		
    	return implode('',array_unique(str_split($in)));
    	
    }
    
	/**
     * 
     * Fix incorrect vowels
     * @param string $in
     * @return string corrected string, or empty string if none is found.
     */
    public function fix_vowels($in) {
    	$indixes = array_keys(array_map( 'self::strip_vowels', $this->dict), $this->strip_vowels($in));
    	
    	//array keys give subset of keys to check which is best possible key to the string use suggest function.
    	$key = $this->suggest($in, $indixes);
    	
		($key === 0 )?$key = 'zero': $key;
    	if(!$key) {
    		return '';
    	} else {
		
			$key = ($key === 'zero')?0:$key;
    		return($this->dict[$key]);
    	}
    }
	
	/**
     * Remove vowels from string 
     * 
     * @param string $in
     * @return consonant only string
     */
    private function strip_vowels($in) {
	
    	$vowels = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'); 
    	return $consonants = str_replace($vowels, '', $in);
    }
    
    
    /**
     * 
     * Combination of all spelling fixes
     * @param string $in
     */
    public function fix_all($in) {
    	
    	$ip_str = $this->strip_vowels($this->remove_duplicates(strtolower($in)));
    	
    	$tmp_arr = array_map('strtolower', $this->dict);
    	$tmp_arr = array_map( 'self::remove_duplicates', $tmp_arr);
    	$tmp_arr = array_map( 'self::strip_vowels', $tmp_arr);
    	
    	$indixes = array_keys( $tmp_arr,$ip_str);
		
		$len = count($tmp_arr)-1;
		while ( $len>-1) {
		   
			if (trim(current($tmp_arr)) == $ip_str) {
				
				array_push($indixes,key($tmp_arr));
				
			}else{
				//	echo " no  match ";
			}
			$len--;
			next($tmp_arr);
		}
		
    	//now if we have a number of suggestions we need to reduce to the best.
    	$key = $this->suggest($in, $indixes);
    	
		($key === 0)? $key = "zero":$key;
		//die();
    	if(!$key) {
    		return '';
    	} else {
		
		$key = ($key === "zero")?0: $key;
    		return($this->dict[$key]);
    	}
    }
    
    
    /**
     * Fix spelling by chaining all functions
     * @param string $in
     * @return string fixed string
     */
    public function google($in) {
    	$result = 'NO SUGGESTION'; //prove otherwise
    	
    	//first check if we should attempt to correct
    	if(in_array($in, $this->dict, TRUE)) {
    		return $in;
    	}
    	
    	$check = $this->fix_case($in);
    	if(!empty($check)) {
			
    		return $check;
    	}
    	
    	$check = $this->fix_repeats($in);
    	if(!empty($check)) {
			
    		return $check;
    	}
    	
    	$check = $this->fix_vowels($in);
    	if(!empty($check)) {
			
    		return $check;
    	}
    	
    	//combo of all fixes
    	$check = $this->fix_all($in);
		
    	if(!empty($check)) {
			
    		return $check;
    	}
    	
    	//if all the above condition fail return no suggesstion
		//echo " should be no suggestion ".$result;
    	return $result;
    }


    /**
     * 
     * Takes an array of indexes and returns
     * the best index match from the dictionary
     * 
     * @param string original search term
     * @param Array $indixes possible indexes to match on
     * @return integer
     */
    private function suggest($in, $indixes) {
    	
    	$count = 0;
    	$index = -1;
		$res="";
		
    	foreach($indixes as $key=>$index) {
    		
			//strsp returns number of characters matched in the string
			$count_similar = strspn($this->dict[$index], $in);
    		
    		if($count_similar >= $count) {
    			$count = $count_similar;
				$res = $index;
    		}
			
    	}
		
    	return $res;
    }

    
    
    
}

?>
