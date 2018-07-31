<script>
	contrato.cancelar_anulado_window = function(record, is_adenda) {
		var adenda = is_adenda||false; 
		var w_config = {
			id:'contrato_cancelar_anulado_window',
			title:'Cancelar la Anulacion del Contrato', 
			modal: true,
			width: 500,
			height: 160, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'contrato/CancelarAnulado',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'contrato_cancelar_anulado_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'contrato_id'
				},{
					id: 'contrato_cancelar_anulado_form_contrato_desc',
					xtype: 'label',
				    x: 10, y: 10, width: 450,
				    style: {
				    	fontWeight: 'bold'
				    }
				},{
					xtype: 'displayfield',
					fieldLabel: 'Trabajador',
    				name: 'contrato_traba_apenom',
				    x: 10, y: 30, width: 450
				},{
					xtype: 'label',
					text: 'Se cambiara el estado del contrato a Registrado o Generado',
					x: 10, y: 60, width: 450,
					style: {
						color: 'gray'
					}
				}]
			}],
			buttons:[{
				text: 'Aceptar', handler: function() {
					frm = Ext.getCmp('contrato_cancelar_anulado_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('contrato_cancelar_anulado_window').close();
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
							Ext.Msg.alert('Cancelar Anulado', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Salir', handler: function() {
					win = Ext.getCmp('contrato_cancelar_anulado_window');
					win.close();
				}
			}],
			listeners: {
				show: function () {					
					Ext.getCmp('contrato_cancelar_anulado_form').loadRecord(record);
					Ext.getCmp('contrato_cancelar_anulado_form_contrato_desc').setText(
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