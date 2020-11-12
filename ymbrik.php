<?php

session_start();
if(@$_SESSION['auth']!=1) header('Location: login.php');
require('conf.php');
## Määrame baasi mida kuvatakse
if(isset($_GET["inst"])) {
        $inst = $_GET["inst"];
} else {
## kui defineerimata siis siis määrame selleks esimese baasi listis
        $inst = key($inst_list);
}


require('func/base.php');
require('theme/header.php');

echo '<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 kapsel">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom">
        <h1 class="h2">'.$inst_list[$inst][0].'</h1>
		<div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" onclick="javascript:history.go(-1)" class="btn btn-sm btn-outline-secondary">Tagasi</button>
          </div>
        </div>
      </div>';

//NB! Apache peab olema õigus failide lugemiseks


//Check for variable
if(isset($_GET['f'])) 
{
	$fileName = $_GET['f'];
	$path    = $inst_list[$inst][2];
	$getFiles = glob("$path/$fileName*");
	if(!empty($getFiles)) {
		$countFilesFound = count($getFiles);
		echo "</ul>";
		for($x=0;$x<$countFilesFound;$x++) {
			$selectFile=$getFiles[$x];
			echo "<b id=\"".$x."\">GUID: ".basename($getFiles[$x])."</b><br><br>";
			$getFileContent = file_get_contents($selectFile);
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
			
			// remove transport tags
			unset($xml->Transport);

			RecurseXML($xml);
		}
	} else {
		echo "<h3>Faile ei leitud!</h3>";
	}
} else {
	echo "Viga: Midagi on puudu....";
}

?>
    </main>
<?php require('theme/footer.php'); ?>
