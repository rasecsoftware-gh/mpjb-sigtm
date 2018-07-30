<script>
	crls.print_window = function(traba_cod) {
		var w_config = {
			title:'Vista de impresion', 
			modal: true,
			width: 800,
			height: 600, 
			id:'crls_print_window',
			loader: {
				url: 'crls/printResumen',
				params: {
		            traba_cod: traba_cod
		        },
				autoLoad: true
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>