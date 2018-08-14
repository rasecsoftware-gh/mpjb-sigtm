<script>
	cat.pdf_generar_window = function() {
		var rows = Ext.getCmp('cat_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: 'Generar PDF',
			    message: 'Realmente desea generar el PDF?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	var w = cat.panel;
						w.mask('generando PDF');
			        	Ext.Ajax.request({
							params: {
								doc_id: record.get('cat_id')
							},
							url: 'cat/generarPDF',
							success: function (response, opts) {
								w.unmask();
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('PDF', result.msg);
									cat.reload_list(record.get('cat_id'));
								} else {
									Ext.Msg.alert('Generar PDF', result.msg);
								}
							},
							failure: function (response, opts){
								w.unmask();
								Ext.Msg.alert('Error', 'Ha ocurrido un error al realizar la operacion.');
							},
							timeout: 5*60*1000 // 5m
						});
			        } else {
			            console.log('Cancel pressed');
			        } 
			    }
			});
		} else {
			Ext.Msg.alert('Error', 'Seleccione un registro.');
		}
	}
</script>