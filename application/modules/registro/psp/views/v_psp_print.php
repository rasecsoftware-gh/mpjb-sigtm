<script>
	clit.print_window = function(record) {
		var w_config = {
			title: 'Ver documento PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'clit_print_window',
			loader: {
				url: 'clit/printPreview',
				params: {
		            doc_id: record.get('clit_id')
		        },
				autoLoad: true,
				scripts: true,
				renderer: 'html',
				success: function () {
				}
			}
		};
		Ext.create('Ext.window.Window', w_config).show();
	}
</script>