<?php

require_once("../funciones.php");
require_once("../conexionBD.php");

$db = conectarse();

/*
 * Leer variables del sistema
 */
$estado = $db->query("SELECT * FROM general");
$leer = $estado->fetch_array(MYSQLI_ASSOC);

if (!isset($_POST['envia_acceso'])) {
    include_once("ingreso.phtml");
} else {
    /**
     * VALIDACION DE INGRESO AL SISTEMA
     */
    if ($_POST['usuario'] == "") {
        include_once("encabezado.phtml");
        print "<strong>No ha escrito el nombre de usuario<br>";
        print "<br/><a href='javascript:history.go(-1)'>Volver al formulario</a></strong></div></body></html>";
        exit();
    }

    if ($_POST['clave'] == "") {
        include_once("encabezado.phtml");
        print "<strong>No ha escrito la contraseña de acceso<br>";
        print "<br><a href='javascript:history.go(-1)'>Volver al formulario</a></strong></div></body></html>";
        exit();
    }
    $clave = md5($_POST['clave']);

    /**
     * Funcion para guardar los datos de control
     */
    function LogControl($faccion2, $idest2)
    {
        require_once("../conexionBD.php");
        $link = conectarse();
        $ffecha = date("Y-m-d");
        $fhora = date("G:i:s");
        $fip = $_SERVER['REMOTE_ADDR'];
        $cons_sql  = sprintf("INSERT INTO control(c_fecha, c_hora, c_ip, c_accion, c_idest) VALUES (%s, %s, %s, %s, %d)", comillas($ffecha), comillas($fhora), comillas($fip), comillas($faccion2), $idest2);
        $link->query($cons_sql);
    }

    $sql = sprintf("SELECT id, nombres, apellidos FROM administradores WHERE usuario = %s AND password = %s", comillas($_POST['usuario']), comillas($clave));
    $resp = $db->query($sql);
    if ($row = $resp->fetch_array(MYSQLI_ASSOC)) {
        //**** Creamos la cookie
        setcookie("VotaDatAdmin", $row['id'], time() + 3600);
        echo '<!doctype html>';
        echo '<html>';
        echo '<head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">';
        echo '<title>' . $leer['institucion'] . ' - Administración EducoVota</title>';
        echo '<link href="../estilo4.css" rel="stylesheet" type="text/css">';
        echo '</head>';
        echo '<body>';
        include_once("../java.html");
        echo '<div align="center">';
        $faccion = "Ingreso_Admin-" . $_POST['usuario'];
        LogControl($faccion, $row['id']);
        echo '<h2>EDUCOVOTA - BIENVENIDO(A): ' . $row['nombres'] . ' ' . $row['apellidos'] . '</h2>';
        echo '<table style="font-weight:bold";>';
        echo '<thead><tr><th>MENÚ DE ADMINISTRACIÓN</th></tr></thead>';
        echo '<tr><td><a href="javascript:NuevaVentana(\'resultados.php\')" title="Consultar resultados de votación"><img src="../iconos/box.png" border="0" alt="Folder" /> Resultados de votación</a></td></tr>';
        echo '<tr><td><a href="javascript:NuevaVentana(\'general.php\')" title="Consulta de estudiantes por grado"><img src="../iconos/folder.png" border="0" alt="Folder" /> Consulta de estudiantes por grado</a></td></tr>';
        if ($row['id'] == 1) {
            echo '<tr><td><a href="javascript:NuevaVentana(\'candidatos.php\')" title="Candidatos"><img src="../iconos/datos.png" border="0" alt="Folder" /> Lista de Candidatos</a></td></tr>';
            echo '<tr><td><a href="javascript:NuevaVentana(\'importar.php\')" title="Importar datos"><img src="../iconos/book.png" border="0" alt="Datos" /> Importar datos estudiantes</a></td></tr>';
            echo '<tr><td><a href="javascript:NuevaVentana(\'administradores.php?id=' . md5($row['id']) . '\')" title="Administradores del sistema"><img src="../iconos/users.png" border="0" alt="Datos" /> Administradores del sistema</a></td></tr>';
            echo '<tr><td><a href="javascript:NuevaVentana(\'configuraciones.php\')" title="Configuración general"><img src="../iconos/hoja.png" border="0" alt="Config" /> Configuración general</a></td></tr>';
        }
        echo '<tr><td><a href="javascript:NuevaVentana(\'bitacora.php\')" title="Bitácora del sistema"><img src="../iconos/find.png" border="0" alt="bitácora" /> Bitácora del sistema</a></td></tr>';
        echo '<tr><td><a href="javascript:NuevaVentana(\'cambiarclave.php?id=' . md5($row['id']) . '\')" title="Cambiar contraseña de acceso"><img src="../iconos/clave.png" border="0" alt="Clave" /> Cambiar contraseña</a></td></tr>';
        echo '<tr><td><a href="salir.php" title="Salir del sistema"><img src="../iconos/salir.png" border="0" alt="Salir" /> Salir del sistema</a></td></tr>';
        echo '</table>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    } else {
        setcookie("VotaDatAdmin", "", time() - 3600);
        include_once("encabezado.phtml");
        $faccion = "Fallido_Admin-" . $_POST['usuario'];
        LogControl($faccion, 0);
        echo '<table>';
        echo '<tr><td class="cen" colspan="2"><strong>Datos de ingreso inválidos<br><br>';
        echo '<a href="javascript:history.go(-1)">Volver a intentar</a></strong></td></tr>';
        echo '</table></div></body></html>';
    }

    $db->close();
}
