<?
//header("Content-Type: text/html; charset=UTF-8");
header("Content-Type: text/html; charset=Shift_JIS");
?>

<html>
<head>
<title>bibliomania_ip</title>
</head>
<body>

<FORM action="bibliomania_ip.php" method="GET">
<input type="text" name="author">author<br>
<input type="text" name="title">title<br>
<input type="text" name="publisherName">publisherName<br>
<input type="text" name="barcode">barcode<br>
<INPUT TYPE="submit">
</FORM>



<?php

if($_GET["author"] != null)$judge = 1;
if($_GET["title"] != null)$judge = 1;
if($_GET["publisherName"] != null)$judge = 1;
if($_GET["barcode"] != null)$judge = 1;

/////////////////////////////////////////////////////// save file "BOOK.TXT"/////////////////////////////////////////////////////
if($judge == 1){
	$fname = 'BOOK.TXT';
	$fp = fopen($fname, 'a');
	fputs($fp,$_GET["author"]."\t".$_GET["title"]."\t".$_GET["publisherName"]."\t".$_GET["barcode"].PHP_EOL);
	fclose($fp);
}


/////////////////////////////////////////////////for jaudge case 0 equal failed, case 1 equal successed////////////////////////////////////////////////////////////////
print $judge;

/////////////////////////////////////////////////input barcode back up/////////////////////////////////////////////////////////////////
if($_GET["barcode"] != null){
	$fname = 'BOOK_BC.TXT';
	$fp = fopen($fname, 'a');
	fputs($fp,$_GET["barcode"].PHP_EOL);
	fclose($fp);
}

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

</BODY>
</html>