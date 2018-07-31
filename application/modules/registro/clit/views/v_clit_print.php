<script>
	contribuyente.print_window = function(record) {
		var w_config = {
			title: 'Ver o Generar PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'contribuyente_print_window',
			loader: {
				url: 'contribuyente/printPreview',
				params: {
		            contribuyente_id: record.get('contribuyente_id')
		        },
				autoLoad: true,
				scripts: true,
				renderer: 'html',
				success: function () {
					if (record.get('tipo_contribuyente_id') == '03') {
						contribuyente.reload_list(record.get('contribuyente_id_parent'));
					} else {
						contribuyente.reload_list(record.get('contribuyente_id'));
					}
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>