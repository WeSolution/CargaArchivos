<?php 
    include './clases/controller/cargaReportesSucursalController.php';
    include './clases/controller/movCortesController.php';
    include './clases/model/cargaArchivoModel.php';
    include './clases/model/phpExcelCargaModel.php';
    ob_start();
    session_start();
    if(!isset($_SESSION['mifran'])){ header('location:./index.php'); exit();}
    $fran = $_SESSION['mifran'];
    date_default_timezone_set("America/Mexico_City"); 
    $hoy = date("Y-m-d");
    $sel_dia = date(date("Y-m-d"));
    $cargaReportes = new CargaReportesSucursal();
    $fechaActual = date("d/m/Y");
    $fechaConsulta = date("Y-m-d");
    $objExcel = new MiPhpExcel();
    $objExcel->setPropiedad('rutaClasesPhpExcel','./PHPExcel/Classes/PHPExcel/IOFactory.php');
    $objExcel->setPropiedad('rutaDeArchivoExcel','./xls/empleados.xls');
    $objExcel->setPropiedad('urlNueva','./xls/empleados.xls');
    if (isset($_GET['accion'])) 
    {
        if($_GET['accion']=='salir')
        {
            unset($_SESSION);
            session_destroy();
            header('location:./index.php');
            exit();
        }
    }
    if (isset($_POST['btnSubirArchivos'])) 
    {
        if(isset($_FILES))
        {
            $files = $_FILES['userfile'];
            if($files['name'][0] != '')
                $ne = count($files['name']);
            
        }    
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Sucursal
    </title>
    <link rel="shortcut icon" href="./ico.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
</head>
<body>
    
    <div class="container">
        <nav style="padding-left: 15px;">
          <div class="nav-wrapper">
            <a href="#!" class="brand-logo"><?= $fran ?></a>
            <ul class="right hide-on-med-and-down">
              <li><a href="./carga.php?accion=salir">Salir</a></li>
            </ul>
          </div>
        </nav>
    </div>
    <div class="container">
        <div class="row">
            <div class="col s12">
              <div class="card">
                <div class="card-content ">
                  <span class="card-title">Cargar Archivos</span>
                    <div class="row my-row">
                        <form  enctype="multipart/form-data" action="carga.php" method="POST" id="formSubirArchivos">
                            <input type="hidden" name="accion" value="validarReportes" />
                            <div class="">
                                <label for="txtFile"  class="btn waves-effect waves-light btn-large orange" data-toggle="tooltip" data-placement="bottom" title="Seleccionar archvios">
                                    <span class="glyphicon glyphicon-import" aria-hidden="true"></span> SELECCIONAR ARCHIVOS
                                </label>
                                <input style="display:none" id="txtFile" class="form-control" name="userfile[]" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" multiple/>
                                <input type="hidden" value="btnSubirArchivos" name="btnSubirArchivos" id="btnSubirArchivos" />
                                <a href="#" id="linkCargaFile" class="btn waves-effect waves-light btn-large " type="submit" data-toggle="tooltip" data-placement="bottom" title="Cargar reportes">CARGAR ARCHIVOS</a>
                            </div>
                        </form>
                    </div>
                    <div class="row my-row">
                        <div class="col-lg-12">
                            <table class="bordered striped card-panel hoverable responsive-table">
                                <thead>
                                    <tr>
                                        <th class="warning center" >
                                            Tipo de reporte
                                        </th>
                                        <th class="warning center" >
                                            Alhajas
                                        </th>
                                        <th class="warning center" >
                                            Varios
                                        </th>
                                        <th class="warning center" >
                                            Plata
                                        </th>
                                        <th class="warning center" >
                                            Relojes
                                        </th>
                                        <th class="warning center" >
                                            Autos
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if (isset($_POST['btnSubirArchivos'])) 
                                        {   
                                            
                                            if(isset($_FILES))
                                            {
                                                if($files['name'][0]!="") 
                                                {
                                                    $html = "";
                                                    $arrArchivosGuardados = array();
                                                    $arrTitulos = array
                                                    (
                                                        'OPERACION POR PARTIDA',
                                                        'OPERACION POR CONTRATO',
                                                        'RESUMEN DE OPERACIONES',
                                                        'GRACIA',
                                                        'OPERACION HORARIA',
                                                        'EN VENTA',
                                                        'EN VENTA REAL TOTAL',
                                                        'REPORTE DE PRESTAMOS POR PERIODO',
                                                        'REPORTE DE VENTAS POR PERIODO',
                                                        'REPORTE DE PASE A VENTA',
                                                        'REPORTE DE RENOVACIONES POR PERIODO',
                                                        'RENOVACIONES EXTEMPORANEAS POR PERIODO',
                                                        'REPORTE DE LIQUIDACIONES POR PERIODO',
                                                        'REPORTE DE LIQUIDACIONES EXTEMPORANEAS POR PERIODO'
                                                        //'REPORTE PRENDARIO DE REMANENTES POR PAGAR'
                                                    );
                                                    $arrTipoReporte = array
                                                    (
                                                        'OPERACION',
                                                        'OPERACION',
                                                        'RESUMEN DE OPERACIONES',
                                                        'GRACIA',
                                                        'OPERACION HORARIA',
                                                        'VENTA',
                                                        'VENTA',
                                                        'REPORTE DE PRESTAMOS POR PERIODO',
                                                        'REPORTE DE VENTAS POR PERIODO',
                                                        'REPORTE DE PASE A VENTA POR PARTIDA',
                                                        'REPORTE DE RENOVACIONES POR PERIODO',
                                                        'REPORTE DE RENOVACIONES EXTEMPORANEAS POR PERIODO',
                                                        'REPORTE DE LIQUIDACIONES POR PERIODO',
                                                        'REPORTE DE LIQUIDACIONES EXTEMPORANEAS POR PERIODO'
                                                        //'REPORTE PRENDARIO DE REMANENTES POR PAGAR'
                                                    );
                                                    $arrTipoReporte2 = array
                                                    (
                                                        'REPORTE DE EXISTENCIA PREN. EN OPERACION POR PARTIDA (Historico)',
                                                        'REPORTE DE EXISTENCIA PRENDARIA EN OPERACION POR CONTRATO (Historico)',
                                                        'N/A',
                                                        'EXISTENCIA PRENDARIA EN PERIODO DE GRACIA (Historico)',
                                                        'REPORTE DE OPERACION HORARIA CORRESPONDIENTE AL PERIODO PARA EL RAMO',
                                                        'EXISTENCIA PRENDARIA EN VENTA (Historico)',
                                                        'EXISTENCIA PRENDARIA EN VENTA (Real Total)',
                                                        'N/A',
                                                        'N/A',
                                                        'N/A',
                                                        'N/A',
                                                        'N/A',
                                                        'N/A',
                                                        'N/A'
                                                        //'N/A'
                                                    );
                                                    $nReportes = count($arrTipoReporte);
                                                    $band = TRUE;
                                                    $band2 = FALSE;
                                                    $resp = '';
                                                    $archivosFinales = array();
                                                    for($j = 0; $j < $nReportes;$j++)
                                                    {
                                                        $html.= '<tr><td><strong><small>' .  $arrTitulos[$j] .'</small></strong></td>';
                                      
                                                        $respRamos = array();
                                                        $arrRamos = array('Alhajas','Varios','Plata','Relojes','Autos');
                                                        $nRamos = count($arrRamos);
                                                        for ($i=0; $i < $ne; $i++) 
                                                        { 
                                                            $objExcel->setPropiedad('name',$files['name'][$i]);
                                                            $objExcel->setPropiedad('tmp_name',$files['tmp_name'][$i]);
                                                            $objExcel->cargaInformacion();
                                                            $objExcel->init();
                                                            $excel = $objExcel->getPropiedad('objExcel');
                                                            if(strpos($excel->getActiveSheet()->getCell('A4')->getValue(),$arrTipoReporte[$j])!==false) 
                                                            {
                                                                if($excel->getActiveSheet()->getCell('A4')->getValue() == $arrTipoReporte2[$j])
                                                                {

                                                                    for ($k=0; $k < $nRamos; $k++) 
                                                                    { 
                                                                        
                                                                        if(strpos($excel->getActiveSheet()->getCell('A5')->getValue(),$arrRamos[$k])!==false) 
                                                                        {
                                                                            #aqui se deben cargar los archivos
                                                                            $respRamos[$arrTipoReporte[$j]][$arrRamos[$k]] = 'CARGADO';
                                                                            $arrArchivosGuardados[]  = array('ramoPrendario'=>$arrRamos[$k],'name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i]);
                                                                        }

                                                                    }
                                                                }
                                                                else 
                                                                {

                                                                    if(
                                                                        'RESUMEN DE OPERACIONES' == $arrTipoReporte[$j] 
                                                                        || 'REPORTE DE PRESTAMOS POR PERIODO' == $arrTipoReporte[$j]
                                                                        || 'REPORTE DE VENTAS POR PERIODO' == $arrTipoReporte[$j]
                                                                        || 'REPORTE DE PASE A VENTA POR PARTIDA' == $arrTipoReporte[$j]
                                                                        || 'REPORTE DE RENOVACIONES POR PERIODO' == $arrTipoReporte[$j]
                                                                        || 'REPORTE DE LIQUIDACIONES POR PERIODO' == $arrTipoReporte[$j]
                                                                    )
                                                                        //|| 'REPORTE PRENDARIO DE REMANENTES POR PAGAR' == $arrTipoReporte[$j])
                                                                    {

                                                                        for ($k=0; $k < $nRamos; $k++) 
                                                                        { 
                                                                            if(strpos($excel->getActiveSheet()->getCell('A4')->getValue(),$arrRamos[$k])!==false) 
                                                                            {
                                                                                #aqui se deben cargar los archivos
                                                                                $respRamos[$arrTipoReporte[$j]][$arrRamos[$k]] = 'CARGADO';
                                                                                $arrArchivosGuardados[]  = array('ramoPrendario'=>$arrRamos[$k],'name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i]);
                                                                            }

                                                                        }
                                                                    }
                                                                    if(
                                                                        'REPORTE DE RENOVACIONES EXTEMPORANEAS POR PERIODO' == $arrTipoReporte[$j]
                                                                        || 'REPORTE DE LIQUIDACIONES EXTEMPORANEAS POR PERIODO' == $arrTipoReporte[$j]
                                                                    )
                                                                    {
                                                                       for ($k=0; $k < $nRamos; $k++) 
                                                                        { 
                                                                            if(strpos($excel->getActiveSheet()->getCell('A5')->getValue(),$arrRamos[$k])!==false) 
                                                                            {
                                                                                #aqui se deben cargar los archivos
                                                                                $respRamos[$arrTipoReporte[$j]][$arrRamos[$k]] = 'CARGADO';
                                                                                $arrArchivosGuardados[]  = array('ramoPrendario'=>$arrRamos[$k],'name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i]);
                                                                            }

                                                                        } 
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                        for ($i=0; $i < $nRamos; $i++) 
                                                        {
                                                            if(isset($respRamos[$arrTipoReporte[$j]][$arrRamos[$i]]))
                                                            {
                                                                $html.= '<td class="center green-text"><i class="material-icons">done_all</i>' . $respRamos[$arrTipoReporte[$j]][$arrRamos[$i]] .'</td>'; 
                                                                $band = TRUE;
                                                            } 
                                                            else
                                                            { 
                                                                $band = FALSE;
                                                                $html.= '<td class="center red-text"><i class="material-icons">close</i> NO CARGADO</td>';
                                                            }
                                                            if(!$band)
                                                            {
                                                                $band2 = TRUE;
                                                            }
                                                        }
                                                        $html.= '</tr>';
                                                    }
                                                    if($band2)
                                                    {
                                                        $html.= '<div class="alert alert-danger">';
                                                        $html.= '<p class="red-text center"><strong>Favor de validar, ya que uno o varios de los ramos no fueron cargados en algún reporte.</strong></p>';
                                                        $html.= '</div>';
                                                    }
                                                    else
                                                    {

                                                        $html.= '<div class="alert alert-success">';
                                                        $html.= '<p class="green-text center"><strong>Todos los reportes se cargaron exitosamente.</strong></p>';
                                                        $html.= '</div>';
                                                    }

                                                    $_SESSION['html'] = $html;

                                                     $forbidden = array(" ");
                                                            for($i = 0; $i < count($forbidden); $i++)
                                                                $fran2 = str_replace($forbidden[$i], "_", $fran);
                                                    for ($i=0; $i < count($arrArchivosGuardados); $i++) 
                                                    { 
                                                       

                                                        $cargaReportes->setPropiedad('dirname',$fran2);
                                                        $r = $cargaReportes->creaCarpeta();
                                                        if($r['resp'] == 1 || $r['resp'] == -8)
                                                        {
                                                            $cargaReportes->getPropiedad('cargaArchivo')->setPropiedad('name',$arrArchivosGuardados[$i]['name']);
                                                            $cargaReportes->getPropiedad('cargaArchivo')->setPropiedad('tmp_name',$arrArchivosGuardados[$i]['tmp_name']);
                                                            $cargaReportes->getPropiedad('cargaArchivo')->setPropiedad('dirname',$fran2.'/'.$fechaConsulta);
                                                            $cargaReportes->getPropiedad('cargaArchivo')->setPropiedad('ruta',$fran2.'/'.$fechaConsulta);
                                                            $cargaReportes->getPropiedad('cargaArchivo')->cargarDeArchivosUnitaria();
                                                        } 
                                                    }
                                                    
                                                    header('location:carga.php?status=200');

                                                }#if
                                            }    
                                        }
                                        
                                       if(isset($_SESSION['html'])) { echo $_SESSION['html']; }
                                       if (isset($_GET['status']))
                                       {
                                            if ($_GET['status']==200) 
                                            {
                                                echo "<script>Materialize.toast('SE REALIZÓ LA CARGA SOLICITADA', 4000) </script>";
                                            }
                                       } 
                                       
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>    
              </div>
            </div>
        </div>
    </div>
    <footer class="page-footer">
      <div class="container">
        <div class="row">
          <div class="col l6 s12">
            <h5 class="white-text">Carga de archivos</h5>
            <p class="grey-text text-lighten-4">
                Puedes cargar tus archivos, en cualquier momento. 
                <br> Si tienes alguna duda comunicate al área de sistemas.
            </p>
          </div>
          
        </div>
      </div>
      <div class="footer-copyright">
        <div class="container">
        <!--<small>© 2017 Ing. Oscar Bonilla Rodríguez </small>-->
        <a class="grey-text text-lighten-4 right" href="http://www.prendamexpuebla.com.mx">www.prendamexpuebla.com.mx</a>
        </div>
      </div>
    </footer>

    <script type="text/javascript">
        $(document).ready(init);
        function init()
        {
            
            $(".dropdown-button").dropdown();
            enviarDatosDeformulario();
        }
        
        function enviarDatosDeformulario()
        {
            $('#linkCargaFile').click(btnSubirArchivosFuncion);
        }
        function btnSubirArchivosFuncion(e)
        {
            e.preventDefault();
            
           $('#formSubirArchivos').submit();
        }
    </script>
</body>
</html>
<?php ob_end_flush(); ?>