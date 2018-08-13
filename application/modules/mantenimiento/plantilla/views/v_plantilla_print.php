<script>
	plantilla.print_window = function(record) {
		var w_config = {
			title: 'Ver o Generar PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'plantilla_print_window',
			loader: {
				url: 'plantilla/printPreview',
				params: {
		            plantilla_id: record.get('plantilla_id')
		        },
				autoLoad: true,
				scripts: true,
				renderer: 'html',
				success: function () {
					if (record.get('tipo_plantilla_id') == '03') {
						plantilla.reload_list(record.get('plantilla_id_parent'));
					} else {
						plantilla.reload_list(record.get('plantilla_id'));
					}
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>