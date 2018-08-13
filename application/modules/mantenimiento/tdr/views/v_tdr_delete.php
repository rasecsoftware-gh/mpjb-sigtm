<script>
	tdr.delete_window = function() {
		var rows = Ext.getCmp('tdr_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			Ext.Msg.show({
			    title: tdr.title,
			    message: 'Realmente desea eliminar?',
			    buttons: Ext.Msg.YESNO,
			    icon: Ext.Msg.QUESTION,
			    fn: function(btn) {
			        if (btn === 'yes') {
			        	Ext.Ajax.request({
							params: {
								tipo_doc_requisito_id: record.get('tipo_doc_requisito_id')
							},
							url: 'tdr/Delete',
							success: function (response, opts) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									Ext.Msg.alert(tdr.title, result.msg);
									tdr.reload_list();
								} else {
									Ext.Msg.alert(tdr.title, result.msg);
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