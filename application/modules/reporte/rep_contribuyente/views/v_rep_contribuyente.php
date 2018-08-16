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
	<form id="form_params" action="<?php echo base_url('rep_contribuyente')?>" method="get" class="hidable">
	<input type="hidden" name="execute" value="1"/>
	<table class="report-form-table">
		<tr>
			<td><label for="tipo_persona_id_field">Tipo de Persona</label></td>
			<td>

			    <select id="tipo_persona_id_field" name="tipo_persona_id" >
			      <option value="N" selected="selected">Natural</option>
			      <option value="Juridica">Juridica</option>
			    </select>
				<script>
				$("#tipo_persona_id_field").selectmenu();
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
		
		</div>
		<script>
		</script>
	</div>
	
</body>
</html>