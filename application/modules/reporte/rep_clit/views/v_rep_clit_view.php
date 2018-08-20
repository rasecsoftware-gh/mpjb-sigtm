<?php
	$title = "Constancia de Libre Infraccion de Transito";
	$controller = "rep_clit";
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
<?php  echo $doc->clit_anio.' - '.str_pad($doc->clit_numero, 4, '0', STR_PAD_LEFT); ?>
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
			<td><?=$doc->clit_fecha?></td>
		</tr>
		<tr>
			<th>Tiene Infraccion?:</th>
			<td><?=$doc->clit_resultado=='N'?'No':'Si'?></td>
		</tr>
		<tr>
			<th>Estado:</th>
			<td><?=$doc->estado_doc_desc?></td>
		</tr>
	</table>
	</div>
	<br>
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