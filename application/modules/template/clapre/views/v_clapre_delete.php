<script>
clapre.window_delete = function(id) {
	Ext.Msg.show({
	    title:'Eliminar Clasificador?',
	    message: 'Realmente desea eliminar el registro seleccionado?',
	    buttons: Ext.Msg.YESNO,
	    icon: Ext.Msg.QUESTION,
	    fn: function(btn) {
	        if (btn === 'yes') {
	   			Ext.Ajax.request({
					params:{
						id: id
					},
					url:'clapre/delete',
					success: function (response, opts){
						var result = Ext.decode(response.responseText);
						if (result.success) {
							clapre.reload_list();
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