<?php 

$base = "http://xln.fr/www.lacour-entreprise.com";  //(NE PAS METTRE LE / A LA FIN) set this to the url you want to scrape 

$ckfile = './tmp/simpleproxy-cookie-'.session_id();  //this can be set to anywhere you fancy!  just make sure it is secure.

$cache_folder = './tmp/'; //writable folder

$cache_time = 86400 * 2; // 2 days 
