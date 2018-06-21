<?php

require_once ('Meli/meli.php');
require_once ('configApp.php');
require_once ("dbFunctions.php");

function sec_session_start() {
    $session_name = 'meliSession';   // Configura un nombre de sesión personalizado.
    $secure = true;
    // Esto detiene que JavaScript sea capaz de acceder a la identificación de la sesión.
    $httponly = true;
    // Obliga a las sesiones a solo utilizar cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: https://".$_SERVER['HTTP_HOST']."//error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Obtiene los params de los cookies actuales.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Configura el nombre de sesión al configurado arriba.
    session_name($session_name);
    session_start();            // Inicia la sesión PHP.
    session_regenerate_id();    // Regenera la sesión, borra la previa. 
}

function login($email, $password, $conn) {
    global $conn;
    // Usar declaraciones preparadas significa que la inyección de SQL no será posible.
   
    if ($stmt = $conn->prepare("SELECT id, username, password, salt 
        FROM users
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Une “$email” al parámetro.
        $stmt->execute();    // Ejecuta la consulta preparada.
        $stmt->store_result();
 
        // Obtiene las variables del resultado.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
        
        // Hace el hash de la contraseña con una sal única.
        //$password = hash('sha512', $password . $salt);
        
        $password = hash('sha512', $password);
        if ($stmt->num_rows == 1) {
            // Si el usuario existe, revisa si la cuenta está bloqueada
            // por muchos intentos de conexión.
           

                // Revisa que la contraseña en la base de datos coincida 
                // con la contraseña que el usuario envió.
                if ($db_password == $password) {
                  
                    // ¡La contraseña es correcta!
                    // Obtén el agente de usuario del usuario.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    //  Protección XSS ya que podríamos imprimir este valor.
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // Protección XSS ya que podríamos imprimir este valor.
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Inicio de sesión exitoso
                    return true;
                } else {

                    return false;
                }
            
        } else {
            // El usuario no existe.
            return false;
        }
    }
}

function login_check($conn) {
    global $conn;
    // Revisa si todas las variables de sesión están configuradas.
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Obtiene la cadena de agente de usuario del usuario.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $conn->prepare("SELECT password 
                                      FROM users 
                                      WHERE id = ? LIMIT 1")) {
            // Une “$user_id” al parámetro.
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Ejecuta la consulta preparada.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // Si el usuario existe, obtiene las variables del resultado.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // ¡¡Conectado!! 
                    return true;
                } else {
                    // No conectado.
                    return false;
                }
            } else {
                // No conectado.
                return false;
            }
        } else {
            // No conectado.
            return false;
        }
    } else {
        // No conectado.
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // Solo nos interesan los enlaces relativos de  $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

//$path = /questions/....
function getMeli($path)
{   
    global $meli;
    global $access_token;
    $result = $meli->get($path, array('access_token' => $access_token));
    return $result;
    

}

function procesarPregunta($idPregunta)
{
    global $meli;
    global $access_token;
    $url = '/questions/' . $idPregunta;
    $result = $meli->get($url, array('access_token' => $access_token));

    if ($result["httpCode"]==200)
    {
        $answer=$result["body"]->answer;
        $from=$result["body"]->from;
        
        if (checkExistsValue('questions','idPregunta',$idPregunta))
        {
            if (($result["body"] ->status =="ANSWERED") || ($result["body"] ->status =="UNANSWERED") )
            {
                updateValueDb("questions",'fechaRespuesta',convertirFecha($answer->date_created,'America/Dominica','America/Argentina/Buenos_Aires'),'idPregunta',$idPregunta);
                updateValueDb("questions",'textoRespuesta',$answer->text,'idPregunta',$idPregunta);
                updateValueDb("questions",'demoraRtaSeg',diffDatesSeg($answer->date_created,$result["body"]->date_created),'idPregunta',$idPregunta);
                updateValueDb("questions",'estadoPregunta',$result["body"]->status,'idPregunta',$idPregunta);
            }
            else
                deleteValueDb('questions','idPregunta',$idPregunta);
        }
        else
        {
            if ($result["body"] ->status =="ANSWERED")
                setValueDb("questions","idPregunta,textoPregunta,estadoPregunta,fechaRecibida,textoRespuesta,fechaRespuesta,idUsuario,idItem,demoraRtaSeg,cantPreguntasUsuario,item_title","'$idPregunta','". $result["body"] ->text ."','".$result["body"] ->status."','". convertirFecha($result["body"] ->date_created,'America/Dominica','America/Argentina/Buenos_Aires') . "','". $answer->text . "','" .convertirFecha($answer->date_created,'America/Dominica','America/Argentina/Buenos_Aires') . "','". $from->id . "','" . $result["body"] ->item_id . "','" . diffDatesSeg($answer->date_created,$result["body"]->date_created) . "','". $from->answered_questions . "','". getItemTitle($result["body"] ->item_id ) . "'");
            else
                if ($result["body"] ->status =="UNANSWERED")
                    setValueDb("questions","idPregunta,textoPregunta,estadoPregunta,fechaRecibida,textoRespuesta,fechaRespuesta,idUsuario,idItem,demoraRtaSeg,cantPreguntasUsuario,item_title","'$idPregunta','". $result["body"] ->text ."','".$result["body"] ->status."','". convertirFecha($result["body"] ->date_created,'America/Dominica','America/Argentina/Buenos_Aires') . "',NULL,NULL,'" . $from->id . "','" . $result["body"] ->item_id . "',NULL,'". $from->answered_questions . "','". getItemTitle($result["body"] ->item_id ). "'");
        }
    }
    else
    {
        echo "Error en httpCode" . $result["httpCode"];
    }


}

function procesarMensaje($idmensaje)
{
    global $meli;
    global $access_token;
    $url = '/messages/' . $idmensaje;
    $result = $meli->get($url, array('access_token' => $access_token));
    if ($result["httpCode"]==200)
    {
        $text=$result["body"]->text;
        $from=$result["body"]->from;
        $moderation=$result["body"]->moderation;
        $to=$result["body"]->to;
        
        if (checkExistsValue('messages','message_id',$idmensaje))
        {
            updateValueDb("messages",'date_received',convertirFecha($result["body"]->date_received,'UTC','America/Argentina/Buenos_Aires'),'message_id',$idmensaje);
            updateValueDb("messages",'date_read',$result["body"]->date_read,'message_id',$idmensaje);
            updateValueDb("messages",'moderada',$moderation->status,'message_id',$idmensaje);
        }
        else
        {
            setValueDb("messages","message_id,date_received,date_read,from_user_id,from_name,text,order_id,moderada,to_user_id,to_user_name","'$idmensaje','". convertirFecha($result["body"] ->date_received,'UTC','America/Argentina/Buenos_Aires') ."','".convertirFecha($result["body"] ->date_read,'UTC','America/Argentina/Buenos_Aires')  ."','". $from->user_id . "','" . $from->name ."','" .  quitarSaltos($text->plain) . "','". $result["body"] ->resource_id ."','" . $moderation->status ."','". $to[0]->user_id . "','" . $to[0]->email ."'");
        }
    }
    else
    {
        echo "Error en httpCode" . $result["httpCode"];
    }


}

function procesarNotification($topic, $date, $resource)
{
    setValueDb("notifications","date,topic,resource","'". convertirFecha($date,'UTC','America/Argentina/Buenos_Aires') ."','" . $topic . "','" . $resource . "'");
}
function diffDatesSeg($dateA,$dateQ)
{
    return round(strtotime($dateA) - strtotime($dateQ));
}

//Convierte formato de fechas ya que depende a que recurso consulto de MeLi. El formato de fecha que devuelve
//Messages --> UTC (+0)
//Questions --> America/Dominica (-4)  
function convertirFecha($date,$dateOrigin,$dateDestiny)
{
    echo "Fecha antes:" . $date . "<br>";
    $fecha= date_create($date, timezone_open($dateOrigin));
    date_timezone_set($fecha, timezone_open($dateDestiny));
    echo "Fecha despues:" . date_format($fecha, 'Y-m-dTH:i:sP') . "<br>";
    return date_format($fecha, 'Y-m-dTH:i:sP');
}

function quitarSaltos($cadena)
{
    return preg_replace("[\n|\r|\n\r]",' ', $cadena);
}

function procesarOrden($idOrden)
{
    global $meli;
    global $access_token;
    $url = '/orders/' . $idOrden;
    $result = $meli->get($url, array('access_token' => $access_token));

    if ($result["httpCode"]==200)
    {
        $buyer=$result["body"]->buyer; //Vector
        $orderItems=$result["body"]->order_items; //Vector
        $date=$result["body"]->date_closed;
        $status=$result["body"]->status;
        $buyer_id=$buyer->id;
        $total_amount=$result["body"]->total_amount;
        $buyer_nick=$buyer->nickname;
        
        if (checkExistsValue('orders','id',$idOrden))
        {
            updateValueDb("orders",'status',$status,$idOrden);
            updateValueDb("orders",'date',$date,$idOrden);
            updateValueDb("orders",'total_amount',$total_amount,$idOrden);
        }
        else
        {
            setValueDb("orders","id,date,status,buyer_id,order_items,total_amount,buyer_nick","'" . $idOrden . "','". convertirFecha($date,'UTC','America/Argentina/Buenos_Aires') . "','" . $status . "','" . $buyer_id . "',NULL,'". $total_amount . "','" .$buyer_nick . "'");
        }
    }
    else
    {
        echo "Error en httpCode" . $result["httpCode"];
    }

}

function getItemTitle($itemId)
{
    global $meli;
    $url = '/items/' . $itemId;
    $result = $meli->get($url, array('access_token' => $access_token));
    if ($result["httpCode"]==200)
    {
        return $result["body"]->title;
    }
    else
    {
        echo "Error en httpCode" . $result["httpCode"];
    }

}

?>
