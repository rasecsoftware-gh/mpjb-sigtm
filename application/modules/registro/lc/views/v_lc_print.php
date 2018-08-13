<script>
	lc.print_window = function(record) {
		var w_config = {
			title: 'Ver documento PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'lc_print_window',
			loader: {
				url: 'lc/printPreview',
				params: {
		            doc_id: record.get('lc_id')
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