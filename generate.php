<?
define('MB_BASE_PATH',dirname(__FILE__));
define('MB_BASE_URL','http://localhost/microjournal/');
require 'lib/bloggenerator.php';
require 'lib/markdown.php';

$mb = new BlogGenerator();
$mb->load_posts();
$mb->write_post_files();
$mb->write_index_file();

?>
