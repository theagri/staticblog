<?
define('MB_BASE_PATH',dirname(__FILE__));
define('MB_BASE_URL','http://kallepersson.se');
require 'lib/bloggenerator.php';
require 'lib/markdown.php';

$mb = new BlogGenerator();
$mb->load_posts();
$mb->write_post_files();
$mb->write_index_file();
$mb->write_feed_file();

?>
