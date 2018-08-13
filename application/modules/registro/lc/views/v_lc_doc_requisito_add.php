<script>
	lc.doc_requisito_add_window = function() {
		var doc_id = Ext.getCmp('lc_form_lc_id_field').getValue();
		var rows = Ext.getCmp('lc_form_doc_requisito_grid').getSelection();
		var record = null;
		if (rows.length > 0) {
			record = rows[0];
		} else {
			Ext.Msg.alert('Error', 'Seleccione un documento para adjuntar.');
			return false;
		} 
		var w_config = {
			id: 'lc_doc_requisito_add_window',
			title: 'Registrar documento', 
			modal: true,
			width: 450,
			height: 240, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'lc/addDocRequisito',
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
					id: 'lc_doc_requisito_form_file_field', // 
    				xtype: 'filefield',
    				name: 'doc_requisito_file',
    				labelStyle : (record.get('tipo_doc_requisito_pdf_flag') == 'N' ? 'color: gray;': ''),
    				x: 10, y: 100, width: 400
				}]
			}],
			buttons:[{
				text: 'Guardar', handler: function() {
					var frm = Ext.getCmp('lc_doc_requisito_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('lc_doc_requisito_add_window').close();
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
					Ext.getCmp('lc_doc_requisito_add_window').close();
				}
			}],
			listeners: {
				show: function () {					
					//Ext.getCmp('lc_cancelar_emitido_form').loadRecord(record);
					if ( record.get('tipo_doc_requisito_pdf_flag') == 'N' ) {
						// opcional
						//Ext.getCmp('lc_doc_requisito_form_file_field').disable();	
					}
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>