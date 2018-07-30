<script>
	cheque.print_window = function(expediente_documento_id) {
		var w_config = {
			title: 'Vista de impresion', 
			modal: true,
			width: 900,
			height: 700, 
			id: 'cheque_print_window',
			loader: {
				url: 'cheque/printPreview',
				params: {
		            expediente_documento_id: expediente_documento_id
		        },
				autoLoad: true
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>