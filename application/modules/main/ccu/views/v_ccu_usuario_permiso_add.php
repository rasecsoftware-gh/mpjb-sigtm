<script>
	ccu.usuario_permiso_add_window = function() {
		var permiso_id =  Ext.getCmp('ccu_usuario_permiso_permiso_id_field').getValue();
		if (permiso_id > 0) {
			Ext.Ajax.request({
				params:{
					permiso_id: permiso_id,
					usuario_id: ccu.usuario_id_selected
				},
				url:'ccu/usuarioPermisoAdd',
				success: function (response, opts){
					var result = Ext.decode(response.responseText);
					if (result.success) {
						//Ext.Msg.alert('Agregar permiso', result.msg);
						ccu.usuario_permiso_reload_list(ccu.usuario_id_selected);
					} else {
						Ext.Msg.alert('Error', result.msg);
					}
				},
				failure: function (response, opts){
					Ext.Msg.alert('Error', 'Error en la conexion.');
				}
			});
		} else {
			Ext.Msg.alert('Te lo advierto!', 'Selecciona un permiso para agregar porfa...');
		}
	};
</script>