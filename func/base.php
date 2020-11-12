<?php

function doPDO($sql, $args = NULL) {
	global $DB_host, $inst, $DB_pass, $DB_user, $inst_list;
	$DB_name = $inst_list[$inst][1];
	$dsn = "pgsql:host=$DB_host;dbname=$DB_name;";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		 $pdo = new PDO($dsn, $DB_user, $DB_pass, $options);
	} catch (\PDOException $e) {
		 throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	if (!$args)
    {
         return $pdo->query($sql);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    return $stmt;
}


## AD LOGIN FUNC

function getDN($ad, $samaccountname, $AD_basedn)
{
	$result = ldap_search($ad, $AD_basedn, "(samaccountname={$samaccountname})", array(
		'dn'
	));
	if (! $result)
	{
		return '';
	}

	$entries = ldap_get_entries($ad, $result);
	if ($entries['count'] > 0)
	{
		return $entries[0]['dn'];
	}

	return '';
}

function getCN($dn)
{
	preg_match('/[^,]*/', $dn, $matchs, PREG_OFFSET_CAPTURE, 3);
	return $matchs[0][0];
}
function checkGroup($ad, $userdn, $groupdn)
{
	$result = ldap_read($ad, $userdn, "(memberof={$groupdn})", array(
		'members'
	));
	if (! $result)
	{
		return false;
	}

	$entries = ldap_get_entries($ad, $result);

	return ($entries['count'] > 0);
}

function checkGroupEx($ad, $userdn, $groupdn)
{
	$result = ldap_read($ad, $userdn, '(objectclass=*)', array(
		'memberof'
	));
	if (! $result)
	{
		return false;
	}

	$entries = ldap_get_entries($ad, $result);
	if ($entries['count'] <= 0)
	{
		return false;
	}

	if (empty($entries[0]['memberof']))
	{
		return false;
	}

	for ($i = 0; $i < $entries[0]['memberof']['count']; $i ++)
	{
		if ($entries[0]['memberof'][$i] == $groupdn)
		{
			return true;
		}
		elseif (checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn))
		{
			return true;
		}
	}

	return false;
}


# ymbriku funktsioonid
function space($parent) {
	$n = 2*substr_count($parent, '.');
	return str_repeat('&nbsp;', $n);
}

function RecurseXML($xml,$parent="") 
	{ 
	global $inst;
	
		if(substr_count($parent, '.')==1){
			echo "<b>".$xml->getName()."</b>";
		}
		$child_count = 0; 
		echo "<ul>";
		foreach($xml as $key=>$value) 
		{ 	
			$child_count++;     
			if(RecurseXML($value,$parent.".".$key) == 0)  // no childern, aka "leaf node" 
			{ 
				if($key != "ZipBase64Content") {
					echo "<li>".$key.": ".$value."</li>";
					if($key == "FileGuid") {
						echo "<li><a href=\"download.php?f=".$_GET['f']."&o=$value&inst=".$inst."\">Lae fail alla</a>";
					}
				} elseif ($key == "ZipBase64Content") {
				}
			}     
		} 
		echo "</ul>";
		return $child_count; 
	} 


	## array search
	function searchArray($array, $key, $value)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, searchArray($subarray, $key, $value));
        }
    }

    return $results;
}