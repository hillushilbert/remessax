<?php


function __autoload($className){
	
	if(!defined('REMESSAX'))
	throw new Exception('Constante da Biblioteca Remessax não informada!');
	
	if(!defined('REMESSAX_REMESSA_PATH'))
	throw new Exception('Constante da Biblioteca Remessax não informada!');
	
	$path = '';
	$file = '';
	$parts = explode('_',$className);
	for($i=1;$i<count($parts)-1;$i++)
	{
		$path .= $parts[$i].'/';
	}
	
	$file = $parts[count($parts)-1];
	
	$path .= $file.'.php';
	include_once REMESSAX.$path;
}


/*
$Db = &ADONewConnection('mysql');  # create a mysql connection
$Db->Connect('localhost','root','root','edunix');


$fb_remessa = new FactoryBradesco($Db);
$remessa = $fb_remessa->makeRemessa('2011-01-01','2011-01-31');
*/
?>