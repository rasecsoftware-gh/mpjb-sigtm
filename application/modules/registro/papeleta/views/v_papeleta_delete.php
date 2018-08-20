<script>
	papeleta.delete_window = function() {
		var rows = Ext.getCmp('papeleta_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: 'Eliminar papeleta',
			    message: 'Realmente desea eliminar la papeleta?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	Ext.Ajax.request({
							params: {
								papeleta_id: record.get('papeleta_id')
							},
							url: 'papeleta/Delete',
							success: function (response, opts) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('Papeleta', result.msg);
									papeleta.reload_list();
								} else {
									Ext.Msg.alert('Papeleta', result.msg);
								}
							},
							failure: function (response, opts){
								Ext.Msg.alert('Error', 'Ha ocurrido un error al realizar la operacion.');
							},
							timeout: 5*1000 // 5m
						});
			        } else {
			            console.log('Cancel pressed');
			        } 
			    }
			});
		} else {
			Ext.Msg.alert('Error', 'Seleccione un registro.');
		}
	};
</script>