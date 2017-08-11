<?php
	include './conexion/conexion2.php';
	include './conexion/conexion.php';
	include './clases/clsUsuario.php';
	ob_start();
	$conexion = new Conexion();
	if (isset($_REQUEST['txtUsuario']) && isset($_REQUEST['txtPwd'])) 
	{
		$objUsuario = new clsUsuario();
		$fran = NULL; $rol = NULL;
		$fran = $objUsuario->devIdFranquicia($_REQUEST['txtUsuario'],$_REQUEST['txtPwd']);
		$rol = $objUsuario->devIdRol($_REQUEST['txtUsuario'],$_REQUEST['txtPwd']);
		if ($fran != NULL) 
		{
			session_start();
			$_SESSION['miusuario'] = $_REQUEST['txtUsuario'];
			$_SESSION['mifran']  = $fran;
			$_SESSION['mirol'] = $rol;
			if ($_SESSION['mifran']=='Oficina') 
			{
				header('location:./indexAdmin.php');
				exit();			
			}
			else
			{
				header('location:./carga.php');
				exit();
			}
		}
	}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="shortcut icon" href="./ico.png">
	<title>Carga de reportes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
</head>
<body>
	<div class="container">	
		<form action="index.php" method="post">
			<div class="row">
				<div class="col s2"></div>
				<div class="col s8">
					<div class="card z-depth-5">
						<div class="card-content green-text">
							<p>Ingresa tu información</p>
							<br>
							<div class="input-field ">
								<i class="material-icons prefix">account_circle</i>
								<input id="txtUsuario" name="txtUsuario" type="text" class="validate" required />
								<label for="txtUsuario">USUARIO</label>
							</div>
							<div class="input-field ">
								<i class="material-icons prefix">lock</i>
								<input id="txtPwd" name="txtPwd" type="password" class="validate" required />
								<label for="txtPwd">CONTRASEÑA</label>
							</div>	
						</div>
						<div class="card-action">
							<button class="btn waves-effect waves-light" type="submit" name="action">Enviar
								<i class="material-icons right">send</i>
							</button>
						</div>
					</div>
				</div>
				<div class="col s2"></div>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
	<script type="text/javascript">
		$(document).ready(init);
	  	function init() 
	  	{
	  		$('select').material_select();
	  	}
	</script>
</body>
</html>
<?php ob_end_flush(); ?>