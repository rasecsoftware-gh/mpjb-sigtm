<script>
	lc.doc_estado_add_window = function(record) { // doc_estado record
		var doc_id = Ext.getCmp('lc_form_lc_id_field').getValue();
		var w_config = {
			id: 'lc_doc_estado_add_window',
			title: 'Cambiar estado a', 
			modal: true,
			width: 450,
			height: 180, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'lc/addDocEstado',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'lc_doc_estado_form',
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
					name: 'estado_doc_id',
					value: record.get('estado_doc_id')
				},{
					id: 'lc_doc_estado_form_estado_doc_desc_field',
					xtype: 'displayfield',
					fieldLabel: 'Estado',
				    value: record.get('estado_doc_desc').toUpperCase(),
				    fieldStyle: {
				    	fontWeight: 'bold'
				    },
				    x: 10, y: 10, width: 400
				},{
					id: 'lc_doc_estado_form_doc_estado_fecha_field',
					xtype: 'datefield',
					fieldLabel: 'Fecha',
    				name: 'doc_estado_fecha',
				    x: 10, y: 40, width: 200
				},{
					xtype: 'textfield',
					fieldLabel: 'Observacion',
    				name: 'doc_estado_obs',
				    x: 10, y: 70, width: 400
				}]
			}],
			buttons:[{
				text: 'Guardar', handler: function() {
					var frm = Ext.getCmp('lc_doc_estado_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('lc_doc_estado_add_window').close();
								lc.reload_list(doc_id);
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Control de estado', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Salir', handler: function() {
					Ext.getCmp('lc_doc_estado_add_window').close();
				}
			}],
			listeners: {
				show: function () {					
					//Ext.getCmp('lc_cancelar_emitido_form').loadRecord(record);
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>