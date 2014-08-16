<?php
/*
    Usage: php ImageDownloader.php <url>
*/

// メイン処理
function main(){
	global $argc;
	global $argv;
	if($argc < 2){
		print("Usage: php ImageDownloader.php <url>\n");
		exit(0);
	}
	$url = $argv[1];
	print("URL: $url\n");

	// HTML取得
	$context = stream_context_create(
		array(
			'http' => array(
				'method' => 'GET',
				'header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.143 Safari/537.36',
			)
		)
	);
	$html = file_get_contents($url, false, $context);
	
	// DOMDocument取得
	$doc = html2doc($html);
	
	// DOMDocumentからa取得
	$elements = $doc->getElementsByTagName("a");
	$index = 0;
	for($i = 0; $i < $elements->length; $i++){
		$item = $elements->item($i);
		$hrefNode = $item->attributes->getNamedItem("href");
		if($hrefNode){
			$href = $hrefNode->nodeValue;
			if(preg_match('/\.jpg$/i', $href)){
				print("OK: " . $href . "\n");
				// 保存ファイル名
				$fname = sprintf("%03d.jpg", $index);
				// 取得・保存
				$content = file_get_contents($href, false, $context);
				file_put_contents($fname, $content);
				// 次のインデックス
				$index++;
			}
		}
	}
}

// DOMDocument取得
function html2doc($html){
    // まずは UTF-8 で決め打ち
    $htmlEncoded = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $doc = new DOMDocument();
    @$doc->loadHTML($htmlEncoded);

    // metaからcharset検出
    $charset = '';
    $elements = $doc->getElementsByTagName("meta");
    for($i = 0; $i < $elements->length; $i++){
        $e = $elements->item($i);

        // charset属性をチェック
        // <meta charset="utf-8"/>
        $node = $e->attributes->getNamedItem("charset");
        if($node){
            $charset = $node->nodeValue;
            break;
        }

        // http-equiv属性をチェック
        // <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        $node = $e->attributes->getNamedItem("http-equiv");
        if($node && strcasecmp($node->nodeValue, 'content-type') == 0){
            $node = $e->attributes->getNamedItem("content");
            if($node && preg_match('/[\; ]charset ?\= ?([A-Za-z0-9\-\_]+)/', $node->nodeValue, $m)){
                $charset = $m[1];
                break;
            }
            continue;
        }
    }

    // 検出されたcharsetがUTF-8じゃなかったら
    if($charset !== '' && !preg_match('/^utf\-?8$/i', $charset)){
        // 文字コード変換し直して
        $htmlEncoded = mb_convert_encoding($html, 'HTML-ENTITIES', $charset);
        // DOMも構築し直す
        $doc = new DOMDocument();
        @$doc->loadHTML($htmlEncoded);
    }

	// 結果
	return $doc;
}

// HTML全体からtitleを取得
function html2title($html){
	// DOMDocument
	$doc = html2doc($html);

    // title取得
    $elements = $doc->getElementsByTagName("title");
    for($i = 0; $i < $elements->length; $i++){
        $e = $elements->item($i);
        return $e->textContent;
    }

    // titleが見つからなかった場合
    return false;
}

// メイン処理
main();
