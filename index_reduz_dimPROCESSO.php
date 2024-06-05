<?php
error_reporting(0);
$total=0;
$convertidos=0;
$valor=1;
if(isset($_POST['valor']))
	$valor=$_POST['valor'];
//Primeiro setamos as variáveis

//Tamanho da thumb, um valor inteiro, que corresponde à porcentagem.
$Tamanho = $valor;

//Diretório onde estão as imagens
$Fotos = "./teste/";

//Diretório onde serão criadas as Thumbs
$Thumbs = "thumbs/";

//Seta qual tipo de arquivo será usado, no caso Jpg, Gif ou PNG
$Ext = ".jpg";

//Seta a qualidade da Thumb
$Qualidade = 95;

//Vamos abrir o diretório das imagens
$dh = opendir(($dir = "$Fotos"));

$ifc=0;
$ifnc=0;
$post=0;
$contaX=0;
$contaY=0;
$contaHE=0;
$contaWI=0;

//Agora vamos varrer todo o diretório à procura das imagens
while (false !== ($filename = readdir($dh))) {
    //Verificamos se o arquivo é uma imagem de extensão igual á setada em $Ext
    if (strtoupper(substr($filename,-4)) != strtoupper($Ext)) {
        continue;
    }
	$fotosConvertidas[$ifc]=$filename;
	$fotosConvertidasTam[$ifc++]=filesize($dir.$filename);
	
    //Verificamos aqui com que tipo de imagem vai trabalhar
    if (strtoupper($Ext) == ".JPG") {
        $ExtFunc = "Jpeg";
    } elseif (strtoupper($Ext) == ".GIF") {
        $ExtFunc = "Gif";
    } elseif (strtoupper($Ext) == ".PNG") {
        $ExtFunc = "Png";
    }
    
    //Criamos a imagem apartir da extensão setada em $ExtFunc
    //Concatenamos o valor de $ExtFunc para termos a função que criará a imagem
    //Podendo ser "ImageCreateFromJpeg" , "ImageCreateFromGif" ou "ImageCreateFromPng"
    
    $CriarImagemDe = "ImageCreateFrom" . $ExtFunc;
    $img = $CriarImagemDe($dir . $filename);
    
    //Aqui tiramos a proporção , o tamanho da thumb em relação à imagem

    //Pega largura da imagem
    $he = ImageSX($img);
	$_he[$contaHE++]=$he;
	
    //Pega altura da imagem
    $wi = ImageSY($img);
	$_wi[$contaWI++]=$wi;

    //Seta valor da largura da thumb
    $x = ($he / 100) * $Tamanho;
	$_x[$contaX++]=$x;
    //Seta valor da altura da thumb
    $y = ($wi / 100) * $Tamanho;
    $_y[$contaY++]=$y;    
    //Aqui é criada a nova imagem, a thumb
    $img_nova = imagecreatetruecolor($x,$y); 
    
    //Agora a nova imagem é redimencionada
    $k=imagecopyresampled($img_nova, $img, 0, 0, 0, 0, $x, $y, $he, $wi); 
	
	if(!$k){
		$convertidos=$convertidos+1;
		echo "A imagem ".$filename." não foi convertida<br>";
		$fotosNConvertidas[$ifnc]=$filename;
		$fotosNConvertidasTam[$ifnc++]=filesize($dir.$filename);
	}
	$total=$total+1;
	
    //Agora salvamos a Thumb no diretório especificado em $Thumbs, com a qualidade setada em $Qualidade
    //Para salvar a nova imagem, usaremos a função correspondente à extensão 
    //Que pode ser "ImageJpeg" , "ImageGif" ou "ImagePng" , concatenando os valores Image + $ExtFunc
    $Image = "Image" . $ExtFunc;
    $Image($img_nova, $Thumbs . $filename, $Qualidade);
	
	$arquivost[$post]=$filename;	
    $arquivostTAM[$post++]=filesize($Thumbs.$filename);
	
    //Destruimos o cache da imagem para liberar uma nova thumb
    ImageDestroy($img_nova);
    ImageDestroY($img);


}

$t='<table class="table table-hover">';
$t.="<thead>";
$t.="<tr class='table-success'><td colspan='3' align='center'>lista de arquivos de ".$Thumbs."</td></tr>";
$t.="<tr>";
  $t.= "<th scope='col' class='bg-success'>Nome</th>";
  $t.= "<th scope='col' class='bg-success'>Tamanho em Kilo Bytes</th>";
  $t.= "<th scope='col' class='bg-success'></th>";
$t.= "</tr>";
$t.= "</thead>";	
for($i=0;$i<count($arquivost);$i++){
  $t.= "<tr class='table-success'>";
    $t.= "<td>".$arquivost[$i]."</td>";
    $t.= "<td><img src='./thumbs/$fotosConvertidas[$i]?".Date('U')."' width='100px'></td>";
    $t.= "<td>".ceil($arquivostTAM[$i]/1024)." KB ".ceil($_x[$i])."x".ceil($_y[$i])."</td>";
  $t.= "</tr>"; 
}
$t.= "</table>"; 

echo $t;