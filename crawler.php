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
    preg_match_all('#(<div class="title"><h2 data-cerberus="txt_pdp_productName1" itemprop="name">(.+)</h2></div>|<div class="title" data-cerberus="lnk_plpProduit_productName1">(.+)</div>)#', $contenu, $nom);

    if(file_exists('fichier_mails.txt')){
      unlink('fichier_mails.txt');
    }
    $fp_fichier_emails = fopen('fichier_mails.txt', 'a');

    echo "<table class='table table-bordered'>
        <tr>
          <th>Nom</th>
          <th>Prix</th>
        </tr>";
    for($i=0; $i< count($prix[0]); $i++){
      echo"
      <tr> 
        <th>"; echo $nom[0][$i]; echo"</th>
        <th>"; echo $prix[0][$i]; echo"</th>
      </tr>";
    }
    echo" </table>";

    fclose($fp_fichier_emails);
}
crawl($url);

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

