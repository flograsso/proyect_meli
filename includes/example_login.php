<?php

require_once ('Meli/meli.php');
require_once ('configApp.php');
require_once ("dbFunctions.php");


//Variable que mantiene el estado de la conexión
$authenticateState=false;



//Me traigo los datos de la DB
$data = json_decode(getAllValuesDb("token"));
$access_token=$data[0]->access_token;
$refresh_token=$data[0]->refresh_token;
$expires_in=$data[0]->expires_in;

$meli = new Meli($appId, $secretKey,$access_token,$refresh_token);


if(isset($_GET['code']) || !empty($access_token)) 
{	

	if(isset($_GET['code']) && empty($access_token)) {
		//echo "No hay token y si hay code <br>";
		//Con el code pido un token
		try{
			$redirectURI='https://pruebameli.herokuapp.com/includes/example_login.php';
			$user = $meli->authorize($_GET["code"], $redirectURI);
			// Now we create the sessions with the authenticated user
			setValueDb("token","access_token,refresh_token,expires_in","'" . $user['body']->access_token . "','" . $user['body']->refresh_token. "','" . (time() + $user['body']->expires_in)."'");

			//echo "Autenticado<br>";
			$authenticateState=true;
			
		}catch(Exception $e){
			echo "Exception: ",  $e->getMessage(), "\n";
		}
	} else {
		//Access token seteado
		//echo "Access token seteado<br>";
		// We can check if the access token in invalid checking the time
		if($expires_in < time()) {
			//echo "Tiempo del token vencido.<br>";
			try {
				// Make the refresh proccess
				$refresh = $meli->refreshAccessToken();

				// Now we create the sessions with the new parameters
				updateLastValueDb("token","access_token",$refresh['body']->access_token);
				//echo "New Token" . $refresh['body']->access_token;
				updateLastValueDb("token","expires_in",(time() + $refresh['body']->expires_in));
				updateLastValueDb("token","refresh_token",$refresh['body']->refresh_token);

				//echo "Token actualizado<br>";
				$authenticateState=true;

			} catch (Exception $e) {
			  	//echo "Exception: ",  $e->getMessage(), "\n";
			}
		}
		else
			//echo "Sesion NO expirada. Correctamente autenticado<br>";
			$authenticateState=true;
	}
} 
	else 
	{
		echo '<a href="https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id='.$appId.'&redirect_uri=https://pruebameli.herokuapp.com/includes/example_login.php">Login using MercadoLibre oAuth 2.0</a>';
	}



?>