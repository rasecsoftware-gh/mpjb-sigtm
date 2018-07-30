<script>
	np.window_rendicion_detail = function(id, default_id_proveedor) {

		var w_detail_config = {
			title:'Detalle de Rendicion', 
			modal: true,
			width: 730,
			height: 360, 
			id:'np_window_rendicion_detail',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/updateRDetail',
				region: 'center',
				layout: 'absolute',
				id: 'np_form_rendicion_detail',
				defaultType:'displayfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_det_np'
				},{
					xtype: 'hiddenfield', name: 'id_np'
				},{
					fieldLabel:'Bien/Servicio:',
    				name: 'desc_bs',
    				x: 10, y: 10, width: 685, readOnly: true
				},{
					fieldLabel:'Unidad Med.:',
    				name: 'desc_unimed',
				    x: 10, y: 40, width: 220, readOnly: true
				},{
					fieldLabel: 'Cantidad:',
					id: 'np_form_rendicion_detail_cant_det_np',
					name: 'cant_det_np',
					format: '0.00',
					x: 10, y: 70, width: 220, readOnly: true
				},{
					fieldLabel: 'Precio Unit.:',
					id: 'np_form_rendicion_detail_preuni_det_np',
					name: 'preuni_det_np',
					x: 10, y: 100, width: 220, readOnly: true
				},{
					fieldLabel: 'Total:',
					id: 'np_form_rendicion_detail_tot_det_np',
					name: 'tot_det_np',
					x: 10, y: 130, width: 220, readOnly: true
				},{
					fieldLabel: '<b>Cantidad R</b>',
					xtype: 'numberfield',
					id: 'np_form_rendicion_detail_cant_final_det_np',
					name: 'cant_final_det_np',
					x: 10, y: 160, width: 220
				},{
					fieldLabel: '<b>Precio Unit. R</b>',
					xtype: 'numberfield',
					id: 'np_form_rendicion_detail_preuni_final_det_np',
					name: 'preuni_final_det_np',
					x: 10, y: 190, width: 220
				},{
					fieldLabel: '<b>Total R</b>',
					xtype: 'numberfield',
					id: 'np_form_rendicion_detail_tot_final_det_np',
					name: 'tot_final_det_np',
					x: 10, y: 220, width: 220, readOnly: true
				},{
					fieldLabel:'Proveedor:',
					xtype: 'combobox',
					store: np.st_proveedor,
					displayField: 'desc_proveedor',
    				valueField: 'id_proveedor',
    				name: 'id_proveedor',
    				id: 'np_form_rendicion_detail_cb_proveedor',
    				queryMode: 'remote',
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{ruc_proveedor} - {desc_proveedor}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{ruc_proveedor} - {desc_proveedor}',
				        '</tpl>'
				    ),
				    x: 10, y: 250, width: 650,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {}
				    }
				},{
					fieldLabel:'Tipo Documento:',
					xtype: 'combobox',
					store: np.st_proveedor,
					displayField: 'desc_tipo_documento',
    				valueField: 'id_tipo_documento',
    				name: 'r_id_tipo_documento',
    				id: 'np_form_rendicion_detail_cb_tipo_documento',
    				queryMode: 'remote',
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{id_tipo_documento} - {nombre_tipo_documento}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{id_tipo_documento} - {nombre_tipo_documento}',
				        '</tpl>'
				    ),
				    x: 10, y: 280, width: 650,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {}
				    }
				},{
					fieldLabel: 'Fecha Documento:',
					xtype: 'datefield',
					id: 'np_form_rendicion_detail_fecha_det_np',
					name: 'r_fecha_det_np',
					x: 10, y: 310, width: 220
				},{
					fieldLabel: 'Serie y/o numero:',
					id: 'np_form_rendicion_detail_numdoc_det_np',
					name: 'r_numdoc_det_np',
					x: 10, y: 340, width: 250
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('np_form_rendicion_detail');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('np_window_rendicion_detail').close();
								Ext.getCmp('np_form_rendicion_grid_det_np').store.reload();	
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure:function(form, action){
							Ext.Msg.alert('Error', action.result.msg);
						}
					});
				}
			},{
				text: 'Cancelar', 
				handler: function () {
					Ext.getCmp('np_window_rendicion_detail').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetailRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful && records.length>0) {
						if (records[0].get('id_proveedor') == null) {
							if (default_id_proveedor != null) {
								np.st_proveedor.reload({
									params: {
										'id_proveedor': default_id_proveedor
									}
								});
							}
						} else {
							np.st_proveedor.reload({
								params: {
									'id_proveedor': records[0].get('id_proveedor')
								}
							});
						}
						var w = Ext.create('Ext.window.Window', w_detail_config);
						w.on('show', function () {
							frm = Ext.getCmp('np_form_rendicion_detail');
							frm.loadRecord(sender.getAt(0));
							var cbp = Ext.getCmp('np_form_rendicion_detail_cb_proveedor');
							if (cbp.getValue() == null) {
								cbp.setValue(default_id_proveedor);
							}
						});
						w.show();
					}
				}
			}
		});
	}
</script>