<?php
// コマンドライン解釈
if(count($argv) < 2)die("Usage: php HtmlView.php <image file path>");
$inputFile = $argv[1];

// 拡張子
if(preg_match('/\.([A-Za-z0-9]+)$/', $inputFile, $m)){
	$ext = strtolower($m[1]);
}
else{
	die("Extension not detected: $inputFile");
}

// ディレクトリ
if(preg_match('/\.(gif|jpg|jpeg|png)$/i', $inputFile)){
	$inputDir = dirname($inputFile);
}
else{
	$inputDir = $inputFile;
}
echo "DIR: $inputDir\n";

// 列挙
$items = array();
$dir = opendir($inputDir) or die("Failed to opendir");
while (($file = readdir($dir)) !== false) {
	if(preg_match('/\.(gif|png|jpg|jpeg)$/i', $file)){
		$items[] = $file;
	}
}
closedir($dir);

// 処理
sort($items);
var_dump($items);

// HTML生成
$html = file_get_contents(dirname(__FILE__) . '/template.html');

// 画像群
$images_html = '';
$images_html .= "<div></div>\n";
foreach($items as $index => $item){
	$images_html .= "<img src='$item'>\n";
	if($index % 2 == 1){
		$images_html .= "<div></div>\n";
	}
}

// 埋め込み
$html = str_replace('{images}', $images_html, $html);

// ファイル出力
file_put_contents($inputDir . '/view.html', $html);

// ファイルオープン
system($inputDir . '/view.html');
