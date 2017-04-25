<?php

//site web à crawler
//prendre cette url : 'http://www.laredoute.fr/pplp/100/84100/142558/cat-84113.aspx#opeco=hp:zsb2:blanc:pe17s13';
$url = $_POST['url'];

function crawl($url){

    $ch = curl_init($url);

    if(file_exists('contenu_page.txt')){
      unlink('contenu_page.txt');
    }

    $fp_donnees = fopen('contenu_page.txt', 'a');
    curl_setopt($ch, CURLOPT_FILE, $fp_donnees);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp_donnees);
    $contenu = file_get_contents('contenu_page.txt');

    preg_match_all('#(<span class="final-price" data-cerberus="txt_plp_discountedPrice1"> <span itemprop="price">(.*)</span>(.*)</span>|<strong class="final-price" data-cerberus="txt_plpProduit_discountedPrice1">(.*)</strong>)#', $contenu, $prix);
    preg_match_all('#(<h2 data-cerberus="txt_pdp_productName1" itemprop="name">(.+)</h2>|<div class="title" data-cerberus="lnk_plpProduit_productName1">(.+)</div>)#', $contenu, $nom);
  
    $nom[0] = preg_replace('#<h2 data-cerberus="txt_pdp_productName1" itemprop="name">#',  '', $nom[0]);
    $nom[0] = preg_replace('#</h2>#', '', $nom[0]);

    echo "<table class='table table-bordered'>
        <tr>
          <th>Nom</th>
          <th>Prix</th>
        </tr>";
    	for($i=0; $i< count($prix[0]); $i++){
      	echo"
      	<tr> 
        	<th>";
        			echo $nom[0][$i]; echo"</th>
        	<th>"; echo $prix[0][$i]; echo"</th>
      	</tr>";
    	}
    echo" </table>";


    //extraction des liens 
   /* preg_match_all('#"/?[a-zA-Z0-9_./-]+.(php|html|htm|aspx)"#', $contenu, $liens_extraits);
    if(file_exists('liens.txt')){
    	 $fichier_liens = fopen('liens.txt', 'a');

    	foreach ($liens_extraits[0] as $element) {
    		$fichier_liens = fopen('liens.txt', 'a');
    		$gestion_doublons = file_get_contents('liens.txt');
    		$element = preg_replace('#"#', '', $element);
    		$follow_url = $element;
    		$pattern = '#'.$follow_url.'#';
    		if (!preg_match($pattern, $gestion_doublons)) {
          		fputs($fichier_liens, $follow_url);
      		}
    	}
    }
    else {
    	$fp_fichier_liens = fopen('liens.txt', 'a');

	    foreach ($liens_extraits[0] as $element) {
	        $element = preg_replace('#"#', '', $element);
	        $follow_url = $element;
	        fputs($fichier_liens, $follow_url);
	    }
  	}
  fclose($fichier_liens);*/
}
crawl($url);

/*$lire_autres_pages = fopen('liens.txt', 'r');
		
$numero_de_ligne = 1;

while(!feof($lire_autres_pages)) {
  $page_suivante = $url;
  $page_suivante .= fgets($lire_autres_pages);
  echo $numero_de_ligne . ' Analyses en cours, page : ' .  $page_suivante . "\n";
  $numero_de_ligne++;
  crawl($page_suivante);
}
fclose ($lire_autres_pages);*/

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8"/>
    <title>Devoir</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>
  <body>
  </body>
</html>

