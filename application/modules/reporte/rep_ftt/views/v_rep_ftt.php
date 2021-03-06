<?php
	$title = "Reporte de Ficha Tecnica de Transportes";
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
	.report-document-title {
		font-size: 15pt!important;
		font-weight: bold;
		text-align: center;
	}
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
	.report-document-box {
		border: 1px solid gray;
		padding: 3px 3px 3px 3px;
	}
	.report-document-box-title {
		font-weight: bold;
		border-bottom: 1px solid gray;
		padding: 3px 0 3px 5px;
		margin: 0 0 5px 0;
	}

	.report-document-table-row {
		border-collapse: collapse;
		border-spacing: 0;
		margin-bottom: 2px;
	}
	.report-document-table-row tr th {
		background-color: #eee;
		border-right: 2px solid white;
		padding: 2px;
		text-align: left;
	}
	.report-document-table-row tr td {
		border: none;
		padding: 0;
	}

	.report-document-table-data {
		border-collapse: collapse;
		border-spacing: 0;
		margin-bottom: 2px;
	}
	.report-document-table-data tr th {
		background-color: #eee;
		border-right: 2px solid white;
		padding: 2px;
		text-align: left;
	}
	.report-document-table-data tr td {
		border: none;
		padding: 5px;
	}
	</style>
</head>
<body>
<?php
	
?>
	<div class="hidable">
		<span class="report-title"><?=$title?></span>&nbsp;&nbsp;&nbsp;
		<button id="consultar_bt">Consultar</button>
	</div>
	<script>
		$("#consultar_bt")
		.button()
		.click( function ( event, ui ) {
			reload();
		});
	</script>
	<form id="form_params" action="<?php echo base_url('rep_ftt')?>" method="get" class="hidable">
	<input type="hidden" name="execute" value="1"/>
	<table class="report-form-table">
		<tr>
			<td>Contribuyente:</td>
			<td>
				<input type="hidden" id="contribuyente_id_field" name="contribuyente_id" value="<?=$p_contribuyente_id?>"/>
				<input type="text" id="contribuyente_desc_field" name="contribuyente_desc" value="<?=$contribuyente_desc?>" style="width: 400px;"/>
				<script>
				$("#contribuyente_desc_field").autocomplete({
					source: 'rep_ftt/getContribuyenteList',
					select: function( event, ui ) {
						$('#contribuyente_id_field').val(ui.item.contribuyente_id);
					}
				});
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
	<div style="width: 19cm;">
		<table class="" style="" width="100%">
			<tr>
				<td><img src="<?php echo base_url('tools/img/escudo_nacional.png')?>" border="0" style="width: 2cm;"/></td>
				<td><div class="report-document-title">FICHA T&Eacute;CNICA DE TRANSPORTES</div></td>
				<td><img src="<?php echo base_url('tools/img/logo_mpjb.png')?>" border="0" style="width: 2cm;"/></td>
			</tr>
		</table>
		<br/>
		<div style="text-align: right;">Locumba, <?=date('d')?> de <?=month_name(intval(date('m')))?> del <?=date('Y')?></div>
		<div class="report-document-box">
			<div class="report-document-box-title">IDENTIFICACION DEL CONTRIBUYENTE</div>
			<table class="report-document-table" style="" width="100%">
<?php
	if (is_null($contrib)):
?>
				<tr>
					<td colspan="7">Seleccione un contribuyenete para mostrar la informacion.</td>
				</tr>
<?php
	else:
