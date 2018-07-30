<script>
	ctl.print_window = function() {
		var w_config = {
			title:'Vista de impresion', 
			modal: true,
			width: 1000,
			height: 700, 
			id:'ctl_print_window',
			loader: {
				url: 'ctl/getPrintPreview',
				params: {
		            rkey: ctl.rkey
		        },
				autoLoad: true
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	}
</script>