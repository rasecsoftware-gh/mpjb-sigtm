<script>
	contrato.emitir_window = function(record, is_adenda) {
		var adenda = is_adenda||false; 
		var w_config = {
			id:'contrato_emitir_window',
			title:'Emitir Contrato', 
			modal: true,
			width: 500,
			height: 160, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'contrato/Emitir',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'contrato_emitir_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'contrato_id'
				},{
					id: 'contrato_emitir_form_contrato_desc',
					xtype: 'label',
				    x: 10, y: 10, width: 450,
				    style: {
				    	fontWeight: 'bold'
				    }
				},{
					xtype: 'displayfield',
					fieldLabel: 'Fecha de emision',
    				name: 'contrato_fecha_emision',
				    x: 10, y: 30, width: 200
				},{
					xtype: 'label',
					text: 'Se cambiara el estado del contrato a Emitido',
					x: 10, y: 60, width: 450,
					style: {
						color: 'gray'
					}
				}]
			}],
			buttons:[{
				text: 'Aceptar', handler: function() {
					frm = Ext.getCmp('contrato_emitir_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('contrato_emitir_window').close();
								if (adenda) {
									contrato.adenda_reload_list(record.get('contrato_id_parent'));
								} else {
									contrato.reload_list(record.get('contrato_id'));
								}
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Emitir', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Salir', handler: function() {
					win = Ext.getCmp('contrato_emitir_window');
					win.close();
				}
			}],
			listeners: {
				show: function () {					
					Ext.getCmp('contrato_emitir_form').loadRecord(record);
					Ext.getCmp('contrato_emitir_form_contrato_desc').setText(
						record.get('tipo_contrato_desc') + ' '
						+ record.get('contrato_numero') + '-'
						+ record.get('contrato_anio')
					);
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>