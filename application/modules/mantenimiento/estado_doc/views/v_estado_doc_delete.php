<script>
	estado_doc.delete_window = function() {
		var rows = Ext.getCmp('estado_doc_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: estado_doc.title,
			    message: 'Realmente desea eliminar?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	Ext.Ajax.request({
							params: {
								tipo_doc_requisito_id: record.get('tipo_doc_requisito_id')
							},
							url: 'estado_doc/Delete',
							success: function (response, opts) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert(estado_doc.title, result.msg);
									estado_doc.reload_list();
								} else {
									Ext.Msg.alert(estado_doc.title, result.msg);
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
		} else {
			Ext.Msg.alert('Error', 'Seleccione un registro.');
		}
	};
</script>