?>
				<tr>
					<th style="width: auto;" colspan="1">TIPO DE PERSONA:</th>
					<td style="width: auto;" colspan="1"><?=$contrib->tipo_persona_desc?></td>
					<th style="width: auto;" colspan="3">TIPO DE DOCUMENTO DE IDENTIDAD:</th>
					<td style="width: auto;" colspan="1"><?=$contrib->tipo_doc_identidad_desc?></td>
				</tr>
				<tr>
					<th style="width: auto;" colspan="1">N&ordm; DOCUMENTO:</th>
					<td style="width: auto;" colspan="1"><?=$contrib->contribuyente_numero_doc?></td>
					<th style="width: auto;" colspan="2">NOMBRE O RAZON SOCIAL:</th>
					<td style="width: auto;" colspan="3"><?=$contrib->contribuyente_nombres.' '.$contrib->contribuyente_apellidos?></td>
				</tr>
				<tr>
					<th style="width: auto;" colspan="1">DEPARTAMENTO:</th>
					<td style="width: auto;" colspan="1"><?=$contrib->ubigeo_departamento?></td>
					<th style="width: auto;" colspan="1">PROVINCIA:</th>
					<td style="width: auto;" colspan="2"><?=$contrib->ubigeo_provincia?></td>
					<th style="width: auto;" colspan="1">DISTRITO:</th>
					<td style="width: auto;" colspan="1"><?=$contrib->ubigeo_distrito?></td>
				</tr>
				<tr>
					<th style="width: auto;" colspan="1">DIRECCION:</th>
					<td style="width: auto;" colspan="5"><?=$contrib->contribuyente_direccion?></td>
				</tr>
				<tr>
					<td style="width: 110px;" colspan="1"></td>
					<td style="width: 100px;" colspan="1"></td>
					<td style="width: 70px;" colspan="1"></td>
					<td style="width: 80px;" colspan="1"></td>
					<td style="width: 50px;" colspan="1"></td>
					<td style="width: 65px;" colspan="1"></td>
					<td style="width: auto;" colspan="1"></td>
				</tr>
<?php
	endif;
?>
			</table>
		</div>
		<br/>
		<div class="report-document-box">
			<div class="report-document-box-title">DE LA LICENCIA DE CONDUCIR</div>
			<table class="report-document-table" style="" width="100%">
<?php
	if ( count($lc_list) == 0 ):

?>
				<tr>
					<td style="width: auto;" colspan="7">No tiene licencias de conducir</td>
				</tr>
<?php
	else:
		foreach ($lc_list as $i=>$lc):
?>
				<tr>
					<th style="width: auto;" colspan="1"><?=$i+1?></th>
					<th style="width: auto;" colspan="1">N&ordm; DE LICENCIA:</th>
					<td style="width: auto;" colspan="1"><?=$lc->lc_codigo?></td>
					<th style="width: auto;" colspan="1">CLASE:</th>
					<td style="width: auto;" colspan="1"><?=$lc->lc_clase?></td>
					<th style="width: auto;" colspan="1">CATEGORIA:</th>
					<td style="width: auto;" colspan="1"><?=$lc->lc_categoria?></td>
				</tr>	
<?php
		endforeach;
	endif;
?>
				<tr>
					<td style="width: 30px;" colspan="1"></td>
					<td style="width: 100px;" colspan="1"></td>
					<td style="width: 90px;" colspan="1"></td>
					<td style="width: 50px;" colspan="1"></td>
					<td style="width: 50px;" colspan="1"></td>
					<td style="width: 70px;" colspan="1"></td>
					<td style="width: auto;" colspan="1"></td>
				</tr>
			</table>
		</div>
		<br/>
		<div class="report-document-box">
			<div class="report-document-box-title">PAPELETAS DE INFRACCION</div>
			<table class="report-document-table" style="" width="100%">
<?php
	if ( count($papeleta_list) == 0 ):

?>
				<tr>
					<td style="width: auto;" colspan="7">No tiene papeletas de infraccion</td>
				</tr>
<?php
	else:
		foreach ($papeleta_list as $i=>$p):
?>
				<tr>
					<th style="width: auto;" colspan="1" rowspan="2"><?=$i+1?></th>
					<th style="width: auto;" colspan="1">N&ordm; DE PAPELETA:</th>
					<td style="width: auto;" colspan="1"><?=$p->papeleta_numero?></td>
					<th style="width: auto;" colspan="1">FECHA:</th>
					<td style="width: auto;" colspan="1"><?=$p->papeleta_fecha?></td>
					<th style="width: auto;" colspan="1">TIPO-CAT:</th>
					<td style="width: auto;" colspan="1"><?=$p->tipo_infraccion_id.'-'.$p->papeleta_infraccion_codigo?></td>
				</tr>	

				<tr>
					<th style="width: auto;" colspan="1">MEDIDA PREVENTIVA:</th>
					<td style="width: auto;" colspan="1"><?=$p->medida_preventiva_desc?></td>
					<th style="width: auto;" colspan="1">ESTADO:</th>
					<td style="width: auto;" colspan="3"><?=$p->estado_papeleta_desc?></td>
				</tr>
