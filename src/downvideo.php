<?php
require_once ('workflows.php');

class App {
	private static $_instance;
	private $_workflows;

	function __construct() {
		$this->_workflows = new workflows();
	}

	public function getInstance() {
		if (!self::$_instance instanceof self) {
			self::$_instance = new App();
		}
		return self::$_instance;
	}

	public function request($url) {
		return $this->_workflows->request($url);
	}

	//获取视频信息
	public function getVideo($site) {
		$api     = "http://api.flvxz.com/jsonp/purejson/url/";
		$code    = base64_encode(str_replace("://", ":##", $site));
		$content = $this->request($api . $code);

		$videoinfo = json_decode($content, true);

		return $videoinfo;
	}

	public function run($query) {
		return $this->getData($query);
	}

	//输出数据
	public function getData($keyword) {
		$results = $this->getVideo($keyword);

		foreach ($results as $item) {
			$size    = $item['files'][0]['size'];
			$time    = $item['files'][0]['time'];
			$furl    = $item['files'][0]['furl'];
			$quality = $item['quality'];

			$this->_workflows->result(time(), $furl, $item['title'], '文件大小:' . $size . ' 播放时长:' . $time . ' 清晰度:' . $quality, 'icon.png');
		}

		return $this->_workflows->toxml();
	}
}
?>
