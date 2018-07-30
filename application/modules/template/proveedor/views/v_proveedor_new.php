<script>
	proveedor.window_new = function() {
		var w_config = {
			title:'Nuevo Proveedor', 
			modal: true,
			width: 800,
			height: 300, 
			id:'proveedor_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'proveedor/Add',
				region: 'center',
				layout: 'absolute',
				id: 'proveedor_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					fieldLabel: 'RUC:',
					xtype: 'textfield',
    				name: 'ruc_proveedor',
    				x: 10, y: 10, width: 250
				},{
					fieldLabel:'Descripcion:',
					xtype: 'textfield',
    				name: 'desc_proveedor',
    				id: 'proveedor_form_desc_proveedor',
				    x: 10, y: 40, width: 650
				},{
					fieldLabel:'Rep. Legal:',
					xtype: 'textfield',
    				name: 'repleg_proveedor',
    				id: 'proveedor_form_repleg_proveedor',
				    x: 10, y: 70, width: 650
				},{
					fieldLabel: 'Direccion:',
					id: 'proveedor_form_dir_proveedor',
					name: 'dir_proveedor',
					x: 10, y: 100, width: 650
				},{
					fieldLabel: 'Telefono:',
					id: 'proveedor_form_telfono_proveedor',
					name: 'telefono_proveedor',
					x: 10, y: 130, width: 250
				},{
					fieldLabel:'Correo Elec.:',
					id: 'proveedor_form_correo_proveedor',
					name:'correo_proveedor',
					x: 10, y: 160, width: 350
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('proveedor_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('proveedor_window_new').close();
								proveedor.window_edit(action.result.rowid);
								proveedor.st_grid_main.reload();	
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
					win = Ext.getCmp('proveedor_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'proveedor/getNewRow',
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
						frm = Ext.getCmp('proveedor_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>