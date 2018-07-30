<script>
	clapre.window_new = function(cod_obra) {
		var w_config = {
			title:'Nuevo Clasificador Presupuestal', 
			modal: true,
			width: 800,
			height: 150, 
			id:'clapre_window',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'clapre/Add',
				region: 'center',
				layout: 'absolute',
				id: 'clapre_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'cod_obra',
    				value: cod_obra
				},{
					fieldLabel: 'Codigo:',
					xtype: 'textfield',
    				name: 'cod_clapre',
    				x: 10, y: 10, width: 250
				},{
					fieldLabel:'Descripcion:',
					xtype: 'textfield',
    				name: 'desc_clapre',
    				id: 'clapre_form_desc_clapre',
				    x: 10, y: 40, width: 650
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function () {
					frm = Ext.getCmp('clapre_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('clapre_window').close();
								clapre.reload_list();
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
				text:'Cancelar', handler:function() {
					Ext.getCmp('clapre_window').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'clapre/getNewRow',
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
						frm = Ext.getCmp('clapre_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>