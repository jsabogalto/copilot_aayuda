
<?php
include("../includes/class.phpmailer.php");
include("../includes/class.smtp.php");
include('../includes/Database.php');
include('../includes/Database_blue.php');
include("../includes/ejemplo.php");

$syscon = new Databas;
$syscon2 = new Databas;
$conexion = new Database;
$conexion2 = new Database;
$conexion3 = new Database;
$conexion4 = new Database;
$conexion5 = new Database;
$conexion6 = new Database;

include('../includes/FastTemplate.php');
$templete = new FastTemplate('../plantillas/pedidos');
$templete->no_strict();
include ('../includes/funciones.php');
include ('../includes/chatApi.php');
validar2($_COOKIE["mkid"], $_COOKIE["mksesion"]);
//VALIDAR PERMISOS EN EL MODULO
$modulo = reg('modulos', 'ruta', 'pedidos');
$verpermis = ver_permisos($mkid, $modulo["id_modulo"]);
if($verpermis == 0){
    redirigir('admin3/index/home/m/37/');
    exit();
}
$calendario_meses = array(
    'January'=>'Enero',
    'February'=>'Febrero',
    'March'=>'Marzo',
    'April'=>'Abril',
    'May'=>'Mayo',
    'June'=>'Junio',
    'July'=>'Julio',
    'August'=>'Agosto',
    'September'=>'Septiembre',
    'October'=>'Octubre',
    'November'=>'Noviembre',
    'December'=>'Diciembre'
);
//*****************************
function fecha_mysql_to_unix($fecha) {
  $phpdate = strtotime( date($fecha) );
    return $phpdate;
}
switch ($a) {
    case '':
        $templete->define(array('principal' => 'pedidos.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $user = reg('usuarios','id_usuario',$_COOKIE["mkid"]);
        
        //CAMBIAR ESTADO
        $conexion2->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '26' AND id_rol_reg = '".$user["rol"]."' ");
        //echo "SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '26' AND id_rol_reg = '".$user["rol"]."' ";
        if($conexion2->num_rows() > 0){
            $templete->assign('CREARPEDIDO', '<a href="admin3/pedidos/crear/"><button type="button" class="btn btn-primary btn-sm">Crear Pedidos</button></a>');
            $templete->assign('CONSULTAP', '<a href="admin3/pedidos/consultar/"><button type="button" class="btn btn-primary btn-sm">Consultar Pedido</button></a>');
        }else{
            $conexion2->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '52' AND id_rol_reg = '".$user["rol"]."' ");
            if($conexion2->num_rows() > 0){
                $templete->assign('CONSULTAP', '<a href="admin3/pedidos/consultar/"><button type="button" class="btn btn-primary btn-sm">Consultar Pedido</button></a>');
            }else{
                $templete->assign('CONSULTAP', '');
            }
        }

        /*$conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'No Vigente' WHERE b.aut_cartera != '1' AND a.estado != 'Anulado' AND a.fecha_cotizacion = DATE_SUB(CURDATE(), INTERVAL 5 DAY) ");
        //$conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.email_vijencia = '0' WHERE a.email_vijencia != '1' ");
        $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Aprobado' WHERE b.aut_compras = '1' AND b.aut_artes = '1' AND b.aut_cliente = '1' AND a.estado != 'No Vigente' ");
        $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Anulado' WHERE b.aut_compras = '3' AND b.aut_artes = '3' AND b.aut_cliente = '3' AND b.aut_cartera = '3' ");
        $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Generado' WHERE b.aut_compras = '0' AND b.aut_artes = '0' AND b.aut_guardado = '1' AND a.estado != 'No Vigente' ");
        $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Autorizado' WHERE b.aut_compras = '1' AND b.aut_artes = '1' AND b.aut_cliente = '1' AND b.aut_cartera = '1' AND b.aut_visual = '1' ");

        $conexion3->query("SELECT * FROM pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido WHERE b.aut_cartera != '1' AND a.email_vijencia = '0' AND a.fecha_cotizacion = DATE_SUB(CURDATE(), INTERVAL 5 DAY)  ");
        //$conexion3->query("SELECT * FROM pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido WHERE a.numero_pedido = 'PA02-67119'  ");
        while($conexion3->next_record()){
            $u = reg('usuarios','id_usuario',$conexion3->f("usuario_creador"));
            $ev = explode("|",$conexion3->f("info_quien"));
            enviar_mail($u['email'], 'Syspromall', 'Pedido # ' . $conexion3->f("numero_pedido") . ' Vigencia', email_vigencia('pruebaspromall@gmail.com',$conexion3->f("numero_pedido"),$conexion3->f("nom_realiza_pedido")));
            enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $conexion3->f("numero_pedido") . ' Vigencia', email_vigencia('pruebaspromall@gmail.com',$conexion3->f("numero_pedido"),$conexion3->f("nom_reak9liza_pedido"))); 
            enviar_mail(trim($ev[0]), 'Syspromall', 'Pedido # ' . $conexion3->f("numero_pedido") . ' Vigencia', email_vigencia($ev[0],$conexion3->f("numero_pedido"),$conexion3->f("nom_realiza_pedido"))); 
            $conexion->query("UPDATE pedidos SET email_vijencia = '1' WHERE numero_pedido = '".$conexion3->f('numero_pedido')."' ");
            $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$conexion3->f("id_pedido")."','<b>Vencimiento de Vigencia del pedido</b>','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
        }*/



        //*************************FIN DE ENVIO DEL CAMBIAR ESTADO//**********************************
    break;
    case 'crear':
        $templete->define(array('principal' => 'cargar_cotizaciones.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $cadena = "SELECT *, FROM_UNIXTIME(fecha_creacion) AS fecha_final, FROM_UNIXTIME(ult_actualizacion) AS ult_actual FROM cotizaciones WHERE estado='Cotizacion' AND vigente='1' ";
        //REVISAR PERMISO PARA VER OTRAS COTIZACIONES O SOLO SUS PROPIAS COTIZACIONES -->
        $modulo = reg('modulos', 'ruta', 'Revisar Otras Cotizaciones');
        $verpermis = ver_permisos($_COOKIE['mkid'], $modulo["id_modulo"]);
        if($verpermis == 0){//SI NO TIENE EL PERMISO PARA VER OTRAS COTIZACIONES
            $cadena .= " AND usuario='".$_COOKIE['mkid']."' ";
        }
        //*******************************************************************************
        $cadena .= "ORDER BY id_cotizacion ASC;";
        $n = 0;
        $templete->define_dynamic('BCOTIZAS', 'principal');
        $conexion->query($cadena);

        while($conexion->next_record())
        {
            $fecha = explode(" ", $conexion->f("fecha_final"));
            $ult_actual = explode(" ", $conexion->f("ult_actual"));

            //DEFINIR VIGENCIA DE LA COTIZACION (5 DIAS CALENDARIO)
            $nuevafecha = strtotime('-5 day' , strtotime(date("Y-m-d")));
            $nuevafecha = date('Y-m-d', $nuevafecha);
            if($fecha[0] < $nuevafecha && $conexion->f("estado") == 'Cotizacion'){//SI LA COTIZACION DEL CICLO ES MAS ANTIGUA A 7 DIAS
                $vigente = 0;
            }else{
                $vigente = 1;
            }
            //******************************************
            if($vigente == 1){
                $cliente = reg('clientes', 'id_cliente', $conexion->f("cliente"));
                $agente= reg('usuarios', 'id_usuario', $conexion->f("usuario"));
                $n++;
                $conexion2->query("SELECT SUM(valortotal_producto + valortotal_servicio) AS suma FROM registros_cotizacion WHERE id_cotizacion_registro='".$conexion->f("id_cotizacion")."';");
                $conexion2->next_record();
                $precio_venta = $conexion2->f("suma");
                $templete->assign('NOCOTIZACION', $conexion->f("id_cotizacion"));
                $templete->assign('ESTADO', str_replace("05:00:00", "", $conexion->f("fecha_final")));
                $templete->assign('EMPRESA', utf8_decode($cliente["empresa_ruy"]));
                $templete->assign('CONTACTO', $cliente["razon_social"]);
                $templete->assign('VALORTOTAL', '$' . number_format($precio_venta, 0, ".", ","));
                $templete->assign('VALORTOTAL_PEDIDO', (($precio_venta * 19) / 100) + $precio_venta );
                $templete->assign('AGENTE', $agente["usuario"]);
                if ($cliente["rut"] == "") {
                    $templete->assign('VERTOLTIP', 'SIN RUT, POR FAVOR REGISTRE EL RUT PARA INICIAR EL PEDIDO');
                    $templete->assign('DISABLE', 'disabled=""');
                }else{
                    $templete->assign('VERTOLTIP', 'DATOS VALIDOS !.');
                    $templete->assign('DISABLE', '');
                }
                $templete->assign('NRADIOACT', 'radio' . $n);
                $templete->parse('LISTABCOTIZAS', '.BCOTIZAS');
            }
        }
        $templete->assign('CONTADOR', $n);
    break;
    case 'pedir_datos':
        if($contador > 0){//cuenta que obtenga una cotizacion
            
            $cadena_cotizaciones = "";
            $id_cot = "";

            for($i = 1; $i <= $contador; $i++){
                if(isset($_POST['radio'.$i])){
                    if($cadena_cotizaciones == ""){
                        $id_cot = $_POST['radio'.$i];
                    }
                    $cadena_cotizaciones .= $_POST['radio'.$i] . ";";
                }
            }

            $array_cotizas = array();

            $exp_cadena = explode(';', $cadena_cotizaciones);
            for($i = 0; $i < count($exp_cadena); $i++){
                if($exp_cadena[$i] != ''){
                    array_push($array_cotizas, $exp_cadena[$i]);
                }
            }

            if($cadena_cotizaciones != ""){

                $precodigo_cot = explode(";", $cadena_cotizaciones);
                $precodigo_cot = explode("-", $precodigo_cot[0]);
                $precodigo_cot = 'P' . strtoupper(substr($precodigo_cot[0], 1, 3)) . '-' . $precodigo_cot[1];
                $precodigo_cot = str_replace(";", "", $precodigo_cot);
                //$precodigo_cot -> Variable la cual lleva el conseucutivo o el id de las cotizaciones

                //Verifica si ya se encuentra registrado el primer paso 
                    $conexion2->query("SELECT * FROM pedidos_temporal WHERE numero_pedido = '".$precodigo_cot."' ");
                    if($conexion2->num_rows()>0){
                        redirigir("admin3/pedidos/confirmar_crear/cadena_cotizaciones/".$precodigo_cot."/cadena/".$cadena_cotizaciones."/");
                    }
                //

                //si anteriormente no encuentra ningun pedido continua con el proceso normal..
                //Valida el cupo del cliente 

                $cotiza = reg('cotizaciones','id_cotizacion', $id_cot);
                $reg_cotiza = reg('registros_cotizacion','id_cotizacion_registro', $cotiza["id_cotizacion"]);
                $cliente = reg('clientes', 'id_cliente', $cotiza["cliente"]);
                $principal = reg('principal', 'codigo', $reg_cotiza["codigo_producto"]);

    
                $conexion3->query("SELECT * FROM pedidos a WHERE a.nit_cc = '".$cliente['no_documento']."' AND a.forma_pago NOT LIKE '%Sin%' AND a.estado != 'Anulado' AND a.estado != 'No Vigente' ");
                if ($conexion3->num_rows() > 0) {
                    $numero = '';

                    while($conexion3->next_record()) {

                        $fact = reg('facturacion','f_ordenc',$conexion3->f("numero_pedido"),'ORDER BY consecutivo_inh DESC');
                        $estf = reg('cartera_testados','factura_testado', $fact["consecutivo_inh"]);


                        if ($estf['clase_estado'] == 'SPP' || $estf['clase_estado'] == 'PPP') {
                            $numero_pedido = $conexion3->f("numero_pedido");                             
                        }else{
                            if ($fact['consecutivo_inh'] != '') {
                               $numero_pedido = '';
                            }else{
                                $numero_pedido = $conexion3->f("numero_pedido");
                            }
                            
                        }

                        if ($numero_pedido != '') {
                            $numero .= $numero_pedido.';';
                        }

                    }
                    
                    $variable_credito = valida_credito_al_crear_pedido($numero,$cliente['no_documento'],$valor_total); 
                } 
                echo $variable_credito;
                /*if ($variable_credito == 0) {
                    echo "  <html>
                              <head>
                                <head>   
                                </head>
                                <body>
                                <script src='https://static.codepen.io/assets/common/stopExecutionOnTimeout-de7e2ef6bfefd24b79a3f68b414b87b8db5b08439cac3f1012092b2290c719cd.js'>
                                </script>
                                <script src='https://cdn.jsdelivr.net/npm/sweetalert2'></script>
                                <script src='https://static.codepen.io/assets/common/stopExecutionOnTimeout-de7e2ef6bfefd24b79a3f68b414b87b8db5b08439cac3f1012092b2290c719cd.js'>
                                </script>
                                <script src='https://cdn.jsdelivr.net/npm/sweetalert2'></script>
                                  <script id='rendered-js'>
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Oops...<br>Solicitud rechazada',
                                        html: 'El cliente debe las lukas',
                                        footer: 'En segundos se volvera a cargar la página' });
                                                
                                      function call(){
                                        window.location.href = 'https://sys.promall.com.co/promall/area/admin3/pedidos/crear/m/41/';
                                      }
                                  setInterval(function() { call(); },10000);
                                </script>
                                
                              </body>
                            </html>";
                }else{*/

                //Envia al primer formualrio de creacion del pedido
                $templete->define(array('principal' => 'ingreso_datos_pedido.html'));
                $templete->assign('MODULOS',modulos());
                $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
                //

                

                $conexion->query("SELECT * FROM registros_cotizacion a INNER JOIN principal b ON a.codigo_producto = b.codigo WHERE id_cotizacion_registro = '".$cotiza["id_cotizacion"]."' AND produccion_nacional = '1'");
                $conexion->next_record();
                $fechaInicial = date("Y-m-d"); //obtenemos la fecha de hoy, solo para usar como referencia al usuario  
                $hora = strtotime(date('h:i:s A'));
                $hora_dos = strtotime('01:00:00 PM');
                if($conexion->num_rows() == 0){
                    $conexion2->query("SELECT * FROM registros_cotizacion WHERE id_cotizacion_registro = '".$cotiza["id_cotizacion"]."' AND codigo_servicio != ''");
                    $conexion2->next_record();
                    if($conexion2->num_rows() == 0){
                        if ($hora > $hora_dos ){
                            $MaxDias = 4;
                        }if($hora < $hora_dos ){
                            $MaxDias = 3;
                        }
                    }else{
                        $foo = $conexion2->f("codigo_servicio");
                        if (strpos($foo, 'SER') !== false){
                            if ($hora > $hora_dos ){
                                $MaxDias = 4;
                            }if($hora < $hora_dos ){
                                $MaxDias = 3;
                            }
                        }else{
                            if ($hora > $hora_dos ){
                                $MaxDias = 9;
                            }if($hora < $hora_dos ){
                                $MaxDias = 8;
                            }
                        }
                    }
                }else{
                    if ($hora > $hora_dos ) {
                        $MaxDias = 19;
                    }if($hora < $hora_dos ){
                        $MaxDias = 18;
                    }
                }
                
                for ($i=0; $i<$MaxDias; $i++)  
                {  
                    $Segundos = $Segundos + 86400;
                    $caduca = date("D",time()+$Segundos);
                    $caducaf = date("Y-m-d",time()+$Segundos);
                    if ($caduca == "Sat"){  
                        $i--;  
                    }elseif($caduca == "Sun"){  
                        $i--;  
                        }elseif ($caducaf == '2020-03-23') {
                            $i--; 
                        }elseif ($caducaf == '2020-04-09') {
                            $i--; 
                        }elseif ($caducaf == '2020-04-10') {
                            $i--; 
                        }elseif ($caducaf == '2020-05-01') {
                            $i--; 
                        }elseif ($caducaf == '2020-05-25') {
                            $i--; 
                        }elseif ($caducaf == '2020-06-15') {
                            $i--; 
                        }elseif ($caducaf == '2020-06-22') {
                            $i--; 
                        }elseif ($caducaf == '2020-07-20') {
                            $i--; 
                        }elseif ($caducaf == '2020-08-07') {
                            $i--; 
                        }elseif ($caducaf == '2020-08-17') {
                            $i--; 
                        }elseif ($caducaf == '2020-10-12') {
                            $i--; 
                        }elseif ($caducaf == '2020-11-02') {
                            $i--; 
                        }elseif ($caducaf == '2020-11-16') {
                            $i--; 
                        }elseif ($caducaf == '2020-12-08') {
                            $i--; 
                        }elseif ($caducaf == '2020-12-25') {
                            $i--; 
                        }else{  
                            $nuevafecha = date("Y-m-d",time()+$Segundos);  
                        }
                  }
                
                
                $templete->assign('FECHARECIBIDO', $nuevafecha);
                $templete->assign('FECHACOTIZACION', date("Y-m-d",$cotiza["fecha_creacion"]));
                $templete->assign('EMAIL',$cliente["email_dilijencia"]);
                $templete->assign('RAZONS',$cliente["empresa_ruy"]);
                $templete->assign('NODOCU',$cliente["no_documento"]);
                $templete->assign('DIRFACT',$cliente["dir_fact"]);
                $templete->assign('CIUFACT',$cliente["ciu_fact"]);
                $templete->assign('TELFACT',$cliente["tel_fact"]);
                $templete->assign('RUTS',$cliente["email_rut"]);
                $templete->assign('TELEFONO',$cliente["telefono"]);
                
                $credito = str_replace('"','', $cliente["credito"]);
                if ($credito == "") {
                    $templete->assign('CREDITOS', 'SIN CREDITO PREVIO');
                }else{
                    $templete->assign('CREDITOS',$cliente["credito"]);
                }
                if ($credito == "Sin credito") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>');
                }
                if ($credito == "Credito 15 dias") {
                    $templete->assign('CREDITO', '<option value='.$credito.' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 15 días">50% anticipo 50% credito 15 días</option>
                        <option value="100% credito 15 días">100% credito 15 días</option>');
                }
                if ($credito == "Credito 30 dias") {
                    $templete->assign('CREDITO', '<option value='.$credito.' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>');
                }
                if ($credito == "Credito 45 dias") {
                    $templete->assign('CREDITO', '<option value='.$credito.' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 45 días">50% anticipo 50% credito 45 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 45 días">100% credito 45 días</option>');
                }
                if ($credito == "Credito 60 dias") {
                    $templete->assign('CREDITO', '<option value='.$credito.' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 60 días">100% credito 60 días</option>');
                }
                if ($credito == "Credito 90 dias") {
                    $templete->assign('CREDITO', '<option value='.$credito.' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                        <option value="50% anticipo 50% credito 90 días">50% anticipo 50% credito 90 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 60 días">100% credito 60 días</option>
                        <option value="100% credito 90 días">100% credito 90 días</option>');
                }
                $templete->assign('REALIZA_PEDIDO',$cliente["razon_social"]);
                $templete->assign('CONTADOR',$contador);
                $templete->assign('CADENA',$cadena_cotizaciones);
                $templete->define_dynamic('BCIUDAD', 'principal');
                $templete->define_dynamic('BCIUDAD2', 'principal');
                $conexion->query("SELECT DISTINCT(nombre_ciudad) AS ciudad, departamento, indicativo_dep FROM ciudades ORDER BY ciudad;");
                while($conexion->next_record()){
                    $templete->assign('CIUDAD',  utf8_encode($conexion->f("ciudad") . ', ' . $conexion->f("departamento") .'/'. $conexion->f("indicativo_dep")));
                    $templete->assign('CIUDAD2', utf8_encode($conexion->f("ciudad") . ', ' . $conexion->f("departamento") .'/'. $conexion->f("indicativo_dep")));
                    $templete->parse('LISTABCIUDAD', '.BCIUDAD');
                    $templete->parse('LISTABCIUDAD2', '.BCIUDAD2');
                }
                $templete->define_dynamic('BDEP', 'principal');
                $conexion->query("SELECT DISTINCT(departamento) AS dpto FROM ciudades ORDER BY departamento;");
                while($conexion->next_record()){
                    $templete->assign('DEPARTAMENTO', utf8_encode($conexion->f("dpto")));
                    $templete->parse('LISTABDEP', '.BDEP');
                }
                
                $templete->define_dynamic('BOBSERVAS', 'principal');
                $conexion->query("SELECT * FROM listado_observaciones_pedidos ORDER BY id_lista_obs;");
                while($conexion->next_record()){
                    $templete->assign('IDOBSERVA', $conexion->f("id_lista_obs"));
                    $templete->assign('DOBSERVA', utf8_encode($conexion->f("titulo")));
                    $templete->parse('LISTABOBSERVAS', '.BOBSERVAS');
                }

                $templete->define_dynamic('BCOTACTS', 'principal');
                for($i = 0; $i < count($array_cotizas); $i++){
                    $n= 0;

                    $control_grupos = 0;
                    $conexion->query("SELECT * FROM cotizaciones INNER JOIN registros_cotizacion ON id_cotizacion=id_cotizacion_registro AND id_cotizacion='".$array_cotizas[$i]."';");
                    while($conexion->next_record()){
                        $producto = reg('principal', 'codigo', $conexion->f("codigo_producto"));
                        $servicio = reg('listado_servicios', 'codigo', $conexion->f("codigo_servicio"));
                        $n++;
                        $templete->assign('N', $n);
                        $templete->assign('NOCOTIZACION', $array_cotizas[$i]);
                        $images = explode("|", $producto["imagen"]);
                        $imagex = $servicio["imagen"];
                        $imagen = $images[0];
                        $templete->assign('IMAGEN', '<img src="'.$imagen.'" style="width:70px;border-radius:160px;">');
                        $templete->assign('IMAGENS', '<img src="'.$imagex.'" style="width:70px;border-radius:160px;">');
                        $templete->assign('PRODUCTO', $conexion->f("codigo_producto") . ' - ' . utf8_encode($producto["nombre_prov"]));
                        $templete->assign('SERVICIO', $conexion->f("codigo_servicio") . ' - ' . utf8_encode($servicio["servicio"]));
                        $templete->assign('CANTIDAD', $conexion->f("cantidad"));
                        $templete->assign('VALORTOTALPRODUCTO', '$ ' . number_format($conexion->f("valortotal_producto"), 0, ",", "."));
                        $templete->assign('VALORTOTALSERVICIO', '$ ' . number_format($conexion->f("valortotal_servicio"), 0, ",", "."));
                        $templete->assign('NAMEA', 'a'.$n.'[]');
                        $templete->assign('NAMEB', 'b'.$n.'[]');
                        $templete->assign('NAMEC', 'c'.$n.'[]');
                        $templete->assign('NAMED', 'd'.$n.'[]');
                        $templete->assign('NAMEAA', 'aa'.$n.'[]');
                        $templete->assign('NAMEBB', 'bb'.$n.'[]');
                        $templete->assign('NAMECC', 'cc'.$n.'[]');
                        $templete->assign('NAMEDD', 'dd'.$n.'[]');
                        $templete->assign('IDREGISTRO', $conexion->f("id_registro_cotizacion"));
                        $templete->assign('NRADIOACT', 'radio' . $n);
                        $templete->assign('NAMESEL', 'select' . $n);
                        /*if($control_grupos == 0){
                            $templete->define_dynamic('DIVGRUPO', 'principal');
                            for($f = 2; $f <= 20; $f++){
                                $templete->assign('VALCICLO', $f);
                                $templete->assign('CICLO', $f . ' Grupos');
                                $templete->parse('LISTADIVGRUPO', '.DIVGRUPO');
                            }
                        }*/
                        $a = $conexion->f("codigo_producto");
                        $b = $conexion->f("codigo_servicio");
                        
                        
                        if ($a == '') {
                            $templete->assign('HIDDS', 'hidden');
                        }else{
                            $templete->assign('HIDDS', '');
                        }

                        if ($b == '') {
                            $templete->assign('HIDDX', 'hidden');
                        }else{
                            $templete->assign('HIDDX', '');
                        }


                        if ($a != '' && $b != '') {
                            $templete->assign('HIDD', 'hidden');
                        }else{
                            $templete->assign('HIDD', '');
                        }

                        $templete->parse('LISTABCOTACT', '.BCOTACTS');
                        $control_grupos++;
                    }
                }
                //}//si tiene cupo en el cresito
            }else{
                redirigir("admin3/pedidos/crear/m/41/");
            }
        }else{
            redirigir("admin3/pedidos/crear/m/41/");
        }
    break;
    case 'guardar':
        $registro = reg('registros_cotizacion', 'id_registro_cotizacion', $_POST['cadena_cotizaciones']);//CODIGO DE REGISTRO DE LA PRIMERA COTIZACION
        $precodigo_cot = explode(";",  $_POST['cadena_cotizaciones']);
        $precodigo_cot = explode("-",  $precodigo_cot[0]);
        $precodigo_cot = 'P' . strtoupper(substr($precodigo_cot[0], 1, 3)) . '-' . $precodigo_cot[1];
        $precodigo_cot = str_replace(";", "", $precodigo_cot);
        
        if ($fecharecibidos == '' && $fecharecibido == '' ){
            $fecharecibidox = $_POST['fechacalculada'];
        }
        
        if ($fecharecibido != '') {
            $fecharecibidox = $_POST['fecharecibido'];
        }if ($fecharecibidos != '') {
            $fecharecibidox = $_POST['fecharecibidos'];
        }


        if ($fechasindespacho != '') {
            $fechasindespacho = $_POST['fechasindespacho'];
        }if ($fechadespachos != '') {
            $fechasindespacho = $_POST['fechasindespachos'];
        }
        if ($fechadespacho != '') {
            $fechadespacho = $_POST['fechadespacho'];
        }if ($fechadespachos != '') {
            $fechadespacho = $_POST['fechadespachos'];
        }

        $fecha_one = $fechasindespacho;
        $fecha_two = $fechadespacho;

        $conexion2->query("SELECT * FROM pedidos_temporal WHERE numero_pedido = '".$precodigo_cot."' ");
        if($conexion2->num_rows()==0){
            $conexion2->query("INSERT INTO pedidos_temporal VALUES (NULL, '".$precodigo_cot."', 'Creado', '".$_POST['realiza_pedido']."', '".$_POST['recibe_pedido']."', '".$_POST['nombre']."', '".$_POST['nitcc']."', '".$_POST['dir_facturacion']." / ".$_POST['ciudad_facturacion']."', '".$_POST['direnvio']." / ".$_POST['ciudad_enviar']."','".$_POST['telcontacto']."', '".$_POST['telcontactodirecto']. '|' .$_POST['celcontactodirecto']."', '".$_POST['email']."', '".$_POST['observas']."', '".$_POST['medio_pago']."', '".$_POST['forma_pago'].'/'.$_POST['credito_cliente']."', '".$_COOKIE['mkid']."', '".date("Y-m-d H:i:s")."', '".$fecharecibidox."', '".$fecha_two."', '".$fecha_one."', '".$_POST['observacionagente']."','".$_POST['dep_pago']."','','".$_POST['email_quien'].'|'.$_POST['tel_quien']."','','".$_POST['fecha_cotizacion']."','".$_POST['cadena_cotizaciones']."','0');");
        }else{
            $conexion2->query("UPDATE pedidos_temporal SET nom_realiza_pedido = '".$_POST['realiza_pedido']."', nom_recibe_pedido ='".$_POST['recibe_pedido']."', razon_social= '".$_POST['nombre']."', nit_cc='".$_POST['nitcc']."',dir_facturacion= '".$_POST['dir_facturacion']." / ".$_POST['ciudad_facturacion']."', dir_envio='".$_POST['direnvio']." / ".$_POST['ciudad_enviar']."',tel_contacto='".$_POST['telcontacto']."', tel_contacto_directo='".$_POST['telcontactodirecto']. '|' .$_POST['celcontactodirecto']."', email='".$_POST['email']."', observaciones='".$_POST['observas']."', medio_pago='".$_POST['medio_pago']."', forma_pago='".$_POST['forma_pago'].'/'.$_POST['credito_cliente']."', fecha_comprobante= '".$fecharecibidox."',fecha_despacho= '".$fecha_two."', fecha_sin_despacho='".$fecha_one."', observacion_agente ='".$_POST['observacionagente']."',dep_pago='".$_POST['dep_pago']."',info_quien='".$_POST['email_quien'].'|'.$_POST['tel_quien']."' WHERE numero_pedido = '".$precodigo_cot."' ");
        }
        redirigir("admin3/pedidos/confirmar_crear/cadena_cotizaciones/".$precodigo_cot."/cadena/".$_POST['cadena_cotizaciones']."/");    
    break;
    case 'guardar_two':
        $registro = reg('registros_cotizacion', 'id_registro_cotizacion', $_POST['cadena_cotizaciones']);//CODIGO DE REGISTRO DE LA PRIMERA COTIZACION
        $precodigo_cot = explode(";",  $_POST['cadena_cotizaciones']);
        $precodigo_cot = explode("-",  $precodigo_cot[0]);
        $precodigo_cot = 'P' . strtoupper(substr($precodigo_cot[0], 1, 3)) . '-' . $precodigo_cot[1];
        $precodigo_cot = str_replace(";", "", $precodigo_cot);
        $pedido = reg('pedidos_temporal', 'numero_pedido', $precodigo_cot);
        $ex = $_POST['binario'];
        $a = $_POST['a'];
        $b = $_POST['b'];
        $c = $_POST['c'];
        $coursex = $_POST['aportes'];
        $numero = $_POST['numero'];
        $id_registro = $_POST['id_registro'];

            if ($id_registro != '') {
                for($i=0, $count = count($coursex);$i<$count;$i++) {
                    if ($coursex[$i] != '') {
                        $conexion2->query("INSERT INTO registros_pedidos_t VALUES (NULL,'".$id_registro[$i]."','".$pedido['id_pedido']."','".$a[$i].'|'.$b[$i].'|'.$c[$i]."','".$coursex[$i]."','".date("Y-m-d H:i:s")."','".$_COOKIE["mkid"]."','".$ex[$i]."');");
                    }
                }
                redirigir("admin3/pedidos/confirmar_crear/cadena_cotizaciones/".$precodigo_cot."/cadena/".$_POST['cadena_cotizaciones']."/"); 
            }
   
    break;
    case 'redirigir':
    if ($_POST["uno"] != '') {
        redirigir('admin3/pedidos/confirmar_crear/cadena_cotizaciones/'.$_POST["unox"].'/cadena/'.$_POST["unos"].$_POST["uno"].'/');
    }if ($_POST["dos"] != '') {
        redirigir('admin3/pedidos/confirmar_crear/cadena_cotizaciones/'.$_POST["dosx"].'/cadena/'.$_POST["doss"].$_POST["dos"].'/');
    }if ($_POST["tre"] != '') {
        redirigir('admin3/pedidos/confirmar_crear/cadena_cotizaciones/'.$_POST["trex"].'/cadena/'.$_POST["tres"].$_POST["tre"].'/');
    }if ($_POST["cua"] != '') {
        redirigir('admin3/pedidos/confirmar_crear/cadena_cotizaciones/'.$_POST["cuax"].'/cadena/'.$_POST["cuas"].$_POST["cua"].'/');
    }if ($_POST["cin"] != '') {
        redirigir('admin3/pedidos/confirmar_crear/cadena_cotizaciones/'.$_POST["cinx"].'/cadena/'.$_POST["cins"].$_POST["cin"].'/');
    }
    break;
    case 'confirmar_crear':
        if ($_GET['cadena_cotizaciones'] != '') {
        $cadenax = $_GET['cadena_cotizaciones'];
        $cadena_cotizaciones = explode("-", $cadenax);
        $cadena_cotizaciones = 'C' . strtoupper(substr($cadena_cotizaciones[0], 1, 3)) . '-' . $cadena_cotizaciones[1] .';';
            $array_cotizas = array();
            $exp_cadena = explode(';', $_GET['cadena']);
            for($i = 0; $i < count($exp_cadena); $i++){
                if($exp_cadena[$i] != ''){
                    array_push($array_cotizas, $exp_cadena[$i]);
                }
            }
            if(count($array_cotizas) > 0){
                //////GUARDA TENPORAL
                $registro = reg('registros_cotizacion', 'id_registro_cotizacion', $cadena_cotizaciones);//CODIGO DE REGISTRO DE LA PRIMERA COTIZACION
                $precodigo_cot = explode("-", $cadena_cotizaciones);
                $precodigo_cot = 'P' . strtoupper(substr($precodigo_cot[0], 1, 3)) . '-' . $precodigo_cot[1];
                $precodigo_cot = str_replace(";", "", $precodigo_cot);
                               

                //////////////INICIO TEMPRAL 2
                
                /////////////////////////////////FIN TEMPORAL 2 /////////////////////
                
                $templete->define(array('principal' => 'mostrar_registros_cotizaciones.html'));
                $templete->assign('MODULOS',modulos());
                $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
                

                $conexion2->query("SELECT * FROM pedidos_temporal WHERE numero_pedido = '".$precodigo_cot."' ");
                while($conexion2->next_record()){
                $observa = reg('listado_observaciones_pedidos', 'id_lista_obs', $conexion2->f('observaciones'));
                $templete->assign('REALIZA_PEDIDO', $conexion2->f('nom_realiza_pedido'));
                $templete->assign('RECIBE_PEDIDO', $conexion2->f('nom_recibe_pedido'));
                $templete->assign('NOMBRE', $conexion2->f('razon_social'));
                $templete->assign('NITCC', $conexion2->f('nit_cc'));
                $dir_factu = explode("/", $conexion2->f('dir_facturacion'));
                $dir_envio = explode("/", $conexion2->f('dir_envio'));
                $templete->assign('DEP_PAGO', $conexion2->f('dep_pago'));  
                $templete->assign('CIUDAD_FACTURACION', $dir_factu[1]);
                $templete->assign('DIR_FACTURACION', $dir_factu[0]);
                $templete->assign('DIRENVIO', $dir_envio[0]);
                $templete->assign('CIUDAD_ENVIAR',$dir_envio[1].'/'.$dir_envio[2]);
                $templete->assign('TELCONTACTO', $conexion2->f('tel_contacto'));
                $tel_cd = explode("|", $conexion2->f('tel_contacto_directo'));
                $templete->assign('TELCONTACTODIRECTO', $tel_cd[0]);
                $templete->assign('CELCONTACTODIRECTO', $tel_cd[1]);
                $templete->assign('EMAIL', $conexion2->f('email'));
                $templete->assign('OBSERVAS', $conexion2->f('observaciones'));
                $templete->assign('MIRAOBSERVA', utf8_encode($observa['titulo']));
                $templete->assign('MEDIO_PAGO', $conexion2->f('medio_pago'));
                $forma_de_pago = explode("/", $conexion2->f('forma_pago'));
                $templete->assign('FORMA_PAGO', $forma_de_pago[0]);
                $templete->assign('CREDITOS', $conexion2->f('creditos'));
                $templete->assign('FECHARECIBIDO', $conexion2->f('fecha_comprobante'));
                
                
                $templete->assign('OBSERVACIONAGENTE', $conexion2->f("observacion_agente"));
                $precodigo_cota = explode("-", $conexion2->f("numero_pedido"));
                $precodigo_cota = 'C' . strtoupper(substr($precodigo_cota[0], 1, 3)) . '-' . $precodigo_cota[1];
                $templete->assign('CADENA', $precodigo_cota.';');
                $templete->assign('ALLCOTI', $conexion2->f("cotizaciones"));
                $all = explode(";", $conexion2->f("cotizaciones"));
                $get = explode(";", $_GET["cadena"]);
                $all_uno = $all[0];
                $all_dos = $all[1];
                $all_tre = $all[2];
                $all_cua = $all[3];
                $all_cin = $all[4];
                $get_uno = $get[0];
                $get_dos = $get[1];
                $get_tre = $get[2];
                $get_cua = $get[3];
                $get_cin = $get[4];
                if ($all_uno != $get_uno) {
                    $templete->assign('ALLUNO', '<input type="hidden" name="uno" value="'.$all_uno.';"><input type="hidden" name="unos" value='.$_GET['cadena'].'><input type="hidden" name="unox" value='.$precodigo_cot.'><input type="submit" name="alluno" value="llamar '.$all_uno.'">');
                }if ($all_dos != $get_dos) {
                    $templete->assign('ALLDOS', '<input type="hidden" name="dos" value="'.$all_dos.';"><input type="hidden" name="doss" value='.$_GET['cadena'].'><input type="hidden" name="dosx" value='.$precodigo_cot.'><input type="submit" name="alldos" value="llamar '.$all_dos.'">');
                }if ($all_tre != $get_tre) {
                    $templete->assign('ALLTRE', '<input type="hidden" name="tre" value="'.$all_tre.';"><input type="hidden" name="tres" value='.$_GET['cadena'].'><input type="hidden" name="trex" value='.$precodigo_cot.'><input type="submit" name="alltre" value="llamar '.$all_tre.'">');
                }if ($all_cua != $get_cua) {
                    $templete->assign('ALLCUA', '<input type="hidden" name="cua" value="'.$all_cua.';"><input type="hidden" name="cuas" value='.$_GET['cadena'].'><input type="hidden" name="cuax" value='.$precodigo_cot.'><input type="submit" name="allcua" value="llamar '.$all_cua.'">');
                }if ($all_cin != $get_cin) {
                    $templete->assign('ALLCIN', '<input type="hidden" name="cin" value="'.$all_cin.';"><input type="hidden" name="cins" value='.$_GET['cadena'].'><input type="hidden" name="cinx" value='.$precodigo_cot.'><input type="submit" name="allcin" value="llamar '.$all_cin.'">');
                }
                //

                if ($conexion2->f("observaciones") == 17) {
                    $templete->assign('SIN', 'style=""');
                    if ($conexion2->f("fecha_despacho") != '') {
                        $templete->assign('FECHADESPACHO', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_despacho"))));
                        $templete->assign('FECHASINDESPACHO', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_sin_despacho"))));
                    }
                }else{
                    $templete->assign('SIN', 'style="display:none;"');
                }if ($conexion2->f("observaciones") == 18) {
                    $templete->assign('CON', 'style=""');
                    if ($conexion2->f("fecha_sin_despacho") != '') {
                        $templete->assign('FECHADESPACHOS', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_despacho"))));
                        $templete->assign('FECHASINDESPACHOS', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_sin_despacho"))));
                    }
                }else{
                    $templete->assign('CON', 'style="display:none;"');
                }
                $historia_cliente = reg('clientes', 'no_documento', $conexion2->f('nit_cc'));
                $info_quien = explode("|", $conexion2->f('info_quien'));
                
                $templete->assign('EMAILQUIEN', $info_quien[0]);
                $templete->assign('TELQUIEN', $info_quien[1]);
                $credito = $forma_de_pago[0];
                $creditos = $historia_cliente['credito'];
                if ($creditos == "") {
                    $templete->assign('CREDITOS', 'SIN CREDITO PREVIO');
                }else{
                    $templete->assign('CREDITOS',$historia_cliente['credito']);
                }
                if ($creditos == "Sin credito") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>"'.$credito.'"</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>');
                }
                if ($creditos == "Credito 15 dias") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>"'.$credito.'"</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 15 días">50% anticipo 50% credito 15 días</option>
                        <option value="100% credito 15 días">100% credito 15 días</option>');
                }
                if ($creditos == "Credito 30 dias") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>"'.$credito.'"</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>');
                }
                if ($credito == "Credito 45 dias") {
                    $templete->assign('CREDITO', '<option value='.$cliente["credito"].' style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$cliente["credito"].'</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 45 días">50% anticipo 50% credito 45 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 45 días">100% credito 45 días</option>');
                }
                if ($creditos == "Credito 60 dias") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>"'.$credito.'"</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 60 días">100% credito 60 días</option>');
                }
                if ($creditos == "Credito 90 dias") {
                    $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>"'.$credito.'"</option>
                        <option value="100% Anticipo">100% Anticipo</option>
                        <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                        <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                        <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                        <option value="50% anticipo 50% credito 90 días">50% anticipo 50% credito 90 días</option>
                        <option value="100% credito 30 días">100% credito 30 días</option>
                        <option value="100% credito 60 días">100% credito 60 días</option>
                        <option value="100% credito 90 días">100% credito 90 días</option>');
                }
            }
                $templete->define_dynamic('BCIUDAD', 'principal');
                $templete->define_dynamic('BCIUDAD2', 'principal');
                
                $conexion->query("SELECT DISTINCT(nombre_ciudad) AS ciudad, departamento, indicativo_dep FROM ciudades ORDER BY ciudad;");
                while($conexion->next_record()){
                    $templete->assign('CIUDAD',  utf8_encode($conexion->f("ciudad") . ', ' . $conexion->f("departamento") .'/'. $conexion->f("indicativo_dep")));
                    $templete->assign('CIUDAD2', utf8_encode($conexion->f("ciudad") . ', ' . $conexion->f("departamento") .'/'. $conexion->f("indicativo_dep")));
                    $templete->parse('LISTABCIUDAD', '.BCIUDAD');
                    $templete->parse('LISTABCIUDAD2', '.BCIUDAD2');
                }
                $templete->define_dynamic('BDEP', 'principal');
                $conexion->query("SELECT DISTINCT(departamento) AS dpto FROM ciudades ORDER BY departamento;");
                while($conexion->next_record()){
                    $templete->assign('DEPARTAMENTO', utf8_encode($conexion->f("dpto")));
                    $templete->parse('LISTABDEP', '.BDEP');
                }
                $templete->define_dynamic('BOBSERVAS', 'principal');
                $conexion->query("SELECT * FROM listado_observaciones_pedidos ORDER BY id_lista_obs;");
                while($conexion->next_record()){
                    $templete->assign('IDOBSERVA', $conexion->f("id_lista_obs"));
                    $templete->assign('DOBSERVA', utf8_encode($conexion->f("titulo")));
                    $templete->parse('LISTABOBSERVAS', '.BOBSERVAS');
                }
                $conexion->query("SELECT * FROM listado_observaciones_pedidos WHERE id_lista_obs = '".$_POST['observas']."' ORDER BY id_lista_obs;");
                while($conexion->next_record()){
                    $templete->assign('MIRAOBSERVA', utf8_encode($conexion->f("titulo")));
                }
                $conexion->query("SELECT * FROM pedidos_temporal WHERE numero_pedido = '".$_GET['cadena_cotizaciones']."' GROUP BY numero_pedido");
                while($conexion->next_record()){
                    $templete->assign('YOSITOKO', yositoko($conexion->f("id_pedido")));
                }
                
                $templete->define_dynamic('BCOTACTS', 'principal');
                
                for($i = 0; $i < count($array_cotizas); $i++){

                $conexion->query("SELECT * FROM cotizaciones INNER JOIN registros_cotizacion ON id_cotizacion=id_cotizacion_registro  AND id_cotizacion = '".$array_cotizas[$i]."' ");
                    while($conexion->next_record()){
                        $producto = reg('principal', 'codigo', $conexion->f("codigo_producto"));
                        $servicio = reg('listado_servicios', 'codigo', $conexion->f("codigo_servicio"));
                        $registros_pt = reg('registros_pedidos_t', 'reg_cotizacion', $conexion->f("id_registro_cotizacion"));
                        $uno = $registros_pt['reg_cotizacion'];
                        $dos = $conexion->f("id_registro_cotizacion");
                        if ($dos == $uno) {
                            $templete->assign('MENSA', '<input type="submit" name="" value="GUARDAR SEGUNDO PASO" style="display: block;margin: auto;width: 30%;"><br>');
                            $templete->assign('CAD', '<input type="hidden" name="cad" value='.$_GET['cadena'].'>');
                            $templete->assign('END', 'display:none;');
                        }if ($dos != $uno) {
                        $n++;

                        
                                 
                        if ($conexion->f("codigo_producto") == '' && $conexion->f("codigo_servicio") != '') {
                            $conexion2->query("SELECT * FROM registros_cotizacion WHERE id_cotizacion_registro = '".$conexion->f("id_cotizacion")."' AND codigo_servicio = 'MAR0300' ");
                            if ($conexion2->num_rows() >= 1) {
                                $templete->assign('BOBLI', '');
                                $templete->assign('EOBLI', '');//muestra el real
                                $templete->assign('BVALI', '<!--');
                                $templete->assign('EVALI', '-->');//oculta la validacion
                            }else{
                                $templete->assign('BOBLI', '<!--');
                                $templete->assign('EOBLI', '-->');//oculta el real
                                $templete->assign('BVALI', '');//muestra la validacion
                                $templete->assign('EVALI', '');
                            }
                        }else{
                            $templete->assign('BOBLI', '');
                            $templete->assign('EOBLI', '');
                            $templete->assign('BVALI', '<!--');
                            $templete->assign('EVALI', '-->');//oculta la validacion
                        }
                        
                        

                        $templete->assign('END', '');
                        $templete->assign('MENSA', '');
                        $templete->assign('CONG', '');
                        $templete->assign('SERV', '');
                        $templete->assign('CAD', '');
                        $templete->assign('N', $n);
                        $templete->assign('NOCOTIZACION', $array_cotizas[$i]);
                        $images = explode("|", $producto["imagen"]);
                        $imagex = $servicio["imagen"];
                        $imagen = $images[0];
                        $templete->assign('ENDS', 'display:none;');

                        $codigo_p = $conexion->f("codigo_producto");
                        $codigo_s = $conexion->f("codigo_servicio");
                        
                        if (strpos($codigo_s, 'SER') !== false) {// si no es ser
                            $templete->assign('HISER', 'hidden');
                            $templete->assign('RISER', '');
                        }else{
                            $templete->assign('HISER', '');
                            $templete->assign('RISER', 'required=""');
                        }

                        if ($codigo_p != '' && $codigo_s != '') {
                            $templete->assign('PRODUCTO', $conexion->f("codigo_producto") . ' - ' . utf8_encode($producto["nombre_prov"]));
                            $templete->assign('SERVICIO', $conexion->f("codigo_servicio") . ' - ' . utf8_encode($servicio["servicio"]));
                            $templete->assign('IMAGEN', '<a href="'.$producto['url'].'"><img src="'.$imagen.'" class="panel-profile-img mb10"></a>');
                            $templete->assign('IMAGENS', '<img src="'.$imagex.'" class="panel-profile-img mb10">');
                            $templete->assign('OP', '<option value="3">EXCLUIR IVA DE LOS DOS ITEMS</option>');
                            $templete->assign('OPD', '<option value="2">EXCLUIR IVA DEL SERVICIO</option>');
                            $templete->assign('OPU', '<option value="1">EXCLUIR IVA DEL PRODUCTO</option>');
                        }
                        if ($codigo_s == '') {
                            $templete->assign('PRODUCTO', $conexion->f("codigo_producto") . ' - ' . utf8_encode($producto["nombre_prov"]));
                            $templete->assign('IMAGEN', '<a href="'.$producto['url'].'"><img src="'.$imagen.'" class="panel-profile-img mb10"></a>');
                            $templete->assign('IMAGENS', '');
                            $templete->assign('SERVICIO', '');
                            $templete->assign('OP', '');
                            $templete->assign('OPD', '');
                            $templete->assign('OPU', '<option value="1">EXCLUIR IVA DEL PRODUCTO</option>');
                        }
                        if ($codigo_p == '') {
                            $templete->assign('PRODUCTO', '');
                            $templete->assign('SERVICIO', $conexion->f("codigo_servicio") . ' - ' . utf8_encode($servicio["servicio"]));
                            $templete->assign('IMAGEN', '');
                            $templete->assign('IMAGENS', '<img src="'.$imagex.'" class="panel-profile-img mb10">');
                            $templete->assign('OP', '');
                            $templete->assign('OPD', '<option value="2">EXCLUIR IVA DEL SERVICIO</option>');
                            $templete->assign('OPU', '');
                        }
                        
                        $templete->assign('CANTIDAD', $conexion->f("cantidad"));
                        $templete->assign('VALORTOTALPRODUCTO', '$ ' . number_format($conexion->f("valortotal_producto"), 0, ",", "."));
                        $templete->assign('VALORTOTALSERVICIO', '$ ' . number_format($conexion->f("valortotal_servicio"), 0, ",", "."));
                        $templete->assign('NAMEA', 'a'.$n.'[]');
                        $templete->assign('NAMEB', 'b'.$n.'[]');
                        $templete->assign('NAMEC', 'c'.$n.'[]');
                        $templete->assign('NAMED', 'd'.$n.'[]');
                        $templete->assign('NAMEAA', 'aa'.$n.'[]');
                        $templete->assign('NAMEBB', 'bb'.$n.'[]');
                        $templete->assign('NAMECC', 'cc'.$n.'[]');
                        $templete->assign('NAMEDD', 'dd'.$n.'[]');
                        $templete->assign('IDREGISTRO', $conexion->f("id_registro_cotizacion"));
                        $templete->assign('NRADIOACT', 'radio' . $n);
                        $templete->assign('NAMESEL', 'select' . $n);
                        $templete->assign('CADENAS', $_GET['cadena']);
                        
                        $a = $conexion->f("codigo_producto");
                        $b = $conexion->f("codigo_servicio");
                        
                        
                        if ($a == '') {
                            $templete->assign('HIDDS', 'hidden');
                            $templete->assign('RIDDS', '');
                        }else{
                            $templete->assign('HIDDS', '');
                            $templete->assign('RIDDS', 'required=""');
                        }
                        if ($b == '') {
                            $templete->assign('HIDDX', 'hidden');
                            $templete->assign('RIDDX', '');
                        }else{
                            $templete->assign('HIDDX', '');
                            $templete->assign('RIDDX', 'required=""');
                        }
                        if ($a != '' && $b != '') {
                            $templete->assign('HIDD', 'hidden');
                            $templete->assign('RIDD', '');
                        }else{
                            $templete->assign('HIDD', '');
                            $templete->assign('RIDD', 'required=""');
                        }

                        $templete->parse('LISTABCOTACT', '.BCOTACTS');
                        }
                        
                    }
                }
                $templete->assign('CONTADOR', $n);
            }else{
                redirigir("admin3/pedidos/crear/m/41/");
            }
        }else{
            redirigir("admin3/pedidos/crear/m/41/");
        }
    break;
    case 'final':
        if($contador > 0){
            $cadena_cotizaciones = "";$id_cot = "";
            for($i = 1; $i <= $contador; $i++){
                if(isset($_POST['radio'.$i])){
                    if($cadena_cotizaciones == ""){
                        $id_cot = $_POST['radio'.$i];
                    }
                    $cadena_cotizaciones .= $_POST['radio'.$i] . ";";
                }
            }
            $array_cotizas = array();
            $exp_cadena = explode(';', $cadena_cotizaciones);
            for($i = 0; $i < count($exp_cadena); $i++){
                if($exp_cadena[$i] != ''){
                    array_push($array_cotizas, $exp_cadena[$i]);
                }
            }
            if($cadena_cotizaciones != ""){
                $cotiza = reg('cotizaciones','id_cotizacion', $id_cot);
                $reg_cotiza = reg('registros_cotizacion','id_cotizacion_registro', $cotiza["id_cotizacion"]);
                $cliente = reg('clientes', 'id_cliente', $cotiza["cliente"]);
                $principal = reg('principal', 'codigo', $reg_cotiza["codigo_producto"]);
                $templete->define(array('principal' => 'final_observaciones.html'));
                /*$nuevafecha = strtotime('+8 day' , strtotime(date("Y-m-d")));
                $nuevafecha = date('Y-m-d', $nuevafecha);*/
                
            }else{
                redirigir("admin3/pedidos/crear/m/41/");
            }
        }else{
            redirigir("admin3/pedidos/crear/m/41/");
        }
    break;
    case 'delete':
        $a = $_POST['a'];
        $b = $_POST['b'];
        $c = $_POST['c'];
        $coursex = $_POST['aportes'];
        
        for($i=0, $count = count($coursex);$i<$count;$i++) {
            $reg_ped = reg('registros_pedidos','id_registro_pedido',$id_reg[$i]);
            $reg_cotiza = reg('registros_cotizacion', 'id_registro_cotizacion', $reg_ped["reg_cotizacion"]);
            $producto = reg('principal', 'codigo',  $reg_cotiza["codigo_producto"]);
            $servicio = reg('listado_servicios', 'codigo',  $reg_cotiza["codigo_servicio"]);
            $pedido = reg('pedidos', 'id_pedido',  $id_pedido);
            $atp = reg('autorizaciones_pedidos', 'num_pedido',  $pedido["numero_pedido"]);
            $correo = explode("|", $pedido['info_quien']);
            $productof = $producto["codigo"] . ' - ' . utf8_encode($producto["nombre_prov"]);
            $serviciof = $servicio["codigo"] . ' - ' . utf8_encode($servicio["servicio"]);
            $usuario = reg('usuarios', 'id_usuario', $pedido['usuario_creador']);
            $dat = explode("|", $reg_ped['datos']);
            $aa = $dat[0];
            $bb = $dat[1];
            $cc = $dat[2];
            $canti = $reg_ped['cantidad'];
            if ($coursex[$i] != '') {
                if ($id_reg[$i] == '') {
                    $conexion->query("INSERT INTO registros_pedidos VALUES (NULL,'".$reg_cotizacion."','".$id_pedido."','".$a[$i].'|'.$b[$i].'|'.$c[$i]."','".$coursex[$i]."','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."','".$reg_ped['ex_iva']."','','','','')");
                    $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','SE AGREGO UN NUEVO REGISTRO <b>".$a[$i].' '.$b[$i].' '.$c[$i]."</b> CON UNA CANTIDAD DE UNIDADES DE <b>".$coursex[$i]."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                }else{
                    if ($aa != $a[$i]) {
                        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','COLOR <b>".$aa."</b> CAMBIA A COLOR <b>".$a[$i]."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");

                        $conexion->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_servicio != '' AND numero_pedido='".$pedido["numero_pedido"]."';");
                        if($conexion->num_rows() != 0){
                            while($conexion->next_record()){
                              $conexion->query("UPDATE autorizaciones_pedidos SET aut_artes='0',aut_visual='0' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                            }
                        }
                        if ($atp['aut_cliente'] == 2) {
                            $conexion->query("UPDATE autorizaciones_pedidos SET aut_compras='0',aut_cartera='0',apr_edicion='1' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                        }else{
                            $conexion->query("UPDATE autorizaciones_pedidos SET aut_compras='0',aut_cliente='0',aut_cartera='0',apr_edicion='1' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                        }
                        
                    }
                    if ($bb != $b[$i]) {
                        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','TINTA <b>".$bb."</b> CAMBIA A TINTA <b>".$b[$i]."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                        $conexion->query("UPDATE autorizaciones_pedidos SET aut_artes='0',apr_edicion='2',aut_visual='0' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                    }
                    if ($cc != $c[$i]) {
                        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','LOGO <b>".$cc."</b> CAMBIA A LOGO <b>".$c[$i]."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                        $conexion->query("UPDATE autorizaciones_pedidos SET aut_artes='0',apr_edicion='2',aut_visual='0' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                    }
                    if ($canti != $coursex[$i]) {
                        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','CANTIDADES DEL ITEM ".$productof.$serviciof." CAMBIAN DE <b>".$canti."</b> A UN VALOR DE <b>".$coursex[$i]."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                        if ($atp['aut_cliente'] == 2) {
                            $conexion->query("UPDATE autorizaciones_pedidos SET aut_compras='0',aut_artes='0',apr_edicion='1',aut_visual='0' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                        }else{
                            $conexion->query("UPDATE autorizaciones_pedidos SET aut_compras='0',aut_artes='0',apr_edicion='1',aut_cliente='0',aut_visual='0' WHERE num_pedido='".$pedido["numero_pedido"]."';");
                        }
                        
                    }
                    $conexion->query("UPDATE registros_pedidos SET datos = '".$a[$i].'|'.$b[$i].'|'.$c[$i]."',cantidad = '".$coursex[$i]."', ex_iva = '".$binario."' WHERE id_registro_pedido = '".$id_reg[$i]."' ");
                    /*$conexion->query("UPDATE pedidos SET estado = 'Generado' WHERE numero_pedido = '".$pedido["numero_pedido"]."' ");
                    if ($pedido["estado"] == 'Aprobado') {
                        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','Estado del pedido cambia de <b>Aprobado</b> a <b> Generado </b> por causa del reinicio de los check´s de aprobacion ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                    }*/                   

                }
            }else{
                if ($id_reg[$i] != '') {
                    $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$id_pedido."','SE ELIMINO UN REGISTRO <b>".$aa.' '.$bb.' '.$cc."</b> CON UNA CANTIDAD DE UNIDADES DE <b>".$canti."</b> ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
                    $conexion->query("DELETE FROM registros_pedidos WHERE id_registro_pedido = '".$id_reg[$i]."' ");
                }
            }
        }

        if ($usuario['rol'] == '2' || $usuario['rol'] == '1') {
            $conexion2->query("UPDATE autorizaciones_pedidos SET aut_edi_cli='1' WHERE num_pedido='".$pedido["numero_pedido"]."';");
        }

        $total_iva = 0;
        $subtotal = 0;
        $solo_compras = 0;
        $solo_servicios = 0;

        $pedido = reg("pedidos","id_pedido", $_POST['id_pedido']);

        $conexion->query("SELECT DISTINCT(reg_cotizacion) AS codigo FROM registros_pedidos INNER JOIN pedidos ON id_pedido=id_pedido_reg AND id_pedido='".$_POST['id_pedido']."';");
        while($conexion->next_record()){
            $registrofinal = reg('registros_cotizacion', 'id_registro_cotizacion', $conexion->f("codigo"));
            $rp = reg('registros_pedidos', 'reg_cotizacion', $conexion->f("codigo"));

            $valortotalproducto = $registrofinal["valortotal_producto"];
            $valortotalservicio = $registrofinal["valortotal_servicio"];

            $subtotal += ($valortotalproducto + $valortotalservicio);
            $solo_compras += $valortotalproducto;
            $solo_servicios += $valortotalservicio;

  
            if ($rp['ex_iva'] == '1') {
                //CALCULAR IVA SERVICIO
                if ($registrofinal["id_cotizacion_registro"] == 'CA02-67531' || $registrofinal["id_cotizacion_registro"] == 'CA02-67547') {

                    $codigo_iva = $registrofinal["codigo_servicio"];

                }else{

                    if (($registrofinal["codigo_servicio"] == 'SER0001' || $registrofinal["codigo_servicio"] == 'SER0009') && $conexion->num_rows() > 1) {

                        $codigo_iva = $registrofinal["codigo_servicio"];

                    }else{

                        $codigo_iva = '';

                    }

                }
                $iva_servicio = reg('ivas', 'codigo_padre', $codigo_iva);
                $total_iva += ($valortotalservicio * ($iva_servicio["iva"] / 100));
            
            }
            if ($rp['ex_iva'] == '2') {
            //CALCULAR IVA PRODUCTO
                $iva_producto = reg('ivas', 'codigo_padre', $registrofinal["codigo_producto"]);

                if ($iva_producto["id_iva"] >= '7632' && $iva_producto["id_iva"] <= '7888' && $pedido["fecha_creacion"] >="2020-08-13 00:00:00" && $pedido["numero_pedido"] != 'PB05-68193') {
                    $valor_iva = 0;
                }else{
                    $valor_iva = $iva_producto["iva"];
                }

                $total_iva += ($valortotalproducto * ($valor_iva / 100));
            
            }
            if ($rp['ex_iva'] == '0') {
                //CALCULAR IVA SERVICIO
                if ($registrofinal["id_cotizacion_registro"] == 'CA02-67531' || $registrofinal["id_cotizacion_registro"] == 'CA02-67547') {

                    $codigo_iva = $registrofinal["codigo_servicio"];  

                }else{

                    if (($registrofinal["codigo_servicio"] == 'SER0001' || $registrofinal["codigo_servicio"] == 'SER0009') && $conexion->num_rows() > 1) {

                        $codigo_iva = $registrofinal["codigo_servicio"];

                    }else{

                        $codigo_iva = '';

                    }
                }

                $iva_servicio = reg('ivas', 'codigo_padre', $codigo_iva);
                $total_iva += ($valortotalservicio * ($iva_servicio["iva"] / 100));
                
                //CALCULAR IVA PRODUCTO
                $iva_producto = reg('ivas', 'codigo_padre', $registrofinal["codigo_producto"]);

                if ($iva_producto["id_iva"] >= '7632' && $iva_producto["id_iva"] <= '7888' && $pedido["fecha_creacion"] >="2020-08-13 00:00:00" && $pedido["numero_pedido"] != 'PB05-68193') {
                    $valor_iva = 0;
                }else{
                    $valor_iva = $iva_producto["iva"];
                }

                $total_iva += ($valortotalproducto * ($valor_iva / 100));
            }
        }

        //echo $total_iva.'<br>';
        //echo $subtotal.'<br>';
        //echo $solo_compras.'<br>';
        //echo $solo_servicios.'<br>';
        //echo $total_iva + $subtotal.'<br>';
        $total_pedido = $total_iva + $subtotal;

        $conexion->query("UPDATE pedidos_values SET total = '".$total_pedido."', sub_total = '".$subtotal."', iva = '".$total_iva."' WHERE pedido_numero = '".$pedido["numero_pedido"]."' ");

        redirigir("admin3/pedidos/editar/id_pedido/".$id_pedido."/");
        
    break;
    case 's_agente':
        $templete->define(array('principal' => 's_agente.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $conexion->query("SELECT * FROM pedidos WHERE numero_pedido = '".$num."' ");
        while($conexion->next_record()){
            $templete->assign('MET', $met);
            $templete->assign('UNO', $conexion->f('numero_pedido'));
        }
    break;
    case 'generar_agente':
        $usuario = reg('usuarios', 'id_usuario', $_COOKIE["mkid"]);
        $pedido = reg ('pedidos', 'numero_pedido', $number);
        if ($met == 'noc') {
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Requerimiento de Agente de Ventas <b>" .$requerimiento."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
            enviar_mail('cartera@ag3.com.co', 'Syspromall', 'Agente ' .$usuario['nombre']. ' Pedido # ' . $number . ' ', $requerimiento);
            enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Agente ' .$usuario['nombre']. ' Pedido # ' . $number . ' ', $requerimiento);
        }
        if ($met == 'nov') {
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Requerimiento de Agente de Ventas <b>" .$requerimiento."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
            enviar_mail('artes@promall.com.co', 'Syspromall', 'Agente ' .$usuario['nombre']. ' Pedido # ' . $number . ' ', $requerimiento);
            enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Agente ' .$usuario['nombre']. ' Pedido # ' . $number . ' ', $requerimiento);
        }
        
        redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$number."'/m/48/");
    break;
    case 'reg':
        //$conexion2->query("UPDATE autorizaciones_pedidos SET apr_edicion = '1' WHERE num_pedido = '".$b."' ");
        location("pedidos_html.php?correo=".$email."&num_pedido=".$b."&valor=".$nit." ");
    break;
    case 'guardarfinal_pedido':
        //ARMAR CODIGO DE PEDIDO E INSERTAR DATOS PRINCIPALES DE PEDIDOS
        $cad_ex = explode(";", $_POST['cad']);
        $precodigo_cot = explode("-", $cad_ex[0]);//CODIGO DE REGISTRO DE LA PRIMERA COTIZACION
        $precodigo_cot = 'P' . strtoupper(substr($precodigo_cot[0], 1, 3)) . '-' . $precodigo_cot[1];
        
        for ($i=0; $i < count($cad_ex); $i++) { 
            $conexion2->query("UPDATE cotizaciones SET estado= 'Pedido' WHERE id_cotizacion = '".$cad_ex[$i]."' ");
        }

        //Calcula los valores
        $total_iva = 0;
        $subtotal = 0;
        $solo_compras = 0;
        $solo_servicios = 0;

        $conexion->query("SELECT DISTINCT(reg_cotizacion) AS codigo FROM registros_pedidos_t INNER JOIN pedidos_temporal ON id_pedido=id_pedido_reg AND numero_pedido='".$precodigo_cot."';");
        while($conexion->next_record()){
            $registrofinal = reg('registros_cotizacion', 'id_registro_cotizacion', $conexion->f("codigo"));
            $rp = reg('registros_pedidos_t', 'reg_cotizacion', $conexion->f("codigo"));

            $valortotalproducto = $registrofinal["valortotal_producto"];
            $valortotalservicio = $registrofinal["valortotal_servicio"];

            $subtotal += ($valortotalproducto + $valortotalservicio);
            $solo_compras += $valortotalproducto;
            $solo_servicios += $valortotalservicio;

  
            if ($rp['ex_iva'] == '1') {
                //CALCULAR IVA SERVICIO
                if ($registrofinal["id_cotizacion_registro"] == 'CA02-67531' || $registrofinal["id_cotizacion_registro"] == 'CA02-67547') {

                    $codigo_iva = $registrofinal["codigo_servicio"];

                }else{

                    if ($registrofinal["codigo_servicio"] == 'SER0001') {

                        $codigo_iva = '';

                    }else{

                        $codigo_iva = $registrofinal["codigo_servicio"];

                    }

                }

                $iva_servicio = reg('ivas', 'codigo_padre', $codigo_iva);
                $total_iva += ($valortotalservicio * ($iva_servicio["iva"] / 100));
            
            }
            if ($rp['ex_iva'] == '2') {
            //CALCULAR IVA PRODUCTO
                $iva_producto = reg('ivas', 'codigo_padre', $registrofinal["codigo_producto"]);

                if ($iva_producto["id_iva"] >= '7632' && $iva_producto["id_iva"] <= '7888' && $pedido["fecha_creacion"] >="2020-08-13 00:00:00" && $pedido["numero_pedido"] != 'PB05-68193') {
                    $valor_iva = 0;
                }else{
                    $valor_iva = $iva_producto["iva"];
                }

                $total_iva += ($valortotalproducto * ($valor_iva / 100));
            
            }
            if ($rp['ex_iva'] == '0') {
                //CALCULAR IVA SERVICIO
                if ($registrofinal["id_cotizacion_registro"] == 'CA02-67531' || $registrofinal["id_cotizacion_registro"] == 'CA02-67547') {

                    $codigo_iva = $registrofinal["codigo_servicio"];  

                }else{

                    if ($registrofinal["codigo_servicio"] == 'SER0001') {

                        $codigo_iva = '';

                    }else{

                        $codigo_iva = $registrofinal["codigo_servicio"];

                    }
                }

                $iva_servicio = reg('ivas', 'codigo_padre', $codigo_iva);
                $total_iva += ($valortotalservicio * ($iva_servicio["iva"] / 100));
                
                //CALCULAR IVA PRODUCTO
                $iva_producto = reg('ivas', 'codigo_padre', $registrofinal["codigo_producto"]);

                if ($iva_producto["id_iva"] >= '7632' && $iva_producto["id_iva"] <= '7888' && $pedido["fecha_creacion"] >="2020-08-13 00:00:00" && $pedido["numero_pedido"] != 'PB05-68193') {
                    $valor_iva = 0;
                }else{
                    $valor_iva = $iva_producto["iva"];
                }

                $total_iva += ($valortotalproducto * ($valor_iva / 100));
            }
        }

        //echo $total_iva.'<br>';
        //echo $subtotal.'<br>';
        //echo $solo_compras.'<br>';
        //echo $solo_servicios.'<br>';
        $total_pedido = $total_iva + $subtotal;

        $conexion->query("INSERT INTO pedidos_values VALUES('','".$precodigo_cot."','".$total_pedido."','".$subtotal."','".$total_iva."','".$solo_compras."','".$solo_servicios."') ");

        #Fin calcula los valores
        
        $conexion->query("SELECT * FROM pedidos_temporal WHERE numero_pedido = '".$precodigo_cot."' ");
        while($conexion->next_record()){
            $conexion2->query("INSERT INTO pedidos VALUES ('".$conexion->f('id_pedido')."', '".$precodigo_cot."', '".$conexion->f('estado')."', '".$conexion->f('nom_realiza_pedido')."', '".$conexion->f('nom_recibe_pedido')."', '".$conexion->f('razon_social')."', '".$conexion->f('nit_cc')."', '".$conexion->f('dir_facturacion')."', '".$conexion->f('dir_envio')."','".$conexion->f('tel_contacto')."', '".$conexion->f('tel_contacto_directo')."', '".$conexion->f('email')."', '".$conexion->f('observaciones')."', '".$conexion->f('medio_pago')."', '".$conexion->f('forma_pago')."', '".$_COOKIE['mkid']."', '".date("Y-m-d H:i:s")."', '".$conexion->f('fecha_comprobante')."', '".$conexion->f('fecha_despacho')."', '".$conexion->f('fecha_sin_despacho')."', '".$conexion->f('observacion_agente')."','".$conexion->f('dep_pago')."','".$conexion->f('visual')."','".$conexion->f('info_quien')."','".$conexion->f('fecha_aut_cartera')."','".$conexion->f('fecha_cotizacion')."','0','','0');");
        }
        $pedidod = reg('pedidos_temporal', 'numero_pedido', $precodigo_cot);
        $conexion->query("SELECT * FROM registros_pedidos_t WHERE id_pedido_reg = '".$pedidod['id_pedido']."' ");
        while($conexion->next_record()){
            $conexion2->query("INSERT INTO registros_pedidos VALUES (NULL,'".$conexion->f('reg_cotizacion')."','".$conexion->f('id_pedido_reg')."','".$conexion->f('datos')."','".$conexion->f('cantidad')."','".date("Y-m-d H:i:s")."','".$_COOKIE["mkid"]."','".$conexion->f('ex_iva')."','','','','')");
        }
        #Valida si no hay servicio para la aprobacion aitomatica de artes
        $conexion2->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_servicio != '' AND numero_pedido='".$precodigo_cot."';");
        $conexion2->next_record();
        if($conexion2->num_rows() == 0){
            $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$precodigo_cot."', '0', '1', '0', '0','1','0','0','0','0');");
        }else{
            $foo = $conexion2->f("codigo_servicio");
            if (strpos($foo, 'SER') !== false) {
                $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$precodigo_cot."', '0', '1', '0', '0','1','0','0','0','0');");
            }else{
                #Valida si no hay servicio para la aprobacion aitomatica de artes
                $conexion3->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_producto != '' AND numero_pedido='".$precodigo_cot."';");
                $conexion3->next_record();
                if($conexion3->num_rows() == 0){
                    $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$precodigo_cot."', '1', '0', '0', '0','0','0','0','0','0');");
                }
                #FIN
                $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$precodigo_cot."', '0', '0', '0', '0','0','0','0','0','0');");
            }
            $conexion->query("SELECT * FROM usuarios WHERE rol=(SELECT id_rol_usuario FROM rol_usuario WHERE nombre_rol LIKE '%Artes%');");
        }
        #FIN
        $email = explode("|", $pedidod["info_quien"]);
        

        $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedidod["id_pedido"]."', 'Creación del pedido <b>".$pedidod["numero_pedido"]."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
        location('pedidos_html.php?correo='.$email[0].'&num_pedido='.$pedidod["numero_pedido"].'&valor='.$pedidod["nit_cc"].' ');
        //*********************************
        
    break;
    case 'consultar':
        $nuevafecha = strtotime('-15 day' , strtotime(date("Y-m-d")));
        $nuevafecha = date('Y-m-d', $nuevafecha);
        $templete->define(array('principal' => 'buscar_pedidos.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('PREFECHA', $nuevafecha);
        $templete->assign('FECHA_ACTUAL', date("Y-m-d"));
        /*$conexion->query("SELECT numero_pedido,estado FROM pedidos WHERE estado = 'No Vigente' ");
        while($conexion->next_record()){
            
            $tampa = explode("-", $conexion->f("numero_pedido"));
            $tampa = 'C' . strtoupper(substr($tampa[0], 1, 3)) . '-' . $tampa[1];
            $conexion2->query("UPDATE cotizaciones SET vigente = '0' WHERE id_cotizacion = '".$tampa."' ");
            
        }*/

        $conexion->query("SELECT fecha_cotizacion,numero_pedido FROM pedidos WHERE fecha_cotizacion >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)");
        while ($conexion->next_record()) {
            //echo $conexion->f("numero_pedido").'<br>';
            $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Aprobado' WHERE b.aut_compras = '1' AND b.aut_artes = '1' AND b.aut_cliente = '1' AND a.estado != 'No Vigente' AND a.estado != 'Aprobado' AND a.numero_pedido = '".$conexion->f("numero_pedido")."' ");
            $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Anulado' WHERE b.aut_compras = '3' AND b.aut_artes = '3' AND b.aut_cliente = '3' AND b.aut_cartera = '3' AND a.estado != 'Anulado' AND a.numero_pedido = '".$conexion->f("numero_pedido")."' ");
            $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Generado' WHERE b.aut_compras = '0' AND b.aut_artes = '0' AND b.aut_guardado = '1' AND a.estado != 'No Vigente' AND a.estado != 'Generado' AND a.numero_pedido = '".$conexion->f("numero_pedido")."' ");
            $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Autorizado' WHERE b.aut_compras = '1' AND b.aut_artes = '1' AND b.aut_cliente = '1' AND b.aut_cartera = '1' AND b.aut_visual = '1' AND a.numero_pedido = '".$conexion->f("numero_pedido")."' ");
        }
        
    break;
    case 'filtrar':
        
        if(!$cadena){
            
            //Consultas segun criterios 
            $cadena = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario ";// AND suspendido='0'
            
            /**********************************************/
            //inner join
            $usu = reg('usuarios','id_usuario', $_COOKIE['mkid']);//inner con usuarios
            /***************************************************/

            $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '32' AND id_rol_reg = '".$usu["rol"]."' ");
            if($conexion->num_rows() > 0){
                if($criterio_busqueda == "Serial"){
                    $cadena .= " WHERE a.numero_pedido LIKE '%".$serial."%' AND a.usuario_creador = '".$_COOKIE['mkid']."' ;";
                }
            }else{
                $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '51' AND id_rol_reg = '".$usu["rol"]."' ");
                if($conexion->num_rows() > 0){
                    if($criterio_busqueda == "Serial"){
                        $cadena .= " WHERE a.numero_pedido LIKE '%".$serial."%' AND b.id_usuario_dir = '".$_COOKIE["mkid"]."' ;";
                    }
                }else{
                    $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '52' AND id_rol_reg = '".$usu["rol"]."' ");
                    if($conexion->num_rows() > 0){
                        if($criterio_busqueda == "Serial"){
                            $cadena .= "WHERE a.numero_pedido LIKE '%".$serial."%'";
                        }
                    }
                }
            }

            

            $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '32' AND id_rol_reg = '".$usu["rol"]."' ");
            if($conexion->num_rows() > 0){

                if($criterio_busqueda == "Cliente y Estado"){

                    $fecha1 = f2ts2($fecha_inicial);$fecha2 = f2ts2($fecha_final);

                    $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";// AND suspendido='0'

                    $cadena_logo = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario INNER JOIN historial_visuales c ON a.numero_pedido = c.npedido ";// AND suspendido='0'

                    if ($criterio == '1') {//Busca por cliente
                        $cadena .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                    }elseif ($criterio == '2') {//Busca por Prodcuto
                        $cadena_pro_ser .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND (d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                    }elseif ($criterio == '3') {//Busca por Servicio
                        $cadena_pro_ser .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                    }elseif ($criterio == '4') {//Busca por Logo
                        $cadena_logo .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND c.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                    }else{
                        $cadena .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                    }
                    
                }//Solo consultan sus propios

            }else{
                $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '51' AND id_rol_reg = '".$usu["rol"]."' ");
                if($conexion->num_rows() > 0){
                    if($criterio_busqueda == "Cliente y Estado"){
                    $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg  INNER JOIN historial_visuales b ON a.visual = b.h_visual INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";
                        if ($criterio == '1') {//Busca por cliente
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                        }elseif ($criterio == '2') {//Busca por Prodcuto
                            $cadena_pro_ser .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                        }elseif ($criterio == '3') {//Busca por Servicio
                            $cadena_pro_ser .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                        }elseif ($criterio == '4') {//Busca por Logo
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND b.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                        }else{
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND fecha_creacion >= '".$fecha_inicial." 00:00:00' AND fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY fecha_creacion DESC;";
                        }
                    }//Solo consulta el director segun los agentes a cargo
                }else{
                    $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '52' AND id_rol_reg = '".$usu["rol"]."' ");
                    if($conexion->num_rows() > 0){
                        $fecha1 = f2ts2($fecha_inicial);$fecha2 = f2ts2($fecha_final);
                        if($criterio_busqueda == "Cliente y Estado"){
                            $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";// AND suspendido='0'

                            $cadena_logo = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario INNER JOIN historial_visuales c ON a.numero_pedido = c.npedido ";// AND suspendido='0'

                            if ($criterio == '1') {//Busca por cliente

                                $cadena .= "WHERE (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";

                            }if ($criterio == '2') {//Busca por Prodcuto

                                $cadena_pro_ser .= "  WHERE d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59' GROUP BY a.numero_pedido ;";

                            }if ($criterio == '3') {//Busca por Servicio

                                $cadena_pro_ser .= " WHERE d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";

                            }if ($criterio == '4') {//Busca por Logo

                                $cadena_logo .= " WHERE c.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";

                            }if ($criterio == ''){
                                $cadena .= " WHERE a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59' GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                            }

                        }//Consulta todos los pedidos
                    }
                }
            }
        }/*else{
            $cadena = $_GET['cadena'];
        }*/

        $busca = strpos($cadena_pro_ser, 'WHERE');
        $buscal = strpos($cadena_logo, 'WHERE');

        if ($busca === false) {
            if ($buscal === false){
                $cadena = $cadena;
            }else{
                $cadena = $cadena_logo;
            }
            
        }else{
            $cadena = $cadena_pro_ser;
        }


        $templete->define(array('principal' => 'detalle_pedidos.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('CADENA',$cadena);
        
        
        $n=1;
        $templete->define_dynamic('BPEDIDOS', 'principal');
        $conexion->query($cadena);
        while($conexion->next_record())
        {
            
            $i=1;
            $fact = reg('facturacion','f_ordenc',$conexion->f("numero_pedido"),' ORDER BY consecutivo_inh DESC');
            $estado_factura = reg('cartera_testados','ref_pedido', $conexion->f("numero_pedido") );
            $factura_nt = reg('factura_ntcredito','nt_pedido', $conexion->f("numero_pedido") );
            $valida_factura = reg('facturacion_enviada','pedido_fenviada', $conexion->f("numero_pedido") );
            $pedido_fin = reg('pedidos_fin','pedido_fin', $conexion->f("numero_pedido") );

            #permitir remisionar 
            $permite_remision = reg('facturacion_siigo','pedido_fsiigo',$conexion->f("numero_pedido"));
            #----
            $recibos = reg('recibos','r_ordenc',$conexion->f("numero_pedido"),'ORDER BY consecutivo_rec DESC');
            $e_recibos= reg('cartera_restados','recibo_restado',$fact['consecutivo_rec']);

            $templete->assign('CPRODUCTO', name_p($conexion->f("numero_pedido")));
            $ptotal = 0;
            $aprobaciones = reg('autorizaciones_pedidos', 'num_pedido', $conexion->f("numero_pedido"));
            $clien = reg('clientes', 'no_documento', $conexion->f("nit_cc"));
            $remisiones = reg('remisiones', 'num_pedido', $conexion->f("numero_pedido"));
            $sin_productos = "";
            $conexion2->query("SELECT DISTINCT(reg_cotizacion) AS codigo,id_pedido_reg FROM registros_pedidos WHERE id_pedido_reg='".$conexion->f("id_pedido")."';");
            while($conexion2->next_record()){
                $registro_cotizacion = reg('registros_cotizacion', 'id_registro_cotizacion', $conexion2->f("codigo"));
                $ptotal += ($registro_cotizacion['valortotal_producto'] + $registro_cotizacion['valortotal_servicio']);

                if ($registro_cotizacion['codigo_producto'] == ""){
                    $sin_productos .= "0";
                }else{
                    $sin_productos .= "1";
                }
            }

            $conexion2->query("SELECT numero_pedido_reg FROM alertas_pedidos WHERE numero_pedido_reg='".$conexion->f("numero_pedido")."';");
            if($conexion2->num_rows() > 0){
                while($conexion2->next_record()){
                    $templete->assign('NUMERO', $conexion2->num_rows());
                }
            }else{
                $templete->assign('NUMERO', '0');
            }
            
            
            $templete->assign('NOFACTURA', factura_pedido($conexion->f("numero_pedido")) );

            /*$conexion2->query("SELECT h_visual,npedido FROM historial_visuales WHERE npedido='".$conexion->f("numero_pedido")."' LIMIT 1;");
            $conexion2->next_record();

            if ($conexion2->f("h_visual") == '') {
                $templete->assign('VISUAL', '');
            }else{
                $templete->assign('VISUAL', $conexion2->f("h_visual"));
            }

            if ($fact['consecutivo_inh'] != "") {
                $templete->assign('NOFACTURA', '<a href="includes/tcpdf/examples/facturacion_pdf.php?nserial='.$fact['id_factura'].'&external=interno" target="_blank"><b>INH </b> '.$fact['consecutivo_inh'] . ' <i class="fa fa-print"></a>' );
            }else{
                $templete->assign('NOFACTURA', '' );
                
            }*/
            
            $syscon->query("SELECT h_visual,npedido FROM blue_sys_visuales WHERE npedido='".$conexion->f("numero_pedido")."' LIMIT 1;");
            $syscon->next_record();

            if ($syscon->f("h_visual") == '') {
                $templete->assign('VISUAL', '');
            }else{
                $templete->assign('VISUAL', str_replace("../blue-sys/", "", $syscon->f("h_visual")));
            }

            $syscon2->query("SELECT * FROM blue_sys_despachos WHERE no_pedido ='".$remisiones["id_remision"]."' LIMIT 1;");
            $syscon2->next_record();

            if ($syscon2->f("ruta") == '') {
                $cierredespacho = '<i class="fa fa-clock-o" aria-hidden="true"></i> Despachos';
            }else{
                $cierredespacho = '<i class="fa fa-check" aria-hidden="true"></i> Despachos';
            }

            $templete->assign('GUIA', guias_pedido($remisiones['id_remision'],$conexion->f("numero_pedido")) );

            if ($conexion->f("estado") == 'Anulado') {
                $templete->assign('CIERREDESPACHO', '<i class="fa fa-close" aria-hidden="true"></i> Despachos');
            }else{
                $templete->assign('CIERREDESPACHO', $cierredespacho);
            }

            
            

            $nuevafecha = date("d-m-Y",strtotime($conexion->f("fecha_cotizacion")."+ 5 days")); 
            $usercrm = reg('usuarios', 'id_usuario', $conexion->f("usuario_creador"));
            $userapr = reg('usuarios', 'id_usuario', $_COOKIE['mkid']);
            $templete->assign('N',$n);

            if ($conexion->f("fecha_sin_despacho") != '0000-00-00 00:00:00') {
                 $templete->assign('IDEX', $conexion->f("numero_pedido") . ' <b>EX</b>');
            }else{
                 $templete->assign('IDEX', $conexion->f("numero_pedido"));
            }
            
            $templete->assign('ID_PEDIDO', $conexion->f("numero_pedido"));

           
            $templete->assign('FECHAV', $nuevafecha);
            $templete->assign('FECHA', date("d-m-Y H:i:s", strtotime($conexion->f("fecha_creacion"))));
            $unix = date("d-m-Y H:i:s", strtotime($conexion->f("fecha_creacion")));
            $templete->assign('FECHAUNIX', fecha_mysql_to_unix($unix));
            $templete->assign('USERCRM', utf8_encode($usercrm["usuario"]));
            $templete->assign('CLIENTE', '<a href="https://sys.promall.com.co/promall/area/admin3/clientes/e/id_cliente/'.$clien["id_cliente"].'/" target="_blank">'.$conexion->f("razon_social"). '</a>');
            $templete->assign('CONTACTO', $conexion->f("nom_realiza_pedido"));
            $templete->assign('ESTADO', utf8_encode($conexion->f("estado")));

            if (strpos($conexion->f("forma_pago"), "Sin") === false || $estado_factura["clase_estado"] == 'PPV' || $estado_factura["clase_estado"] == 'OK CON RTE' || $estado_factura["clase_estado"] == 'OK SIN RTE' || $estado_factura["clase_estado"] == 'PSAFC' || $estado_factura["aut_director"] == '1' ) {

                #Estado de cartera, el check de cierre
                $templete->assign('CIERRECARTERA', '<i class="fa fa-check" aria-hidden="true"></i> Cartera');

                $findme = '1';
                $pos = strpos($sin_productos, $findme);

                if ($pedido_fin['id_fin'] != ""){
                    $templete->assign('FINALIZAR', '');
                }else{
                    if ($pos !== false) {
                        $templete->assign('FINALIZAR', '');
                    }else{
                        $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '55' AND id_rol_reg = '".$userapr["rol"]."' ");
                        if($conexion2->num_rows() > 0){
                            $templete->assign('FINALIZAR', '<form method="post" action="admin3/pedidos/finalizar/" class="dropdown-item item" style="width: 255px;">
                                                                    <input type="hidden" name="id_pedido" id="id_pedido" value="'.$conexion->f("numero_pedido").'">
     FECHACOTIZACION                                                               <button type="submit" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-check"></i> Finalizar Pedido</button>
                                                                </form>');
                        }else{
                            $templete->assign('FINALIZAR', '');
                        }
                    }
                }

                

                #Enviar o cargar la factura según corresponda en siigo
                if ($valida_factura['pedido_fenviada'] == '') {

                    if ($recibos['r_ordenc'] == '') {
                        $templete->assign('ENVIAR_SIIGO', '<form method="post" action="admin/UsoApiFacturas.php" target="_blank" id="from4'.$n.'" class="dropdown-item item" style="width: 255px;">
                                                        <input type="hidden" name="numero_pedido" id="numero_pedido" value="'.$conexion->f("numero_pedido").'">
                                                        <input type="submit" value="FACTURAR ELECTRONICAMENTE SIIGO" class="form-control" style="width: 240px;text-align: left;font-size:12px;">
                                                    </form>');
                    }else{
                        $templete->assign('ENVIAR_SIIGO', '');
                    }
                    

                }else{

                    $templete->assign('ENVIAR_SIIGO', '<a class="dropdown-item item" href="admin3/facturacion/adjuntarf/id_pedido/'.$conexion->f("numero_pedido").'/"><i class="fa fa-upload"></i> CARGAR FACTURA SIIGO</a>');

                    if ($factura_nt['nt_pedido'] == '') {
                        $templete->assign('ENVIAR_SIIGONT', '<a class="dropdown-item item" href="admin3/facturacion/ntcredito/id_pedido/'.$conexion->f("numero_pedido").'/" target="_blank"><i class="fa fa-upload"></i> ASOCIAR NOTA CREDITO</a>');
                    }else{
                        $templete->assign('ENVIAR_SIIGONT', '<form method="post" action="admin/UsoApiFacturas.php" target="_blank" id="from4'.$n.'" class="dropdown-item item" style="width: 255px;">
                                                        <input type="hidden" name="numero_pedido" id="numero_pedido" value="'.$conexion->f("numero_pedido").'">
                                                        <input type="submit" value="FACTURAR ELECTRONICAMENTE SIIGO" class="form-control" style="width: 240px;text-align: left;font-size:12px;">
                                                    </form>');
                    }

                    
                }
                
                //facturar nuevamente
                /*$templete->assign('ENVIAR_SIIGO', '<form method="post" action="admin/UsoApiFacturas.php" target="_blank" id="from4'.$n.'" class="dropdown-item item" style="width: 255px;">
                                                        <input type="hidden" name="numero_pedido" id="numero_pedido" value="'.$conexion->f("numero_pedido").'">
                                                        <input type="submit" value="FACTURAR ELECTRONICAMENTE SIIGO" class="form-control" style="width: 240px;text-align: left;font-size:12px;">
                                                    </form>');*/
                

                

            }elseif ($conexion->f("estado") == 'Anulado') {
                $templete->assign('CIERRECARTERA', '<i class="fa fa-close" aria-hidden="true"></i> Cartera');
                $templete->assign('ENVIAR_SIIGO' , '');
            }else{
                $templete->assign('CIERRECARTERA', '<i class="fa fa-clock-o" aria-hidden="true"></i> Cartera');
                //$templete->assign('ENVIAR_SIIGONT', '<a class="dropdown-item item" href="admin3/facturacion/adjuntarnt/id_pedido/'.$conexion->f("numero_pedido").'/" target="_blank"><i class="fa fa-upload"></i> ASOCIAR NOTA CREDITO</a>');
                
            }




            if($aprobaciones['aut_compras']  == 0){
                $templete->assign('ACOMPRA', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 1){
                $templete->assign('ACOMPRA', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 2){
                $templete->assign('ACOMPRA', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 3){
                $templete->assign('ACOMPRA', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_artes']  == 0){
                $templete->assign('AARTES', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 1){
                $templete->assign('AARTES', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 2){
                $templete->assign('AARTES', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }

            if($aprobaciones['aut_edi_cli'] == 1){
                $templete->assign('ECLIENTE', '<i class="fa fa-pencil-square-o" aria-hidden="true" title="Cliente no ha aprobado las modificaciones"></i>');
            }else{
                $templete->assign('ECLIENTE', '');
            }

            if($aprobaciones['aut_cliente']  == 0){
                $templete->assign('ACLIENTE', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 2){//rechazado por el cliente
                $templete->assign('ACLIENTE', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 1){
                $templete->assign('ACLIENTE', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_cartera']  == 0){
                $templete->assign('ACARTERA', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 2){
                $templete->assign('ACARTERA', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 1){
                $templete->assign('ACARTERA', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_visual']  == 0){
                $templete->assign('AVISUAL', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_visual']  == 2){
                $templete->assign('AVISUAL', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_visual']  == 1){
                $templete->assign('AVISUAL', '<i class="fa fa-check" aria-hidden="true"></i>');   
            }if($aprobaciones['aut_visual']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            $templete->assign('NOREALIZA', $conexion->f("nom_realiza_pedido"));
            $templete->assign('MEDIOPAGO', $conexion->f("medio_pago"));
            $templete->assign('NITCC', $conexion->f("nit_cc"));
            $templete->assign('FORMAPAGO', $conexion->f("forma_pago"));
            $templete->assign('PRECIOTOTAL', '$' . number_format($ptotal, 0, ".", ","));
            $info_quien = $conexion->f("info_quien");
            $esplode = explode("|", $info_quien);
            $templete->assign('EMAIL', $esplode[0]);
            $templete->assign('ID_CLIENTE', $conexion->f("id_cliente"));
            
            if ($conexion->f("estado") == 'Anulado' || $conexion->f("estado") == 'No Vigente') {
                $templete->assign('ESTADOACTUAL', '');
            }else{
                $templete->assign('ESTADOACTUAL', estado_actual($conexion->f("numero_pedido")));
            }


            if($aprobaciones['aut_cartera'] != 1 ){
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '33' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){
                    if ($_COOKIE['mkid'] == $conexion->f("usuario_creador")) {
                        $templete->assign('EDITAR', '<a class="dropdown-item item" href="admin3/pedidos/editar/id_pedido/'.$conexion->f("id_pedido").'/"><i class="fa fa-edit"></i> Editar</a>');
                    }else{
                        $templete->assign('EDITAR', '');
                    }
                }
            }
            
            //EDICION HASTA QUE CARTERA APRUEBA
            if($aprobaciones['aut_cartera'] != 1 ){
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '33' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){
                    if ($_COOKIE['mkid'] == $conexion->f("usuario_creador")) {
                        $templete->assign('EDITAR', '<a class="dropdown-item item" href="admin3/pedidos/editar/id_pedido/'.$conexion->f("id_pedido").'/"><i class="fa fa-edit"></i> Editar</a>');
                    }else{
                        $templete->assign('EDITAR', '');
                    }
                }//FIN EDICION POR PARTE DEL AGENTE
            }//EDICION DEL DIRECTOR COMERCIAL YA QUE ESTE PUEDE EDITAR EN CUALQUIER MOMENTO CUALQUER PEDIDO
            $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '45' AND id_rol_reg = '".$userapr["rol"]."' ");
            if($conexion2->num_rows() > 0){
                $templete->assign('EDITAR', '<a class="dropdown-item item" href="admin3/pedidos/editar/id_pedido/'.$conexion->f("id_pedido").'/"><i class="fa fa-edit"></i> Editar</a>');
            }
            //FIN EDICION POR PARTE DEL DIRECTOR
           //COMPROBANTE
            if ($aprobaciones['aut_compras'] == 1 && $aprobaciones['aut_artes'] == 1){
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '41' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){
                    $noc = 'snc';
                    $templete->assign('COMPROBANTE', '<a class="dropdown-item item" href="admin3/pedidos/solicitar_comprobante/id_pedido/'.$conexion->f("numero_pedido").'/estado/'.$noc.'/"><i class="fa fa-file-o"></i> Comprobante</a>');
                }else{
                    $templete->assign('COMPROBANTE', '');
                }
            }else{
                $templete->assign('COMPROBANTE', '');
            }
            //*************************FIN DE ENVIO DEL COMPROBANTE//**********************************
            //CAMBIAR ESTADO
            $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '53' AND id_rol_reg = '".$userapr["rol"]."' ");
            if($conexion2->num_rows() > 0){
                $templete->assign('CESTADO', '<a class="dropdown-item item" href="admin3/pedidos/cambiar_estado/id_pedido/'.$conexion->f("numero_pedido").'/"><i class="fa fa-location-arrow"></i> Cambiar estado</a>');
            }else{
                $templete->assign('CESTADO', '');
            }
            //*************************FIN DE ENVIO DEL CAMBIAR ESTADO//**********************************
            //CAMBIAR ESTADO
            if ($conexion->f("estado") == 'No Vigente') {
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '54' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){
                    $templete->assign('TWOVIGENCIAR', '<form method="post" action="admin3/pedidos/agregar_vigencia/" class="dropdown-item item" style="width: 255px;">
                                                            <input type="hidden" name="id_pedido" id="id_pedido" value="'.$conexion->f("numero_pedido").'">
                                                            <button type="submit" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-calendar-check-o"></i> +2 Vigencia y reiniciar checks</button>
                                                        </form>');//Help everything, Hel 
                }else{
                    $templete->assign('TWOVIGENCIAR', '');
                }
            }else{
                $templete->assign('TWOVIGENCIAR', '');
            }

            if ($conexion->f("estado") == 'No Vigente') {
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '55' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){
                    $templete->assign('TWOVIGENCIA', '<form method="post" action="admin3/pedidos/agregar_vigencia_s/" class="dropdown-item item" style="width: 255px;">
                                                            <input type="hidden" name="id_pedido" id="id_pedido" value="'.$conexion->f("numero_pedido").'">
                                                            <button type="submit" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-calendar-plus-o"></i> +2 Vigencia</button>
                                                        </form>');
                }else{
                    $templete->assign('TWOVIGENCIA', '');
                }
            }else{
                $templete->assign('TWOVIGENCIA', '');
            }

            
            
            /**********************OTORGA PERMISOS PARA REMISIONAR EL PEDIDO CONSULTANDO LA TABLA DE PERMISOS Y PRIVILEGIOS TENIENDO EN CUENTA SI O SI LA CLASE DE ROL**************/

            if ($aprobaciones['aut_cartera'] == 1 ) {
                $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '57' AND id_rol_reg = '".$userapr["rol"]."' ");
                if($conexion2->num_rows() > 0){

                    $busca = strpos($conexion->f("forma_pago"), 'Sin');

                    //if ($estado_factura['clase_estado'] == 'PPV' || $estado_factura['clase_estado'] == 'PSAFC' || $estado_factura['clase_estado'] == 'OK SIN RTE' || $estado_factura['clase_estado'] == 'OK CON RTE' || $e_recibos['clase_estado'] == 'PPV') {
                    if ($permite_remision['archivo_fsiigo'] =! '') {
                        $templete->assign('REMISIONAR', '<form method="POST" action="admin3/remisiones/crear_aut/" class="dropdown-item item" style="width: 255px;" target="_blank">
                                                            <input type="hidden" name="npedido" value="'.$conexion->f("numero_pedido").'">
                                                            <button type="submit" name="enviar" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-file-text-o "></i> Remisionar pedido</button>
                                                        </form>');
                    }else{
                        $templete->assign('REMISIONAR', '');
                    }
                }else{
                    $templete->assign('REMISIONAR', '');
                }
            }else{
                $templete->assign('REMISIONAR', '');
            }

            /***********************Fin de los permisos para remisionar el pedido ****************************************************/
            
            
            //*************************FIN DE ENVIO DEL CAMBIAR ESTADO**********************************//
            //Continuar a facturar
            
            $conexion2->query("SELECT id_priv_reg,id_rol_reg FROM `privilegio_usu_rol` WHERE id_priv_reg = '56' AND id_rol_reg = '".$userapr["rol"]."' ");
            if($conexion2->num_rows() > 0){
                
                //$conexion2->query("SELECT f_estado,consecutivo_inh,f_ordenc FROM facturacion WHERE f_ordenc = '".$conexion->f("numero_pedido")."' ORDER BY consecutivo_inh DESC LIMIT 1");
                //if ($conexion2->num_rows() > 0) {
                if ($fact['f_ordenc'] != '') {
                    //$conexion2->next_record();

                        //if ($conexion2->f("f_estado") == 'ANL') {
                        if ($fact["f_estado"] == 'ANL') {
                            if ($conexion->f("estado") == 'Aprobado' || $conexion->f("estado") == 'Autorizado' ) {
                                if ($recibos['r_ordenc'] == '') {
                                    $templete->assign('CONTIFACTU', '<form method="post" action="admin3/facturacion/pedir_datos/" class="dropdown-item item" style="width: 255px;">
                                                                    <input type="hidden" name="radio'.$n.'" id="radio'.$n.'" value="'.$conexion->f("id_pedido").'" >  
                                                                    <input type="hidden" name="contador" id="contador" value="'.$n.'" >  
                                                                    <button type="submit" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-calendar-plus-o"></i> Proforma</button>
                                                                </form>' );
                                }else{
                                    $templete->assign('CONTIFACTU', '' );
                                }
                            }else{
                                $templete->assign('CONTIFACTU', '' );
                            }
                        }else{
                            $templete->assign('CONTIFACTU', '' );
                        }

                }else{

                    if ($conexion->f("estado") == 'Aprobado' || $conexion->f("estado") == 'Autorizado' ) {
                        if ($recibos['r_ordenc'] == '') {
                            $templete->assign('CONTIFACTU', '<form method="post" action="admin3/facturacion/pedir_datos/" class="dropdown-item item" style="width: 255px;">
                                                            <input type="hidden" name="radio'.$n.'" id="radio'.$n.'" value="'.$conexion->f("id_pedido").'" >  
                                                            <input type="hidden" name="contador" id="contador" value="'.$n.'" >  
                                                            <button type="submit" class="form-control" style="width: 240px;text-align: left;"><i class="fa fa-calendar-plus-o"></i> Proforma</button>
                                                        </form>' );
                        }else{
                            $templete->assign('CONTIFACTU', '' );
                        }
                    }else{
                        $templete->assign('CONTIFACTU', '' );
                    }

                }
                    
            }else{
                $templete->assign('CONTIFACTU', '' );
            }
            
            //*************************FIN DE ENVIO DEL CAMBIAR ESTADO//**********************************
            
            $templete->parse('LISTABPEDIDOS', '.BPEDIDOS');
            $n++;
        }

    break;

     case 'filtrar2':
        
        $templete->define(array('principal' => 'estado_actual.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );

        $pedido = reg('pedidos','numero_pedido', $serial);

        $tabla = '';

        $n = 1;
        
        $templete->assign('NOPEDIDO', $serial);

        echo "SELECT * FROM registros_pedidos WHERE id_pedido_reg = '".$pedido['id_pedido']."' ";
        $conexion->query("SELECT * FROM registros_pedidos WHERE id_pedido_reg = '".$pedido['id_pedido']."' ");
        if ($conexion->num_rows() > 0) {
            
            while ($conexion->next_record()) {

                    $registros_cotizacion = reg('registros_cotizacion','id_registro_cotizacion', $conexion->f("reg_cotizacion"));
                    $servicio = reg('listado_servicios','codigo' , $registros_cotizacion['codigo_servicio']);


                
                    $imagenes = explode("|", $registros_cotizacion["link_imagenes"]);

                    $datos = explode("|", $conexion->f("datos"));
                    $datos_one = $datos[1];//cuando va el servicio con el producto

                    $conexion3->query("SELECT * FROM oc_principal WHERE cantidad_unidades= '".$conexion->f("cantidad")."' AND OC_generada LIKE '%OC-1%' AND pedido LIKE '%".$serial."|".$registros_cotizacion['id_registro_cotizacion']."%' ");
                    $conexion3->next_record();

                    $reg_mercancia = reg('reg_mercancia','id_OC', $conexion3->f("id"));

                    if ($reg_mercancia['id_OC'] == '') {
                        $aprov = 'No se registro';
                        //$aprov = $reg_mercancia['id_OC'];
                        $c_recibida = 0;
                        $style_ap = " style='background-color: rgba(255, 0, 2, 0.5);' ";
                    }else{
                        $aprov = 'Se registro';
                        //$aprov = $reg_mercancia['id_OC'];
                        $c_recibida = $reg_mercancia['cantidad_recibida'];
                        $style_ap = " style='background-color: rgba(0, 255, 2, 0.5);' ";
                    }

                    $cadena_texto1 = '';

                    /*if(empty($datos_one)){
                      $conexion2->query("SELECT * FROM registros_pedidos WHERE reg_cotizacion='".$conexion->f("reg_cotizacion")."' GROUP BY reg_cotizacion;");
                      while($conexion2->next_record()){
                        $datox = explode("|", $conexion2->f("datos"));
                        $cadena_texto1 .= ' <b>TINTA: </b>' . $datox[1] . ' - <b>LOGO: </b> ' . $datox[2] .' <br>';
                      }
                    }else{*/
                      $cadena_texto1 .= ' <b>TINTA: </b>' . $datos[1] . ' - <b>LOGO: </b> ' . $datos[2] .' <br>';
                    //}

                    //Compras, si se descargo o no
                    if ($conexion->f("liberado") == 1) {
                        $liberado_p = 'Si tiene OC';
                        $style_lp = " style='background-color: rgba(0, 255, 2, 0.5);' ";
                    }else{
                        $liberado_p = 'No tiene OC';
                        $style_lp = " style='background-color: rgba(255, 0, 2, 0.5);' ";
                    }

                    if ($conexion->f("servicio") == 1) {
                        $liberado_s = 'Si tiene OC';
                        $style_ls = " style='background-color: rgba(0, 255, 2, 0.5);' ";
                    }else{
                        $liberado_s = 'No tiene OC';
                        $style_ls = " style='background-color: rgba(255, 0, 2, 0.5);' ";
                    }

                    if ($registros_cotizacion["codigo_servicio"] == '') {

                        $tabla .= '<tr>';
                        $tabla .= '<td class="border">'.$n.'</td>';
                        $tabla .= '<td class="border"><img src="'.$imagenes[1].'" width="50px" height="50px"></td>';
                        $tabla .= '<td class="border">'.$registros_cotizacion["codigo_producto"] . ' // ' . $datos[0] .'</td>';
                        $tabla .= '<td class="border">'.$conexion->f("cantidad") . '</td>';
                        $tabla .= '<td class="border" '.$style_lp.'>'.$liberado_p . '</td>';
                        $tabla .= '<td class="border" '.$style_ap.'>'.$aprov . '</td>';
                        $tabla .= '<td class="border" '.$style_ap.'>'.$c_recibida . '</td>';
                        $tabla .= '</tr>';

                        $n++;

                    }else{
                        
                        if ($registros_cotizacion["codigo_producto"] != '') {
                            $tabla .= '<tr>';
                            $tabla .= '<td class="border">'.$n.'</td>';
                            $tabla .= '<td class="border"><img src="'.$imagenes[1].'" width="50px" height="50px"></td>';
                            $tabla .= '<td class="border">'.$registros_cotizacion["codigo_producto"] . ' // ' . $datos[0] .'</td>';
                            $tabla .= '<td class="border">'.$conexion->f("cantidad") .'</td>';
                            $tabla .= '<td class="border" '.$style_lp.'>'.$liberado_p .'</td>';
                            $tabla .= '<td class="border" '.$style_ap.'>'.$aprov . '</td>';
                            $tabla .= '<td class="border" '.$style_ap.'>'.$c_recibida . '</td>';
                            $tabla .= '</tr>';

                            $n++;
                        }
                        

                        $tabla .= '<tr>';
                        $tabla .= '<td class="border">'.$n.'</td>';
                        $tabla .= '<td class="border"><img src="'.$servicio['imagen'].'" width="50px" height="50px"></td>';
                        $tabla .= '<td class="border">'.$registros_cotizacion["codigo_servicio"]. ' // ' .$cadena_texto1 . '</td>';
                        $tabla .= '<td class="border">'.$conexion->f("cantidad") . '</td>';
                        $tabla .= '<td class="border" '.$style_ls.'>'.$liberado_s . '</td>';
                        $tabla .= '<td class="border"></td>';
                        $tabla .= '<td class="border"></td>';
                        $tabla .= '</tr>';
                        
                        $n++;

                    }
                
            }

        }


        $templete->assign('TABLA', $tabla);

    break;
    case 'filtrar3':
        
        if(!$cadena){
            
            //Consultas segun criterios 
            $cadena = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario ";// AND suspendido='0'
            
            /**********************************************/
            //inner join
            $usu = reg('usuarios','id_usuario', $_COOKIE['mkid']);//inner con usuarios
            /***************************************************/

            $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '32' AND id_rol_reg = '".$usu["rol"]."' ");
            if($conexion->num_rows() > 0){
                if($criterio_busqueda == "Serial"){
                    $cadena .= " WHERE a.numero_pedido LIKE '%".$serial."%' AND a.usuario_creador = '".$_COOKIE['mkid']."' ;";
                }
            }else{
                $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '51' AND id_rol_reg = '".$usu["rol"]."' ");
                if($conexion->num_rows() > 0){
                    if($criterio_busqueda == "Serial"){
                        $cadena .= " WHERE a.numero_pedido LIKE '%".$serial."%' AND b.id_usuario_dir = '".$_COOKIE["mkid"]."' ;";
                    }
                }else{
                    $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '52' AND id_rol_reg = '".$usu["rol"]."' ");
                    if($conexion->num_rows() > 0){
                        if($criterio_busqueda == "Serial"){
                            $cadena .= "WHERE a.numero_pedido LIKE '%".$serial."%'";
                        }
                    }
                }
            }

            

            $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '32' AND id_rol_reg = '".$usu["rol"]."' ");
            if($conexion->num_rows() > 0){

                if($criterio_busqueda == "Cliente y Estado"){

                    $fecha1 = f2ts2($fecha_inicial);$fecha2 = f2ts2($fecha_final);

                    $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";// AND suspendido='0'

                    $cadena_logo = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario INNER JOIN historial_visuales c ON a.numero_pedido = c.npedido ";// AND suspendido='0'

                    if ($criterio == '1') {//Busca por cliente
                        $cadena .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                    }elseif ($criterio == '2') {//Busca por Prodcuto
                        $cadena_pro_ser .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND (d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                    }elseif ($criterio == '3') {//Busca por Servicio
                        $cadena_pro_ser .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                    }elseif ($criterio == '4') {//Busca por Logo
                        $cadena_logo .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND c.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                    }else{
                        $cadena .= " WHERE a.usuario_creador = '".$_COOKIE["mkid"]."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                    }
                    
                }//Solo consultan sus propios

            }else{
                $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '51' AND id_rol_reg = '".$usu["rol"]."' ");
                if($conexion->num_rows() > 0){
                    if($criterio_busqueda == "Cliente y Estado"){
                    $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg  INNER JOIN historial_visuales b ON a.visual = b.h_visual INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";
                        if ($criterio == '1') {//Busca por cliente
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";
                        }elseif ($criterio == '2') {//Busca por Prodcuto
                            $cadena_pro_ser .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                        }elseif ($criterio == '3') {//Busca por Servicio
                            $cadena_pro_ser .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                        }elseif ($criterio == '4') {//Busca por Logo
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND b.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";
                        }else{
                            $cadena .= " WHERE b.id_usuario_dir = '".$_COOKIE["mkid"]."' AND (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND fecha_creacion >= '".$fecha_inicial." 00:00:00' AND fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY fecha_creacion DESC;";
                        }
                    }//Solo consulta el director segun los agentes a cargo
                }else{
                    $conexion->query("SELECT * FROM `privilegio_usu_rol` WHERE id_priv_reg = '52' AND id_rol_reg = '".$usu["rol"]."' ");
                    if($conexion->num_rows() > 0){
                        $fecha1 = f2ts2($fecha_inicial);$fecha2 = f2ts2($fecha_final);
                        if($criterio_busqueda == "Cliente y Estado"){
                            $cadena_pro_ser = "SELECT * FROM pedidos a INNER JOIN (SELECT id_pedido_reg,reg_cotizacion FROM registros_pedidos) c ON a.id_pedido = c.id_pedido_reg INNER JOIN (SELECT id_registro_cotizacion,codigo_producto,codigo_servicio FROM registros_cotizacion) d ON c.reg_cotizacion = d.id_registro_cotizacion INNER JOIN (SELECT codigo,nombre_prov FROM principal) e ON d.codigo_producto = e.codigo";// AND suspendido='0'

                            $cadena_logo = "SELECT * FROM pedidos a INNER JOIN (SELECT id_usuario,id_usuario_dir FROM usuarios ) b ON a.usuario_creador=b.id_usuario INNER JOIN historial_visuales c ON a.numero_pedido = c.npedido ";// AND suspendido='0'

                            if ($criterio == '1') {//Busca por cliente

                                $cadena .= "WHERE (a.nom_realiza_pedido LIKE '%".$paciente."%' OR a.nom_recibe_pedido LIKE '%".$paciente."%' OR a.razon_social LIKE '%".$paciente."%' OR a.nit_cc LIKE '%".$paciente."%' OR a.email LIKE '%".$paciente."%' OR a.observaciones LIKE '%".$paciente."%') AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  ORDER BY a.fecha_creacion DESC;";

                            }if ($criterio == '2') {//Busca por Prodcuto

                                $cadena_pro_ser .= "  WHERE d.codigo_producto LIKE '%".$paciente."%' OR e.nombre_prov LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59' GROUP BY a.numero_pedido ;";

                            }if ($criterio == '3') {//Busca por Servicio

                                $cadena_pro_ser .= " WHERE d.codigo_servicio = '".$paciente."' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";

                            }if ($criterio == '4') {//Busca por Logo

                                $cadena_logo .= " WHERE c.h_visual LIKE '%".$paciente."%' AND a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59'  GROUP BY a.numero_pedido";

                            }if ($criterio == ''){
                                $cadena .= " WHERE a.fecha_creacion >= '".$fecha_inicial." 00:00:00' AND a.fecha_creacion <= '".$fecha_final." 23:59:59' GROUP BY a.numero_pedido ORDER BY a.fecha_creacion DESC;";
                            }

                        }//Consulta todos los pedidos
                    }
                }
            }
        }/*else{
            $cadena = $_GET['cadena'];
        }*/

        $busca = strpos($cadena_pro_ser, 'WHERE');
        $buscal = strpos($cadena_logo, 'WHERE');

        if ($busca === false) {
            if ($buscal === false){
                $cadena = $cadena;
            }else{
                $cadena = $cadena_logo;
            }
            
        }else{
            $cadena = $cadena_pro_ser;
        }


        $templete->define(array('principal' => 'detalle_pedidos3.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('CADENA',$cadena);
        
        
        $n=1;
        $templete->define_dynamic('BPEDIDOS', 'principal');
        $conexion->query($cadena);
        while($conexion->next_record())
        {
            $i=1;
            $ptotal = 0;
            $fact = reg('facturacion','f_ordenc',$conexion->f("numero_pedido"),'ORDER BY consecutivo_inh DESC');
            $estado_factura = reg('cartera_testados','ref_pedido', $conexion->f("numero_pedido") );
            $templete->assign('CPRODUCTO', name_p($conexion->f("numero_pedido")));
            $aprobaciones = reg('autorizaciones_pedidos', 'num_pedido', $conexion->f("numero_pedido"));
            $clien = reg('clientes', 'no_documento', $conexion->f("nit_cc"));
            $remisiones = reg('remisiones', 'num_pedido', $conexion->f("numero_pedido"));
            

            $conexion2->query("SELECT DISTINCT(reg_cotizacion) AS codigo,id_pedido_reg FROM registros_pedidos WHERE id_pedido_reg='".$conexion->f("id_pedido")."';");
            while($conexion2->next_record()){
                $registro_cotizacion = reg('registros_cotizacion', 'id_registro_cotizacion', $conexion2->f("codigo"));
                $ptotal += ($registro_cotizacion['valortotal_producto'] + $registro_cotizacion['valortotal_servicio']);
            }
            
            $templete->assign('NOFACTURA', factura_pedido($conexion->f("numero_pedido")) );

            $templete->assign('GUIA', guias_pedido($remisiones['id_remision'],$conexion->f("numero_pedido")) );


            $nuevafecha = date("d-m-Y",strtotime($conexion->f("fecha_cotizacion")."+ 5 days")); 
            $usercrm = reg('usuarios', 'id_usuario', $conexion->f("usuario_creador"));
            $templete->assign('N',$n);
            
            if ($conexion->f("fecha_sin_despacho") != '0000-00-00 00:00:00') {
                 $templete->assign('IDEX', $conexion->f("numero_pedido") . ' <b>EX</b>');
            }else{
                 $templete->assign('IDEX', $conexion->f("numero_pedido"));
            }

            $templete->assign('ID_PEDIDO', $conexion->f("numero_pedido"));
            $templete->assign('FECHAV', $nuevafecha);
            $templete->assign('FECHA', date("d-m-Y H:i:s", strtotime($conexion->f("fecha_creacion"))));
            $unix = date("d-m-Y H:i:s", strtotime($conexion->f("fecha_creacion")));
            $templete->assign('FECHAUNIX', fecha_mysql_to_unix($unix));
            $templete->assign('USERCRM', utf8_encode($usercrm["usuario"]));

            $templete->assign('CLIENTE', '<a href="https://sys.promall.com.co/promall/area/admin3/clientes/e/id_cliente/'.$clien["id_cliente"].'/" target="_blank">'.$conexion->f("razon_social"). '</a>');  
            
            $templete->assign('CONTACTO', $conexion->f("nom_realiza_pedido"));
            $templete->assign('ESTADO', utf8_encode($conexion->f("estado")));

            if ($estado_factura["clase_estado"] == 'PPV' ) {
                $templete->assign('CIERRECARTERA', '<i class="fa fa-check" aria-hidden="true"></i> Cierre Cartera');
            }elseif ($conexion->f("estado") == 'Anulado') {
                $templete->assign('CIERRECARTERA', '<i class="fa fa-close" aria-hidden="true"></i> Cierre Cartera');
            }else{
                $templete->assign('CIERRECARTERA', '<i class="fa fa-clock-o" aria-hidden="true"></i> Cierre Cartera');
            }

            if($aprobaciones['aut_compras']  == 0){
                $templete->assign('ACOMPRA', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 1){
                $templete->assign('ACOMPRA', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 2){
                $templete->assign('ACOMPRA', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_compras']  == 3){
                $templete->assign('ACOMPRA', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_artes']  == 0){
                $templete->assign('AARTES', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 1){
                $templete->assign('AARTES', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 2){
                $templete->assign('AARTES', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_artes']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }

            if($aprobaciones['aut_edi_cli'] == 1){
                $templete->assign('ECLIENTE', '<i class="fa fa-pencil-square-o" aria-hidden="true" title="Cliente no ha aprobado las modificaciones"></i>');
            }else{
                $templete->assign('ECLIENTE', '');
            }

            if($aprobaciones['aut_cliente']  == 0){
                $templete->assign('ACLIENTE', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 2){//rechazado por el cliente
                $templete->assign('ACLIENTE', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 1){
                $templete->assign('ACLIENTE', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cliente']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_cartera']  == 0){
                $templete->assign('ACARTERA', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 2){
                $templete->assign('ACARTERA', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 1){
                $templete->assign('ACARTERA', '<i class="fa fa-check" aria-hidden="true"></i>');
            }if($aprobaciones['aut_cartera']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }
            if($aprobaciones['aut_visual']  == 0){
                $templete->assign('AVISUAL', '<i class="fa fa-clock-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_visual']  == 2){
                $templete->assign('AVISUAL', '<i class="fa fa-question-circle-o" aria-hidden="true"></i>');
            }if($aprobaciones['aut_visual']  == 1){
                $templete->assign('AVISUAL', '<i class="fa fa-check" aria-hidden="true"></i>');   
            }if($aprobaciones['aut_visual']  == 3){
                $templete->assign('AARTES', '<i class="fa fa-close" aria-hidden="true"></i>');
            }

            if ($conexion->f("estado") == 'Anulado' || $conexion->f("estado") == 'No Vigente') {
                $templete->assign('ESTADOACTUAL', '');
            }else{
                $templete->assign('ESTADOACTUAL', estado_actual($conexion->f("numero_pedido")));
            }

            


            $templete->assign('NOREALIZA', $conexion->f("nom_realiza_pedido"));
            $templete->assign('NITCC', $conexion->f("nit_cc"));
            $templete->assign('PRECIOTOTAL', '$' . number_format($ptotal, 0, ".", ","));
            $templete->assign('ID_CLIENTE', $conexion->f("id_cliente"));
            
           
            //*************************FIN DE ENVIO DEL CAMBIAR ESTADO//**********************************
            
            $templete->parse('LISTABPEDIDOS', '.BPEDIDOS');
            $n++;
        }

    break;
   

    case 'cambiar_estado':
        $pedidos = reg('pedidos', 'numero_pedido', $id_pedido);
        $aprobaciones = reg('autorizaciones_pedidos', 'num_pedido', $id_pedido);
        $envia_siigo = reg('facturacion_enviada','pedido_fenviada',$id_pedido);

        if($pedidos['estado'] != "Anulado" AND $pedidos['estado'] != "No Vigente"){
            $conexion->query("SELECT * FROM usuarios INNER JOIN rol_usuario ON rol=id_rol_usuario AND id_usuario='".$_COOKIE['mkid']."';");
            $conexion->next_record();
            /*$conexion2->query("SELECT * FROM `facturacion` WHERE f_ordenc = '".$pedidos['numero_pedido']."' ORDER BY consecutivo_inh DESC LIMIT 0,1");
            $conexion2->next_record();*/
            $conexion2->query("SELECT * FROM `facturacion_enviada` WHERE pedido_fenviada = '".$pedidos['numero_pedido']."'");
            $conexion2->next_record();
            $conexion3->query("SELECT * FROM anticipos WHERE np_anticipo = '".$pedidos['numero_pedido']."' ");
            $num_anticipos = $conexion3->next_record();
            $conexion4->query("SELECT * FROM pagos WHERE nume_pedido = '".$pedidos['numero_pedido']."' ");
            $num_pagos = $conexion4->next_record();

            if ($conexion2->num_rows() > 0) {
                $factura_e = 'OK';
            }else{
                $factura_e = 'ANL';
            }

            $sigs_estados = array();
            $validacion_anticipos = array();
            switch($conexion->f("nombre_rol")){
                
                
                case "Agente":
                    
                    $opciones = 0;
                    if ($factura_e == 'ANL') {
                        array_push($sigs_estados, 'No aprobar definitivamente');
                    }
                    
                break;
                case "Administrador":
                    $opciones = 0;
                    //Autorizaciones compras
                    if($aprobaciones['aut_cliente'] == 0 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar pedido compras');
                        array_push($sigs_estados, 'No aprobar por ahora compras');
                        
                        if ($factura_e == 'Ok') {
                            array_push($sigs_estados, 'No aprobar definitivamente compras');
                        }
                    }
                    if($aprobaciones['aut_cliente'] == 1 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Compras');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Compras');
                    }
                    if($aprobaciones['aut_cliente'] == 2 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Compras');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Compras');
                    }//Fin autorizaciones por parte de compras 

                    if($aprobaciones['aut_cliente'] == 0 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){//si el cliente no ha echo nada  y artes no ha aprobado
                        array_push($sigs_estados, 'aprobar pedido artes');
                        array_push($sigs_estados, 'No aprobar por ahora artes');
                        if ($factura_e == 'ANL') {
                            array_push($sigs_estados, 'No aprobar definitivamente artes');
                        }
                        
                    }
                    if($aprobaciones['aut_cliente'] == 1 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){// se modifico el pedido y artes no ha aprobado
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Artes');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Artes');
                    }
                    if($aprobaciones['aut_cliente'] == 2 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){// se modifico el pedido y artes no ha aprobado
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Artes');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Artes');
                    }
                    if ($num_anticipos > 0) {
                        if($aprobaciones['aut_cartera'] == 0 || $aprobaciones['aut_cartera'] == 2){
                            if($aprobaciones['aut_compras'] == 1 && $aprobaciones['aut_artes'] == 1 && $aprobaciones['aut_cliente'] == 1){
                                array_push($sigs_estados, 'Autorizar pedido cartera');
                                array_push($sigs_estados, 'No autorizar por ahora cartera');
                            }
                        }
                    }else{
                        array_push($validacion_anticipos, 'Por favor carga primero un anticipo para poder autorizar este pedido');
                    }
                    
                    if ($factura_e == 'ANL') {
                        array_push($sigs_estados, 'No aprobar definitivamente');
                    }
                break;
                case "Cartera":
                    if ($num_anticipos > 0 || $num_pagos > 0) {
                        if($aprobaciones['aut_cartera'] == 0 || $aprobaciones['aut_cartera'] == 2){
                            if($aprobaciones['aut_cliente'] == 1){
                                array_push($sigs_estados, 'Autorizar pedido cartera');
                                array_push($sigs_estados, 'No autorizar por ahora cartera');
                            }
                        }
                    }else{
                        array_push($validacion_anticipos, 'Por favor carga primero un anticipo para poder autorizar este pedido');
                    }
                    if ($factura_e == 'OK') {
                        array_push($sigs_estados, 'Anular pedido');
                    }
                break;
                case "Artes":
                    if($aprobaciones['aut_cliente'] == 0 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){
                        array_push($sigs_estados, 'aprobar pedido artes');
                        array_push($sigs_estados, 'No aprobar por ahora artes');
                        if ($factura_e == 'ANL') {
                            array_push($sigs_estados, 'No aprobar definitivamente artes');
                        }
                    }
                    if($aprobaciones['aut_cliente'] == 1 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Artes');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Artes');
                    }
                    if($aprobaciones['aut_cliente'] == 2 && $aprobaciones['aut_artes'] == 0 || $aprobaciones['aut_artes'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Artes');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Artes');
                    }
                break;
                case "Compras":
                    if($aprobaciones['aut_cliente'] == 0 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar pedido compras');
                        array_push($sigs_estados, 'No aprobar por ahora compras');
                        if ($factura_e == 'ANL') {
                            array_push($sigs_estados, 'No aprobar definitivamente compras');
                        }
                    }
                    if($aprobaciones['aut_cliente'] == 1 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Compras');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Compras');
                    }
                    if($aprobaciones['aut_cliente'] == 2 && $aprobaciones['aut_compras'] == 0 || $aprobaciones['aut_compras'] == 2){
                        array_push($sigs_estados, 'Aprobar modificaciones del pedido Compras');
                        array_push($sigs_estados, 'No aprobar modificaciones del pedido por ahora Compras');
                    }
                    
                break;
                case "Director Comercial":
                    array_push($sigs_estados, 'Anular pedido');
                break;
                
            }
        }
        if ($aprobaciones['aut_cliente'] == 0){
            $templete->assign('AGREGAR','Agregar Aprobación');
        }else{
            $templete->assign('AGREGAR','Agregar Autorización');
        }
        $templete->assign('SNC','Cambiar Estado Pedido N°');
        $orig_estado = $pedidos['estado'];
        $templete->define(array('principal' => 'cambiar_estado_pedido.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('NPEDIDO',$id_pedido);
        $templete->assign('ESTADOACTUAL',$orig_estado);
        
        $templete->define_dynamic('BESTADOS', 'principal');
        for($i = 0; $i < count($sigs_estados); $i++){
                $templete->assign('ESTADO',$sigs_estados[$i]);
            $templete->parse('LISTABESTADOS', '.BESTADOS');
        }

        for($i = 0; $i < count($validacion_anticipos); $i++){
            $templete->assign('VALIDACIONANTICIPOS',$validacion_anticipos[$i]);
        }

    break;
    case 'solicitar_comprobante':
        $pedidos = reg('pedidos', 'numero_pedido', $id_pedido);
        $aprobaciones = reg('autorizaciones_pedidos', 'num_pedido', $id_pedido);
        if($pedidos['estado'] == "Aprobado" || $pedidos['estado'] == "Autorizado"){
            $conexion->query("SELECT * FROM usuarios INNER JOIN rol_usuario ON rol=id_rol_usuario AND id_usuario='".$_COOKIE['mkid']."';");
            $conexion->next_record();
            $sigs_estados = array();
            switch($conexion->f("nombre_rol")){
                case "Administrador":
                    $opciones = 0;
                    if($aprobaciones['aut_cartera'] >= 0){
                        array_push($sigs_estados, 'Solicitar comprobante de saldo');
                        array_push($sigs_estados, 'Volver a solicitar primer comprobante');
                    }
                break;
                case "Cartera":
                    if($aprobaciones['aut_cartera'] >= 0){
                        array_push($sigs_estados, 'Solicitar comprobante de saldo');
                        array_push($sigs_estados, 'Volver a solicitar primer comprobante');
                    }
                break;   
            }
        }
        $orig_estado = $pedidos['estado'];
        $templete->define(array('principal' => 'cambiar_estado_pedido.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('NPEDIDO',$id_pedido);
        $templete->assign('ESTADOACTUAL',$orig_estado);
        

        if ($_GET['estado']) {
            $templete->assign('AGREGAR','Solictud de comprobante');
            $templete->assign('SNC','Comprobantes del pedido');
        }

        $templete->define_dynamic('BESTADOS', 'principal');
        for($i = 0; $i < count($sigs_estados); $i++){
            $templete->assign('ESTADO',$sigs_estados[$i]);
            $templete->parse('LISTABESTADOS', '.BESTADOS');
        }
    break;
    case 'guardarcambio_estado':

        $conexion->query("SELECT * FROM cotizaciones WHERE id_cotizacion=(SELECT id_cotizacion_registro FROM registros_cotizacion WHERE id_registro_cotizacion=(SELECT reg_cotizacion FROM registros_pedidos WHERE id_pedido_reg=(SELECT id_pedido FROM pedidos WHERE numero_pedido='".$npedido."') LIMIT 0, 1) LIMIT 0, 1);");
        $conexion->next_record();
        $agente = reg('usuarios', 'id_usuario', $conexion->f("usuario"));
        $pedido = reg('pedidos', 'numero_pedido', $npedido);

        
        if($estadoultimo == 'No aprobar definitivamente'){
            $loki = reg('usuarios', 'id_usuario', $_COOKIE['mkid']);
            $rol = reg('rol_usuario', 'id_rol_usuario', $loki["rol"]);

            $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='3',aut_artes='3',aut_cliente='3',aut_cartera='3',aut_visual='3' WHERE num_pedido='".$npedido."';");
            
            $conexion2->query("UPDATE pedidos SET estado='Anulado' WHERE numero_pedido='".$npedido."';");
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', '".$observas.", Cambia De ".$pedido["estado"]." A Anulado', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
            enviar_mail($agente["email"], 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
            enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
        }else{
            switch ($estadoultimo){//
                case 'Anular pedido':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='3',aut_artes='3',aut_cliente='3',aut_cartera='3',aut_visual='3' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("UPDATE pedidos SET estado='Anulado' WHERE numero_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'El Director Comercial anulo totalmente el pedido ".$pedido["numero_pedido"].", <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //enviar_mail($agente["email"], 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamen tode Compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamen tode Compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $msg = '2.1 Pedido # ' . $npedido . '  Anulado  <br> Observaciones: ' . $observas;

                    $asunto = '2.1 Pedido # ' . $npedido . ' Anulado';
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Compras por los siguientes motivos:
                    <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');
                    send_mail('cartera@ag3.com.co', $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                break;
                case 'Aprobar modificaciones del pedido Compras':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='1' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Compras autoriza las modificaciones el pedido # <b>".$npedido."</b>,  <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    enviar_mail($agente["email"], 'Syspromall', 'Compras - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, las modificaciones de tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Compras - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, las modificaciones de tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                break;
                case 'No aprobar modificaciones del pedido por ahora Compras':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='2' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Compras no aprueba las modificaciones del pedido por ahora <b># ".$npedido." </b>, <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    enviar_mail($agente["email"], 'Syspromall', 'Compras - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, las modificaciones de tu pedido # ' . $npedido . ' no se han autorizado por el departamento de Compras por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Compras - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, las modificaciones de tu pedido # ' . $npedido . ' no se han autorizado por el departamento de Compras por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                break;
                case 'Aprobar modificaciones del pedido Artes':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='1' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Artes autoriza las modificaciones el pedido # <b>".$npedido."</b>,  <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    enviar_mail($agente["email"], 'Syspromall', 'Artes - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, las modificaciones de tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de Artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Artes - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, las modificaciones de tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de Artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                break;
                case 'No aprobar modificaciones del pedido por ahora Artes':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='2' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Artes no aprueba las modificaciones del pedido por ahora <b># ".$npedido." </b>, <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    enviar_mail($agente["email"], 'Syspromall', 'Artes - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, las modificaciones de tu pedido # ' . $npedido . ' no se han autorizado por el departamento de Artes por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Artes - Autorizacion de modificaciones del pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, las modificaciones de tu pedido # ' . $npedido . ' no se han autorizado por el departamento de Artes por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                break;
                case 'Aprobar pedido compras'://CAMBIAR A Aprobado COMPRAS (1)
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='1' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Compras autoriza el pedido # <b>".$npedido."</b>,  <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //enviar_mail($agente["email"], 'Syspromall', '2.1 Aprobado Compras - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', '2.1 Aprobado Compras - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $msg = '2.1 Aprobado Compras - Pedido # ' . $npedido . '<br> Observaciones: ' . $observas;
                    $asunto = '2.1 Aprobado Compras - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                break;
                case 'No aprobar por ahora compras'://NO AUTORIZAR POR AHORA
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='2' WHERE num_pedido='".$npedido."';");

                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Compras no aprueba por ahora el pedido <b># ".$npedido." </b>, <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    /*enviar_mail($agente["email"], 'Syspromall', 'No Aprobado Compras - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Compras por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'No Aprobado Compras - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Compras por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));*/

                    $msg = '2.1 No aprobado Compras - Pedido # ' . $npedido . '<br> Observaciones: ' . $observas;
                    $asunto = '2.1 No Aprobado Compras - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Compras por los siguientes motivos:
                    <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                break;
                case 'No aprobar definitivamente compras':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='3' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("UPDATE pedidos SET estado='Anulado' WHERE numero_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Compras anulo totalmente el pedido ".$pedido["numero_pedido"].", <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //enviar_mail($agente["email"], 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamen tode Compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamen tode Compras con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $msg = '2.1 Pedido # ' . $npedido . '  Anulado  <br> Observaciones: ' . $observas;

                    $asunto = '2.1 Pedido # ' . $npedido . ' Anulado';
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Compras por los siguientes motivos:
                    <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                break;
                case 'aprobar pedido artes'://CAMBIAR A AUTORIZADO ARTES (1)
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='1' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Artes autoriza el pedido # <b>".$npedido."</b>,  <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //enviar_mail($agente["email"], 'Syspromall', '2.2 Aprobado Artes - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', '2.2 Aprobado Artes - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $msg = '2.2 Aprobado Artes - Pedido # ' . $npedido . ' <br> Observaciones: ' . $observas;

                    $asunto = '2.2 Aprobado Artes - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido aprobado por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);

                break;
                case 'No aprobar por ahora artes'://NO AUTORIZAR POR AHORA
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='2' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Artes no aprueba por ahora el pedido <b># ".$npedido." </b>, <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    /*enviar_mail($agente["email"], 'Syspromall', 'No Aprobado Artes - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Artes por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'No Aprobado Artes - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Artes por los siguientes motivos:
                     <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));*/

                    $msg = '2.1 No aprobado Artes - Pedido # ' . $npedido . '<br> Observaciones: ' . $observas;

                    $asunto = '2.2 No Aprobado Artes - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Por ahora, tu pedido # ' . $npedido . ' no se ha autorizado por el departamento de Artes por los siguientes motivos:
                    <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                break;
                case 'No aprobar definitivamente artes':
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='3' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("UPDATE pedidos SET estado='Anulado' WHERE numero_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', '".$observas.", Cambia De ".$pedido["estado"]." A Anulado', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //enviar_mail($agente["email"], 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Anulado', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $msg = '2.1 Pedido # ' . $npedido . '  Anulado  <br> Observaciones: ' . $observas;

                    $asunto = '2.2 Pedido # ' . $npedido . ' Anulado';
                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se ha anulado tu pedido # ' . $npedido . ' por el departamento de artes con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com');

                    //$result = sendFree('573219113262', $msg);
                    
                break;
                case 'Autorizar pedido cartera'://CAMBIAR A AUTORIZADO CARTERA (1)

                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_cartera='1' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("UPDATE pedidos SET por_confirmar_oc='1' WHERE numero_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Cartera autoriza el pedido # <b>".$npedido."</b>,  <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                     
                    $conexion->query("SELECT * FROM usuarios WHERE id_usuario = '".$agente["id_usuario_dir"]."' ;");
                    $conexion->next_record();
                    $correo_dir = $conexion->f("email");
                    $nombre_dir = $conexion->f("nombre");

                    //Enviar Email Director...
                    //enviar_mail(trim($correo_dir), 'Syspromall', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $nombre_dir . ' <br> Felicidades, el pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    
                    $asunto = '4. Autorizado Cartera - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $nombre_dir . ' <br> Felicidades, el pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com','compras@ag3.com.co');

                    //smail(trim($correo_dir), '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $nombre_dir . ' <br> Felicidades, el pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>') ,'','');

                    $conexion2->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_servicio != '' AND codigo_servicio NOT LIKE '%SER%' AND numero_pedido='".$npedido."';");
                    if($conexion2->num_rows() > 0){


                        $asunto = '4.1 Pedido # ' . $npedido . ' Solicitud de visual';
                        $cuerpo = utf8_decode('Cordial Saludo Dpto de Artes <br><br> El departamento de Cartera ha autorizado el pago del pedido #' . $npedido . ' por favor adjuntar el visual para que el cliente lo apruebe. <br><br> Este proceso lo podrá realizar en la opción "Ver detalle" ubicada en Syspromall al buscar el pedido # '.$npedido.'<br><br>
                        <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$npedido.'/m/42/">
                          INGRESA AQUI
                        </a> ');

                        /*smail(trim('artes@promall.com.co'),  $asunto , $cuerpo , '' ,'');
                        smail('pruebaspromall@gmail.com',  $asunto , $cuerpo , '' ,'');*/
                        send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com','artes@promall.com.co');
                        /*enviar_mail($conexion->f("email"), 'Syspromall', 'Pedido # ' . $npedido . ' Solicitud de visual', utf8_decode('Cordial Saludo Dpto de Artes <br><br> El departamento de Cartera ha autorizado el pago del pedido #' . $npedido . ' por favor adjuntar el visual para que el cliente lo apruebe. <br><br> Este proceso lo podrá realizar en la opción "Ver detalle" ubicada en Syspromall al buscar el pedido # '.$npedido.'<br><br>
                        <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$npedido.'/m/42/">
                          INGRESA AQUI
                        </a> '));
                        enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Solicitud de visual', utf8_decode('Cordial Saludo Dpto de Artes <br><br> El departamento de Cartera ha autorizado el pago del pedido #' . $npedido . ' por favor adjuntar el visual para que el cliente lo apruebe. <br><br> Este proceso lo podrá realizar en la opción "Ver detalle" ubicada en Syspromall al buscar el pedido # '.$npedido.'<br><br>
                        <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$npedido.'/m/42/">
                          INGRESA AQUI
                        </a> '));*/

                    }

                    
                    //Enviar Email Compras...
                    //enviar_mail('compras@ag3.com.co', 'Syspromall', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo Departamento de Compras <br> Felicidades, el pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail($agente["email"], 'Syspromall', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo Agente' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo PruebasPromallOne' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    //smail('compras@ag3.com.co', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo Departamento de Compras <br> Felicidades, el pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'),'','');
                    //smail($agente["email"], '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo Agente' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'),'','');
                    //smail('pruebaspromall@gmail.com', '4. Autorizado Cartera - Pedido # ' . $npedido, utf8_decode('Cordial Saludo PruebasPromallOne' . $agente["nombre"] . ' <br> Felicidades, tu pedido # ' . $npedido . ' ha sido autorizado por el departamento de cartera con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'),'','');
                    
                break;
                case 'No autorizar por ahora cartera'://NO AUTORIZAR POR AHORA
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_cartera='2' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Cartera no aprueba por ahora el pedido <b># ".$npedido." </b>, <b>".$observas."</b>', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    $cuerpo = '
                    Cordial Saludo  '.$agente["nombre"].'.<br> Por ahora tu pedido ' . $npedido . ' no se autorizo por el departamento de Cartera con las siguientes observaciones: <br>
                    ' . $observas . '<br>
                    por favor informa a tu cliente y solicita a cartera la generación de solicitud de un nuevo "primer comprobante" <br><br>
                    <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$npedido.'/m/42/">INGRESA Y VERIFICA EL PEDIDO</a><br><br>Atentamente,<br><b>Syspromall</b> ';

                    /*enviar_mail($agente["email"], 'Syspromall', 'Pedido # ' . $npedido . ' no autorizado por el momento', utf8_decode($cuerpo));
                    enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' no autorizado por el momento', utf8_decode($cuerpo));*/

                    $asunto = '4 No Aprobado Cartera - Pedido # ' . $npedido;
                    send_mail($agente["email"], $asunto, utf8_decode($cuerpo) ,'pruebaspromall@gmail.com');

                break;
                case 'Volver a solicitar primer comprobante'://NO AUTORIZAR POR AHORA
                    //send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com','artes@promall.com.co');

                    $conexion->query("UPDATE autorizaciones_pedidos SET aut_cliente='0',aut_cartera='0',aut_sol='0' WHERE num_pedido='".$npedido."';");
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Cartera envio la solicitud al cliente de adjuntar el primer comprobante, ".$observas."', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    $bacc = 'noc';

                    $cuerpo = utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se solicita el envio de email de requerimiento del comprobante normal al cliente con el pedido asociado # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');
                    
                    send_mail('pruebaspromall@gmail.com', 'Autorización - Pedido # ' . $npedido , utf8_decode( email_p($esplod[0],$npedido) ), '');
                    send_mail($agente["email"], 'Pedido # ' . $npedido . ' Solicitud comprobante normal' , $cuerpo , 'pruebaspromall@gmail.com');


                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Pedido # ' . $npedido . ' Solicitud comprobante normal', utf8_decode('Cordial Saludo ' . $agente["nombre"] . ' <br> Se solicita el envio de email de requerimiento del comprobante normal al cliente con el pedido asociado # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    $esplod = explode("|", $pedido['info_quien']);

                    //send_mail(trim($esplod[0]), 'Autorización - Pedido # ' . $npedido , utf8_decode( email_p($esplod[0],$npedido) ), 'pruebaspromall@gmail.com');

                    //enviar_mail(trim('pruebaspromall@gmail.com'), $nombre_persona, utf8_decode('Autorización - Pedido # ' . $npedido), utf8_decode(email_p('pruebaspromall@gmail.com',$npedido)));

                break;
                case 'Solicitar comprobante de saldo':

                    $conexion->query("SELECT * FROM usuarios WHERE id_usuario = '".$agente["id_usuario_dir"]."' ;");
                    while($conexion->next_record()){
                        $correo_dir = $conexion->f("email");
                        $nombre_dir = $conexion->f("nombre");
                    }

                    //email del director
                    //enviar_mail($correo_dir, 'Syspromall', '7. Saldo Cliente - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $nombre_dir . '<br>El Departamento de cartera solicito un nuevo comprobante asociado a el pedido # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail('pruebaspromall@gmail.com', 'Syspromall', '7. Saldo Cliente - Pedido # ' . $npedido, utf8_decode('Cordial Saludo PruebasPromallOne<br>El Departamento de cartera solicito un nuevo comprobante asociado a el pedido # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));
                    //enviar_mail($agente["email"], 'Syspromall', '7. Saldo Cliente - Pedido # ' . $npedido, utf8_decode('Cordial Saludo ' . $agente["nombre"] . '<br> El Departamento de cartera solicito un nuevo comprobante asociado a el pedido # ' . $npedido . ' con las siguientes observaciones <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>'));

                    
                    $explo_quien = explode("|", $pedido["info_quien"]);

                    $html_correo = solicitud_nuevo_comprobante($explo_quien[0],$npedido);//correo que se envia cuando se solciita un nuevo comprobante

                    enviar_mail(trim($explo_quien[0]), 'Syspromall', utf8_decode('7. Saldo Cliente - Pedido #' . $npedido), utf8_decode($html_correo));
                    //enviar_mail(trim('pruebaspromall@gmail.com'), 'Promall', utf8_decode('7. Saldo Cliente - Pedido # ' . $npedido), utf8_decode($html_correo));


                    $asunto = '7. Saldo Cliente - Pedido # ' . $npedido;
                    $cuerpo = utf8_decode('Cordial Saludo ' . $nombre_dir . '<br>El Departamento de cartera solicito un nuevo comprobante asociado a el pedido # ' . $npedido . ' con las siguientes observaciones: <br><br>' . $observas . '<br><br>Atentamente,<br><b>Syspromall</b>');

                    send_mail($agente["email"], $asunto, $cuerpo ,'pruebaspromall@gmail.com',$correo_dir);

                    //reinicia la autorizacion para que adjunte ell cpmprobante y registra en el historial el movimineot que acaba de hacer 
                    $conexion2->query("UPDATE autorizaciones_pedidos SET aut_sol='0' WHERE num_pedido='".$npedido."';");                    
                    $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido['id_pedido']."', 'Cartera solicto un nuevo comprobante al pedido asociado # <b>" . $npedido . "</b> , ".$observas."', '".date("Y-m-d H:i:s")."', '".$_COOKIE['mkid']."');");
                    //

                break;
            }//cierra el switch
        }//cierra el estado de la peticion 


        //hasta aqui se encuentran las validaciones 

        $autorizaciones_pedido = reg('autorizaciones_pedidos', 'num_pedido', $npedido);
        if ($autorizaciones_pedido["aut_compras"] == 1 && $autorizaciones_pedido["aut_compras"] == 1) {
            $conexion2->query("UPDATE autorizaciones_pedidos SET apr_edicion='0' WHERE num_pedido='".$npedido."';");
        }
        //no se que hace lo de arriba :(

        $explo_quien = explode("|", $pedido["info_quien"]);//correo del cliente

        if($estadoultimo == 'Autorizar pedido cartera'){

            //estamos gestionando tu peidod valida si el pedido tiene visual 
            if ($autorizaciones_pedido["aut_cartera"] == 1) {

                $conexion->query("SELECT DISTINCT(reg_cotizacion) AS codigo FROM registros_pedidos INNER JOIN pedidos ON id_pedido=id_pedido_reg AND numero_pedido='".$pedido['numero_pedido']."';");

                //fecha en letras 
                $data = explode('-', $pedido['fecha_aut_cartera']); 
                $fechat = $data[2] . '&nbsp;de&nbsp;' . date('F \d\e\l Y', strtotime($data[0] . '-' . $data[1]));
                $fechat = strtr($fechat, $calendario_meses);
                //

                if ($autorizaciones_pedido["aut_visual"] == 1) {
                    $dato = '
                            <div class="">    
                                <div style="font-family:Roboto, Tahoma, Verdana, Segoe, sans-serif;color:#555555;line-height:120%; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">  
                                    <div style="font-size:12px;line-height:14px;color:#555555;font-family:Roboto, Tahoma, Verdana, Segoe, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="font-size: 13px; line-height: 15px;">Hola '.$pedido["nom_realiza_pedido"].', Te damos la bienvenida.</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left"><span style="font-size: 13px; line-height: 15px;">Cordial Saludo. Tu Pedido esta siendo gestionado, de acuerdo la fecha en que recibimos tu pago, tu pedido sera despachado el dia '.$fechat.'. seguiremos en contacto con tigo para completar este proceso.<br> si tienes alguna pregunta o inquietud por favor comunicate con tu agente de ventas.</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left"><strong><span style="font-size: 13px; line-height: 15px;"></span></strong></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px">&#160;</p></div> 
                                </div>
                            </div>';

                    //******************************************************************************//
                    $cre = explode("/", $pedido["forma_pago"]);
                    $credito = $cre[1];

                    $nuevafecha = $pedido['fecha_aut_cartera'];

                    if ($credito == 'Sin credito') {
                        $nfecha = $nuevafecha;
                    }elseif ($credito == 'Credito 15 dias') {
                        $nuevaf = strtotime('+15 day' , strtotime($nuevafecha));
                        $nuevaf = date('Y-m-d', $nuevaf);
                        $nfecha = $nuevaf;
                    }elseif ($credito == 'Credito 30 dias') {
                        $nuevaf = strtotime('+30 day' , strtotime($nuevafecha));
                        $nuevaf = date('Y-m-d', $nuevaf);
                        $nfecha = $nuevaf;
                    }elseif ($credito == 'Credito 45 dias') {
                        $nuevaf = strtotime('+45 day' , strtotime($nuevafecha));
                        $nuevaf = date('Y-m-d', $nuevaf);
                        $nfecha = $nuevaf;
                    }elseif ($credito == 'Credito 60 dias') {
                        $nuevaf = strtotime('+60 day' , strtotime($nuevafecha));
                        $nuevaf = date('Y-m-d', $nuevaf);
                        $nfecha = $nuevaf;
                    }elseif ($credito == 'Credito 90 dias') {
                        $nuevaf = strtotime('+90 day' , strtotime($nuevafecha));
                        $nuevaf = date('Y-m-d', $nuevaf);
                        $nfecha = $nuevaf;
                    }else{
                        $nfecha = $nuevafecha;
                    }

                    $vfecha = strtotime('-2 day' , strtotime($nfecha));
                    $vfecha = date('Y-m-d', $vfecha);

                    //$vfecha = fecha en la que se planea enviar el email
                    //nfecha = si existe credito le suma los dias si no muestra la normal

                        $conexion2->query("SELECT * FROM programacion_de_fecha_pago WHERE numeropedido_pago = '".$pedido['numero_pedido']."' ");
                        if ($conexion2->num_rows() <= 0 ) {
                            $conexion3->query("INSERT INTO programacion_de_fecha_pago VALUES ('','".$pedido['numero_pedido']."','".$vfecha."','".$nfecha."','1','0')");    
                        }else{

                        }

                    //
                }else{
                    $dato = '
                            <div class="">    
                                <div style="font-family:Roboto, Tahoma, Verdana, Segoe, sans-serif;color:#555555;line-height:120%; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">  
                                    <div style="font-size:12px;line-height:14px;color:#555555;font-family:Roboto, Tahoma, Verdana, Segoe, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="font-size: 13px; line-height: 15px;">Hola '.$pedido["nom_realiza_pedido"].', Te damos la bienvenida.</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left"><span style="font-size: 13px; line-height: 15px;">Cordial Saludo. Tú pago ha sido aprobado. En este momento tú pedido queda en cola de descargo y reservación de productos en inventario, y tú logo ha sido remitido al departamento de artes para realizar el visual digital de marcación, el cual, en cuanto se desarrolle, se te enviará para revisión y aprobación del mismo, (si no has enviado el logo editable, por favor hazlo llegar a tu agente de ventas lo más pronto para continuar con el procedimiento). Recuerda que hasta tener el aprobado del visual digital de marcación se comienza a contar el tiempo para despacho.</span></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: left"><strong><span style="font-size: 13px; line-height: 15px;"></span></strong></p><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px">&#160;</p><p style="margin: 0;font-size: 12px;line-height: 14px">&#160;</p></div> 
                                </div>
                            </div>';
                }//else
                
                $html_estamos = estamos_gestionando($dato);

                enviar_mail(trim($explo_quien[0]), 'Syspromall', 'Pedido # ' . $npedido . ' Gestionado ', utf8_decode($html_estamos));
                enviar_mail(trim('pruebaspromall@gmail.com'), 'Syspromall', 'Pedido # ' . $npedido . ' Gestionado ', utf8_decode($html_estamos));

            }//si cartera es igual a 1
        }//si cartera va a aprobar 
        

        //Email de autorizacion por parte del cliente
        if($autorizaciones_pedido["aut_compras"] == 1 && $autorizaciones_pedido["aut_artes"] == 1 && $autorizaciones_pedido["aut_cliente"] == 0){
            
            $email_cliente = explode("|", $pedido["info_quien"]);
            //enviar_mail(trim($explo_quien[0]), 'Promall', utf8_decode('Autorización - Pedido # ' . $npedido), utf8_decode(email_p($explo_quien[0],$npedido)));
            //enviar_mail('pruebaspromall@gmail.com', 'Promall', utf8_decode('Autorización - Pedido # ' . $npedido), utf8_decode(email_p($explo_quien[0],$npedido)));
            $msg = 'AUTORIZACION - PEDIDO No. ' . $npedido;

            send_mail(trim($email_cliente[0]), $msg , utf8_decode( email_p($explo_quien[0],$npedido) ) );
            send_mail('pruebaspromall@gmail.com', $msg , utf8_decode( email_p($explo_quien[0],$npedido) ) );

            $conexion2->query("UPDATE pedidos SET estado='Aprobado' WHERE numero_pedido='".$pedido['numero_pedido']."';");

        }
        //

        /*if($autorizaciones_pedido["aut_compras"] == 1 && $autorizaciones_pedido["aut_artes"] == 1 ){
            if ($autorizaciones_pedido["aut_cliente"] == 1 || $autorizaciones_pedido["aut_cliente"] == 2) {
                //Correo del pedido que acaba de aprobar
                $m = 'm';
                enviar_mail(trim($agente['email']), 'Syspromall', 'Se ha modificado tu pedido #' . $pedido["numero_pedido"], utf8_decode(daniel($esplode[0],$pedido["numero_pedido"],$m)));
                enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Se ha modificado tu pedido #' . $pedido["numero_pedido"], utf8_decode(daniel($esplode[0],$pedido["numero_pedido"],$m)));
                enviar_mail('cartera@ag3.com.co', 'Syspromall', 'Se ha modificado tu pedido #' . $pedido["numero_pedido"], utf8_decode(daniel($esplode[0],$pedido["numero_pedido"],$m)));
                enviar_mail(trim($esplode[0]), 'Promall', 'Se ha modificado tu pedido #' . $pedido["numero_pedido"], utf8_decode(daniel($esplode[0],$pedido["numero_pedido"],$m)));
                //Fin del correo que acaba de aprobar 
            }
        }*/

        //modifica el estado cuando el pedido todas los checks se encuentren en 1
        $conexion2->query("UPDATE pedidos a INNER JOIN autorizaciones_pedidos b ON a.numero_pedido = b.num_pedido SET a.estado = 'Autorizado' WHERE b.aut_compras = '1' AND b.aut_artes = '1' AND b.aut_cliente = '1' AND b.aut_cartera = '1' AND b.aut_visual = '1' ");
        //

        redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$npedido."'/m/45/");
    break;
    case 'ver_historial':
        
        $templete->define(array('principal' => 'ver_historico.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $templete->assign('CADENA',$cadena);
        $templete->assign('NPEDIDO',$id_pedido);

        $regp = reg('pedidos','numero_pedido',$id_pedido);
        $email = explode("|", $regp['info_quien']);

        $templete->assign('EMAIL', $email[0]);
        $templete->assign('ID_PEDIDO', $id_pedido);
        $templete->assign('NITCC', $regp['nit_cc']);

        $completo_h = array();

        $conexion2->query("SELECT * FROM pedidos_historial WHERE id_pedido=(SELECT id_pedido FROM pedidos WHERE numero_pedido LIKE '".$id_pedido."');");
        while($conexion2->next_record()){

            if(is_numeric($conexion2->f("usuario_bitacora"))){

                $usuario = reg('usuarios', 'id_usuario', $conexion2->f("usuario_bitacora"));
                $nom_usuario = $usuario["usuario"];

            }else{

                $nom_usuario = 'Cliente';

            }

            array_push($completo_h, array( "fecha" => $conexion2->f("fecha_hora"), "obser" => $conexion2->f("observaciones"), "user" => $nom_usuario ));

        }


        $conexion2->query("SELECT * FROM oc_historico WHERE id_oc=(SELECT numero_pedido FROM pedidos WHERE numero_pedido LIKE '".$id_pedido."');");
        while($conexion2->next_record()){

            $usuario = reg('usuarios', 'id_usuario', $conexion2->f("usuario"));

            if($conexion2->f("usuario") == 'Cliente'){
                
                $nom_usuario = 'Cliente';

            }elseif($conexion2->f("usuario") == 0){

                $nom_usuario = 'Compras';

            }else{
                
                $nom_usuario = $usuario["usuario"];
                
            }

            array_push($completo_h, array( "fecha" => $conexion2->f("fecha"), "obser" => $conexion2->f("observacion"), "user" => $nom_usuario ));

        }

        $templete->define_dynamic('BHISTORICO', 'principal');
        foreach ($completo_h as $key => $value) {


            
            $templete->assign('FECHA',$value["fecha"]);
            $templete->assign('OBSERVACIONES', $value["obser"]);
            $templete->assign('USUARIO', $value["user"]);
            $templete->parse('LISTABHISTORICO', '.BHISTORICO');
        }


        /*$conexion2->query("SELECT * FROM historial_visuales WHERE npedido=(SELECT numero_pedido FROM pedidos WHERE numero_pedido='".$id_pedido."');");
        $templete->define_dynamic('BHISTORICOS', 'principal');
        while($conexion2->next_record()){
            $templete->assign('IDVISUAL',$conexion2->f('id_visual'));
            $templete->assign('HVISUAL','<embed src="https://sys.promall.com.co/'.$conexion2->f('h_visual').'"></embed>');
            $templete->assign('FCVISUAL',$conexion2->f('fc_visual'));
            $templete->assign('OBSER',$conexion2->f('observa_visual'));
            $templete->parse('LISTABHISTORICOS', '.BHISTORICOS');
        }*/
        $syscon->query("SELECT * FROM blue_sys_visuales WHERE npedido= '".$id_pedido."';");
        $templete->define_dynamic('BHISTORICOS', 'principal');
        while($syscon->next_record()){
            $templete->assign('IDVISUAL',$syscon->f('id_visual'));
            $templete->assign('HVISUAL','<embed src="https://promall.com.co/'.$syscon->f('h_visual').'" style="width:300px;height:200px;"></embed>');
            $templete->assign('RUTAVISUAL','https://promall.com.co/'.$syscon->f('h_visual').' ');
            $templete->assign('FCVISUAL',$syscon->f('fc_visual'));
            $templete->assign('OBSER',$syscon->f('observa_visual'));
            $templete->parse('LISTABHISTORICOS', '.BHISTORICOS');
        }

        $syscon->query("SELECT * FROM blue_sys_prototipo WHERE pedido = '".$id_pedido."';");
        $templete->define_dynamic('BPROTOTIPO', 'principal');
        while($syscon->next_record()){
            $templete->assign('ID',$syscon->f('id'));
            $templete->assign('HPROTOTIPO','<embed src="https://promall.com.co/'.$syscon->f('prototipo').'" style="width:300px;height:200px;"></embed>');
            $templete->assign('RUTAPROTOTIPO','https://promall.com.co/'.$syscon->f('prototipo').' ');
            $templete->assign('FCPROTOTIPO',$syscon->f('fecha'));
            $templete->parse('LISTABPROTOTIPO', '.BPROTOTIPO');
        }

        $syscon->query("SELECT * FROM blue_sys_nt WHERE pedido_nt = '".$regp['numero_pedido']."';");
        $templete->define_dynamic('BNT', 'principal');
        while($syscon->next_record()){
            $templete->assign('HNT','<embed src="https://promall.com.co/'.$syscon->f('ruta').'" style="width:300px;height:200px;"></embed>');
            $templete->assign('RUTANT','https://promall.com.co/'.$syscon->f('ruta').' ');
            $templete->assign('FCNT', $syscon->f('fecha_nt'));
            $templete->assign('OBNT', $syscon->f('obser'));
            $templete->parse('LISTABNT', '.BNT');
        }

        $syscon->query("SELECT * FROM blue_sys_archivos WHERE npedido_c= '".$regp['numero_pedido']."';");
        $templete->define_dynamic('BHCOMPROBANTES', 'principal');
        while($syscon->next_record()){
            $templete->assign('HCOMPROBANTE','<embed src="https://promall.com.co/'.$syscon->f('h_comprobante').'" style="width:300px;height:200px;"></embed>');
            $templete->assign('RUTA','https://promall.com.co/'.$syscon->f('h_comprobante').' ');
            $templete->assign('FCOMPROBANTE', $syscon->f('fc_comprobante'));
            $templete->assign('COBSERVA', $syscon->f('observa_comprobante'));
            $templete->parse('LISTABHCOMPROBANTES', '.BHCOMPROBANTES');
        }

        $conexion2->query("SELECT * FROM remisiones_manual WHERE num_pedido = '".$regp['numero_pedido']."' ");
        $templete->define_dynamic('BRMANUAL', 'principal');
        while($conexion2->next_record()){

            $templete->assign('HRMANUAL', '<a class="btn btn-primary" href="https://sys.promall.com.co/promall/area/includes/tcpdf/examples/remision_manual.php?id='.$conexion2->f("id_remision").'" target="_blank"> <i class="fa fa-print"></i> Ver remision manual </a>');
            

            $templete->parse('LISTABRMANUAL', '.BRMANUAL');
        }

        $conexion2->query("SELECT * FROM remisiones WHERE num_pedido = '".$regp['numero_pedido']."' ");
        $templete->define_dynamic('BRNORMAL', 'principal');
        while($conexion2->next_record()){

            $templete->assign('HRNORMAL', '<a class="btn btn-primary" href="https://sys.promall.com.co/promall/area/includes/tcpdf/examples/remision.php?id='.$conexion2->f("id_remision").'" target="_blank"> <i class="fa fa-print"></i> Ver remision </a>');
            

            $templete->parse('LISTABRNORMAL', '.BRNORMAL');
        }


    break;
    case 'editar':
        $templete->define(array('principal' => 'editar_pedido.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );
        $pedido = reg('pedidos', 'id_pedido', $id_pedido);
        $apedido = reg('autorizaciones_pedidos', 'num_pedido', $pedido['numero_pedido']);
        $r_pedido = reg('registros_pedidos', 'id_pedido_reg', $id_pedido);
        $r_cotizacion = reg('registros_cotizacion', 'id_registro_cotizacion', $r_pedido['reg_cotizacion']);
        $templete->assign('NUMPEDIDO', $pedido['numero_pedido']);

        $remisiones = reg("remisiones",'noremision', $pedido["numero_pedido"]);
        $facturacion = reg("facturacion",'f_ordenc', $pedido["numero_pedido"]);

        if ($remisiones["noremision"] != '' ) {
            $templete->assign('VALIDACION', 'none');
        }else{
            $templete->assign('VALIDACION', '');
        }

        $conexion2->query("SELECT * FROM pedidos WHERE numero_pedido = '".$pedido['numero_pedido']."' ");
        while($conexion2->next_record()){
        $observa = reg('listado_observaciones_pedidos', 'id_lista_obs', $conexion2->f('observaciones'));
        $templete->assign('REALIZA_PEDIDO', $conexion2->f('nom_realiza_pedido'));
        $templete->assign('RECIBE_PEDIDO', $conexion2->f('nom_recibe_pedido'));
        $templete->assign('NPED', $conexion2->f('numero_pedido'));
        $templete->assign('NOMBRE', $conexion2->f('razon_social'));
        $templete->assign('NITCC', $conexion2->f('nit_cc'));
        $dir_factu = explode("/", $conexion2->f('dir_facturacion'));
        $dir_envio = explode("/", $conexion2->f('dir_envio'));
        $templete->assign('DEP_PAGO', $conexion2->f('dep_pago'));  
        $templete->assign('CIUDAD_FACTURACION', $dir_factu[1]);
        $templete->assign('DIR_FACTURACION', $dir_factu[0]);
        $templete->assign('DIRENVIO', $dir_envio[0]);
        $templete->assign('CIUDAD_ENVIAR',$dir_envio[1].'/'.$dir_envio[2]);
        $templete->assign('TELCONTACTO', $conexion2->f('tel_contacto'));
        $tel_cd = explode("|", $conexion2->f('tel_contacto_directo'));
        $templete->assign('TELCONTACTODIRECTO', $tel_cd[0]);
        $templete->assign('CELCONTACTODIRECTO', $tel_cd[1]);
        $templete->assign('EMAIL', $conexion2->f('email'));
        $templete->assign('OBSERVAS', $conexion2->f('observaciones'));
        $templete->assign('MIRAOBSERVA', utf8_encode($observa['titulo']));
        $templete->assign('MEDIO_PAGO', $conexion2->f('medio_pago'));
        $forma_de_pago = explode("/", $conexion2->f('forma_pago'));
        $templete->assign('FORMA_PAGO', $forma_de_pago[0]);
        $templete->assign('CREDITOS', $conexion2->f('creditos'));
        
        $templete->assign('OBSERVACIONAGENTE', $conexion2->f("observacion_agente"));
        $precodigo_cota = explode("-", $conexion2->f("numero_pedido"));
        $precodigo_cota = 'C' . strtoupper(substr($precodigo_cota[0], 1, 3)) . '-' . $precodigo_cota[1];
        $templete->assign('CADENA', $precodigo_cota.';');
        $templete->assign('ALLCOTI', $conexion2->f("cotizaciones"));
        $all = explode(";", $conexion2->f("cotizaciones"));
        $get = explode(";", $_GET["cadena"]);
        $all_uno = $all[0];
        $all_dos = $all[1];
        $all_tre = $all[2];
        $all_cua = $all[3];
        $all_cin = $all[4];
        $get_uno = $get[0];
        $get_dos = $get[1];
        $get_tre = $get[2];
        $get_cua = $get[3];
        $get_cin = $get[4];
        if ($all_uno != $get_uno) {
            $templete->assign('ALLUNO', '<input type="hidden" name="uno" value="'.$all_uno.';"><input type="hidden" name="unos" value='.$_GET['cadena'].'><input type="hidden" name="unox" value='.$precodigo_cot.'><input type="submit" name="alluno" value="llamar '.$all_uno.'">');
        }if ($all_dos != $get_dos) {
            $templete->assign('ALLDOS', '<input type="hidden" name="dos" value="'.$all_dos.';"><input type="hidden" name="doss" value='.$_GET['cadena'].'><input type="hidden" name="dosx" value='.$precodigo_cot.'><input type="submit" name="alldos" value="llamar '.$all_dos.'">');
        }if ($all_tre != $get_tre) {
            $templete->assign('ALLTRE', '<input type="hidden" name="tre" value="'.$all_tre.';"><input type="hidden" name="tres" value='.$_GET['cadena'].'><input type="hidden" name="trex" value='.$precodigo_cot.'><input type="submit" name="alltre" value="llamar '.$all_tre.'">');
        }if ($all_cua != $get_cua) {
            $templete->assign('ALLCUA', '<input type="hidden" name="cua" value="'.$all_cua.';"><input type="hidden" name="cuas" value='.$_GET['cadena'].'><input type="hidden" name="cuax" value='.$precodigo_cot.'><input type="submit" name="allcua" value="llamar '.$all_cua.'">');
        }if ($all_cin != $get_cin) {
            $templete->assign('ALLCIN', '<input type="hidden" name="cin" value="'.$all_cin.';"><input type="hidden" name="cins" value='.$_GET['cadena'].'><input type="hidden" name="cinx" value='.$precodigo_cot.'><input type="submit" name="allcin" value="llamar '.$all_cin.'">');
        }
        //

        if ($conexion2->f("observaciones") >= 17 || $conexion2->f("observaciones") >= 20) {
            $templete->assign('SIN', 'style=""');
            if ($conexion2->f("fecha_despacho") != '') {
                $templete->assign('FECHARECIBIDO', $conexion2->f('fecha_comprobante'));
                $templete->assign('FECHADESPACHO', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_despacho"))));
                $templete->assign('FECHASINDESPACHO', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_sin_despacho"))));
            }
        }else{
            $templete->assign('SIN', 'style="display:none;"');
        }
        if ($conexion2->f("observaciones") >= 21 || $conexion2->f("observaciones") >= 24) {
            $templete->assign('CON', 'style=""');
            if ($conexion2->f("fecha_sin_despacho") != '') {
                $templete->assign('FECHARECIBIDOS', $conexion2->f('fecha_comprobante'));
                $templete->assign('FECHADESPACHOS', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_despacho"))));
                $templete->assign('FECHASINDESPACHOS', date("Y-m-d\TH:i:s",strtotime($conexion2->f("fecha_sin_despacho"))));
            }
        }else{
            $templete->assign('CON', 'style="display:none;"');
        }

        $historia_cliente = reg('clientes', 'no_documento', $conexion2->f('nit_cc'));
        $info_quien = explode("|", $conexion2->f('info_quien'));
        
        $templete->assign('EMAILQUIEN', $info_quien[0]);
        $templete->assign('TELQUIEN', $info_quien[1]);
        $credito = $forma_de_pago[0];
        $creditos = $historia_cliente['credito'];
        if ($creditos == "") {
            $templete->assign('CREDITOS', 'SIN CREDITO PREVIO');
        }else{
            $templete->assign('CREDITOS',$historia_cliente['credito']);
        }
        if ($creditos == "Sin credito") {
            $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                <option value="100% Anticipo">100% Anticipo</option>
                <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>');
        }
        if ($creditos == "Credito 15 dias") {
            $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                <option value="100% Anticipo">100% Anticipo</option>
                <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                <option value="50% anticipo 50% credito 15 días">50% anticipo 50% credito 15 días</option>
                <option value="100% credito 15 días">100% credito 15 días</option>');
        }
        if ($creditos == "Credito 30 dias") {
            $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                <option value="100% Anticipo">100% Anticipo</option>
                <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                <option value="100% credito 30 días">100% credito 30 días</option>');
        }
        if ($creditos == "Credito 60 dias") {
            $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                <option value="100% Anticipo">100% Anticipo</option>
                <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                <option value="100% credito 30 días">100% credito 30 días</option>
                <option value="100% credito 60 días">100% credito 60 días</option>');
        }
        if ($creditos == "Credito 90 dias") {
            $templete->assign('CREDITO', '<option value="'.$credito.'" style="background-color: rgba(0, 115, 255, 0.3);" selected>'.$credito.'</option>
                <option value="100% Anticipo">100% Anticipo</option>
                <option value="50% Anticipo 50% Para Despacho">50% Anticipo 50% Para Despacho</option>
                <option value="50% anticipo 50% credito 30 días">50% anticipo 50% credito 30 días</option>
                <option value="50% anticipo 50% credito 60 días">50% anticipo 50% credito 60 días</option>
                <option value="50% anticipo 50% credito 90 días">50% anticipo 50% credito 90 días</option>
                <option value="100% credito 30 días">100% credito 30 días</option>
                <option value="100% credito 60 días">100% credito 60 días</option>
                <option value="100% credito 90 días">100% credito 90 días</option>');
        }
    }
        
    $templete->define_dynamic('BCIUDAD', 'principal');
    $templete->define_dynamic('BCIUDAD2', 'principal');
    $conexion->query("SELECT DISTINCT(nombre_ciudad) AS ciudad, departamento, indicativo_dep FROM ciudades ORDER BY ciudad;");
    while($conexion->next_record()){
        $templete->assign('CIUDAD2', utf8_encode($conexion->f("ciudad") . ', '.$conexion->f("departamento").'/'.$conexion->f("indicativo_dep")));
        $templete->parse('LISTABCIUDAD', '.BCIUDAD');
        $templete->parse('LISTABCIUDAD2', '.BCIUDAD2');
    }
    $templete->define_dynamic('BOBSERVAS', 'principal');
    $conexion->query("SELECT * FROM listado_observaciones_pedidos ORDER BY id_lista_obs;");
    while($conexion->next_record()){
        $templete->assign('IDOBSERVA', $conexion->f("id_lista_obs"));
        $templete->assign('DOBSERVA', utf8_encode($conexion->f("titulo")));
        $templete->parse('LISTABOBSERVAS', '.BOBSERVAS');
    }

    if ($apedido['aut_cliente'] == 1) {
        $templete->assign('AUTCLIENTE', 'none');
    }else{
        $templete->assign('AUTCLIENTE', '');
    }
    
    $templete->assign('DATO', toko($id_pedido));
    break;
    case 'editar_fines_one':
        $pedido = reg('pedidos', 'numero_pedido', $_POST['nped']);
        $aut = reg('autorizaciones_pedidos', 'num_pedido', $_POST['nped']);
        $usuario = reg('usuarios', 'id_usuario', $pedido['usuario_creador']);
        $id_pedido = $pedido['id_pedido'];
        $numero_pedido = $pedido['nped'];
        $estado = $pedido['estado'];

        //EXPLODE//
        $info_quien = $pedido['info_quien'];
        $uquien = explode("|", $info_quien);
        $dir_envio = $pedido['dir_envio'];
        $dx = explode("/", $dir_envio);
        $tel_contacto_directo = $pedido['tel_contacto_directo'];
        $tx = explode("|", $tel_contacto_directo);
        //
        $nom_recibe_pedido = $pedido['nom_recibe_pedido'];
        $nom_realiza_pedido = $pedido['nom_realiza_pedido'];
        $razon_social = $pedido['razon_social'];
        $nit_cc = $pedido['nit_cc'];
        $dir_facturacion = $pedido['dir_facturacion'];
        $dir_envio = $pedido['dir_envio'];
        $tel_contacto = $pedido['tel_contacto'];
        $tel_contacto_directo = $pedido['tel_contacto_directo'];
        $email = $pedido['email'];
        $observaciones = $pedido['observaciones'];
        $medio_pago = $pedido['medio_pago'];
        $forma_pago = $pedido['forma_pago'];
        $usuario_creador = $pedido['usuario_creador'];
        $fecha_creacion = $pedido['fecha_creacion'];
        $fecha_comprobante = $pedido['fecha_comprobante'];
        $fecha_despacho = $pedido['fecha_despacho'];
        $fecha_sin_despacho = $pedido['fecha_sin_despacho'];
        $observacion_agente = $pedido['observacion_agente'];
        $dep_pago = $pedido['dep_pago'];
        $visual = $pedido['visual'];
        $info_quien = $pedido['info_quien'];

        if ($_POST['fecharecibidos'] != '') {
            $fecharecibidox = $_POST['fecharecibidos'];
        }if ($_POST['fecharecibido'] != '') {
            $fecharecibidox = $_POST['fecharecibido'];
        }

        if ($_POST['fechasindespacho'] != '') {
            $u = str_replace("T", " ", $_POST['fechasindespacho']);
            $fechasindespacho = date("Y-m-d\ H:i:s",strtotime($u));
        }if ($_POST['fechasindespachos'] != '') {
            $w = str_replace("T", " ", $_POST['fechasindespachos']);
            $fechasindespacho = date("Y-m-d\ H:i:s",strtotime($w));
        }

        if ($_POST['fechadespacho'] != '') {
            $i = str_replace("T", " ", $_POST['fechadespacho']);
            $fechadespacho = date("Y-m-d\ H:i:s",strtotime($i));
        }if ($_POST['fechadespachos'] != '') {
            $o = str_replace("T", " ", $_POST['fechadespachos']);
            $fechadespacho = date("Y-m-d\ H:i:s",strtotime($o));
        }

        
        if ($nom_realiza_pedido != $_POST['realiza_pedido']) {
            $edicion_uno = 'NOMBRE DE QUIEN REALIZA EL PEDIDO ' .$pedido['nom_realiza_pedido']. ' CAMBIO ' .$_POST['realiza_pedido'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_uno."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }
        if ($uquien[0] != $_POST['email_q']) {
            $edicion_uno = 'EMAIL DE QUIEN REALIZA EL PEDIDO ' .$uquien[0]. ' CAMBIO ' .$_POST['email_q'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_uno."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($uquien[1] != $_POST['tel_q']) {
            $edicion_uno = 'TELEFONO DE QUIEN REALIZA EL PEDIDO ' .$uquien[0]. ' CAMBIO ' .$_POST['tel_q'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_uno."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($nom_recibe_pedido != $_POST['NOMRECIBEPEDIDO']) {
            $edicion_uno = 'NOMBRE DE QUIEN RECIBE EL PEDIDO ' .$pedido['nom_recibe_pedido']. ' CAMBIO ' .$_POST['NOMRECIBEPEDIDO'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_uno."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($dx[0] != $_POST["direnvio"]) {
            $edicion_tres = 'DIR. DE ENVIO ' .$dx[0]. 'CAMBIO' .$_POST["direnvio"];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_tres."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($dx[1].'/'.$dx[2] != $_POST["ciudad_enviar"]) {
            $edicion_tres = 'CIUDAD DE ENVIO ' .$dx[1].'/'.$dx[2]. 'CAMBIO' .$_POST["ciudad_enviar"];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_tres."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($tx[0] != $_POST['telcontactodirecto']) {
            $edicion_dos = 'TELÉFONO DE CONTACTO DIRECTO (DE QUIEN RECIBE) ' .$tx[0]. ' CAMBIO ' .$_POST['telcontactodirecto'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_dos."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($tx[1] != $_POST['celcontactodirecto']) {
            $edicion_dos = 'CELULAR DE CONTACTO DIRECTO (DE QUIEN RECIBE) ' .$tx[1]. ' CAMBIO ' .$_POST['celcontactodirecto'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_dos."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($observaciones != $_POST['observas']) {
            $edicion_cin = 'OBSERVACIONES ' .$observaciones. 'CAMBIO' .$_POST['observas'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_cin."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($medio_pago != $_POST['medio_pago']) {
            $edicion_sei = 'MEDIO DE PAGO ' .$medio_pago. 'CAMBIO' .$_POST['medio_pago'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_sei."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }if ($forma_pago != $_POST['forma_pago']) {
            $edicion_sie = 'FORMA DE PAGO ' .$pedido['forma_pago']. 'CAMBIO' .$_POST['forma_pago'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_sie."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }

        if ($fecharecibidox != '') {
            if ($pedido['fecha_comprobante'] != $fecharecibidox) {
                $edicion_och = 'FECHA ACORDADA PARA EL DESPACHO ' .$pedido['fecha_comprobante'].  ' CAMBIO ' .$fecharecibidox;
                $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_och."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
            }
        }
        
        if ($fechasindespacho != '') {
            if ($pedido['fecha_sin_despacho'] != $fechasindespacho) {
                $edicion_cien = 'FECHA MÁXIMA DE RECEPCIÓN DE COMPROBANTE DE PAGO ' .$pedido['fecha_sin_despacho'].  ' CAMBIO ' .$fechasindespacho;
                $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_cien."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
            }
        }
        
        if ($fechadespacho != '') {
            if ($pedido['fecha_despacho'] != $fechadespacho) {
                $edicion_dosc = 'FECHA MÁXIMA DE APROBACIÓN DEL VISUAL ' .$pedido['fecha_despacho'].  ' CAMBIO ' .$fechadespacho;
                $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_dosc."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
            }
        }

        

        if ($dep_pago != $_POST['dep_pago']) {
            $edicion_nue = 'DEPARTAMENTO DE PAGO ' .$dep_pago. 'CAMBIO' .$_POST['dep_pago'];
            $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_nue."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
        }
        
        if ($aut['aut_cliente'] == '1') {
            if ($observacion_agente != $_POST['observacionagente']) {
                $edicion_nue = 'OBSERVACIONES AGENTE ' .$observacion_agente. 'CAMBIO' .$_POST['observacionagente'];
                $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_nue."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
                $aut = reg('autorizaciones_pedidos', 'num_pedido', $_POST['nped']);
                $conexion2->query("UPDATE autorizaciones_pedidos SET aut_cliente='0' WHERE num_pedido='".$_POST['nped']."';");
            }
        }else{
            if ($observacion_agente != $_POST['observacionagente']) {
                $edicion_nue = 'OBSERVACIONES AGENTE ' .$observacion_agente. 'CAMBIO' .$_POST['observacionagente'];
                $conexion2->query("INSERT INTO pedidos_historial VALUES (NULL, '".$pedido["id_pedido"]."','".$edicion_nue."', '".date("Y-m-d H:i:s")."', '".$_COOKIE["mkid"]."');");
                $aut = reg('autorizaciones_pedidos', 'num_pedido', $_POST['nped']);
                $conexion->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_servicio != '' AND numero_pedido='".$_POST['nped']."';");
                if($conexion->num_rows() != 0){
                    while($conexion->next_record()){
                        $conexion2->query("UPDATE autorizaciones_pedidos SET aut_artes='0' WHERE num_pedido='".$_POST['nped']."';");
                    }
                }
                $conexion2->query("UPDATE autorizaciones_pedidos SET aut_compras='0' WHERE num_pedido='".$_POST['nped']."';");
            }
        }
        
        $conexion2->query("UPDATE autorizaciones_pedidos SET apr_edicion='1' WHERE num_pedido='".$_POST['nped']."';");
        $conexion2->query("UPDATE pedidos SET nom_realiza_pedido = '".$_POST['realiza_pedido']."' ,nom_recibe_pedido = '".$_POST['NOMRECIBEPEDIDO']."',tel_contacto_directo = '".$_POST["telcontactodirecto"]. '|' .$_POST["celcontactodirecto"]."',dir_envio = '".$_POST["direnvio"]."/".$_POST["ciudad_enviar"]."',observaciones = '".$_POST["observas"]."',medio_pago  = '".$_POST["medio_pago"]."',forma_pago = '".$_POST["forma_pago"].'/'.$forma_de_pago[1]."',fecha_comprobante = '".$fecharecibidox."',fecha_despacho = '".str_replace("T", " ", $fechadespacho)."',fecha_sin_despacho = '".str_replace("T", " ", $fechasindespacho)."',observacion_agente = '".$_POST["observacionagente"]."',info_quien = '".$_POST["email_q"].'|'.$_POST["tel_q"]."' WHERE numero_pedido='".$_POST['nped']."';");

        if ($usuario['rol'] == '2' || $usuario['rol'] == '1') {
            if ($aut['aut_compras'] == '1' && $aut['aut_artes'] == '1' && $aut['aut_cliente'] == '1') {
                $conexion2->query("UPDATE autorizaciones_pedidos SET aut_edi_cli='1' WHERE num_pedido='".$_POST['nped']."';");
                //Correo del pedido que acaba de aprobar
                $m = 'm';
                enviar_mail(trim($usuario['email']), 'Syspromall', 'Se ha modificado tu pedido #' . $_POST['nped'], utf8_decode(daniel($_POST["email_q"],$_POST['nped'],$m)));
                enviar_mail('pruebaspromall@gmail.com', 'Syspromall', 'Se ha modificado tu pedido #' . $_POST['nped'], utf8_decode(daniel($_POST["email_q"],$_POST['nped'],$m)));
                enviar_mail('cartera@ag3.com.co', 'Syspromall', 'Se ha modificado tu pedido #' . $_POST['nped'], utf8_decode(daniel($_POST["email_q"],$_POST['nped'],$m)));
                enviar_mail($_POST["email_q"], 'Promall', 'Se ha modificado tu pedido #' . $_POST['nped'], utf8_decode(daniel($_POST["email_q"],$_POST['nped'],$m)));
                //Fin del correo que acaba de aprobar 
            }
        }else{

        }

        location('pedidos_html.php?correo='.$uquien[0].'&num_pedido='.$_POST['nped'].'&valor='.$pedido['nit_cc'].' ');
        exit();
        
    break;
    case 'agregar_vigencia':


    $conexion2->query("SELECT * FROM pedidos WHERE numero_pedido = '".$id_pedido."' ");
    $conexion2->next_record();
    $numero = $conexion2->f('numero_pedido');
    $html = '
      <!DOCTYPE html>
        <html>
        <head>
          <title></title>
          <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
          <style>
           body {
            margin:0;
            padding:0;
            font-family:sans-serif;
            background:#fbfbfb;
        }
        .card {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            width:300px;
            min-height:400px;
            background:#fff;
            box-shadow:0 20px 50px rgba(0,0,0,.1);
            border-radius:10px;
            transition:0.5s;
        }
        .card:hover {
            box-shadow:0 30px 70px rgba(0,0,0,.2);
        }
        .card .box {
            position:absolute;
            top:50%;
            left:0;
            transform:translateY(-50%);
            text-align:center;
            padding:20px;
            box-sizing:border-box;
            width:100%;
        }
        .card .box .img {
            width:80px;
            height:80px;
            margin:0 auto;
            border-radius:50%;
            overflow:hidden;
        }
        .card .box .img img {
            width:100%;
            height:100%;
        }
        .card .box h2 {
            font-size:20px;
            color:#262626;
            margin:20px auto;
        }
        .card .box h2 span {
            font-size:14px;
            background:#e91e63;
            color:#fff;
            display:inline-block;
            padding:4px 10px;
            border-radius:15px;
        }
        .card .box p {
            color:#262626;
        }
        .card .box span {
            display:inline-flex;
        }
        .card .box ul {
            margin:0;
            padding:0;
        }
        .card .box ul li {
            list-style:none;
            float:left;
        }
        .card .box ul li a {
            display:block;
            color:#aaa;
            margin:0 10px;
            font-size:20px;
            transition:0.5s;
            text-align:center;
        }
        .card .box ul li:hover a {
            color:#e91e63;
            transform:rotateY(360deg);
        } 
          </style>
          <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
          <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
          <!------ Include the above in your HEAD tag ---------->
          
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        </head>
        <body>
          <div class="card">
              <div class="box">
                  <div class="img">
                      <img src="https://www.promall.com.co/wp-content/uploads/fbrfg/apple-touch-icon-60x60.png">
                  </div>
                  <h2>Modificación fecha de vigencia del pedido<br><span>#'.$numero.'</span></h2>
                  <p>
                    Ha sido modificada la fecha de vigencia del pedido <span># '.$numero.' </span>, por favor ingresa y valida que la informacion registrada se encuentre disponible 
                  </p>
                  <span>
                    <ul>
                        <li><a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$numero.'/m/42/"><i class="fa fa-edit" aria-hidden="true"></i>Ver mas..</a></li>
                    </ul>
                </span>
              </div>
          </div>
        </body>
        </html>';
    $nuevafecha = strtotime('-3 day' , strtotime(date("Y-m-d")));
    $nuevafecha = date('Y-m-d', $nuevafecha);

    
    
    $agenteq = reg('usuarios', 'id_usuario', $conexion2->f('usuario_creador')); 
    enviar_mail(trim($agenteq["email"]), $agenteq["nombre"], 'Pedido # ' . $numero . ' Vigencia', utf8_decode($html));
    enviar_mail(trim('pruebaspromall@gmail.com'), $agenteq["nombre"], 'Pedido # ' . $numero . ' Vigencia', utf8_decode($html));

    $verfecha = strtotime('+5 day' , strtotime($nuevafecha));
    $verfecha = date('Y-m-d', $verfecha);

    $fecha_vpedido = strtotime('+5 day' , strtotime($conexion2->f("fecha_creacion")));
    $fecha_vpedido = date('Y-m-d', $fecha_vpedido);

    $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$conexion2->f("id_pedido")."','SE MODIFICO LA FECHA DE LA VIGENCIA DEL PEDIDO, INICIALMENTE SE ENCONTRABA LA VIGENCIA HASTA EL DIA ".$fecha_vpedido." Y EL DIA ".date("Y-m-d H:i:s")." SE AGREGARON 2 DIAS, FINALMENTE LA VIGENCIA SE ENCUENTRA HASTA EL DIA ".$verfecha." ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
    $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$conexion2->f("id_pedido")."','<b>Se reinician las validaciones de cada porceso por incremento en la fecha de la vigencia</b>','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");

    
    
    $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado = 'Generado' WHERE numero_pedido = '".$id_pedido."' ");
    $conexion->query("DELETE FROM autorizaciones_pedidos WHERE num_pedido = '".$id_pedido."' ");
    /*Valida si no hay servicio para la aprobacion aitomatica de artes*/
    $conexion2->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_servicio != '' AND numero_pedido='".$id_pedido."';");
    $conexion2->next_record();
    if($conexion2->num_rows() == 0){
        $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$id_pedido."', '0', '1', '0', '0','1','0','0','0','0');");
    }else{
        $foo = $conexion2->f("codigo_servicio");
        if (strpos($foo, 'SER') !== false) {
            $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$id_pedido."', '0', '1', '0', '0','1','0','0','0','0');");
        }else{
            /*Valida si no hay servicio para la aprobacion aitomatica de artes*/
            $conexion3->query("SELECT * FROM registros_pedidos INNER JOIN registros_cotizacion ON id_registro_cotizacion=reg_cotizacion INNER JOIN pedidos ON id_pedido=id_pedido_reg AND codigo_producto != '' AND numero_pedido='".$id_pedido."';");
            $conexion3->next_record();
            if($conexion3->num_rows() == 0){
                $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$id_pedido."', '1', '0', '0', '0','0','0','0','0','0');");
            }
            /*************************FIN**************************/
            $conexion->query("INSERT INTO autorizaciones_pedidos VALUES (NULL, '".$id_pedido."', '0', '0', '0', '0','0','0','0','0','0');");
        }
        $conexion->query("SELECT * FROM usuarios WHERE rol=(SELECT id_rol_usuario FROM rol_usuario WHERE nombre_rol LIKE '%Artes%');");
        $conexion->next_record();
        enviar_mail(trim($conexion->f("email")), 'Syspromall','Pedido # ' . $numero . ' Vigencia','Cordial Saludo Dpto de Artes<br>'.utf8_decode($html));
        enviar_mail(trim('pruebaspromall@gmail.com'), 'Syspromall','Pedido # ' . $numero . ' Vigencia','Cordial Saludo Dpto de Artes<br>'.utf8_decode($html));
    }

    $conexion->query("SELECT * FROM usuarios WHERE rol=(SELECT id_rol_usuario FROM rol_usuario WHERE nombre_rol LIKE '%Compras%');");
    while($conexion->next_record()){
        enviar_mail(trim($conexion->f("email")), 'Syspromall','Pedido # ' . $numero . ' Vigencia','Cordial Saludo Dpto de Compras<br>'.utf8_decode($html));
        enviar_mail(trim('pruebaspromall@gmail.com'), 'Syspromall','Pedido # ' . $numero . ' Vigencia','Cordial Saludo Dpto de Compras<br>'.utf8_decode($html));
      }
    /*************************FIN**************************/
    
    redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$id_pedido."'/");



    break;
    case 'agregar_vigencia_s':

    $nuevafecha = strtotime('-3 day' , strtotime(date("Y-m-d")));
    $nuevafecha = date('Y-m-d', $nuevafecha);
    
    $conexion2->query("SELECT * FROM pedidos WHERE numero_pedido = '".$id_pedido."' ");
    $conexion2->next_record();

    $usuario_c = reg('usuarios','id_usuario',$conexion2->f("usuario_creador"));

    $verfecha = strtotime('+5 day' , strtotime($nuevafecha));
    $verfecha = date('Y-m-d', $verfecha);

    $html = '
      <!DOCTYPE html>
        <html>
        <head>
          <title></title>
          <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
          <style>
           body {
            margin:0;
            padding:0;
            font-family:sans-serif;
            background:#fbfbfb;
        }
        .card {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            width:300px;
            min-height:400px;
            background:#fff;
            box-shadow:0 20px 50px rgba(0,0,0,.1);
            border-radius:10px;
            transition:0.5s;
        }
        .card:hover {
            box-shadow:0 30px 70px rgba(0,0,0,.2);
        }
        .card .box {
            position:absolute;
            top:50%;
            left:0;
            transform:translateY(-50%);
            text-align:center;
            padding:20px;
            box-sizing:border-box;
            width:100%;
        }
        .card .box .img {
            width:80px;
            height:80px;
            margin:0 auto;
            border-radius:50%;
            overflow:hidden;
        }
        .card .box .img img {
            width:100%;
            height:100%;
        }
        .card .box h2 {
            font-size:20px;
            color:#262626;
            margin:20px auto;
        }
        .card .box h2 span {
            font-size:14px;
            background:#e91e63;
            color:#fff;
            display:inline-block;
            padding:4px 10px;
            border-radius:15px;
        }
        .card .box p {
            color:#262626;
        }
        .card .box span {
            display:inline-flex;
        }
        .card .box ul {
            margin:0;
            padding:0;
        }
        .card .box ul li {
            list-style:none;
            float:left;
        }
        .card .box ul li a {
            display:block;
            color:#aaa;
            margin:0 10px;
            font-size:20px;
            transition:0.5s;
            text-align:center;
        }
        .card .box ul li:hover a {
            color:#e91e63;
            transform:rotateY(360deg);
        } 
          </style>
          <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
          <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
          <!------ Include the above in your HEAD tag ---------->
          
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        </head>
        <body>
          <div class="card">
              <div class="box">
                  <div class="img">
                      <img src="https://www.promall.com.co/wp-content/uploads/fbrfg/apple-touch-icon-60x60.png">
                  </div>
                  <h2>Modificación fecha de vigencia del pedido<br><span>#'.$id_pedido.'</span></h2>
                  <p>
                    Ha sido modificada la fecha de vigencia del pedido <span># '.$id_pedido.' </span>, por favor ingresa y valida que la informacion registrada se encuentre disponible 
                  </p>
                  <span>
                    <ul>
                        <li><a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$id_pedido.'/m/42/"><i class="fa fa-edit" aria-hidden="true"></i>Ver mas..</a></li>
                    </ul>
                </span>
              </div>
          </div>
        </body>
        </html>';

    $fecha_vpedido = strtotime('+5 day' , strtotime($conexion2->f("fecha_cotizacion")));
    $fecha_vpedido = date('Y-m-d', $fecha_vpedido);

    $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$conexion2->f("id_pedido")."','SE MODIFICO LA FECHA DE LA VIGENCIA DEL PEDIDO, INICIALMENTE SE ENCONTRABA LA VIGENCIA HASTA EL DIA ".$fecha_vpedido." Y EL DIA ".date("Y-m-d H:i:s")." SE AGREGARON 2 DIAS, FINALMENTE LA VIGENCIA SE ENCUENTRA HASTA EL DIA ".$verfecha." ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");

    $conexion3->query("SELECT * FROM autorizaciones_pedidos WHERE num_pedido = '".$id_pedido."' ");
    while ($conexion3->next_record()) {
        
        $aut_compras = $conexion3->f("aut_compras");
        $aut_artes = $conexion3->f("aut_artes");
        $aut_cliente = $conexion3->f("aut_cliente");
        $aut_cartera = $conexion3->f("aut_cartera");
        $aut_visual = $conexion3->f("aut_visual");
        $aut_edicion = $conexion3->f("aut_edicion");
        $aut_guardado = $conexion3->f("aut_guardado");

        if ($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 1 && $aut_cartera == 1 && $aut_visual == 1 && $aut_guardado == 1) {
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Autorizado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 1 && $aut_cartera == 1 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 1 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 1 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 0 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 0 && $aut_artes == 1 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 1 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 0 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 0){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Creado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 0 && $aut_artes == 1 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 1 && $aut_guardado == 0){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Creado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 0 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 0){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Creado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 2 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 1 && $aut_artes == 1 && $aut_cliente == 1 && $aut_cartera == 0 && $aut_visual == 1 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Aprobado' WHERE numero_pedido = '".$id_pedido."' ");
        }elseif($aut_compras == 0 && $aut_artes == 1 && $aut_cliente == 0 && $aut_cartera == 0 && $aut_visual == 0 && $aut_guardado == 1){
            $conexion->query("UPDATE pedidos SET fecha_cotizacion = '".$nuevafecha."',estado='Generado' WHERE numero_pedido = '".$id_pedido."' ");
        }
    }

    enviar_mail($usuario_c["email"], $usuario_c["nombre"], 'Pedido # ' . $numero . ' Vigencia', utf8_decode($html));
    enviar_mail('pruebaspromall@gmail.com', $usuario_c["nombre"], 'Pedido # ' . $numero . ' Vigencia', utf8_decode($html));
    
    
    redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$id_pedido."'/");

    break;
    case 'finalizar':
        
        $pedido = reg("pedidos","numero_pedido",$id_pedido);

        $conexion->query("INSERT INTO pedidos_fin VALUES ('','".$id_pedido."','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."'); ");
        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$pedido["id_pedido"]."','Pedido Terminado','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
        
        redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$id_pedido."'/");

    break;
    case 'alerta_ped':
        
        $p = reg('pedidos','numero_pedido',$num_ped_reg);
        $u = reg('usuarios','id_usuario',$p['usuario_creador']);

        $html = 'Se ha generado una alerta para el pedido '.$num_ped_reg.', por favor revisa el requerimiento a continuación para realizar el procedimiento que esta conlleve.<br><br>Observaciones: '.nl2br($ob).'<br><br>
                <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/alertas_pedidos/id_pedido/'.$num_ped_reg.'/cadena/'.$cadena.'/">
                  INGRESA AQUI
                </a> ';


        $aq = '';

        for ($i=0; $i < count($aquien) ; $i++) { 
            $aq .= $aquien[$i].' ';
            enviar_mail(trim($aquien[$i]), $conexion->f("nombre"), ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//CArtera
        }

        $conexion->query("INSERT INTO alertas_pedidos VALUES (NULL,'".$num_ped_reg."','".$ob."','".date("Y-m-d H:i:s")."','".$aq."','".$_COOKIE["mkid"]."')");

        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$p["id_pedido"]."','SE GENERA AVISO A ".$aq." DE REVISION URGENTE DEL PEDIDO ".$ob." ','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
        
        enviar_mail(trim($u['email']), $u["nombre"], ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//agente de ventas

        enviar_mail('pruebaspromall@gmail.com', 'Syspromall', ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//agente de ventas

        redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$num_ped_reg."'/m/45/");
        
        
    break;
    case 'alerta_car':
        $p = reg('pedidos','numero_pedido',$num_ped_reg);
        $u = reg('usuarios','id_usuario',$p['usuario_creador']);

        $conexion->query("INSERT INTO alerta_cartera VALUES (NULL,'".$num_ped_reg."','".$ob."','".date("Y-m-d H:i:s")."')");
        $conexion->query("INSERT INTO pedidos_historial VALUES (NULL,'".$p["id_pedido"]."','SE GENERA AVISO A CARTERA DE REDACTAR DOCUMENTOS PENDIENTES ASOCIADOS AL PEDIDO, <b>".$ob."</b>','".date("Y-m-d H:i:s")."','".$_COOKIE['mkid']."') ");
        
        $html = 'El agente '.$u["nombre"].' ha generado una alerta para el pedido '.$num_ped_reg.', por favor revisa el requerimiento a continuación para redactar los documentos que el procedimiento conlleve.<br>Observaciones: '.$ob.' <br><br>
                <a href="https://sys.promall.com.co/promall/area/admin3/pedidos/filtrar/criterio_busqueda/Serial/serial/'.$num_ped_reg.'/m/42/">
                  INGRESA AQUI
                </a> ';
        enviar_mail(trim($u['email']), $u["nombre"], ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//agente de ventas
        enviar_mail('cartera@ag3.com.co', $u["nombre"], ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//agente de ventas


        enviar_mail('pruebaspromall@gmail.com', 'Syspromall', ' URGENTE - ALERTA GENERADA PEDIDO # ' . $num_ped_reg, utf8_decode($html));//agente de ventas
        redirigir("admin3/pedidos/filtrar/cadena/SELECT * FROM pedidos WHERE numero_pedido='".$num_ped_reg."'/m/45/");
    break;
    case 'alertas_pedidos':
        $templete->define(array('principal' => 'alertas_pedidos.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );

        $num = $id_pedido;
        $regp = reg('pedidos','numero_pedido',$num);

        $templete->assign('CADENA',$cadena);
        $templete->assign('NPEDIDO',$num);

        
        $email = explode("|", $regp['info_quien']);

        $templete->assign('EMAIL', $email[0]);
        $templete->assign('ID_PEDIDO', $num);
        $templete->assign('NITCC', $regp['nit_cc']);

        $conexion2->query("SELECT numero_pedido_reg,observaciones FROM alertas_pedidos WHERE numero_pedido_reg = '".$num."' ");
        if ($conexion2->num_rows() > 0) {
            while ($conexion2->next_record()){
                $count = $conexion2->num_rows();
                $templete->assign('COUNTS', $count);
                $templete->assign('FECHAEC', alerta_ped($conexion2->f("numero_pedido_reg")));
            }
        }

        $conexion->query("SELECT id_usuario,email,usuario FROM usuarios");
        if ($conexion->num_rows() > 0) {
            $templete->define_dynamic('BCHECK', 'principal');
            while ($conexion->next_record()) {
                $templete->assign('CHECK', '<input type="checkbox" name="aquien[]" id="chice" value="'.$conexion->f("email").'" />'.$conexion->f("usuario").'<br>');
                $templete->parse('LISTABCHECK', '.BCHECK');
            }
        }

    break;
    case 'historico':
        $templete->define(array('principal' => 'historico.html'));
        $templete->assign('MODULOS',modulos());
        $templete->assign('AVATAR',  avatar($_COOKIE["mkid"]) );

        $conexion->query("SELECT * FROM pedidos GROUP BY numero_pedido");
        $templete->define_dynamic('BHIS', 'principal');
        while ($conexion->next_record()) {
            $f = reg('facturacion','f_ordenc',$conexion->f('numero_pedido'));
            $r = reg('remisiones','noremision',$conexion->f('numero_pedido'));
            $correo = explode("|", $conexion->f('info_quien'));
            $templete->assign('PED', '<a href="https://sys.promall.com.co/pedidos_html.php?correo='.$correo[0].'&num_pedido='.$conexion->f("numero_pedido").'&valor='.$conexion->f("nit_cc").'" target="_blank"><i class="fa fa-print"></i>'.$conexion->f("numero_pedido").'</a>');
            $templete->assign('FAC', '<a href="https://sys.promall.com.co/promall/area/includes/tcpdf/examples/facturacion_pdf.php?nserial='.$f['id_factura'].'&external=interno" target="_blank"><i class="fa fa-print"></i>'.$f['consecutivo_inh'].'</a>');
            $templete->assign('REM', '<a href="https://sys.promall.com.co/promall/area/includes/tcpdf/examples/remision.php?id='.$r['id_remision'].'" target="_blank"><i class="fa fa-print"></i>'.$r['noremision'].'</a>');
            $templete->parse('LISTABHIS', '.BHIS');
        }
    break;
    case 'downloads':
        error_reporting(0);
        header("Content-type: application/octet-stream");

        //header( "Content-type: application/vnd.ms-excel; charset=UTF-8" );
        //header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: filename=PFRInforme.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo utf8_decode($_POST['datos_a_enviars']);
        exit();
    break;

    
}
if($m)
{
    $templete->assign('MENSAJE',mensaje($m));   
    $templete->assign('VERMENSAJE','block');
}
else
{
    $templete->assign('MENSAJE',$m);
    $templete->assign('VERMENSAJE','none');
}
$templete->parse('PRINCIPAL', 'principal');
$templete->FastPrint('PRINCIPAL');
?>