<?php
		endforeach;
	endif;
?>
				<tr>
					<td style="width: 30px;" colspan="1"></td>
					<td style="width: 100px;" colspan="1"></td>
					<td style="width: 90px;" colspan="1"></td>
					<td style="width: 50px;" colspan="1"></td>
					<td style="width: 50px;" colspan="1"></td>
					<td style="width: 70px;" colspan="1"></td>
					<td style="width: auto;" colspan="1"></td>
				</tr>
			</table>
		</div>
		<br/>
		<div class="report-document-box">
			<div class="report-document-box-title">DE LAS AUTORIZACIONES PARA SERVICIO DE TRANSPORTE PUBLICO</div>
			<table class="report-document-table-row" style="" width="100%">
<?php
	if ( count($psp_list) == 0 ):

?>
			<tr>
				<td style="width: auto;" colspan="7">No tiene autorizaciones de servicio de transporte publico</td>
			</tr>
<?php
	else:
		foreach ($psp_list as $i=>$psp):
?>
			<tr>
				<th style="width: 15px;" rowspan="2"><?=$i+1?></th>
				<td>
					<table class="report-document-table-data" style="" width="100%">
					<tr>
						<th style="width: 45px;" colspan="1">TIPO:</th>
						<td style="width: auto;" colspan="1"><?=$psp->tipo_permiso_desc?></td>
					</tr>
					</table>
					<table class="report-document-table-data" style="" width="100%">
					<tr>
						<th style="width: 225px;" colspan="1">DISPOSITIVO MUNICIPAL QUE APRUEBA:</th>
						<td style="width: auto;" colspan="1">Resoluci&oacute;n N&ordm; <?=$psp->psp_resolucion?></td>
						<th style="width: 35px;" colspan="1">RUTA:</th>
						<td style="width: auto;" colspan="1"><?=$psp->psp_ruta?></td>
					</tr>
					</table>
					<table class="report-document-table-data" style="" width="100%">
					<tr>
						<th style="width: 120px;" colspan="1">FECHA DE INICIO:</th>
						<td style="width: auto;" colspan="1"><?=$psp->psp_fecha_inicio?></td>
						<th style="width: 120px;" colspan="1">FECHA DE TERMINO:</th>
						<td style="width: auto;" colspan="1"><?=$psp->psp_fecha_fin?></td>
						<th style="width: 50px;" colspan="1">ESTADO:</th>
						<td style="width: auto;" colspan="1"><?=$psp->vigente?'VIGENTE':'VENCIDO'?></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
<?php
			foreach ($psp->vehiculo_list as $j=>$v):
?>
					<table class="report-document-table-row" style="" width="100%">
					<tr>
						<th style="width: auto;" colspan="1"><?=$j+1?></th>
						<td>
							<table class="report-document-table-data" style="" width="100%">
								<tr>
									<th style="width: 65px;" colspan="1">CATEGORIA:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_categoria?></td>
									<th style="width: 50px;" colspan="1">MARCA:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_marca?></td>
									<th style="width: 55px;" colspan="1">MODELO:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_modelo?></td>
									<th style="width: 45px;" colspan="1">COLOR:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_color?></td>
								</tr>
							</table>
							<table class="report-document-table-data" style="" width="100%">
								<tr>
									<th style="width: 45px;" colspan="1">PLACA:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_placa?></td>
									<th style="width: 120px;" colspan="1">TARJETA PROPIEDAD:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_ntp?></td>
									<th style="width: 65px;" colspan="1">CONDUCTOR:</th>
									<td style="width: auto;" colspan="1"><?=$v->psp_vehiculo_conductor_nomape?></td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
<?php
			endforeach;
?>
				</td>
			</tr>
<?php
		endforeach;
	endif;
?>
			</table>
		</div>
		<script>
		</script>
	</div>
	
</body>
</html>