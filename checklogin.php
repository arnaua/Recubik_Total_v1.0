 <?php
    session_start();
    //session_destroy();

    include('db.php');
    if($_GET['login'] == 'arnau') { // comprobamos que se hayan enviado los datos del formulario
        // comprobamos que los campos usuarios_nombre y usuario_clave no estén vacíos
        if(empty($_POST['username']) || empty($_POST['password'])) {
            echo "The user or password has not been entered. <a href='javascript:history.back();'>Retry</a>";
        }else {
            // "limpiamos" los campos del formulario de posibles códigos maliciosos
            //$username = mysql_real_escape_string($_POST['username']);
            //$password = mysql_real_escape_string($_POST['password']);

            $username = $_POST['username'];
            $password = md5($_POST['password']);

            /*test login xibo*/

            $conn = bdconnectionXibo();
            $sql = "SELECT count(*) num, usuario_id, usuario_nombre, usuario_clave, usuario_roll, client FROM usuarios WHERE usuario_nombre='".$username."' AND usuario_clave='".$password."'";
            $res = $conn->query($sql)->fetch();
            $idclient = $res['client'];

             echo $idclient;




            // comprobamos que los datos ingresados en el formulario coincidan con los de la BD
            $conn = bdconnection();

            $sql = "SELECT count(*) num, usuario_id, usuario_nombre, usuario_clave, usuario_roll, client FROM usuarios WHERE usuario_nombre='".$username."' AND usuario_clave='".$password."'";

            $res = $conn->query($sql)->fetch();

            $idclient = $res['client'];

            $sql = "SELECT logo FROM client WHERE id='".$idclient."'";

            $reslogo = $conn->query($sql)->fetch();


            if ($res['num'] == 1) {
                $_SESSION['usuario_id'] = $res['usuario_id']; // creamos la sesion "usuario_id" y le asignamos como valor el campo usuario_id
                $_SESSION['usuario_nombre'] = $res["usuario_nombre"]; // creamos la sesion "usuario_nombre" y le asignamos como valor el campo usuario_nombre
                $_SESSION['usuario_roll'] = $res["usuario_roll"]; // creamos la sesion "usuario_roll" y le asignamos como valor el campo usuario_roll
                $_SESSION['logoclient'] = $reslogo["logo"]; 
                $_SESSION['dia'] = date('d-m-Y'); // creamos la sesion "dia" y le asignamos como valor dia de hoy

                header("Location: inicio");
            }elseif ($res['num'] == 0) {
                $_SESSION['errorlogin'] = 'errorlogin';
                header("Location: login");
            }else { //error login
                header("Location: 0x0001");
            }
        }
    }elseif (isset($_POST['recuperar'])) {
        if(empty($_POST['mail_nombre'])) {
            echo "The user has not entered. <a href='javascript:history.back();'>Retry</a>";
        }else {
            $mail_nombre = mysql_real_escape_string($_POST['mail_nombre']);
            $mail_nombre = trim($mail_nombre);
            $sql = mysql_query("SELECT usuario_nombre, usuario_clave, usuario_email FROM usuarios WHERE usuario_nombre='".$mail_nombre."'");
            if(mysql_num_rows($sql)) {
                $row = mysql_fetch_assoc($sql);
                $num_caracteres = "10"; // asignamos el número de caracteres que va a tener la nueva contraseña
                $nueva_clave = substr(md5(rand()),0,$num_caracteres); // generamos una nueva contraseña de forma aleatoria
                $usuario_nombre = $row['usuario_nombre'];
                $usuario_clave = $nueva_clave; // la nueva contraseña que se enviará por correo al usuario
                $usuario_clave2 = md5($usuario_clave); // encriptamos la nueva contraseña para guardarla en la BD
                $usuario_email = $row['usuario_email'];
                // actualizamos los datos (contraseña) del usuario que solicitó su contraseña
                mysql_query("UPDATE usuarios SET usuario_clave='".$usuario_clave2."' WHERE usuario_nombre='".$usuario_nombre."'");
                // Enviamos por email la nueva contraseña
                $remite_nombre = ""; // Tu nombre o el de tu página
                $remite_email = ""; // tu correo
                $asunto = "Recuperación de contraseña"; // Asunto (se puede cambiar)
                $mensaje = "Se ha generado una nueva contraseña para el usuario <strong>".$usuario_nombre."</strong>. La nueva contraseña es: <strong>".$usuario_clave."</strong>.";
                $cabeceras = "From: ".$remite_nombre." <".$remite_email.">\r\n";
                $cabeceras = $cabeceras."Mime-Version: 1.0\n";
                $cabeceras = $cabeceras."Content-Type: text/html";
                $enviar_email = mail($usuario_email,$asunto,$mensaje,$cabeceras);
                if($enviar_email) {
                    echo "La nueva contraseña ha sido enviada al email asociado al usuario ".$usuario_nombre.".";
                }else {
                    echo "No se ha podido enviar el email. <a href='javascript:history.back();'>Retry</a>";
                }
            }else {
                echo "The user <strong> ". $usuario_nombre." </ Strong> is not registered. <a href='javascript:history.back();'>Retry</a>";
            }
        }
    }else {
        header("Location: inicio");
    }
?> 