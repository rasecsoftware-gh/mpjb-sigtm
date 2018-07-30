<script>
	ctl.print_detalle_window = function() {
		var w_config = {
			title:'Vista de impresion', 
			modal: true,
			width: 1000,
			height: 700, 
			id:'ctl_print_detalle_window',
			loader: {
				url: 'ctl/getPrintDetallePreview',
				params: {
		            rkey: ctl.rkey
		        },
				autoLoad: true,
				ajaxOptions: {
					timeout: 300000
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>