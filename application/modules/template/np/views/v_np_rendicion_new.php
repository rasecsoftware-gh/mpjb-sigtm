<script>
	np.window_rendicion_np_new = function(id_np) {

		var w_config = {
			title:'Nuevo Comprobante de Pago', 
			modal: true,
			width: 700,
			height: 230, 
			id:'np_window_rendicion_np',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/addRendicionNP',
				region: 'center',
				layout: 'absolute',
				id: 'np_form_rendicion_np',
				defaultType:'textfield',
				defaults: {
					labelWidth: 130
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_np',
    				value: id_np
				},{
					fieldLabel:'Tipo Documento:',
					xtype: 'combobox',
					store: np.st_tipo_documento,
					displayField: 'desc_tipo_documento',
    				valueField: 'id_tipo_documento',
    				name: 'id_tipo_documento',
    				id: 'np_form_rendicion_np_cb_tipo_documento',
    				queryMode: 'local',
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
				    x: 10, y: 10, width: 650,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {}
				    }
				},{
					fieldLabel:'Proveedor:',
					xtype: 'combobox',
					store: np.st_proveedor,
					displayField: 'desc_proveedor',
    				valueField: 'id_proveedor',
    				name: 'id_proveedor',
    				id: 'np_form_rendicion_np_cb_proveedor',
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
				    x: 10, y: 40, width: 650,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'np_form_rendicion_np_fechadoc_rendicion_np',
					name: 'fechadoc_rendicion_np',
					format: 'd/m/Y',
					x: 10, y: 70, width: 250
				},{
					fieldLabel: 'Serie y/o numero:',
					id: 'np_form_rendicion_np_numdoc_rendicion_np',
					name: 'numdoc_rendicion_np',
					x: 10, y: 100, width: 250
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('np_form_rendicion_np');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('np_window_rendicion_np').close();
								Ext.getCmp('np_form_rendicion_grid_rendicion_np').store.reload();
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
					Ext.getCmp('np_window_rendicion_np').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getRendicionNPNewRow',
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
						frm = Ext.getCmp('np_form_rendicion_np');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>