<?php
       $host        = "host = ec2-54-221-207-192.compute-1.amazonaws.com";
   $port        = "port = 5432";
   $dbname      = "dbname = dg5btd4vm4f4b";
   $credentials = "user = ezpqrjxealchtz password=bdbacaf6a770548e4e6ea11f4b37cc2860a06209902626fd8071cd28a9d76a9a";
   $db = pg_connect( "$host $port $dbname $credentials"  );
   $sql =<<<EOF
update sessions set bc = false;
EOF;
   $ret= pg_query($db, $sql);