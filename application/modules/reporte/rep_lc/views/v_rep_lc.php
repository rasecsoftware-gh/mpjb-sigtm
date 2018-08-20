<?php
	$title = "Reporte de Licencias de Conducir";
	$controller = "rep_lc";
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$title?></title>
	<meta charset="UTF-8">
	<meta name="report" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<script src="../jquery-ui-1.12.1/external/jquery/jquery.js" type="text/javascript"></script>
	<link href="../jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="../jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
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
<?php
	if ($p_execute == '1'):
?>
		<button id="reset_bt" class="hidable">X</button>
<?php
	endif;
?>		
	</div>
	<script>
		$("#consultar_bt")
		.button()
		.click( ( event, ui ) => {
			reload();
		});

		$("#reset_bt")
		.button()
		.click( ( event, ui ) => {
			location.href = '<?=base_url($controller)?>';
		});
	</script>
	<form id="form_params" action="<?php echo base_url($controller)?>" method="get" class="">
	<input type="hidden" name="execute" value="1"/>
	<table class="report-form-table">
		<tr>
			<td><label for="anio_field">A&ntilde;o:</label></td>
			<td>
			    <select id="anio_field" name="anio" style="width: 100px;">
			    	<option value="" >- todo -</option>
<?php
	for ($a = date('Y'); $a >= 2018; $a--):
		$selected = '';
		if ( $a == $p_anio ) {
			$selected = 'selected="selected"';
		}
?>
					<option value="<?=$a?>" <?=$selected?> ><?=$a?></option>
<?php
	endfor;
?>
			    </select>
				<script>
				$("#anio_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td>Contribuyente:</td>
			<td>
				<input type="text" id="contribuyente_desc_field" name="contribuyente_desc" value="<?=$p_contribuyente_desc?>" style="width: 400px;"/>
				<script>
				$("#contribuyente_desc_field").autocomplete({
					source: '<?=$controller?>/getContribuyenteList',
					select: function( event, ui ) {
						$('#contribuyente_id_field').val(ui.item.contribuyente_id);
					}
				});
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
					source: '<?=$controller?>/getUbigeoList',
					select: function( event, ui ) {
						//$('#ubigeo_id_field').val(ui.item.ubigeo_id);
					}
				});
				</script>
			</td>
		</tr>
		<tr class="fecha-tr hidable">
			<td>
				<label for="fecha_flag_field">Fecha</label>
				<input id="fecha_flag_field" type="checkbox" name="fecha_flag" <?=$p_fecha_flag=='1'?'checked':''?> value="1"/>
			</td>
			<td>
				<div id="fecha_info" style="<?=$p_fecha_flag!='1'?'display: none':''?>">
					<input type="text" id="fecha_from_field" name="fecha_from" value="<?=$p_fecha_from?>" style="width: 75px;" autocomplete="off"/>
					<span>&nbsp;a&nbsp;</span>
					<input type="text" id="fecha_to_field" name="fecha_to" value="<?=$p_fecha_to?>" style="width: 75px;" autocomplete="off"/>
				</div>
				<script>
				$("#fecha_flag_field")
				//.checkboxradio()
				.click( ( e ) => {
					if( e.currentTarget.checked ) {
						$('.fecha-tr').removeClass('hidable');
						$('#fecha_info').show();
					} else {
						$('.fecha-tr').addClass('hidable');
						$('#fecha_info').hide();
					}
				});
				$("#fecha_from_field, #fecha_to_field").datepicker({
					dateFormat: 'dd/mm/yy',
					dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ]
				});
				//$("#fecha_to_field").datepicker();
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="categoria_field">Categoria:</label></td>
			<td>
			    <select id="categoria_field" name="categoria" style="width: 100px;">
<?php 
	foreach ($categoria_list as $r):
		$selected = ( $r == $p_categoria )?'selected="selected"':'';
?>
			      <option value="<?=$r?>" <?=$selected?> ><?=$r==''?'- toto -':$r?></option>
<?php
	endforeach;
