<?
//header("Content-Type: text/html; charset=UTF-8");
header("Content-Type: text/html; charset=Shift_JIS");
?>

<html>
<head>
<title>bibliomania_bc</title>
</head>
<body>

<FORM action="bibliomania_bc.php" method="GET">
Type barcode, please!
<input type="text" name="barcode">
<INPUT TYPE="submit">
</FORM>

<?php
if($_GET["barcode"] != null){
	/////////////////////////////////////////////////////rakuten serch/////////////////////////////////////////////////////
	$url = "http://api.rakuten.co.jp/rws/3.0/rest?developerId=xxxxxxxxxxxxxxxxxxxxxxxxxxxx&operation=BooksBookSearch&version=2010-03-18&isbn=";
	$query = $url.$_GET["barcode"];
	$proxy_opts = array('http' => array('proxy' => 'tcp://proxy.example.co.jp:8080','request_fulluri' => true,),);
	$proxy_context=stream_context_create($proxy_opts);
	$raw = mb_convert_encoding(file_get_contents($query,false,$proxy_context),'UTF-8','auto');
	$judge = preg_match_all('@<title>(.*?)</title>.*?<author>(.*?)</author>.*?<publisherName>(.*?)</publisherName>.*?<isbn>(.*?)</isbn>@s',$raw,$matches,PREG_SET_ORDER);


	/////////////////////////////////////////////////////// rakuten yousho serch/////////////////////////////////////////////////////
	if($judge != 1){
		sleep(1);
		$url = "http://api.rakuten.co.jp/rws/3.0/rest?developerId=xxxxxxxxxxxxxxxxxxxxxxx&operation=BooksForeignBookSearch&version=2010-03-18&isbn=";
		$query = $url.$_GET["barcode"];
		$proxy_opts = array('http' => array('proxy' => 'tcp://proxy.example.co.jp:8080','request_fulluri' => true,),);
		$proxy_context=stream_context_create($proxy_opts);
		$raw = mb_convert_encoding(file_get_contents($query,false,$proxy_context),'UTF-8','auto');
		$judge = preg_match_all('@<title>(.*?)</title>.*?<author>(.*?)</author>.*?<publisherName>(.*?)</publisherName>.*?<isbn>(.*?)</isbn>@s',$raw,$matches,PREG_SET_ORDER);

	}


	/////////////////////////////////////////////////for celler phone////////////////////////////////////////////////////////////////
	if($_GET["barcode"] != null){

		$fname = 'BOOK_CP.TXT';
		$fp = fopen($fname, 'w+');
		if($judge == 1){
			fputs($fp,mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][4], "SJIS-win", "UTF-8")."R");
		}
		else{
			fputs($fp,"NotFound");
		}
		fclose($fp);

	}



	/////////////////////////////////////////////////////// save file "BOOK.TXT"/////////////////////////////////////////////////////
	if($judge == 1){
		$fname = 'BOOK.TXT';
		$fp = fopen($fname, 'a');
		fputs($fp,mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][4], "SJIS-win", "UTF-8").PHP_EOL);
		fclose($fp);
	}


	/////////////////////////////////////////////////for jaudge case 0 equal failed, case 1 equal successed////////////////////////////////////////////////////////////////
	print "rakuten".$judge;






	/////////////////////////////////////////////////////// amazon serch/////////////////////////////////////////////////////
	if($judge == 1){}
	else{
		// Access Key ID と Secret Access Key は必須です
		$access_key_id = 'xxxxxxxxxxxxxxxxxx';
		$secret_access_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

		// RFC3986 形式で URL エンコードする関数
		function urlencode_rfc3986($str)
		{
		    return str_replace('%7E', '~', rawurlencode($str));
		}

		// 基本的なリクエストを作成します
		$baseurl = 'http://ecs.amazonaws.jp/onca/xml';
		$params = array();
		$params['Service']        = 'AWSECommerceService';
		$params['AWSAccessKeyId'] = $access_key_id;
		$params['Version']        = '2009-07-01';
		$params['Operation']      = 'ItemLookup';
		$params['IdType']         = 'EAN';
		$params['SearchIndex']    = 'Books';
		$params['ItemId']         =  $_GET["barcode"];

		// Timestamp パラメータを追加します
		// - 時間の表記は ISO8601 形式、タイムゾーンは UTC(GMT)
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

		// パラメータの順序を昇順に並び替えます
		ksort($params);

		// canonical string を作成します
		$canonical_string = '';
		foreach ($params as $k => $v) {
		    $canonical_string .= '&'.urlencode_rfc3986($k).'='.urlencode_rfc3986($v);
		}
		$canonical_string = substr($canonical_string, 1);

		// 署名を作成します
		// - 規定の文字列フォーマットを作成
		// - HMAC-SHA256 を計算
		// - BASE64 エンコード
		$parsed_url = parse_url($baseurl);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_access_key, true));

		// URL を作成します
		// - リクエストの末尾に署名を追加
		$url = $baseurl.'?'.$canonical_string.'&Signature='.urlencode_rfc3986($signature);

		$query = $url;
		$proxy_opts = array('http' => array('proxy' => 'tcp://proxy.example.co.jp:8080','request_fulluri' => true,),);
		$proxy_context=stream_context_create($proxy_opts);
		$raw = mb_convert_encoding(file_get_contents($query,false,$proxy_context),'UTF-8','auto');
		$judge = preg_match_all('@<Author>(.*?)</Author>.*?<Manufacturer>(.*?)</Manufacturer>.*?<ProductGroup>.*?</ProductGroup>.*?<Title>(.*?)</Title>@s',$raw,$matches,PREG_SET_ORDER);

		/////////////////////////////////////////////////////// save file "BOOK.TXT"/////////////////////////////////////////////////////
		if($judge == 1){
			$fname = 'BOOK.TXT';
			$fp = fopen($fname, 'a');
			fputs($fp,mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($_GET["barcode"], "SJIS-win", "UTF-8").PHP_EOL);
			fclose($fp);
		}

		/////////////////////////////////////////////////for celler phone////////////////////////////////////////////////////////////////
		if($_GET["barcode"] != null){

			$fname = 'BOOK_CP.TXT';
			$fp = fopen($fname, 'w+');
			if($judge == 1){
				fputs($fp,mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($_GET["barcode"], "SJIS-win", "UTF-8")."A");
			}
			else{
				fputs($fp,"NotFound");
			}

		fclose($fp);

		}

	}





	/////////////////////////////////////////////////////// amazon yousho serch/////////////////////////////////////////////////////
	if($judge == 1){}
	else{

		// Access Key ID と Secret Access Key は必須です
		$access_key_id = 'xxxxxxxxxxxxxxxxxxxxxxxxxx';
		$secret_access_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

		// RFC3986 形式で URL エンコードする関数
		function urlencode_rfc3986($str)
		{
		    return str_replace('%7E', '~', rawurlencode($str));
		}

		// 基本的なリクエストを作成します
		// - この部分は今まで通り
		$baseurl = 'http://ecs.amazonaws.jp/onca/xml';
		$params = array();
		$params['Service']        = 'AWSECommerceService';
		$params['AWSAccessKeyId'] = $access_key_id;
		$params['Version']        = '2009-07-01';
		$params['Operation']      = 'ItemLookup';
		$params['IdType']         = 'EAN';
		$params['SearchIndex']    = 'ForeignBooks';
		$params['ItemId']         =  $_GET["barcode"];

		// Timestamp パラメータを追加します
		// - 時間の表記は ISO8601 形式、タイムゾーンは UTC(GMT)
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');

		// パラメータの順序を昇順に並び替えます
		ksort($params);

		// canonical string を作成します
		$canonical_string = '';
		foreach ($params as $k => $v) {
		    $canonical_string .= '&'.urlencode_rfc3986($k).'='.urlencode_rfc3986($v);
		}
		$canonical_string = substr($canonical_string, 1);

		// 署名を作成します
		// - 規定の文字列フォーマットを作成
		// - HMAC-SHA256 を計算
		// - BASE64 エンコード
		$parsed_url = parse_url($baseurl);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_access_key, true));

		// URL を作成します
		// - リクエストの末尾に署名を追加
		$url = $baseurl.'?'.$canonical_string.'&Signature='.urlencode_rfc3986($signature);

		$query = $url;
		$proxy_opts = array('http' => array('proxy' => 'tcp://proxy.example.co.jp:8080','request_fulluri' => true,),);
		$proxy_context=stream_context_create($proxy_opts);
		$raw = mb_convert_encoding(file_get_contents($query,false,$proxy_context),'UTF-8','auto');
		$judge = preg_match_all('@<Author>(.*?)</Author>.*?<Manufacturer>(.*?)</Manufacturer>.*?<ProductGroup>.*?</ProductGroup>.*?<Title>(.*?)</Title>@s',$raw,$matches,PREG_SET_ORDER);


		/////////////////////////////////////////////////////// save file "BOOK.TXT"/////////////////////////////////////////////////////
		//if($NotFound[0][1] != "NotFound"){
		if($judge == 1){
			$fname = 'BOOK.TXT';
			$fp = fopen($fname, 'a');
			fputs($fp,mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($_GET["barcode"], "SJIS-win", "UTF-8").PHP_EOL);
			fclose($fp);
		}


		/////////////////////////////////////////////////for celler phone////////////////////////////////////////////////////////////////
		if($_GET["barcode"] != null){

			$fname = 'BOOK_CP.TXT';
			$fp = fopen($fname, 'w+');
			if($judge == 1){
				fputs($fp,mb_convert_encoding($matches[0][1], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][3], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($matches[0][2], "SJIS-win", "UTF-8")."\t".mb_convert_encoding($_GET["barcode"], "SJIS-win", "UTF-8")."AF");
			}
			else{
				fputs($fp,"NotFound");
			}
		fclose($fp);

		}

	}




	/////////////////////////////////////////////////for jaudge case 0 equal failed, case 1 equal successed////////////////////////////////////////////////////////////////
	print "amazon".$judge;


	/////////////////////////////////////////////////input barcode back up/////////////////////////////////////////////////////////////////

	$fname = 'BOOK_BC.TXT';
	$fp = fopen($fname, 'a');
	fputs($fp,$_GET["barcode"].PHP_EOL);
	fclose($fp);

}

