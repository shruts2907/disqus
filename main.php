#!/usr/bin/php
<?php

include("SpellChecker.php");
include("CreateMistake.php");

$spell_check = new SpellCheck();
echo "\n type extra for second program or enter any word to get suggestion \n";
while(true){
	if(count($argv) >= 0){
		
		echo '>';
		$f = fopen('php://stdin', 'r');
		$line = fgets($f) ;
		//echo "line : ".$line;
			if(trim($line) === "extra"){
			  //echo "in here ";
				$create = new CreatMistake();
			
				$dict = $spell_check->get_dictionary();
			
					$rand = rand(0, 6);
					
					$rand_str = $create->mashup($dict[$rand]);
					
					$result = $spell_check->google(trim($rand_str));
					if($result == 'NO SUGGESTION') {
						echo ' No Suggestion!'."\n";
						exit;
					}else{
					
						//echo "\n".$result."\n";
					}
			}else{
				
				$result = $spell_check->google(trim($line));
			}
		echo "\n".$result."\n";
		
	}//end of if

}


?>
