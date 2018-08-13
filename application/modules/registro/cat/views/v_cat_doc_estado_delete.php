<script>
	cat.doc_estado_delete_window = function(record) {
		Ext.Msg.show({
		    title: 'Control de estado',
		    message: 'Realmente desea revertir el estado?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		        	Ext.Ajax.request({
						params: {
							doc_estado_id: record.get('doc_estado_id')
						},
						url: 'cat/deleteDocEstado',
						success: function (response, opts) {
							var result = Ext.decode(response.responseText);
							if (result.success) {
								Ext.Msg.alert('Documento', result.msg);
								cat.reload_list(record.get('doc_id'));
							} else {
								Ext.Msg.alert('Documento', result.msg);
							}
						},
						failure: function (response, opts){
							Ext.Msg.alert('Error', 'Ha ocurrido un error al realizar la operacion.');
						},
						timeout: 5*60*1000 // 5m
					});
		        } else {
		            console.log('Cancel pressed');
		        } 
		    }
		});
	};
</script>