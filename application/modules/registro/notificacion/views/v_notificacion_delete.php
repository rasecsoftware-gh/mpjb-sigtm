<script>
	notificacion.delete_window = function() {
		var rows = Ext.getCmp('notificacion_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: 'Eliminar notificacion',
			    message: 'Realmente desea eliminar la notificacion?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	Ext.Ajax.request({
							params: {
								notificacion_id: record.get('notificacion_id')
							},
							url: 'notificacion/Delete',
							success: function (response, opts) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('notificacion', result.msg);
									notificacion.reload_list();
								} else {
									Ext.Msg.alert('notificacion', result.msg);
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