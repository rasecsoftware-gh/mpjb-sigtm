<script>
	lc.doc_requisito_edit_window = function() {
		var doc_id = Ext.getCmp('lc_form_lc_id_field').getValue();
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
		var w_config = {
			id: 'lc_doc_requisito_edit_window',
			title: 'Modificar documento adjuntado', 
			modal: true,
			width: 450,
			height: 240, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'lc/updateDocRequisito',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'lc_doc_requisito_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'doc_requisito_id',
					value: doc_id
				},{
					xtype: 'hiddenfield',
					name: 'doc_id',
					value: doc_id
				},{
					xtype: 'hiddenfield',
					name: 'tipo_doc_requisito_id',
					value: record.get('tipo_doc_requisito_id')
				},{
					id: 'lc_doc_requisito_form_tipo_doc_requisito_desc_field',
					xtype: 'displayfield',
					fieldLabel: 'Documento',
				    value: record.get('tipo_doc_requisito_desc').toUpperCase(),
				    fieldStyle: {
				    	fontWeight: 'bold'
				    },
				    x: 10, y: 10, width: 400
				},{
					id: 'lc_doc_requisito_form_doc_requisito_fecha_field',
					xtype: 'datefield',
					fieldLabel: 'Fecha documento',
    				name: 'doc_requisito_fecha',
				    x: 10, y: 40, width: 200
				},{
					xtype: 'textfield',
					fieldLabel: 'Nro. Doc./Registro',
    				name: 'doc_requisito_numero',
    				labelStyle : (record.get('tipo_doc_requisito_numero_flag') == 'N' ? 'color: gray;': ''),
				    x: 10, y: 70, width: 350
				},{
					fieldLabel: 'Escaneado en PDF',
					id: 'lc_doc_requisito_form_doc_requisito_pdf_field', // 
    				xtype: 'displayfield',
    				name: 'doc_requisito_pdf',
    				x: 10, y: 100, width: 400
				},{
					fieldLabel: 'Cambiar PDF',
					id: 'lc_doc_requisito_form_file_field', // 
    				xtype: 'filefield',
    				name: 'doc_requisito_file',
    				labelStyle : (record.get('tipo_doc_requisito_pdf_flag') == 'N' ? 'color: gray;': ''),
    				x: 10, y: 130, width: 400,
    				accept: 'application/pdf'
				}]
			}],
			buttons:[{
				text: 'Guardar', handler: function() {
					var frm = Ext.getCmp('lc_doc_requisito_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('lc_doc_requisito_edit_window').close();
								lc.doc_requisito_reload_list(doc_id);
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Adjuntar documento', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Salir', handler: function() {
					Ext.getCmp('lc_doc_requisito_edit_window').close();
				}
			}],
			listeners: {
				show: function () {					
					Ext.getCmp('lc_doc_requisito_form').loadRecord(record);
					if ( record.get('tipo_doc_requisito_pdf_flag') == 'N' ) {
						//Ext.getCmp('lc_doc_requisito_form_file_field').disable();	
					}
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>