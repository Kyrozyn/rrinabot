<?php
	$Judul = $_GET['judul'];
        $number = $_GET['no'];
	$link = 'https://kyrozyn:anyand32@myanimelist.net/api/anime/search.xml?q='.$Judul;
        //$content = file_get_contents($link);
        //$content_decoded = new SimpleXMLElement($content);
		//print_r($content_decoded);
                
		$xml = simplexml_load_file($link);    
		$json_string = json_encode($xml);    
		$result_array = json_decode($json_string, TRUE);
		$a=$result_array;
                //print_r($result_array);
                echo "<br><br><br>=========================================================<br><br><br>";
		
                /*
		echo "<br>";
		echo "Judul : ".$a['entry']['title']."<br>";
		echo "Synonyms : ".$a['entry']['synonyms']."<br>";
		echo "Episodes : ".$a['entry']['episodes']."<br>";
		echo "Score [MAL] : ".$a['entry']['score']."<br>";
		echo "Type : ".$a['entry']['type']."<br>";
		echo "Status : ".$a['entry']['staus']."<br>";
		echo "Start Date : ".$a['entry']['start_date']."<br>";
		echo "End Date : ".$a['entry']['end_date']."<br>";
		echo "Synopsis : ".$a['entry']['synopsis']."<br>";*/
		
                /*if(is_array($a['entry']['synonyms'])){
                    echo 'Ya ini ARRAY!!';
                }
                else{
                    echo 'BUKAN ARRAY!!!';
                }*/
                $no = 0;
                $no = $number+$no;
                if(!empty($a['entry'][$no]['title'])){
                if(is_array($a['entry'][$no]['synonyms'])){
                    $synonyms ='--';
                }
                else{
                    $synonyms = $a['entry'][$no]['synonyms'];
                }
		echo "== ".$a['entry'][$no]['title']." ==\n"
                            . "Synonyms : ".$synonyms."\n"
                            . "Episodes : ".$a['entry'][$no]['episodes']."\n"
                            . "Score : ".$a['entry'][$no]['score']."\n"
                            . "Type : ".$a['entry'][$no]['type']."\n"
                            . "Status : ".$a['entry'][$no]['status']."\n"
                            . "Start Date : ".$a['entry'][$no]['start_date']."\n"
                            . "End Date : ".$a['entry'][$no]['end_date']."\n"
                            . "Synopsis : ".$a['entry'][$no]['synopsis']."\n";
		//echo $content_decoded->entry[0]['id'];
		//print_r($content_decoded->current());
		
		?>
		<img src= "<?php echo $a['entry'][$no]['image']?>">
                <?php
                }
                else{
                    if(is_array($a['entry']['synonyms'])){
                    $synonyms ='--';
                    }
                else{
                    $synonyms = $a['entry']['synonyms'];
                }
                    echo "== ".$a['entry']['title']." ==\n"
                            . "Synonyms : ".$synonyms."\n"
                            . "Episodes : ".$a['entry']['episodes']."\n"
                            . "Score : ".$a['entry']['score']."\n"
                            . "Type : ".$a['entry']['type']."\n"
                            . "Status : ".$a['entry']['status']."\n"
                            . "Start Date : ".$a['entry']['start_date']."\n"
                            . "End Date : ".$a['entry']['end_date']."\n"
                            . "Synopsis : ".$a['entry']['synopsis']."\n";
                }
                ?>
		<img src= "<?php echo $a['entry']['image']?>">
                <br><br>
                <?php print_r($result_array);?>