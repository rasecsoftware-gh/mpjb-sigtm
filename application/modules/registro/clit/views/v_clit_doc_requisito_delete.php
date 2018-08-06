<script>
	clit.doc_requisito_delete_window = function() {
		var rows = Ext.getCmp('clit_form_doc_requisito_grid').getSelection();
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
		
	};
</script>