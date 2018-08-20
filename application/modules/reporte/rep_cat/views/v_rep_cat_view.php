<?php
	$title = "Constancia de Autorizacion Temporal";
	$controller = "rep_cat";
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$title?></title>
	<meta charset="UTF-8">
	<meta name="report" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<script src="../../jquery-ui-1.12.1/external/jquery/jquery.js" type="text/javascript"></script>
	<link href="../../jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="../../jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('tools/css/rpt.css')?>">
	<style>
	.report-document-table {
		border-collapse: separate;
		border-spacing: 2px;
	}	
	.report-document-table tr th {
		background-color: #eee;
		color: black;
		text-align: left;
		padding: 3px 3px 3px 3px;
	}
	
	</style>
</head>
<body>
<?php
	
?>
	<div class="">
		<span class="report-title">Reporte <?=$title?></span>&nbsp;&nbsp;&nbsp;
		<button id="back_bt" class="hidable">Volver</button>		
		<script>
			$("#back_bt")
			.button()
			.click( ( event, ui ) => {
				history.back();
			});
		</script>
	</div>
	<div>
	<table class="report-document-table">
		<tr>
			<th>A&ntilde;o y Numero:</th>
			<td>
<?php  echo $doc->cat_anio.' - '.str_pad($doc->cat_numero, 4, '0', STR_PAD_LEFT); ?>
			</td>
		</tr>
		<tr>
			<th>Nombres y Apellidos:</th>
			<td><?=$doc->contribuyente_nombres.' '.$doc->contribuyente_apellidos?></td>
		</tr>
		<tr>
			<th><?=$doc->tipo_doc_identidad_desc?>:</th>
			<td><?=$doc->contribuyente_numero_doc?></td>
		</tr>
		<tr>
			<th>Fecha:</th>
			<td><?=$doc->cat_fecha?></td>
		</tr>
		<tr>
			<th>Descripcion:</th>
			<td><?=$doc->cat_desc?></td>
		</tr>
		<tr>
			<th>Fecha de Inicio:</th>
			<td><?=$doc->cat_fecha_inicio?></td>
		</tr>
		<tr>
			<th>Fecha de Termino:</th>
			<td><?=$doc->cat_fecha_fin?></td>
		</tr>
		<tr>
			<th>Ruta:</th>
			<td><?=$doc->cat_ruta?></td>
		</tr>
		<tr>
			<th>Estado:</th>
			<td><?=$doc->estado_doc_desc?></td>
		</tr>
		<tr>
			<th>Estado Constancia:</th>
			<td><?=$doc->estado?></td>
		</tr>
	</table>
	</div>
	<br>
	<div style="">
		<div>Vehiculos y conductores:</div>
		<table class="report-table" style="" width="auto">
		<thead>
			<tr>
				<th >#</th>
				<th >Categoria</th>
				<th >Marca</th>
				<th >Modeloa</th>
				<th >Color</th>
				<th >Placa</th>
				<th >Nro. Tarjeta Propiedad</th>
				<th >Conductor</th>
				<th >DNI</th>
				<th >Nro. Licencia</th>
			</tr>
		</thead>
	<?php
		$nr = 0;
		foreach($vehiculo_list as $r) {
			$nr++;
	?>
			<tr>
				<td title="<?=$r->cat_vehiculo_id?>"><?=$nr?></td>
				<td><?=$r->cat_vehiculo_categoria?></td>
				<td><?=$r->cat_vehiculo_marca?></td>
				<td><?=$r->cat_vehiculo_modelo?></td>
				<td><?=$r->cat_vehiculo_color?></td>
				<td><?=$r->cat_vehiculo_placa?></td>
				<td><?=$r->cat_vehiculo_ntp?></td>
				<td><?=$r->cat_vehiculo_conductor_nomape?></td>
				<td><?=$r->cat_vehiculo_conductor_dni?></td>
				<td><?=$r->cat_vehiculo_conductor_nlc?></td>
			</tr>
	<?php
		}
	?>
		</table>
	</div>
	<br/>
	<div style="">
		<div>Documentos y requisitos adjuntados:</div>
		<table class="report-table" style="" width="auto">
		<thead>
			<tr>
				<th >#</th>
				<th >Descripcion</th>
				<th >Fecha</th>
				<th >Numero</th>
				<th ></th>
			</tr>
		</thead>
	<?php
		$nr = 0;
		foreach($doc_requisito_list as $r) {
			$nr++;
	?>
			<tr>
				<td title="<?=$r->doc_requisito_id?>"><?=$nr?></td>
				<td><?=$r->tipo_doc_requisito_desc?></td>
				<td><?=$r->doc_requisito_fecha?></td>
				<td><?=$r->doc_requisito_numero?></td>
				<td>
	<?php
			$filename = $r->doc_requisito_pdf;
			$path = "dbfiles/public.doc_requisito/";
			if (file_exists(FCPATH.$path.$filename) && $filename != '') {
				$url = $this->config->item('base_url')."{$path}{$filename}";
				echo "&nbsp;<a href=\"{$url}\" target=\"_blank\" class=\"hidable\">documento</a>";
			}
	?>
				</td>
			</tr>
	<?php
		}
	?>
		</table>
	</div>
	<br/>
	<div style="">
		<div>Control de estados:</div>
		<table class="report-table" style="" width="auto">
		<thead>
			<tr>
				<th >#</th>
				<th >Descripcion</th>
				<th >Fecha</th>
				<th >Usuario</th>
				<th >Observacion</th>
			</tr>
		</thead>
	<?php
		$nr = 0;
		foreach($doc_estado_list as $r) {
			$nr++;
	?>
			<tr>
				<td title="<?=$r->doc_estado_id?>"><?=$nr?></td>
				<td><?=$r->estado_doc_desc?></td>
				<td><?=$r->doc_estado_fecha?></td>
				<td><?=$r->doc_estado_usuario?></td>
				<td><?=$r->doc_estado_obs?></td>
			</tr>
	<?php
		}
	?>
		</table>
	</div>
</body>
</html>