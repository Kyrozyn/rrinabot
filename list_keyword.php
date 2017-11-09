<head>
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
<style>

</style>
</head>

<center>
<h2>Keyword list Chatbot@Ririna </h2>
<table border="1" class="pure-table pure-table-bordered">
    
    
<?php
    $groupId = base64_decode($_GET['groupid']);
    if ($groupId){
       $host        = "host = ec2-54-221-207-192.compute-1.amazonaws.com";
   $port        = "port = 5432";
   $dbname      = "dbname = dg5btd4vm4f4b";
   $credentials = "user = ezpqrjxealchtz password=bdbacaf6a770548e4e6ea11f4b37cc2860a06209902626fd8071cd28a9d76a9a";
   $db = pg_connect( "$host $port $dbname $credentials"  );
   $sql =<<<EOF
select * from command_text where groupid = '$groupId';
EOF;
   $ret= pg_query($db, $sql);
    echo "<br><br>Keyword ini hanya berlaku di <br>GroupId = ".$groupId."<br><br>";
    echo "<thead><th>Keyword</th><th>Balasan</th></thead>";
 
   
   while($row = pg_fetch_row($ret)){
        echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td></tr>";
   }
   ?> <tr ><td colspan="2"><center>Pic Keyword</center></td></tr> <?php
      $sql =<<<EOF
select * from command_text_img where groupid = '$groupId';
EOF;
   $ret= pg_query($db, $sql);
      while($row = pg_fetch_row($ret)){
        echo "<tr><td>".$row[0]."</td><td>"."<img class='pure-img' src=".$row[1].">"."</td></tr>";
   }
?>
</table>
    <br><br>©kyrozyn.2017
    <?php 
    }
 else{
     header('Location: https://www.herokucdn.com/error-pages/no-such-app.html');exit();
 }