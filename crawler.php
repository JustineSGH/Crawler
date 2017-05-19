<?php session_start(); ?>
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

 //Je récupère l'URL du site à crawler
$url = $_POST['url'];

function crawl($url){
  //Initialisation de cURL
  $ch = curl_init($url);
  //Suppresion du fichier s'il existe
  if(file_exists('contenu_page.txt')){
    unlink('contenu_page.txt');
  }
  $fp_donnees = fopen('contenu_page.txt', 'a');
  curl_setopt($ch, CURLOPT_FILE, $fp_donnees);//définit une option de transmission
  curl_exec($ch);//exécute une session 
  curl_close($ch);//ferme la session cURL
  //je lis et récupère les données html de la page
  $contenu = file_get_contents('contenu_page.txt');
  
  //extraction des prix grâce à une expression régulière
  preg_match_all('#(<span class="final-price" data-cerberus="txt_plp_discountedPrice1"> <span itemprop="price">(.*)</span>(.*)</span>|<strong class="final-price" data-cerberus="txt_plpProduit_discountedPrice1">(.*)</strong>|<meta itemprop="price" content="(.+)")#iU', $contenu, $prix);
  $prix[0] = preg_replace('#<strong class="final-price" data-cerberus="txt_plpProduit_discountedPrice1">#', '', $prix[0]);
  $prix[0] = preg_replace('#</strong>#', '', $prix[0]);
  $prix[0] = preg_replace('#<span class="final-price" data-cerberus="txt_plp_discountedPrice1"><span itemprop="price">#', '', $prix[0]);
  $prix[0] = preg_replace('#</span>#', '', $prix[0]);
  $prix[0] = preg_replace('#<meta itemprop="price" content=#', '', $prix[0]);
  $prix[0] = preg_replace('#"#', '', $prix[0]);
  $prix[0] = str_replace('€', '', $prix[0]);

  //extraction des noms
  preg_match_all('#(<h2 data-cerberus="txt_pdp_productName1" itemprop="name">(.+)</h2>|<div class="title" data-cerberus="lnk_plpProduit_productName1">(.+)</div>|<span class="title bold inline" itemprop="name">(.+)</span>|<span>(.+)</span>)#iU', $contenu, $nom);

  //je remplace les informations dont je n'ai pas besoin.
  $nom[0] = preg_replace('#<span class="bandBrand title bold" itemprop="brand">#',  '', $nom[0]);
  $nom[0] = preg_replace('#<h2 data-cerberus="txt_pdp_productName1" itemprop="name">#',  '', $nom[0]);
  $nom[0] = preg_replace('#</h2>#', '', $nom[0]);  
  $nom[0] = preg_replace('#<div class="title" data-cerberus="lnk_plpProduit_productName1">#',  '', $nom[0]);
  $nom[0] = preg_replace('#</div>#', '', $nom[0]); 
  $nom[0] = preg_replace('#<span class="title bold inline" itemprop="name">#',  '', $nom[0]);
   $nom[0] = preg_replace('#<span>#', '', $nom[0]);
  $nom[0] = preg_replace('#</span>#', '', $nom[0]);

  //J'affiche ce que j'ai récupéré grâce aux regex.
  echo count($prix[0]). " produits ont été parcourus.<br />";
  echo "Le moins cher des produits est à " . min($prix[0]) . " €" . ".<br />";
  echo "Le plus cher des produits est à " . max($prix[0]). " €" . ".<br />";
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
      	<th>"; echo $prix[0][$i] . " €"; echo"</th>
    	</tr>";
  	}
  echo" </table>";
  //mise en session
  $_SESSION['tableau_nom'] = $nom[0];
  $_SESSION['tableau_prix'] = $prix[0];

  echo "<a href='session.php' target='_blank'><input type='submit' value='Voir PDF'></a>";
  //Je récupère le lien d'une page qui est paginée. 
 preg_match_all('#(<li class="next" data-cerberus="lnk_plpProduit_paginationNext1"><a data-page="(.+)" href="http://www.laredoute.fr/?[a-zA-Z0-9_./-]+.aspx\?pgnt=[0-9]{1,2}"></a></li>|<a class="pageTextBoxRight pageColor positionRelative displayInlineBlock" title="Page suivante " href="http://www.emp-online.fr/?[a-zA-Z0-9_./-]"><span class="pageArrowRight iconset icon_simpleArrowRight"></span>Suivant</a>)#iU', $contenu, $liens_extraits);
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

  //Affichage de la page suivante à crawler.
  while(list(, $element) = each($tab_liens)){
    echo "Page suivante à Crawler : $element<br />\n";
  }
  if(file_exists('contenu_autre_page.txt')){
    unlink('contenu_autre_page.txt');
  }
  $fichier_liens = fopen('contenu_autre_page.txt', 'a');

  //je parcours toutes les pages en fonction du nombre de pagination. 
  foreach ($tab_liens as $element){
    ini_set('max_execution_time', 300);//5 minutes
    $suivant = file_get_contents($element);
    fputs($fichier_liens, $suivant);
    crawl($element);
  }
  fclose($fichier_liens);
}
//j'appelle ma fonction pour crawler ma page. 
crawl($url);
?>




