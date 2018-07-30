<script>
	np.window_det_rendicion_np_delete = function(id) {

		Ext.Msg.show({
		    title:'Eliminar detalle comprobante?',
		    message: 'Realmente desea eliminar el detalle de comprobante?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		   			Ext.Ajax.request({
						params:{
							id: id
						},
						url:'np/deleteDetRendicionNP',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								np.det_rendicion_np_modified = true;
								Ext.getCmp('np_form_rendicion_grid_det_rendicion_np').store.reload();
								Ext.getCmp('np_form_rendicion_grid_det_np').store.reload();
							} else {
								Ext.Msg.alert('Error', result.msg);
							}
						},
						failure: function (response, opts){
							Ext.Msg.alert('Error', 'Error en la conexion');
						}
					});         
		        } 
		    }
		});
	}
</script>