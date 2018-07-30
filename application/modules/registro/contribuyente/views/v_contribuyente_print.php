<script>
	contrato.print_window = function(record) {
		var w_config = {
			title: 'Ver o Generar PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'contrato_print_window',
			loader: {
				url: 'contrato/printPreview',
				params: {
		            contrato_id: record.get('contrato_id')
		        },
				autoLoad: true,
				scripts: true,
				renderer: 'html',
				success: function () {
					if (record.get('tipo_contrato_id') == '03') {
						contrato.reload_list(record.get('contrato_id_parent'));
					} else {
						contrato.reload_list(record.get('contrato_id'));
					}
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>