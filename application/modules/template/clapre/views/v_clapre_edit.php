<script>
	clapre.window_edit = function(id) {
		var w_config = {
			title:'Clasificador Presupuestal', 
			modal: true,
			width: 800,
			height: 200, 
			id:'clapre_window',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'clapre/Update',
				region: 'center',
				layout: 'absolute',
				id: 'clapre_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_clapre',
    				value: id
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
				},{
					fieldLabel:'Estado:',
					xtype: 'combobox',
					store: Ext.create('Ext.data.Store', {
					    fields: ['id_estado', 'desc_estado'],
					    data : [
					        {"id_estado":"A", "desc_estado":"Activo"},
					        {"id_estado":"I", "desc_estado":"Inactivo"}
					    ]
					}),
					displayField: 'desc_estado',
    				valueField: 'id_estado',
    				name: 'estado_clapre',
    				id: 'clapre_form_cb_estado_clapre',
    				queryMode: 'local',
				    x: 10, y: 70, width: 230,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
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
				url:'clapre/getRow/'+id,
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