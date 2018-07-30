<script type="text/javascript">
comprador.window_comprador_cb_edit = function(id) {

		var w_detail_config = {
			title:'Cuenta Bancaria', 
			modal: true,
			width: 800,
			height: 300, 
			id:'comprador_window_comprador_cb',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'comprador/updateCompradorCB',
				region: 'center',
				layout: 'absolute',
				id: 'comprador_form_comprador_cb',
				height: 160,
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_comprador_cb'
				},{
					xtype: 'hiddenfield', name: 'id_comprador'
				},{
					fieldLabel:'Banco:',
					xtype: 'combobox',
					store: comprador.st_banco,
					displayField: 'desc_banco',
    				valueField: 'id_banco',
    				name: 'id_banco',
    				id: 'comprador_form_comprador_cb_cb_banco',
    				queryMode: 'local',
				    x: 10, y: 10, width: 500,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				},{
					fieldLabel:'Moneda:',
					xtype: 'combobox',
					store: comprador.st_moneda,
					displayField: 'desc_moneda',
    				valueField: 'id_moneda',
    				name: 'id_moneda',
    				id: 'comprador_form_comprador_cb_cb_moneda',
    				queryMode: 'local',
				    x: 10, y: 40, width: 300,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				},{
					fieldLabel:'Nro. Cuenta:',
    				name: 'nro_comprador_cb',
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
    				name: 'estado_comprador_cb',
    				id: 'comprador_form_comprador_cb_cb_estado_comprador_cb',
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
					frm = Ext.getCmp('comprador_form_comprador_cb');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action){
							if (action.result.success) {
								Ext.getCmp('comprador_window_comprador_cb').close();
								Ext.getCmp('comprador_form_grid_comprador_cb').store.reload();	
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
					Ext.getCmp('comprador_window_comprador_cb').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'comprador/getCompradorCBRow/'+id,
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
						frm = Ext.getCmp('comprador_form_comprador_cb');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>