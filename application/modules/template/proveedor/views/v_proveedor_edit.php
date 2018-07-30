<script>
	proveedor.window_edit = function(id) {
		var st_proveedor_cb = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'proveedor/getProveedorCBList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var grid = Ext.getCmp('proveedor_form_grid_proveedor_cb');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Proveedor', 
			modal: true,
			width: 800,
			height: 600, 
			id:'proveedor_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'proveedor/Update',
				region: 'north',
				layout: 'absolute',
				id: 'proveedor_form',
				height: 220,
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_proveedor'
				},{
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
					x: 10, y: 160, width: 300
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
    				name: 'estado_proveedor',
    				id: 'proveedor_form_cb_estado_proveedor',
    				queryMode: 'local',
				    x: 10, y: 190, width: 230,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				}]
			},{
				xtype: 'grid',
				id: 'proveedor_form_grid_proveedor_cb',
				region: 'center', 
				//forceFit:true,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Banco', dataIndex:'desc_banco', width: 200},
					{text:'Moneda', dataIndex:'desc_moneda', width: 150},
					{text:'Numero', dataIndex:'nro_proveedor_cb', width: 200},
					{text:'Estado', dataIndex:'estado_proveedor_cb', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Cuenta Bancaria: '
				},{
					text:'Agregar', 
					handler: function() {
						proveedor.window_proveedor_cb_new(id);
					}
				},{
					text:'Modificar', 
					handler: function () {
						rows = Ext.getCmp('proveedor_form_grid_proveedor_cb').getSelection();
						if (rows.length>0) {
							proveedor.window_proveedor_cb_edit(rows[0].get('id_proveedor_cb'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text:'Eliminar',
					handler: function () {
						rows = Ext.getCmp('proveedor_form_grid_proveedor_cb').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar Cuenta?',
							    message: 'Realmente desea eliminar el registro seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							   			Ext.Ajax.request({
											params:{
												id: rows[0].get('id_proveedor_cb')
											},
											url:'proveedor/deleteProveedorCB',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													st_proveedor_cb.reload();
												} else {
													Ext.Msg.alert('Error', result.msg);
												}
											},
											failure: function (response, opts){
												Ext.Msg.alert('Error', 'Error en la conexion de datos');
											}
										});         
							        } else {
							            console.log('Cancel pressed');
							        } 
							    }
							});
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}],
				store: st_proveedor_cb,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('proveedor_form');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('proveedor_window_edit').close();
								proveedor.st_grid_main.reload();	
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
				text:'Salir',
				handler:function() {
					Ext.getCmp('proveedor_window_edit').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'proveedor/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						st_proveedor_cb.reload({
			    			params: {
			    				id_proveedor: records[0].get('id_proveedor')
			    			}
			    		});
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