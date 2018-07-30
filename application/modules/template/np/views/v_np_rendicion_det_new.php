<script>
	np.window_det_rendicion_np_new = function(id_rendicion_np, id_det_np, comprobante) {

		var w_config = {
			title:'Agregar detalle a comprobante', 
			modal: true,
			width: 800,
			height: 390, 
			id:'np_window_det_rendicion_np',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/addDetRendicionNP',
				region: 'center',
				layout: 'absolute',
				id: 'np_form_det_rendicion_np',
				defaultType:'displayfield',
				defaults: {
					labelWidth: 155
				},
				items:[{
					xtype: 'hiddenfield', name: 'id_rendicion_np', value: id_rendicion_np
				},{
					xtype: 'hiddenfield', name: 'id_det_np'
				},{
					fieldLabel:'Bien/Servicio:',
    				name: 'desc_bs',
    				x: 10, y: 10, width: 785, readOnly: true
				},{
					fieldLabel:'Unidad Med.:',
    				name: 'desc_unimed',
				    x: 10, y: 40, width: 400, readOnly: true
				},{
					fieldLabel: 'Cantidad:',
					id: 'np_form_det_rendicion_np_cant_det_np',
					name: 'cant_det_np',
					format: '0.00',
					x: 10, y: 70, width: 300, readOnly: true
				},{
					fieldLabel: 'Precio Unit.:',
					id: 'np_form_det_rendicion_np_preuni_det_np',
					name: 'preuni_det_np',
					x: 10, y: 100, width: 300, readOnly: true
				},{
					fieldLabel: 'Total:',
					id: 'np_form_det_rendicion_np_tot_det_np',
					name: 'tot_det_np',
					x: 10, y: 130, width: 300, readOnly: true
				},{
					fieldLabel:'<b>Comprobante de Pago</b>',
    				value: comprobante,
				    x: 10, y: 160, width: 400, readOnly: true
				},{
					fieldLabel: '<b>Cantidad Rendicion</b>',
					xtype: 'numberfield',
					id: 'np_form_det_rendicion_np_cant_det_rendicion_np',
					name: 'cant_det_rendicion_np',
					x: 10, y: 190, width: 300
				},{
					fieldLabel: '<b>Precio Unit. Rendicion</b>',
					xtype: 'numberfield',
					id: 'np_form_det_rendicion_np_preuni_det_rendicion_np',
					name: 'preuni_det_rendicion_np',
					x: 10, y: 220, width: 300
				},{
					fieldLabel: '<b>Total Rendicion</b>',
					xtype: 'numberfield',
					id: 'np_form_det_rendicion_np_tot_det_rendicion_np',
					name: 'tot_det_rendicion_np',
					x: 10, y: 250, width: 300, readOnly: true
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('np_form_det_rendicion_np');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								np.det_rendicion_np_modified = true;
								Ext.getCmp('np_window_det_rendicion_np').close();
								Ext.getCmp('np_form_rendicion_grid_det_np').store.reload();
								Ext.getCmp('np_form_rendicion_grid_det_rendicion_np').store.reload({
									params: {
										id_rendicion_np: id_rendicion_np
									}
								});
							} else {
								Ext.Msg.alert('Error',action.result.msg);
							}
						},
						failure:function(form, action){
							Ext.Msg.alert('Error',action.result.msg);
						}
					});
				}
			},{
				text:'Cancelar', 
				handler: function () {
					Ext.getCmp('np_window_det_rendicion_np').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetNPForImportRow/'+id_det_np,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('np_form_det_rendicion_np');
						frm.loadRecord(sender.getAt(0));
						w.show();
						Ext.getCmp('np_form_det_rendicion_np_cant_det_rendicion_np').setValue(records[0].get('saldo'));
						Ext.getCmp('np_form_det_rendicion_np_preuni_det_rendicion_np').setValue(records[0].get('preuni_det_np'));
						Ext.getCmp('np_form_det_rendicion_np_tot_det_rendicion_np').setValue(records[0].get('tot_det_np'));
						
					}
				}
			}
		});
	}
</script>