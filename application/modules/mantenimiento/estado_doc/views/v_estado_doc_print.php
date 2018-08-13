<script>
	tdr.print_window = function(record) {
		var w_config = {
			title: 'Ver o Generar PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'tdr_print_window',
			loader: {
				url: 'tdr/printPreview',
				params: {
		            tdr_id: record.get('tdr_id')
		        },
				autoLoad: true,
				scripts: true,
				renderer: 'html',
				success: function () {
					if (record.get('tipo_tdr_id') == '03') {
						tdr.reload_list(record.get('tdr_id_parent'));
					} else {
						tdr.reload_list(record.get('tdr_id'));
					}
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>