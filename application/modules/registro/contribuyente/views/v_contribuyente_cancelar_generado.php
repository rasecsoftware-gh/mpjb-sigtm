<script>
	contrato.cancelar_generado_window = function(record) {
		Ext.Msg.show({
		    title: 'Cancelar Generado de PDF',
		    message: 'Realmente desea cancelar el generado?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		        	Ext.Ajax.request({
						params:{
							contrato_id: record.get('contrato_id')
						},
						url:'contrato/cancelarGenerado',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								Ext.Msg.alert('Cancelar Generado', result.msg);
								if (record.get('tipo_contrato_id') == '03') {
									contrato.reload_list(record.get('contrato_id_parent'));
								} else {
									contrato.reload_list(record.get('contrato_id'));
								}
							} else {
								Ext.Msg.alert('Error', result.msg);
							}
						},
						failure: function (response, opts){
							Ext.Msg.alert('Error', 'Error en la conexion.');
						}
					});
		        } else {
		            console.log('Cancel pressed');
		        } 
		    }
		});
	};
</script>