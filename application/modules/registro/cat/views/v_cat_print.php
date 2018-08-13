<script>
	cat.print_window = function(record) {
		var w_config = {
			title: 'Ver documento PDF', 
			modal: true,
			width: 850,
			height: 600, 
			id:'cat_print_window',
			loader: {
				url: 'cat/printPreview',
				params: {
		            doc_id: record.get('cat_id')
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