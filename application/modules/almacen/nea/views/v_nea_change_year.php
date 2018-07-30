<script>
	nea.change_year_window = function (anio) {
		Ext.Msg.show({
		    title:'Cambiar Año?',
		    message: 'Realmente desea cambiar el año a '+anio+'?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		   			Ext.Ajax.request({
						params:{
							anio: anio
						},
						url:'nea/setAnio',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								nea.anio = anio;
								Ext.getCmp('nea_anio_list_label').setText(anio);
								nea.main_store.reload();
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