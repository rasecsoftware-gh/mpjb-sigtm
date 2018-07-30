<script>
</script>
<style>
	* {
		font-family: Arial;
		font-size: 8pt;
	}
	.fecha {
		color: green;
	}
	.nombre {
		color: gray;
	}
	.item {
		padding: 0 0 2px 0;
	}
	.image {
		border: none;
		margin: 0;
		padding: 0;
		vertical-align: bottom;
	}
	body {
		margin: 0;
		padding: 0;
	}
</style>
<?php
	//date_default_timezone_set('America/Lima');
	foreach($list as $t) {
		$img_torta = base_url('tools/img/torta_'.strtolower($t['traba_sexo']).'.png');
		$cumple = '';
		//echo "{$t['md_naci']},".date('nj');
		//echo date('F');
		if ($t['md_naci'] == date('nd')) {
			$cumple = "<img class=\"image\" src=\"{$img_torta}\" width=\"15\"/>";
		}
?>
	<div class="item"><span class="fecha"><?=substr($t['traba_fecha_naci'], 0, 5)?></span>&nbsp;&nbsp;<span class="nombre"><?=$t['traba_nomape']?>&nbsp;&nbsp;</span><?=$cumple?></div>
<?php
	}
?>
