<?

class BlogGenerator {

	private $posts = array();
	private $templates_path;
	private $posts_path;

	public function __construct() {
		if(!defined('MB_BASE_PATH')) {
			die("MB_BASE_PATH is not defined");
		}
		$this->posts_path = sprintf('%s/src',MB_BASE_PATH);
	}

	public function load_posts() {
		if(!empty($this->entries)) { return; }

		$dir = scandir($this->posts_path);
		$files = array();
		foreach($dir as $k=>$filename) {
			if(substr($filename,-3) == '.md' && $filename[0] != '_') {
				$post_path = sprintf('%s/%s',$this->posts_path,$filename);
				$post_body = file_get_contents($post_path);
				$post_timestamp = filemtime($post_path);
				$post_html_filename = str_replace('.md','.html',$filename);
				$post_url = sprintf('%s/%s',MB_BASE_URL,$post_html_filename);
				$this->posts[$post_timestamp] = array(
					'filename' => $filename,
					'html_filename' => $post_html_filename,
					'timestamp' => $post_timestamp,
					'url' => $post_url,
					'content' => $post_body,
					'title' => substr(reset(explode("\n",$post_body)),1)
				);
			}
		}
		krsort($this->posts);
	}

	public function write_post_files() {
		foreach($this->posts as $post) {
			file_put_contents($post['html_filename'],$this->render_post_file($post));
		}
	}

	public function write_index_file() {
		file_put_contents('index.html',$this->render_archive());
	}

	public function write_feed_file() {
		file_put_contents('rss.xml',$this->render_feed());
	}

	public function render_template($name,$replacements=array()) {
		$template_html = file_get_contents(sprintf('%s/templates/%s.tpl',MB_BASE_PATH,$name));
		foreach($replacements as $key=>$value) {
			$template_html = str_replace($key, $value, $template_html);
		}
		$template_html = str_replace('#base_href',MB_BASE_URL, $template_html);
		return $template_html;
	}

	public function render_post_file($post) {
		return sprintf('%s %s %s',
			$this->render_template('header',array('#title'=>$post['title'])),
			$this->render_post($post),
			$this->render_template('footer')
		);
	}

	public function render_archive() {
		$archive_posts = '';
		foreach(array_slice($this->posts,0,100) as $post) {
			$archive_posts .= $this->render_post($post);
		}

		return sprintf('%s %s %s',
			$this->render_template('header',array('#title'=>'Kalle Persson')),
			$archive_posts,
			$this->render_template('footer')
		);
	}

	public function render_feed() {
		$feed_posts = '';
		foreach(array_slice($this->posts,0,100) as $post) {
			 $feed_posts .= $this->render_template('feed_post',array(
				'#title'=>$post['title'],
				'#content'=>Markdown($post['content']),
				'#url'=>$post['url'],
				'#pubdate'=>@date('r',$post['timestamp'])
			));
		}

		return sprintf('%s %s %s',
			$this->render_template('feed_header',array('#title'=>'Kalle Persson', '#baseurl'=>MB_BASE_URL)),
			$feed_posts,
			$this->render_template('feed_footer')
		);
	}
	public function render_post($post) {
		return $this->render_template('post',array(
			'#title'=>$post['title'],
			'#html_filename'=>$post['html_filename'],
			'#content'=>Markdown($post['content']),
			'#timestamp'=>@date('Y-m-d H:i',$post['timestamp'])
		));
	}
}

?>
