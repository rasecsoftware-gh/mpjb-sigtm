<script>
	np.window_rendicion_np_delete = function(id) {
		Ext.Msg.show({
		    title:'Eliminar comprobante?',
		    message: 'Realmente desea eliminar el comprobante?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		   			Ext.Ajax.request({
						params:{
							id: id
						},
						url:'np/deleteRendicionNP',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								Ext.getCmp('np_form_rendicion_grid_rendicion_np').store.reload();
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