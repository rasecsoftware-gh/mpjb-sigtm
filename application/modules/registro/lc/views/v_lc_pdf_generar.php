<script>
	lc.pdf_generar_window = function() {
		var rows = Ext.getCmp('lc_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: 'Generar PDF',
			    message: 'Realmente desea generar el PDF?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	var w = lc.panel;
						w.mask('generando PDF');
			        	Ext.Ajax.request({
							params: {
								doc_id: record.get('lc_id')
							},
							url: 'lc/generarPDF',
							success: function (response, opts) {
								w.unmask();
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('PDF', result.msg);
									lc.reload_list(record.get('lc_id'));
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