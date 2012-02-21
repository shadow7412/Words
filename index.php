<?php
$dictionary = "linux.words";
if(!file_exists($dictionary)) die("Please check dictionary settings.");

function error_function($level, $message, $file, $line, $context){
	echo $message;
	die(); //Only show an error once (much better than iterating through the whole dictionary and printing 500,000 errors
}

set_error_handler("error_function");

if(empty($_POST['action'])){
?>
<form action="." method="post" target="output" onsubmit="document.getElementById('output').contentDocument.body.innerHTML='Loading...';">
<label for="word">Word:<input type="text" name="word"/></label><br/>
<label><input type="radio" name="action" value="in" checked="checked">Find words that contain this word</label><br/>
<label><input type="radio" name="action" value="rex">Use regular expressions to find words</label><br/>
<label><input type="radio" name="action" value="out">Find words within this word (slow and broken)</label><br/>
<label><input type="radio" name="action" value="ana">Find anagrams of this word (slow and broken)</label><br/>
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
			foreach($file as $test){
				if(preg_match($word,$test)==1)
					$words[] = $test;
			}
			break;
		case('ana'):
		case('out'):/*
			$alpha = 'qwertyuiopasdfghjklzxcvbnm';
			$testLetters = array();
			$wordLetters = array();
			$alphaNo = strlen($alpha);
			//Set up word array
			for($i=0;$i<$alphaNo;$i++){
				$wordLetters[$alpha[$i]] = 0;
			}
			//Add letters to array
			for($i=0;$i<$alphaNo;$i++){
				$wordLetters[$alpha[$i]]++;
			}
			//Now for the dictionary:
			foreach($file as $test){
				//Map the letters in the test word
				for($i=0;$i<$alphaNo;$i++){
					$testLetters[$alpha[$i]] = 0;
				}
				for($i=0;$i<$alphaNo;$i++){
					if(++$testLetters[$alpha[$i]]>$wordLetters[$alpha[$i]]) continue 3; //ignore rest of word
				}
				//Test
				if($_POST['action'] == 'ana'){
				//Perfectly equal
					if(strlen($test)==strlen($word)) continue 2; //They're not gunna be the same without the same no of charas
					for($i=0;$i<$alphaNo;$i++){
						if($testLetters[$alpha[$i]]!=$wordLetters[$alpha[$i]]) continue 3;
					}
				} else {
				//Equal or less
					for($i=0;$i<$alphaNo;$i++){
						if($testLetters[$alpha[$i]]<$wordLetters[$alpha[$i]]) continue 3;
					}
				}
				$words[] = $test;
			}
			*/
			$words[] = "Anagrams are having issues right now...";
			break;
		default:
			$words[] = "A confusion was found.";
	}
	if(@$words[0]==null) echo "None found.";
	else echo implode(", ", $words);
}
?>