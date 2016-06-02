<?php
include "util.php"; // permet de démarrer la session
// vérifier les paramètres d'entrée : identifiant (en session) + cours 
$persono_id=isset($_SESSION["persono_id"])?$_SESSION["persono_id"]:"";
if ($persono_id=="") {header("Location:index.php?erarkodo=8");}
$persono = apartigiPersonon($persono_id);
$kurso=isset($_GET["kurso"])?$_GET["kurso"]:"";
if ($kurso=="" || ($kurso!="CG" && $kurso!="GR" && $kurso!="KE")) {
	// Cours inconnu 
	header("Location:personinformoj.php?erarkodo=16");
}
// vérifier en base que le cours est bien terminé, récupérer le nom-prénom du correcteur, la date de fin et le statut (si le statut!=F => exit)
$infos = getInfoPorDiplomoElLernanto($persono_id,$kurso);
if ($infos==null) {
	// on n'a pas trouvé de cours finis pour cet élève (tentative de fraude ?)
	header("Location:personinformoj.php?erarkodo=17");	
}
// Afficher le résultat en PDF


header ("Content-type: image/png");
if ($infos["kurso"]=="CG") {
	$image = imagecreatefrompng("bildoj/diplomeCG.png");
} elseif ($infos["kurso"]=="GR") {
	$image = imagecreatefrompng("bildoj/diplomeGR.png");
}


// gestion des couleurs
	$blanc = imagecolorallocate($image,hexdec("FF"),hexdec("FF"),hexdec("FF"));
	$noir = imagecolorallocate($image,hexdec("00"),hexdec("00"),hexdec("00"));
if ($kurso=="CG") {
	// on découpe la couleur #4A90E2 en rouge/vert/bleu :
	$couleur = imagecolorallocate($image,hexdec("4A"),hexdec("90"),hexdec("E2"));
} elseif ($kurso=="GR") {
	// on découpe la couleur #68AC1D en rouge/vert/bleu :
	$couleur = imagecolorallocate($image,hexdec("68"),hexdec("AC"),hexdec("1D"));
}

// si on a un nom de famille ou un prénom on l'utilise pour afficher sur le diplome, sinon on prend l'identifiant
if ($persono["familinomo"]!="" || $persono["personnomo"]!="") {
	$nom = $persono["personnomo"]." ".$persono["familinomo"];
} else {
	$nom = $persono["enirnomo"];
}

// remplissage du texte
imagettftext($image, 16, 0, 300, 30, $couleur, 'fonts/OpenSans-Light.ttf', 'Parizo, la '.$infos["findato"]);
imagettftext($image, 48, 0, 300, 300, $noir, 'fonts/OpenSans-Light.ttf', $nom);
imagettftext($image, 16, 0, 600, 500, $couleur, 'fonts/OpenSans-Light.ttf', 'Instruisto korektanto :');
imagettftext($image, 16, 0, 600, 550, $couleur, 'fonts/OpenSans-Regular.ttf', $infos["personnomo"]." ".$infos["familinomo"]); // nom-prénom du correcteur

imagepng($image);

?>