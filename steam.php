<!DOCTYPE html>
<html>
<head>
	<!-- <meta charset="utf-8" /> -->
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
	<!-- <title>Page Title</title> -->
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
</head>
<body id="teste">
<h1>Rola</h1>
</body>
</html>

<?php
require 'lightopenid/openid.php';
$_STEAMAPI = "0043DE5DB0FF46B20A258CC0FA0AF578";
try 
{
	// Usa OpenId para a integração do login.
    $openid = new LightOpenID('http://localhost:8080/SteamIntegration/steam.php');
    if(!$openid->mode) 
    {
        if(isset($_GET['login'])) 
        {
            $openid->identity = 'http://steamcommunity.com/openid/?l=portuguese';
			// Redireciona para URL setada.
            header('Location: ' . $openid->authUrl());
        }
?>
<form action="?login" method="post">
    <input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png">
</form>
<?php
    } 
    elseif($openid->mode == 'cancel') 
    {
        echo 'O usuário cancelou a autenticação!';
    } 
    else 
    {
        if($openid->validate()) 
        {
			// URL com ID do usuário
			$id = $openid->identity;
			// Pattern para obter somente o id do usuário, precisamos disso para usar o ID para buscar mais informações
			$ptn = "/^https:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
			preg_match($ptn, $id, $matches);
			
			echo "Usuário <strong>Logado</strong> (Steam ID: $matches[1])<br/><br/>";

			// URL para buscar mais dados
			$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=$matches[1]";
			// Transforma o resultado em json
			$json_object= file_get_contents($url);
			// Decodifica o objeto Json
			$json_decoded = json_decode($json_object);

			// Exibe os dados
			foreach ($json_decoded->response->players as $player)
			{
				echo "
				<br/>ID: $player->steamid
				<br/>Nome: $player->personaname
				<br/>URL do Perfil: $player->profileurl
				<br/>
				<br/>Avatar pequeno:<br/> <img src='$player->avatar'/> 
				<br/>Avatar Médio:<br/> <img src='$player->avatarmedium'/> 
				<br/>Avatar Grande:<br/> <img src='$player->avatarfull'/> 
				";
			}

        } 
        else 
        {
                echo "Usuário não logado.\n";
        }
    }
} 
catch(ErrorException $e) 
{
    echo $e->getMessage();
}
?>