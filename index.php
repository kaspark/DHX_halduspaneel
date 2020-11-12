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

## Vigaste dokumentide kuvamise limiit
$errLimit = (isset($_GET["errLimit"]) AND is_numeric($_GET["errLimit"])) ? $_GET["errLimit"] : $errorRowCount;
$sentLimit = (isset($_GET["sentLimit"]) AND is_numeric($_GET["sentLimit"])) ? $_GET["sentLimit"] : $sentRowCount;
$receiveLimit = (isset($_GET["receiveLimit"]) AND is_numeric($_GET["receiveLimit"])) ? $_GET["receiveLimit"] : $receiveRowCount;


  
require('func/base.php');
require('theme/header.php');

## Kõik asutused
$getAllTheInstitutes = doPDO("SELECT * FROM asutus;")->fetchAll();
$allTheInstitutes = array();
foreach ($getAllTheInstitutes as $key => $val )
  {
    $allTheInstitutes[$val['asutus_id']] = $val;
  } # end foreach loop

## Otsime valitud asutuse
$getMainInstitute = searchArray($allTheInstitutes, 'nimetus', $inst_list[$inst][0]);

?>
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom">
        <h1 class="h2"><?php echo $inst_list[$inst][0]; ?> [<?php if(!empty($getMainInstitute[0])){echo $getMainInstitute[0]["registrikood"];} ?>]</h1>
      </div>
	  
	  <?php
	  $getAdrCount = doPDO("select count(*) from asutus where nimetus is null;")->fetchColumn();
	  $getDublicateCount = doPDO("select count(1) from (select registrikood, nimetus, subsystem, count(*) from asutus group by registrikood, nimetus, subsystem having count(*) > 1) a;")->fetchColumn();
	  ?>
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-1 mb-1">
	  <h5>Aadressiraamat - <?php echo ($getAdrCount == 0 AND $getDublicateCount == 0) ?  "<span class='text-success font-weight-bold'>OK</span>" : "<span class='text-danger font-weight-bold'>NOK (Nimetuid: $getAdrCount; Dublikaate: $getDublicateCount)</span>"; ?></h5>
	  </div>
	  <?php
	  if($getAdrCount != 0 OR $getDublicateCount != 0) {
		$getAdrNokList = doPDO("select * from asutus where nimetus is null;")->fetchAll(); 
		$getDublicateNokList = doPDO("select a.* from asutus a join (select b.registrikood, b.nimetus, b.subsystem, count(*) from asutus b group by b.registrikood, b.nimetus, b.subsystem having count(*) > 1) b on a.registrikood = b.registrikood and (a.nimetus = b.nimetus or a.nimetus is null and b.nimetus is null) and (a.subsystem = b.subsystem or a.subsystem is null and b.subsystem is null) order by a.nimetus;")->fetchAll();
	?>
	<div class="table-responsive">
        <table class="table table-striped table-sm table-sm-dhx">
          <thead class="small">
            <tr>
              <th>#</th>
              <th>date_created</th>
              <th>date_modified</th>
              <th>registrikood</th>
              <th>nimetus</th>
              <th>vahendaja_asutus_id</th>
			  <th>dhx_asutus</th>
			  <th>dhl_saatmine</th>
            </tr>
          </thead>
          <tbody>
	<?php
		foreach ($getAdrNokList as $key => $val )
			{
	  ?>
            <tr>
              <td><?php echo $val["asutus_id"]; ?></td>
              <td><?php echo $val["date_created"]; ?></td>
              <td><?php echo $val["date_modified"]; ?></td>
              <td><?php echo $val["registrikood"]; ?></td>
              <td><?php echo $val["nimetus"]; ?></td>
              <td><?php echo $val["vahendaja_asutus_id"]; ?></td>
              <td><?php echo ($val["dhx_asutus"] == "t") ? "jah" : "<b>ei</b>"; ?></td>
              <td><?php echo ($val["dhl_saatmine"] == "t") ? "jah" : "<b>ei</b>"; ?></td>
            </tr>
			<?php } # end foreach loop ?>
	<?php
		foreach ($getDublicateNokList as $key => $val )
			{
	  ?>
            <tr>
              <td><?php echo $val["asutus_id"]; ?></td>
              <td><?php echo $val["date_created"]; ?></td>
              <td><?php echo $val["date_modified"]; ?></td>
              <td><?php echo $val["registrikood"]; ?></td>
              <td><?php echo $val["nimetus"]; ?></td>
              <td><?php echo $val["vahendaja_asutus_id"]; ?></td>
              <td><?php echo ($val["dhx_asutus"] == "t") ? "jah" : "<b>ei</b>"; ?></td>
              <td><?php echo ($val["dhl_saatmine"] == "t") ? "jah" : "<b>ei</b>"; ?></td>
            </tr>
			<?php } # end foreach loop ?>
	
          </tbody>
        </table>
      </div>
	  <?php 
	# End Aadressiraamat
	  }
	  
	 
	  $getErrCount = doPDO("select count(*) FROM Asutus a, asutus a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
  WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (t.staatus_id = 103 OR v.staatus_id = 103);")->fetchColumn();
	  ?>
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-1 mb-1 border-top">
	  <h5>Vigased saatmised - kuvatakse <?php echo $errLimit; ?> viimast (kokku <?php echo $getErrCount;?>)</h5>
    <?php if($getErrCount > 1) { ?>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&errLimit=20'" class="btn btn-sm btn-outline-secondary">Viimased 20</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&errLimit=50'" class="btn btn-sm btn-outline-secondary">Viimased 50</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&errLimit=200'" class="btn btn-sm btn-outline-secondary">Viimased 200</button>
          </div>
      </div>
    <?php } ?>
	</div>
	  <?php 
	  if($getErrCount != 0) {
		$getErrNokList = doPDO("SELECT a1.nimetus as saatja, a1.subsystem as saatjasub, a1.registrikood as saatjareg, a1.vahendaja_asutus_id as saatjavahendaja, a.nimetus as saaja, a.subsystem as saajasub, a.registrikood as saajareg, a.vahendaja_asutus_id as saajavahendaja, v.saatmise_algus::timestamp(0) as saatmise_algus,v.saatmise_lopp::timestamp(0) as saatmise_lopp,v.outgoing,f_get_lo_val(v.fault_code) as fault_code, f_get_lo_val(v.fault_string) as fault_string,v.vastuvotja_id, f_get_lo_val(d.sisu) as sisu FROM Asutus a, asutus a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
  WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (t.staatus_id = 103 OR v.staatus_id = 103) order by v.saatmise_algus DESC limit ?;", [$errLimit])->fetchAll();
	?>
	<div class="table-responsive">
        <table class="table table-striped table-sm table-sm-dhx">
          <thead>
            <tr>
              <th>saatja</th>
              <th>saaja</th>
              <th>algus</th>
              <th>lõpp</th>
              <th><img src="img\envelope.png" width="20"></th>
              <th>viga <a href="#" id="toggleError">[+]</a></th>
              <th class="errorDesc">kirjeldus</th>
            </tr>
          </thead>
          <tbody>
	<?php
		foreach ($getErrNokList as $key => $val )
			{
        $link='<a my-title="näita ümbrikku" href="ymbrik.php?f='.$val['sisu'].'&inst='.$inst.'">[Ava]</a>';
	  ?>
            <tr>
              <td><?php echo $val['saatja']." [".$val['saatjareg']; echo $val['saatjasub'] ? ":".$val['saatjasub']."]" : "]"; ?>
              <?php if($val['saatjavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saatjavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saatjavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saatjavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val['saaja']." [".$val['saajareg']; echo $val['saajasub'] ? ":".$val['saajasub']."]" : "]"; ?>
              <?php if($val['saajavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saajavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saajavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saajavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val["saatmise_algus"]; ?></td>
              <td><?php echo $val["saatmise_lopp"]; ?></td>
              <td><?php echo $link; ?></td>
              <td><?php echo $val["fault_code"]; ?></td>
              <td class="errorDesc"><?php echo $val["fault_string"]; ?></td>
            </tr>
			<?php } # end foreach loop ?>
          </tbody>
        </table>
      </div>
	
<?php

	# End vigased saatmised
	  }
	  
	 
	  $getSendCount = doPDO("SELECT count(*) FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 101);")->fetchColumn();
	  ?>
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-1 mb-1 border-top">
	  <h5>Saatmisel dokumendid (kokku <?php echo $getSendCount;?>)</h5>
	  </div>
	  <?php 
	  if($getSendCount != 0) {
		$getSendList = doPDO("SELECT a1.nimetus as saatja,a1.subsystem as saatjasub, a1.registrikood as saatjareg, a1.vahendaja_asutus_id as saatjavahendaja, a.nimetus as saaja, a.subsystem as saajasub, a.registrikood as saajareg, a.vahendaja_asutus_id as saajavahendaja, v.saatmise_algus::timestamp(0) as saatmise_algus,v.outgoing, f_get_lo_val(d.sisu) as sisu FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 101) order by v.saatmise_algus DESC;")->fetchAll();
		
	?>
	<div class="table-responsive tableSmaller">
        <table class="table table-striped table-sm table-sm-dhx">
          <thead class="small">
            <tr>
              <th>saatja</th>
              <th>saaja</th>
              <th>algus</th>
              <th>outgoing</th>
			  <th><img src="img\envelope.png" width="20"></th>
            </tr>
          </thead>
          <tbody>
	<?php
		foreach ($getSendList as $key => $val )
			{
				
        $link='<a my-title="näita ümbrikku" href="ymbrik.php?f='.$val['sisu'].'&inst='.$inst.'">[Ava]</a>'; 
	  ?>
            <tr>
              <td><?php echo $val['saatja']." [".$val['saatjareg']; echo $val['saatjasub'] ? ":".$val['saatjasub']."]" : "]"; ?>
              <?php if($val['saatjavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saatjavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saatjavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saatjavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val['saaja']." [".$val['saajareg']; echo $val['saajasub'] ? ":".$val['saajasub']."]" : "]"; ?>
              <?php if($val['saajavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saajavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saajavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saajavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val["saatmise_algus"]; ?></td>
              <td><?php echo $val["outgoing"]; ?></td>
			  <td><?php echo $link; ?></td>
            </tr>
			<?php } # end foreach loop ?>
          </tbody>
        </table>
      </div>
	
<?php

	# End saatmisel
	  }
	  
	 
	  $getSentCount = doPDO("SELECT count(*) FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 102) AND v.outgoing = 't';")->fetchColumn();
	  ?>
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-1 mb-1 border-top">
	  <h5>Saadetud dokumendid (kokku <?php echo $getSentCount;?>)</h5>
    <?php if($getSentCount > 1) { ?>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&sentLimit=20'" class="btn btn-sm btn-outline-secondary">Viimased 20</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&sentLimit=50'" class="btn btn-sm btn-outline-secondary">Viimased 50</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&sentLimit=200'" class="btn btn-sm btn-outline-secondary">Viimased 200</button>
          </div>
        </div>
    <?php } ?>
	  </div>
	  <?php 
	  if($getSentCount != 0) {
	$getSentList = doPDO("SELECT a1.nimetus as saatja, a1.subsystem as saatjasub, a1.registrikood as saatjareg, a1.vahendaja_asutus_id as saatjavahendaja, a.nimetus as saaja, a.subsystem as saajasub, a.registrikood as saajareg, a.vahendaja_asutus_id as saajavahendaja, v.saatmise_algus::timestamp(0) as saatmise_algus, v.saatmise_lopp::timestamp(0) as saatmise_lopp, f_get_lo_val(d.sisu) as sisu, d.date_created FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 102) AND v.outgoing = 't' order by v.saatmise_algus DESC LIMIT ?;", [$sentLimit])->fetchAll();
	?>
	<div class="table-responsive tableSmaller">
        <table class="table table-striped table-sm table-sm-dhx">
          <thead class="small">
            <tr>
              <th>saatja</th>
              <th>saaja</th>
              <th>algus</th>
              <th>lopp</th>
			  <th><img src="img\envelope.png" width="20"></th>
            </tr>
          </thead>
          <tbody>
	<?php
		foreach ($getSentList as $key => $val )
			{
        $link='<a my-title="näita ümbrikku" href="ymbrik.php?f='.$val['sisu'].'&inst='.$inst.'">[Ava]</a>'; 
	  ?>
            <tr>
              <td><?php echo $val['saatja']." [".$val['saatjareg']; echo $val['saatjasub'] ? ":".$val['saatjasub']."]" : "]"; ?>
              <?php if($val['saatjavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saatjavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saatjavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saatjavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val['saaja']." [".$val['saajareg']; echo $val['saajasub'] ? ":".$val['saajasub']."]" : "]"; ?>
              <?php if($val['saajavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saajavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saajavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saajavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val["saatmise_algus"]; ?></td>
              <td><?php echo $val["saatmise_lopp"]; ?></td>
			<td><?php echo $link; ?></td>
            </tr>
			<?php } # end foreach loop ?>
          </tbody>
        </table>
      </div>
	
<?php
    } #end saatmised

    $getReceiveCount = doPDO("SELECT count(*) FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 102) AND v.outgoing != 't';")->fetchColumn();
	  ?>
	  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-1 mb-1 border-top">
	  <h5>Vastuvõetud dokumendid (kokku <?php echo $getReceiveCount;?>)</h5>
    <?php if($getReceiveCount > 1) { ?>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&receiveLimit=20'" class="btn btn-sm btn-outline-secondary">Viimased 20</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&receiveLimit=50'" class="btn btn-sm btn-outline-secondary">Viimased 50</button>
            <button type="button" onclick="location.href='?inst=<?php echo $inst; ?>&receiveLimit=200'" class="btn btn-sm btn-outline-secondary">Viimased 200</button>
          </div>
        </div>
    <?php } ?>
	  </div>
	  <?php 
	  if($getReceiveCount != 0) {
	$getReceiveList = doPDO("SELECT a1.nimetus as saatja, a1.subsystem as saatjasub, a1.registrikood as saatjareg, a1.vahendaja_asutus_id as saatjavahendaja, a.nimetus as saaja, a.subsystem as saajasub, a.registrikood as saajareg, a.vahendaja_asutus_id as saajavahendaja, v.saatmise_algus::timestamp(0) as saatmise_algus, v.saatmise_lopp::timestamp(0) as saatmise_lopp, f_get_lo_val(d.sisu) as sisu, d.date_created FROM ASUTUS a, ASUTUS a1, DOKUMENT d, TRANSPORT t, VASTUVOTJA v
WHERE t.dokument_id = d.dokument_id AND v.transport_id = t.transport_id AND a.asutus_id=v.asutus_id AND a1.asutus_id=d.asutus_id
        AND (v.staatus_id = 102) AND v.outgoing != 't' order by v.saatmise_algus DESC LIMIT ?;", [$receiveLimit])->fetchAll();
	?>
	<div class="table-responsive tableSmaller">
        <table class="table table-striped table-sm table-sm-dhx">
          <thead class="small">
            <tr>
              <th>saatja</th>
              <th>saaja</th>
              <th>algus</th>
              <th>lopp</th>
			  <th><img src="img\envelope.png" width="20"></th>
            </tr>
          </thead>
          <tbody>
	<?php
		foreach ($getReceiveList as $key => $val )
			{
        $link='<a my-title="näita ümbrikku" href="ymbrik.php?f='.$val['sisu'].'&inst='.$inst.'">[Ava]</a>'; 
	  ?>
            <tr>
              <td><?php echo $val['saatja']." [".$val['saatjareg']; echo $val['saatjasub'] ? ":".$val['saatjasub']."]" : "]"; ?>
              <?php if($val['saatjavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saatjavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saatjavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saatjavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val['saaja']." [".$val['saajareg']; echo $val['saajasub'] ? ":".$val['saajasub']."]" : "]"; ?>
              <?php if($val['saajavahendaja']) { echo '<button type="button" class="btn btn-info btn-smx" data-toggle="tooltip" title="Esindaja: '.$allTheInstitutes[$val['saajavahendaja']]["nimetus"].' ['.$allTheInstitutes[$val['saajavahendaja']]["registrikood"].':'.$allTheInstitutes[$val['saajavahendaja']]["subsystem"].']'.'">+</button>'; } ?> </td>
              <td><?php echo $val["saatmise_algus"]; ?></td>
              <td><?php echo $val["saatmise_lopp"]; ?></td>
			<td><?php echo $link; ?></td>
            </tr>
			<?php } # end foreach loop ?>
          </tbody>
        </table>
      </div>
	
<?php
    } #end vastuvõtmised

    if($showRepInst and !empty($getMainInstitute[0])) {
      #otsime esindatud asutused
      $getMainRep = searchArray($allTheInstitutes, 'vahendaja_asutus_id', $getMainInstitute[0]["asutus_id"]);
      
      $getRepCount = count($getMainRep);
        ?>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-1 mb-1 border-top">
        <h5>Esindatud asutused (kokku <?php echo $getRepCount;?>)</h5>
      </div>
        <?php 
        if($getRepCount != 0) {
      ?>
      <div class="table-responsive">
            <table class="table table-striped table-sm table-sm-dhx">
              <thead>
                <tr>
                  <th>asutus_id</th>
                  <th>nimetus</th>
                  <th>dhl_saatmine</th>
                  <th>dhx_asutus</th>
                  <th>own_representee</th>
                  <th>kapsel_versioon</th>
                  <th>date_created</th>
                  <th>date_modified</th>
                </tr>
              </thead>
              <tbody>
      <?php
        foreach ($getMainRep as $key => $val )
          {
        ?>
                <tr>
                  <td><?php echo $val['asutus_id']; ?></td>
                  <td><?php echo $val['nimetus'].' ['.$val['registrikood'].']'; ?></td>
                  <td><?php echo $val['dhl_saatmine'] ? 'true' : 'false'; ?></td>
                  <td><?php echo $val['dhx_asutus'] ? 'true' : 'false'; ?></td>
                  <td><?php echo $val['own_representee'] ? 'true' : 'false'; ?></td>
                  <td><?php echo $val['kapsel_versioon']; ?></td>
                  <td><?php echo date('Y-m-d H:i:s',strtotime($val['date_created'])); ?></td>
                  <td><?php echo date('Y-m-d H:i:s',strtotime($val['date_modified'])); ?></td>
                </tr>
          <?php } # end foreach loop ?>
              </tbody>
            </table>
          </div>
      
    <?php
        # End esindatud astused
        } 
      }?>

    </main>
<?php require('theme/footer.php'); ?>
