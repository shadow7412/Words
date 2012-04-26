<?php
$dictionary = "linux.words";
if(!file_exists($dictionary)) die("Please check dictionary settings.");

function error_function($level, $message, $file, $line, $context){
	echo $message;
	die(); //Only show an error once (much better than iterating through the whole dictionary and printing 500,000 errors
}

//set_error_handler("error_function");

if(empty($_POST['action'])){
?>
<form action="." method="post" target="output" onsubmit="document.getElementById('output').contentDocument.body.innerHTML='Loading...';">
<label for="word">Word:<input type="text" name="word"/></label><br/>
<label><input type="radio" name="action" value="in" checked="checked">Find words that contain this word</label><br/>
<label><input type="radio" name="action" value="rex">Use <a href="http://www.regular-expressions.info/reference.html">regular expressions,</a> to find words</label><br/>
<label><input type="radio" name="action" value="out">Find words within this word (slow and broken)</label><br/>
<label><input type="radio" name="action" value="ana">Find anagrams of this word</label><br/>
<input type="submit"/>
</form>
<iframe id="output" name="output" height="100%" width="100%" style="border:none;"/>
<?php
} else {
	$file = file($dictionary);
	$results = array();
	$words = array();
	$word = $_POST['word'];
	if(@$word[2]==null)die("Not enough characters");
	switch($_POST['action']){
		case('in'):
			foreach($file as $test){
				if(strpos($test,$word)!==FALSE)
				$words[] = $test;
			}
			break;
		case('rex'):
			if($word[0]!="/")
				$word = "/$word/"; //add regex quotes if they aren't present
			foreach($file as $test){
				if(preg_match($word,$test)==1)
					$words[] = $test;
			}
			break;
		case('ana'):
			$anagrams = true;
		case('out'):
			$word = str_replace(' ','',strtolower($word));
			$alpha = 'qwertyuiopasdfghjklzxcvbnm';
			$alphalen = strlen($alpha);
			//create blank word array
			$blank = array();
			for($i=0;$i!=$alphalen;$i++)
				$blank[$alpha[$i]] = 0;
			$wordmap = $blank;
			$wordlen = strlen($word);
			for($i=0;$i!=$wordlen;$i++){
				@$wordmap[$word[$i]]++;
			}

			//Now test dictionary words
			$testmap = array();
			foreach($file as $test){
				$test = trim(strtolower($test));
				$testlen = strlen($test);
				//if not enough letters, then it can't be.
				if((isset($anagrams) && $testlen != $wordlen) || (!isset($anagrams) && $testlen>$wordlen)) continue;
				$testmap = $blank;
				for($i=0;$i!=$wordlen;$i++){
					if(@isset($testmap[$test[$i]]))
						$testmap[$test[$i]]++;
					else
						continue 2;
				}
				
				if(isset($anagrams)){
					for($i=0; $i!=$alphalen; $i++){
						if($testmap[$alpha[$i]] != $wordmap[$alpha[$i]]) continue(2);
					}
				} else {
					for($i=0; $i!=$alphalen; $i++){
						if($testmap[$alpha[$i]] < $wordmap[$alpha[$i]]) continue(2);//flawed
					}
				}
				$words[] = $test;
			}
			break;
		default:
			$words[] = "A confusion was found.";
	}
	if(@$words[0]==null) echo "None found.";
	else echo implode(", ", $words);
}
?>