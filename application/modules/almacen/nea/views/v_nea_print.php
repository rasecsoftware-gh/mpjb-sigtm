<script>
	nea.print_window = function(nea_id, version) {
		var w_config = {
			title:'Vista de impresion', 
			modal: true,
			width: 800,
			height: 600, 
			id:'nea_print_window',
			loader: {
				url: 'nea/getPrintPreview',
				params: {
		            nea_id: nea_id,
		            version: version
		        },
				autoLoad: true
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>