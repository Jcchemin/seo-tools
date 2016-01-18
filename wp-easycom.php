<?php
header( 'content-type: text/html; charset=utf-8' );
/**
 * @package EasyCom SEO
 * @version 1.3
 * @plugin URI: http://
 * @description: SEO Tools to easily comments articles on wordpress
 * @author: @Jcchemin
 * 
 *  type = "comment" or "post"
 * 
 * @sample comment: /wp-easycom.php?key=g3j7H7g959DJBNxh&article_url=&link_url=https://www.ifitness.fr&author_name=Clara&comment=Super%20article%20merci%20bien
 * @sample post: /wp-easycom.php?key=g3j7H7g959DJBNxh
 * 
 */

$token = array('g3j7H7g959DJBNxh', 'uChj2U33R7krU85H');

if (is_file('wp-config.php')){
	include 'wp-config.php';
}else{
	define('DB_NAME', 'votre_nom_de_bdd');
	define('DB_USER', 'votre_utilisateur_de_bdd');
	define('DB_PASSWORD', 'votre_mdp_de_bdd');
	define('DB_HOST', 'localhost');
	$table_prefix = 'wp_';
}

try {
	$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch(Exception $e) {
	echo 'PDO Error : '.$e->getMessage().'<br />';
	echo 'N° : '.$e->getCode();
}

$key = !empty($_GET['key'])?$db->quote($_GET['key']):die('key missing');
check_access($token, $key);
$type = !empty($_GET['type'])?$db->quote($_GET['type']):'comment');

$article_url = !empty($_GET['article_url'])?$db->quote($_GET['article_url']):die('article_url missing');
$comment = !empty($_GET['comment'])?$db->quote($_GET['comment']):die('comment missing');

$id_post = get_pageid($db, $table_prefix, $article_url);

if ($type == "comment"){
	$link_url = !empty($_GET['link_url'])?$db->quote($_GET['link_url']):die('link_url missing');
	$author_name = !empty($_GET['author_name'])?$db->quote($_GET['author_name']):die('author_name missing');
	$sql = "INSERT INTO `".$table_prefix."comments` (`comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`) VALUES ($id_post, $author_name, 'contact@wp-easycom.fr', $link_url, '127.0.0.1', NOW(), NOW(), $comment, '0', '1', '', '', '0', '0');";
}else if ($type == "post"){
	$sql = "UPDATE `".$table_prefix."comments` SET `post_content` = CONCAT(`post_content`, $comment) WHERE `ID` = ".$id_post;
}else{die('type error');}

$query = $db->prepare($sql);
$query->execute();
echo $db->lastInsertId();

function check_access($token, $key){
	foreach ($token as $t){
		if ($t === $key)return true;
	}
	die();
}

function get_pageid($db, $table_prefix, $article_url){
	$url_parts = parse_url($article_url);
	if (!empty($url_parts['query'])){
		parse_str($url_parts['query'], $path_parts);
		if (!empty($path_parts['p'])){
			return $path_parts['p'];
		}
	}
	$url_exp = explode('/', $article_url);
	for ($i = count($url_exp)-1; $i > 0; $i--){
		if (!empty($url_exp[$i])){
			$sql = "SELECT `ID` FROM `".$table_prefix."posts` WHERE `post_name` LIKE '".$url_exp[$i]."' AND `post_type` = 'post' LIMIT 1";
			$query = $db->prepare($sql);
			$query->execute();
			if ($query->rowCount() > 0) {
				return reset($query->fetch());
			}			
		}	
	}
	die('page not found');
}
