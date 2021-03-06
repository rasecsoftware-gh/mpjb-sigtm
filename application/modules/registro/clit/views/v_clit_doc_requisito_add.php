<script>
	clit.doc_requisito_add_window = function() {
		var doc_id = Ext.getCmp('clit_form_clit_id_field').getValue();
		var rows = Ext.getCmp('clit_form_doc_requisito_grid').getSelection();
		var record = null;
		if (rows.length > 0) {
			record = rows[0];
		} else {
			Ext.Msg.alert('Error', 'Seleccione un documento para adjuntar.');
			return false;
		} 
		var w_config = {
			id: 'clit_doc_requisito_add_window',
			title: 'Registrar documento', 
			modal: true,
			width: 450,
			height: 240, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'clit/addDocRequisito',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'clit_doc_requisito_form',
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
					id: 'clit_doc_requisito_form_tipo_doc_requisito_desc_field',
					xtype: 'displayfield',
					fieldLabel: 'Documento',
				    value: record.get('tipo_doc_requisito_desc').toUpperCase(),
				    fieldStyle: {
				    	fontWeight: 'bold'
				    },
				    x: 10, y: 10, width: 400
				},{
					id: 'clit_doc_requisito_form_doc_requisito_fecha_field',
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
					id: 'clit_doc_requisito_form_file_field', // 
    				xtype: 'filefield',
    				name: 'doc_requisito_file',
    				labelStyle : (record.get('tipo_doc_requisito_pdf_flag') == 'N' ? 'color: gray;': ''),
    				x: 10, y: 100, width: 400,
    				accept: 'application/pdf'
				}]
			}],
			buttons:[{
				text: 'Guardar', handler: function() {
					var frm = Ext.getCmp('clit_doc_requisito_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('clit_doc_requisito_add_window').close();
								clit.doc_requisito_reload_list(doc_id);
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
					Ext.getCmp('clit_doc_requisito_add_window').close();
				}
			}],
			listeners: {
				show: function () {					
					//Ext.getCmp('clit_cancelar_emitido_form').loadRecord(record);
					if ( record.get('tipo_doc_requisito_pdf_flag') == 'N' ) {
						// opcional
						//Ext.getCmp('clit_doc_requisito_form_file_field').disable();	
					}
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>