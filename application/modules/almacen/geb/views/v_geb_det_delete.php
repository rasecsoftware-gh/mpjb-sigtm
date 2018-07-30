<script>
	geb.window_geb_det_delete = function (get_det_id) {
		Ext.Msg.show({
		    title:'Eliminar detalle?',
		    message: 'Realmente desea eliminar el detalle seleccionado?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		   			Ext.Ajax.request({
						params:{
							geb_det_id: get_det_id
						},
						url:'geb/deleteGebDet',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								Ext.Msg.alert('Eliminar', result.msg);
								Ext.getCmp('geb_form_grid_geb_det').store.reload();
							} else {
								Ext.Msg.alert('Error', result.msg);
							}
						},
						failure: function (response, opts){
							Ext.Msg.alert('Error', 'Error en la conexion de datos');
						}
					});         
		        } else {
		            console.log('Cancel pressed');
		        } 
		    }
		});
	};
</script>