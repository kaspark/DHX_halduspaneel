<?php
session_start();
if(@$_SESSION['auth']!=1) header('Location: login.php');
require('conf.php');
if(isset($_GET["inst"])) {
        $inst = $_GET["inst"];
} else {
        $inst = key($inst_list);
}
require('func/base.php');

if(isset($_GET['f']) && isset($_GET['o'])) 
{
	$fileName = $_GET['f'];
	$path    = $inst_list[$inst][2];
	$getFiles = glob("$path/$fileName*");
	$firstFileMatch=$getFiles[0];
	$getFileContent = file_get_contents($firstFileMatch);
	$cleanTags = str_replace(array('S:','SOAP-ENV:','SOAP:','&#x0;','tsl:','ns2:','ns3:','ns4:','ns5:','ns6:','ns7:','ns8:','ns9:'),array('','','','','','','','','','','','',''),$getFileContent);
	$xml = simplexml_load_string($cleanTags, 'SimpleXMLElement', LIBXML_NOBLANKS);
	//Remove empty tags
	$xpath = '/*//*[
		normalize-space(.) = "" and
		not(
		  @* or 
		  .//*[@*] or 
		  .//comment() or
		  .//processing-instruction()
		)
	  ]';
	  foreach (array_reverse($xml->xpath($xpath)) as $remove) {
		unset($remove[0]);
	  }
	$query = "//*[FileGuid=\"".$_GET['o']."\"]";
	foreach($xml->xpath($query) as $item)
	header('Content-Type: '. $item->MimeType.'; charset=utf-8');
	header('Content-Length: ' . $item->FileSize);
	header('Content-Disposition: attachment; filename="'.urlencode($item->FileName).'"');
	echo gzdecode(base64_decode($item->ZipBase64Content));
} else {
	echo "Error: Some variable seems to be missing.";
}