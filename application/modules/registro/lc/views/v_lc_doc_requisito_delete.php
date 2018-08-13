<script>
	lc.doc_requisito_delete_window = function() {
		var rows = Ext.getCmp('lc_form_doc_requisito_grid').getSelection();
		var record = null;
		if (rows.length > 0) {
			record = rows[0];
			if ( record.get('doc_requisito_id') == null ) {
				Ext.Msg.alert('Error', 'El registro seleccionado no tiene un documento registrado todavia.');
				return false;	
			}
		} else {
			Ext.Msg.alert('Error', 'Seleccione un documento para modificar.');
			return false;
		} 
		
		Ext.Msg.show({
		    title: 'Documento adjunto',
		    message: 'Realmente desea quitar el documento?',
		    buttons: Ext.Msg.YESNO,
		    icon: Ext.Msg.QUESTION,
		    fn: function(btn) {
		        if (btn === 'yes') {
		        	Ext.Ajax.request({
						params: {
							doc_requisito_id: record.get('doc_requisito_id')
						},
						url: 'lc/deleteDocRequisito',
						success: function (response, opts) {
							var result = Ext.decode(response.responseText);
							if (result.success) {
								Ext.Msg.alert('Documento', result.msg);
								lc.doc_requisito_reload_list(record.get('doc_id'));
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