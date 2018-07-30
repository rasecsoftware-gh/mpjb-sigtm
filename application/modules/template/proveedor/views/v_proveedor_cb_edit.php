<script type="text/javascript">
proveedor.window_proveedor_cb_edit = function(id) {

		var w_detail_config = {
			title:'Cuenta Bancaria', 
			modal: true,
			width: 800,
			height: 300, 
			id:'proveedor_window_proveedor_cb',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'proveedor/updateProveedorCB',
				region: 'center',
				layout: 'absolute',
				id: 'proveedor_form_proveedor_cb',
				height: 160,
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_proveedor_cb'
				},{
					xtype: 'hiddenfield', name: 'id_proveedor'
				},{
					fieldLabel:'Banco:',
					xtype: 'combobox',
					store: proveedor.st_banco,
					displayField: 'desc_banco',
    				valueField: 'id_banco',
    				name: 'id_banco',
    				id: 'proveedor_form_proveedor_cb_cb_banco',
    				queryMode: 'local',
				    x: 10, y: 10, width: 500,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				},{
					fieldLabel:'Moneda:',
					xtype: 'combobox',
					store: proveedor.st_moneda,
					displayField: 'desc_moneda',
    				valueField: 'id_moneda',
    				name: 'id_moneda',
    				id: 'proveedor_form_proveedor_cb_cb_moneda',
    				queryMode: 'local',
				    x: 10, y: 40, width: 300,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				},{
					fieldLabel:'Nro. Cuenta:',
    				name: 'nro_proveedor_cb',
    				x: 10, y: 70, width: 300
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
    				name: 'estado_proveedor_cb',
    				id: 'proveedor_form_proveedor_cb_cb_estado_proveedor_cb',
    				queryMode: 'local',
				    x: 10, y: 100, width: 230,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('proveedor_form_proveedor_cb');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action){
							if (action.result.success) {
								Ext.getCmp('proveedor_window_proveedor_cb').close();
								Ext.getCmp('proveedor_form_grid_proveedor_cb').store.reload();	
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
					Ext.getCmp('proveedor_window_proveedor_cb').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'proveedor/getProveedorCBRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_detail_config);
						frm = Ext.getCmp('proveedor_form_proveedor_cb');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>