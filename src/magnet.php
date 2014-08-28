<?php
//header("Content-Type:text/html; charset=utf-8");
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

	public function getMagnetLinks($keyword) {
		$html   = file_get_contents("http://bt.shousibaocai.com/list/" . $keyword . "/1");
		$result = array();

		if ($html != "") {
			preg_match_all('/<table class="table">(.*?)<\/table>/', str_replace("\n", "", $html), $dataList);

			if (isset($dataList[1]) && !empty($dataList[1][0])) {
				preg_match_all("/<td.*>(.*)<\/td>/iUs", $dataList[1][0], $items);

				if (!empty($items[1])) {
					foreach ($items[1] as $key => $item) {

						preg_match('/<span class="ctime">(.*)<\/span>/iUs', $item, $time);
						$result[$key]['time'] = $time[1];

						preg_match_all('/<a class="title".*>(.*)<\/a>/iUs', $item, $titles);
						$result[$key]['title'] = str_replace('</span>', "", str_replace('<span class="highlight">', "", $titles[1][0]));

						preg_match('/<div class="tail">(.*)<\/span>/iUs', $item, $info);
						$result[$key]['info'] = preg_replace('/<span style=".*">(.*)/iUs', "$1", $info[1]);

						preg_match('/<a class="title" href="(.*)>.*<\/a>/i', $titles[0][1], $magnet);
						$result[$key]['magnet'] = $magnet[1];

					}
				}
			}

		}

		return $result;
	}

	public function getData($keyword) {
		$results = $this->getMagnetLinks($keyword);

		foreach ($results as $item) {
			$this->_workflows->result(time(), $item['magnet'], $item['title'], $item['info'] . ' 更新时间:' . $item['time'], 'icon.png');
		}
		return $this->_workflows->toxml();
	}
	public function run($query) {
		return $this->getData($query);
	}
}
//echo App::getInstance()->run("后会无期");
?>