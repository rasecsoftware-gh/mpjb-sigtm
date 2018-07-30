<script>
	req.window_new = function() {
		var w_config = {
			title:'Nuevo Requerimiento', 
			modal: true,
			width: 800,
			height: 280, 
			id:'req_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'req/Add',
				region: 'center',
				layout: 'absolute',
				id: 'req_form_new',
				defaultType:'textfield',
				items:[{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_requer',
    				x: 10, y: 20, width: 220, disabled: true
				},{
					fieldLabel:'Centro de Costo:',
					xtype: 'combobox',
					store: req.st_obra,
					displayField: 'desc_obra',
    				valueField: 'cod_obra',
    				name: 'cod_obra',
    				id: 'req_form_cb_obra',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_obra} - {desc_obra}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_obra} - {desc_obra}',
				        '</tpl>'
				    ),
				    x: 10, y: 50, width: 720,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		req.st_frente.reload({
				    			params: {
				    				cod_obra: record.get('cod_obra')
				    			}
				    		});
				    		Ext.getCmp('req_form_cb_frente').focus();
				    	}
				    }
				},{
					fieldLabel:'Frente:',
					xtype: 'combobox',
    				name: 'id_frente',
    				id: 'req_form_cb_frente',
    				displayField: 'desc_frente',
    				valueField: 'id_frente',
    				queryMode: 'local',
    				store: req.st_frente,
				    x: 10, y: 80, width: 620,
				    forceSelection: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('req_form_fecha').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'req_form_fecha',
					name: 'fecha_requer',
					format: 'd/m/Y',
					x: 10, y: 110, width: 220
				},{
					fieldLabel:'Descripcion:',
					name:'desc_requer',
					x: 10, y: 140, width: 620
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function (){
					frm = Ext.getCmp('req_form_new');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('req_window_new').close();
								req.window_edit(action.result.rowid);
								req.st_grid_main.reload();	
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
					win = Ext.getCmp('req_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'req/getNewRow',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						req.st_frente.removeAll();
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('req_form_new');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>