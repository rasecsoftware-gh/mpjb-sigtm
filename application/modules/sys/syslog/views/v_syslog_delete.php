<script>
	contribuyente.delete_window = function() {
		var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
		if (rows.length > 0) {
			Ext.Msg.show({
			    title: 'Eliminar Contribuyente',
			    message: 'Realmente desea eliminar el contribuyente?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	Ext.Ajax.request({
							params: {
								contribuyente_id: record.get('contribuyente_id')
							},
							url: 'contribuyente/Delete',
							success: function (response, opts) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('Contribuyente', 'Se ha eliminado.');
								} else {
									Ext.Msg.alert('Contribuyente', result.msg);
								}
							},
							failure: function (response, opts){
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
	};
</script>