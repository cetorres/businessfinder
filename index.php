<?php
if ($_POST["searchText"] != null)
{
	include('json.php');
	
	$key = "ABQIAAAAQONmqhMalr12XEJAI5cApRSVD0632HfXlIHrBhU_WwhZ291DkRThx64K7v9fsHf5XQsQYFnMRQgmxQ";
	$defaultSearchUrl = "http://www.google.com/uds/GlocalSearch?key=$key&v=1.0&rsz=large&mrt=blended&sspn=0.165746,0.802002&lssrc=lsc&lscstyle=final&start=0";
	
	$searchText = urlencode($_POST["searchText"]);
	$latitude = $_POST["latitude"];
	$longitude = $_POST["longitude"];
	
    $search = $defaultSearchUrl . "&sll=$latitude,$longitude&q=$searchText";
	
	// Retrieve the URL contents
	$page = file_get_contents($search);

	// Parse results
	$json = new Services_JSON();
	$json = $json->decode($page);

	// Build final html result
	if ($json->responseData->results) {
		foreach($json->responseData->results as $searchresult)
		{
			if($searchresult->GsearchResultClass == 'GlocalSearch')
			{
				//$businessesFound .= '<li class="withimage"><a class="noeffect" onclick="urlGoMap=\''.$searchresult->ddUrl.'\';iWebkit.popup(\'popupGoMap\')">';
				$businessesFound .= '<li class="withimage"><a href="' . $searchresult->ddUrl .'">';
				$businessesFound .= '<img src="' . $searchresult->staticMapUrl . '" />';
				//$businessesFound .= '<img alt="thumb" src="thumbs/maps.png" />';
				$businessesFound .= '<span class="name">' . utf8_decode($searchresult->titleNoFormatting) . '</span>';
				$businessesFound .= '<span class="comment">';
				$businessesFound .= utf8_decode($searchresult->streetAddress);
				$businessesFound .= '<br/>';
				$businessesFound .= utf8_decode($searchresult->phoneNumbers[0]->number);
				$businessesFound .= '</span>';
				$businessesFound .= '<span class="arrow"></span></a></li>' . "\n";
			}
		}
	}
	else {
		$businessesFound = '<li><a class="noeffect"><span class="name">Nenhum negócio encontrado.</span></a></li>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<link href="images/SearchMagnifier2.png" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="javascript/functions.js" type="text/javascript"></script>
<title>Business Finder</title>
<script type="text/javascript">
	//var urlGoMap;
	
	function getLocation() {
		if(navigator.geolocation) {
			var watchID = navigator.geolocation.watchPosition(function(position) {
					document.getElementById("formAddress").elements["latitude"].value = position.coords.latitude;
					document.getElementById("formAddress").elements["longitude"].value = position.coords.longitude;
				}
			);
		} else {
			try {
				document.getElementById("gpsInfoMsg").innerHTML = "browser não suporta geolocation";
			} catch(e) {
			
			}
		}
	}
</script>
</head>
<body <?if ($businessesFound != null) {?> class="list" <?}?>>
	<div id="topbar">
		<?if ($businessesFound != null) {?>
			<div id="leftnav"><a href="index.php"><img alt="home" src="images/home.png" /></a></div>
		<?}?>
		<div id="title">Business Finder</div>
	</div>
	<div id="content">
		<?if ($businessesFound != null) {?>
			<ul class="autolist">
				<li class="title">Resultado da pesquisa</li>
				<?=$businessesFound?>
				<li class="hidden autolisttext"><a class="noeffect" href="#">Carregar mais 10 itens...</a></li>
			</ul>
		<?} else {?>
			<form id="formAddress" name="formAddress" action="index.php" method="post">
				<span class="graytitle">Pesquisa de negócios locais</span>
				<ul class="pageitem">
					<li class="form"><input type="text" name="searchText" /></li>
				</ul>
				
				<span class="graytitle">Posição geográfica</span>
				<ul class="pageitem">
					<li class="form"><span class="narrow"><span class="name">Latitude</span><input type="text" name="latitude" /></span></li>
					<li class="form"><span class="narrow"><span class="name">Longitude</span><input type="text" name="longitude" /></span></li>
					<li id="gpsInfoMsg" class="textbox" style="text-align:center">posição atual do GPS</li>
				</ul>
				
				<ul class="pageitem">
					<li class="form"><input name="Submit" type="submit" value="Pesquisar" /></li>
				</ul>
			</form>
		<?}?>
	</div>
	<div id="footer"><a href="http://www.carloseugeniotorres.com">Developed by Carlos Eugênio Torres</a></div>
	
	<script type="text/javascript">getLocation();</script>
	
	<?/*?>
	<div id="popupGoMap" class="popup">
		<div id="frame" class="confirm_screen">
			<span>deseja visualizar no mapa?</span>
			<a href="javascript:void(0);" onclick="window.location=urlGoMap"><span class="red">Sim</span></a>
			<a class="noeffect" onclick="iWebkit.closepopup(event)"><span class="black">Não</span></a>
		</div>
	</div>
	<?*/?>
</body>
</html>
