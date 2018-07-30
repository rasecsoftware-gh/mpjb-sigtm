<script>
	ccu.usuario_permiso_delete_window = function() {
		var rows = Ext.getCmp('ccu_usuario_permiso_grid').getSelection();
		if (rows.length > 0) {
			Ext.Msg.show({
			    title: 'Quitar permiso',
			    message: 'Realmente desea quitar el permiso seleccionado?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
						Ext.Ajax.request({
							params:{
								usuario_permiso_id: rows[0].get('usuario_permiso_id')
							},
							url:'ccu/usuarioPermisoDelete',
							success: function (response, opts){
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert('Usuario - Permiso', result.msg);
									ccu.usuario_permiso_reload_list(ccu.usuario_id_selected);
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