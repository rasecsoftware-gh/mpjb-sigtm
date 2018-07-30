<script>
	comprador.window_new = function() {
		var w_config = {
			title:'Nuevo Comprador', 
			modal: true,
			width: 800,
			height: 200, 
			id:'comprador_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'comprador/Add',
				region: 'center',
				layout: 'absolute',
				id: 'comprador_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					fieldLabel: 'Codigo:',
					xtype: 'textfield',
    				name: 'cod_comprador',
    				x: 10, y: 10, width: 250
				},{
					fieldLabel:'Descripcion:',
					xtype: 'textfield',
    				name: 'desc_comprador',
    				id: 'comprador_form_desc_comprador',
				    x: 10, y: 40, width: 650
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('comprador_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('comprador_window_new').close();
								comprador.window_edit(action.result.rowid);
								comprador.st_grid_main.reload();	
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
					win = Ext.getCmp('comprador_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'comprador/getNewRow',
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
						frm = Ext.getCmp('comprador_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>