?>
			    </select>
				<script>
				$("#categoria_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="restricciones_field">Restricciones:</label></td>
			<td>
			    <select id="restricciones_field" name="restricciones" style="width: 100px;">
<?php 
	foreach ($restricciones_list as $r):
		$selected = ( $r->lc_restricciones == $p_restricciones )?'selected="selected"':'';
?>
			      <option value="<?=$r->lc_restricciones?>" <?=$selected?> ><?=$r->lc_restricciones==''?'- toto -':$r->lc_restricciones?></option>
<?php
	endforeach;
?>
			    </select>
				<script>
				$("#restricciones_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="resolucion_field">Resolucion:</label></td>
			<td>

			    <input id="resolucion_field" type="text" name="resolucion" value="<?=$p_resolucion?>" style="width: 200px;">
			</td>
		</tr>
		<tr>
			<td><label for="estado_doc_id_field">Estado del Tramite:</label></td>
			<td>
			    <select id="estado_doc_id_field" name="estado_doc_id" style="width: 100px;">
<?php 
	foreach ($estado_doc_list as $r):
		$selected = '';
		if ( $r->estado_doc_id == $p_estado_doc_id ) {
			$selected = 'selected="selected"';
		}
?>
			      <option value="<?=$r->estado_doc_id?>" <?=$selected?> ><?=$r->estado_doc_desc?></option>
<?php
	endforeach;
?>
			    </select>
				<script>
				$("#estado_doc_id_field").selectmenu();
				</script>
			</td>
		</tr>
		<tr>
			<td><label for="estado_field">Estado de la Licencia:</label></td>
			<td>
			    <select id="estado_field" name="estado" style="width: 100px;">
<?php 
	foreach ($estado_list as $r):
		$selected = ( $r == $p_estado )?'selected="selected"':'';
?>
			      <option value="<?=$r?>" <?=$selected?> ><?=$r==''?'- toto -':$r?></option>
<?php
	endforeach;
?>
			    </select>
				<script>
				$("#estado_field").selectmenu();
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
				<th >A&Ntilde;O</th>
				<th >NUMERO</th>
				<th >DNI</th>
				<th >NOMBRES</th>
				<th >APELLIDOS</th>
				<th >FECHA INICIO TRA.</th>
				<th >CLASE</th>
				<th >CATEGORIA</th>
				<th >RESOLUCION</th>
				<th >RES. FECHA</th>
				<th >FECHA EXP.</th>
				<th >FECHA VEN.</th>
				<th >RESTRICCION</th>
				<th >ESTADO TRAMITE</th>
				<th >ESTADO LICENCIA</th>
			</tr>
		</thead>
	<?php
		$nr = 0;
		$total = array(0,0,0,0);
		foreach($list as $r) {
			$nr++;
	?>
			<tr>
				<td title="<?=$r->lc_id?>"><?=$nr?></td>
				<td><?=$r->lc_anio?></td>
				<td>
					<a class="list-row" href="#<?=$r->lc_id?>" rowid="<?=$r->lc_id?>"><?=str_pad($r->lc_numero, 4, '0', STR_PAD_LEFT)?></a>
				</td>
				<td><?=$r->contribuyente_numero_doc?></td>
				<td><?=$r->contribuyente_nombres?></td>
				<td><?=$r->contribuyente_apellidos?></td>
				<td><?=$r->lc_fecha?></td>
				<td><?=$r->lc_clase?></td>
				<td><?=$r->lc_categoria?></td>
				<td>
					<?=$r->lc_resolucion?>
	<?php
			$filename = $r->lc_resolucion_pdf;
			$path = "dbfiles/public.lc/";
			if (file_exists(FCPATH.$path.$filename) && $filename != '') {
				$url = $this->config->item('base_url')."{$path}{$filename}";
				echo "&nbsp;<a href=\"{$url}\" target=\"_blank\" class=\"hidable\" title=\"Ver resolucion en formato PDF\">ver</a>";
			}
	?>					
				</td>
				<td><?=$r->lc_resolucion_fecha?></td>
				<td><?=$r->lc_fecha_exp?></td>
				<td><?=$r->lc_fecha_ven?></td>
				<td><?=$r->lc_restricciones?></td>
				<td><?=$r->estado_doc_desc?></td>
				<td><?=$r->estado?></td>
			</tr>
	<?php
		}
	?>
		</table>
		<script>
			$('#record_count').html('<?=$nr?>');
			$('.list-row').click( function(event) {
				window.open('<?=base_url($controller).'/getView?lc_id='?>'+$(event.currentTarget).attr('rowid'), '_self');
			});
		</script>
	</div>
	
</body>
</html>