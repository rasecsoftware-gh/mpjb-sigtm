<script>
	ccu.usuario_delete_window = function() {
		var rows = Ext.getCmp('ccu_main_grid').getSelection();
		if (rows.length > 0) {
			Ext.Msg.show({
			    title: 'Eliminar usuario',
			    message: 'Realmente desea eliminar el usuario?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
						Ext.Ajax.request({
							params: {
								usuario_id: rows[0].get('usuario_id')
							},
							url:'ccu/Delete',
							success: function (response, opts){
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('Usuario', result.msg);
									ccu.reload_list();
								} else {
									Ext.Msg.alert('Error', result.msg);
								}
							},
							failure: function (response, opts){
								Ext.Msg.alert('Error', 'Error en la conexion.');
							}
						});	
					}
				}
			});
		} else {
			Ext.Msg.alert('Error','Seleccione un registro');
		}
	};
</script>