/////////////////////////////////////////////////////display table/////////////////////////////////////////////////////
$fn = 'BOOK.TXT'; //データファイル名
$line = file($fn);
echo '<TABLE cellpadding="4" cellspacing="1" style="background-color : #aaaaaa;"><TBODY>';
for ($a = 0; $a < count($line); $a++) {
    $data = split("\t", $line[$a]); //タブ区切り "\t"　　カンマ区切り ","
    echo '<TR>';
    if ($a == 0) $style = 'background-color : #e5e5e5;'; else $style = 'background-color : #ffffff;';
    for ($b = 0; $b < count($data); $b++) echo '<TD style="' . $style . '">' . $data[$b] . '</TD>';
    echo '</TR>';
}
echo '</TBODY></TABLE>';
fclose($fn);


//////////////////////////////////////////////////data sort////////////////////////////////////////////////////////
if($judge == 1){
	/*
	sortパラメント
	    SORT_ASC - 昇順
	    SORT_DESC - 降順
	    SORT_REGULAR - 標準認識
	    SORT_NUMERIC - 数値認識
	    SORT_STRING - 文字列認識
	*/

	/////////////////////////////read book data//////////////////////////////////////////////////
	$fn = 'BOOK.TXT'; //データファイル名
	$line = file($fn);
	for ($a = 1; $a < count($line); $a++) {
	    $data = split("\t", $line[$a]); //タブ区切り "\t"　　カンマ区切り ","
	    for ($b = 0; $b < count($data); $b++) $array[$b][] = $data[$b];
	}

	/////////////////////////////authorで昇順///////////////////////////////////////
	array_multisort($array[0],SORT_ASC,$array[1],$array[2],$array[3]);
	$data = split("\t", $line[0]); //タブ区切り "\t"　　カンマ区切り ","

	$fname = 'BOOK_A.TXT';
	$fp = fopen($fname, 'w+');

	//title author pub barcode
	fputs($fp,$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]);

	//data
	/*success
	fputs($fp,$array[0][0]."\t".$array[1][0]."\t".$array[2][0]."\t".$array[3][0]);
	fputs($fp,$array[0][1]."\t".$array[1][1]."\t".$array[2][1]."\t".$array[3][1]);
	fputs($fp,$array[0][2]."\t".$array[1][2]."\t".$array[2][2]."\t".$array[3][2]);
	fputs($fp,$array[0][3]."\t".$array[1][3]."\t".$array[2][3]."\t".$array[3][3]);
	$temp = $array[0][3]."\t".$array[1][3]."\t".$array[2][3]."\t".$array[3][3];
	fputs($fp,$temp);
	*/
	for ($a = 0; $a < count($line)-1; $a++) {

		/*success
		title	author	publicherName	barcode
		array[0][0]	array[1][0]	array[2][0]	array[3][0]
		array[0][1]	array[1][1]	array[2][1]	array[3][1]
		array[0][2]	array[1][2]	array[2][2]	array[3][2]
		array[0][3]	array[1][3]	array[2][3]	array[3][3]
		array[0][4]	array[1][4]	array[2][4]	array[3][4]
		array[0][5]	array[1][5]	array[2][5]	array[3][5]
		*/

		$temp = $array[0][$a]."\t".$array[1][$a]."\t".$array[2][$a]."\t".$array[3][$a];
		fputs($fp,$temp);
	}
	fclose($fp);

	//////////////////////////////////////titleで昇順/////////////////////////////////////////

	array_multisort($array[1],SORT_ASC,$array[0],$array[2],$array[3]);

	$fname = 'BOOK_T.TXT';
	$fp = fopen($fname, 'w+');
	//title author pub barcode
	fputs($fp,$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]);

	for ($a = 0; $a < count($line)-1; $a++) {
		$temp = $array[0][$a]."\t".$array[1][$a]."\t".$array[2][$a]."\t".$array[3][$a];
		fputs($fp,$temp);
	}

	fclose($fp);

	///////////////////////////////////////publicherNameで昇順////////////////////////////////////////

	array_multisort($array[2],SORT_ASC,$array[0],$array[1],$array[3]);

	$fname = 'BOOK_P.TXT';
	$fp = fopen($fname, 'w+');
	//title author pub barcode
	fputs($fp,$data[0]."\t".$data[1]."\t".$data[2]."\t".$data[3]);

	for ($a = 0; $a < count($line)-1; $a++) {
		$temp = $array[0][$a]."\t".$array[1][$a]."\t".$array[2][$a]."\t".$array[3][$a];
		fputs($fp,$temp);
	}

	fclose($fp);

}

?>

<BR>
<!-- Rakuten Web Services Attribution Snippet FROM HERE -->
<a href="http://webservice.rakuten.co.jp/" target="_blank"><img src="http://webservice.rakuten.co.jp/img/credit/200709/credit_7052.gif" border="0" alt="楽天ウェブサービスセンター" title="楽天ウェブサービスセンター" width="70" height="52"/></a>
<!-- Rakuten Web Services Attribution Snippet TO HERE -->

<!-- Amazon Web Services Attribution Snippet FROM HERE -->
<a  href="http://aws.amazon.com"><img alt="Amazon Web Services" src="Powered-by-Amazon-Web-Services.jpg" title="Amazon Web Services" width="127" height="52"/></a>

</BODY>
</html>
