<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8"/>
    <title>Devoir</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Crawler</a>
        </div>
        <form class="navbar-form navbar-right" action="index.html">
          <button type="submit" class="btn btn-warning">Retour</button>
        </form>
      </div>
    </nav>
  </body>
</html>
<?php

//site web à crawler
//prendre cette url : 'http://www.laredoute.fr/pplp/100/84100/142558/cat-84113.aspx?pgnt=1';
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
  $contenu = file_get_contents('contenu_page.txt');
  
  //extraction des prix
  preg_match_all('#(<span class="final-price" data-cerberus="txt_plp_discountedPrice1"> <span itemprop="price">(.*)</span>(.*)</span>|<strong class="final-price" data-cerberus="txt_plpProduit_discountedPrice1">(.*)</strong>)#', $contenu, $prix);
  //extraction des noms
  preg_match_all('#(<h2 data-cerberus="txt_pdp_productName1" itemprop="name">(.+)</h2>|<div class="title" data-cerberus="lnk_plpProduit_productName1">(.+)</div>)#', $contenu, $nom);
  
  $nom[0] = preg_replace('#<h2 data-cerberus="txt_pdp_productName1" itemprop="name">#',  '', $nom[0]);
  $nom[0] = preg_replace('#</h2>#', '', $nom[0]);

  echo count($prix[0]). " produits ont été parcourus.<br />";
  echo "Le moins cher des produits est à " . min($prix[0]) . ".<br />";
  echo "Le plus cher des produits est à " . max($prix[0]). ".<br />";
  echo "<table class='table table-bordered'>
    <tr>
      <th>Nom</th>
      <th>Prix</th>
    </tr>";
  	for($i=0; $i < count($prix[0]); $i++){
    	echo"
    	<tr> 
      	<th>"; echo $nom[0][$i];
         echo"</th>
      	<th>"; echo $prix[0][$i]; echo"</th>
    	</tr>";
  	}
  echo" </table>";
  
  //Les liens paginées 
 preg_match_all('#<li class="next" data-cerberus="lnk_plpProduit_paginationNext1"><a data-page="(.+)" href="http://www.laredoute.fr/pplp/?[a-zA-Z0-9_./-]/?[a-zA-Z0-9_./-]/?[a-zA-Z0-9_./-]+.aspx\?pgnt=[0-9]{1,2}"></a></li>#iU', $contenu, $liens_extraits);
  $liens_extraits[0] = preg_replace('#<li class="next" data-cerberus="lnk_plpProduit_paginationNext1">#', '', $liens_extraits[0]);
  $liens_extraits[0] = preg_replace('#<a data-page="([0-9])*"#', '', $liens_extraits[0]);
  $liens_extraits[0] = preg_replace('#href=#', '', $liens_extraits[0]);
  $liens_extraits[0] = preg_replace('#</a>#', '', $liens_extraits[0]);
  $liens_extraits[0] = preg_replace('#</li>#', '', $liens_extraits[0]);
  $liens_extraits[0] = preg_replace('#>#', '', $liens_extraits[0]);//j'enlève les fermetures de balises
  $liens_extraits[0] = preg_replace('#"#', '', $liens_extraits[0]);//j'enlève les guillemets
  $liens_extraits[0] = preg_replace('# #', '', $liens_extraits[0]);//j'enlève un espace qui était en trop
  $tab_liens = array_unique($liens_extraits[0]);
  reset($tab_liens);

  fclose($fp_donnees);

  while(list(, $element) = each($tab_liens)){
    echo "Page suivante à Crawler : $element<br />\n";
  }
  if(file_exists('contenu_autre_page.txt')){
    unlink('contenu_autre_page.txt');
  }
  $fichier_liens = fopen('contenu_autre_page.txt', 'a');

 foreach ($tab_liens as $element){
    ini_set('max_execution_time', 300);//5 minutes
    $suivant = file_get_contents($element);
    fputs($fichier_liens, $suivant);
    crawl($element);
  }
  fclose($fichier_liens);
}
crawl($url);
?>



