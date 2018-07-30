<script>
	np.window_new = function() {
		var w_config = {
			title:'Nueva Nota de Pedido', 
			modal: true,
			width: 800,
			height: 310, 
			id:'np_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'np/Add',
				region: 'center',
				layout: 'absolute',
				id: 'np_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 135
				},
				items:[{
					xtype: 'hiddenfield',
					id: 'np_form_cod_obra',
    				name: 'cod_obra'
				},{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_np',
    				x: 10, y: 10, width: 250, disabled: true
				},{
					fieldLabel:'Centro de Costo:',
					xtype: 'combobox',
					store: np.st_frente,
					displayField: 'desc_frente',
    				valueField: 'id_frente',
    				name: 'id_frente',
    				id: 'np_form_cb_frente',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_obra} - {desc_obra}</div><div>{desc_frente}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_obra} - {desc_obra}',
				        '</tpl>'
				    ),
				    x: 10, y: 40, width: 750,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('np_form_cod_obra').setValue(record.get('cod_obra'));
				    		Ext.getCmp('np_form_desc_frente').setValue(record.get('desc_frente'));
				    		Ext.getCmp('np_form_fecha').focus();
				    	}
				    }
				},{
					fieldLabel:'Frente:',
					xtype: 'textfield',
    				name: 'desc_frente',
    				id: 'np_form_desc_frente',
				    x: 10, y: 70, width: 650,
				    readOnly: true
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'np_form_fecha',
					name: 'fecha_np',
					format: 'd/m/Y',
					x: 10, y: 100, width: 250
				},{
					fieldLabel:'Comprador:',
					xtype: 'combobox',
					store: np.st_comprador,
					displayField: 'desc_comprador',
    				valueField: 'id_comprador',
    				name: 'id_comprador',
    				id: 'np_form_cb_comprador',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_comprador} - {desc_comprador}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_comprador} - {desc_comprador}',
				        '</tpl>'
				    ),
				    x: 10, y: 130, width: 750,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('np_form_desc_np').focus();
				    	}
				    }
				},{
					fieldLabel:'Descripcion:',
					id: 'np_form_desc_np',
					name:'desc_np',
					x: 10, y: 160, width: 650
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('np_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('np_window_new').close();
								np.window_edit(action.result.rowid);
								np.st_grid_main.reload();	
							} else {
								Ext.Msg.alert('Error',action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Error',action.result.msg);
						}
					});
				}
			},{
				text:'Cancelar',handler:function() {
					win = Ext.getCmp('np_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getNewRow',
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
						frm = Ext.getCmp('np_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>