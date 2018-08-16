<?php
	$title = "Reporte de Contribuyentes";
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$title?></title>
	<meta charset="UTF-8">
	<meta name="report" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<script src="../jquery-ui-1.11.4/external/jquery/jquery.js" type="text/javascript"></script>
	<link href="../jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="../jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('tools/css/rpt.css')?>">
	<style>
	.ui-menu { position: absolute; width: 200px; }
	
	</style>
</head>
<body>
<?php
	
?>
	<div class="">
		<span class="report-title"><?=$title?></span>&nbsp;&nbsp;&nbsp;
		<button id="consultar_bt" class="hidable">Consultar</button>
	</div>
	<script>
		$("#consultar_bt")
		.button()
		.click( function ( event, ui ) {
			reload();
		});
	</script>
	<form id="form_params" action="<?php echo base_url('rep_contribuyente')?>" method="get" class="">
	<input type="hidden" name="execute" value="1"/>
	<table class="report-form-table">
		<tr>
			<td><label for="tipo_persona_id_field">Tipo de Persona:</label></td>
			<td>
			    <select id="tipo_persona_id_field" name="tipo_persona_id" style="width: 100px;">
<?php 
	foreach ($tipo_persona_list as $r):
		$selected = '';
		if ( $r->tipo_persona_id == $p_tipo_persona_id ) {
			$selected = 'selected="selected"';
		}
?>
			      <option value="<?=$r->tipo_persona_id?>" <?=$selected?> ><?=$r->tipo_persona_desc?></option>
<?php
	endforeach;
?>
			    </select>
				<script>
				$("#tipo_persona_id_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="tipo_doc_identidad_id_field">Tipo de Documento de Identidad:</label></td>
			<td>

			    <select id="tipo_doc_identidad_id_field" name="tipo_doc_identidad_id" style="width: 200px;">
<?php 
	foreach ($tipo_doc_identidad_list as $r):
		$selected = '';
		if ( $r->tipo_doc_identidad_id == $p_tipo_doc_identidad_id ) {
			$selected = 'selected="selected"';
		}
?>
			      <option value="<?=$r->tipo_doc_identidad_id?>" <?=$selected?> ><?=$r->tipo_doc_identidad_desc?></option>
<?php
	endforeach;
?>			      
			    </select>
				<script>
				$("#tipo_doc_identidad_id_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td>Ubicacion:</td>
			<td>
				<!--<input type="hidden" id="ubigeo_id_field" name="ubigeo_id" value=""/>-->
				<input type="text" id="ubigeo_desc_field" name="ubicacion_desc" value="<?=$p_ubigeo_desc?>" style="width: 500px;"/>
				<script>
				$("#ubigeo_desc_field").autocomplete({
					source: 'rep_contribuyente/getUbigeoList',
					select: function( event, ui ) {
						//$('#ubigeo_id_field').val(ui.item.ubigeo_id);
					}
				});
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="filter_field">Filtrar por:</label></td>
			<td>

			    <input id="filter_field" type="text" name="filter" value="<?=$p_filter?>" style="width: 200px;">
				<script>
				//$("#filter_field").selectmenu();
				</script>
			</td>
		</tr>
	</table>
	</form>
	<br>
	<script>
	function reload () {
		$('#form_params').submit();
	};
	</script>
	<div style="width: 100%;">
		<div style="text-align: left;"><span id="record_count" style="font-weight: bold;">0</span> registros encontrados.</div>
		<table class="report-table" style="" width="100%">
		<thead>
			<tr>
				<th >#</th>
				<th title="Tipo de Persona">TP</th>
				<th title="Tipo de Documento de Identidad">TD</th>
				<th >NUMERO DOC.</th>
				<th >NOMBRES O RAZON SOCIAL</th>
				<th >APELLIDOS</th>
				<th >DEPARTAMENTO</th>
				<th >PROVINCIA</th>
				<th >DISTRITO</th>
				<th >DIRECCION</th>
				<th >TELEFONO</th>
				<th >E-MAIL</th>				
				<th >FECHA NAC.</th>
				<th >ESTADO</th>
			</tr>
		</thead>
	<?php
		$nr = 0;
		$total = array(0,0,0,0);
		foreach($contrib_list as $r) {
			$nr++;
	?>
			<tr>
				<td title="<?=$r->contribuyente_id?>"><?=$nr?></td>
				<td><?=$r->tipo_persona_id?></td>
				<td><?=$r->tipo_doc_identidad_desc?></td>
				<td><?=$r->contribuyente_numero_doc?></td>
				<td><?=$r->contribuyente_nombres?></td>
				<td><?=$r->contribuyente_apellidos?></td>
				<td><?=$r->ubigeo_departamento?></td>
				<td><?=$r->ubigeo_provincia?></td>
				<td><?=$r->ubigeo_distrito?></td>
				<td><?=$r->contribuyente_direccion?></td>
				<td><?=$r->contribuyente_telefono?></td>
				<td><?=$r->contribuyente_email?></td>
				<td><?=$r->contribuyente_fecha_nac?></td>
				<td><?=$r->contribuyente_estado?></td>
			</tr>
	<?php
		}
	?>
		</table>
		<script>
			$('#record_count').html('<?=$nr?>');
		</script>
	</div>
	
</body>
</html>