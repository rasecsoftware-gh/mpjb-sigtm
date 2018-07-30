<script>
	comprador.window_edit = function(id) {
		var st_comprador_cb = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'comprador/getCompradorCBList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var grid = Ext.getCmp('comprador_form_grid_comprador_cb');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'comprador', 
			modal: true,
			width: 800,
			height: 600, 
			id:'comprador_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'comprador/Update',
				region: 'north',
				layout: 'absolute',
				id: 'comprador_form',
				height: 150,
				defaultType:'textfield',
				defaults: {
					labelWidth: 110
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_comprador'
				},{
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
    				name: 'estado_comprador',
    				id: 'comprador_form_cb_estado_comprador',
    				queryMode: 'local',
				    x: 10, y: 70, width: 230,
				    listeners: {
				    	select: function ( combo, record, eOpts ) { }
				    }
				}]
			},{
				xtype: 'grid',
				id: 'comprador_form_grid_comprador_cb',
				region: 'center', 
				//forceFit:true,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Banco', dataIndex:'desc_banco', width: 200},
					{text:'Moneda', dataIndex:'desc_moneda', width: 150},
					{text:'Numero', dataIndex:'nro_comprador_cb', width: 200},
					{text:'Estado', dataIndex:'estado_comprador_cb', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Cuenta Bancaria: '
				},{
					text:'Agregar', 
					handler: function() {
						comprador.window_comprador_cb_new(id);
					}
				},{
					text:'Modificar', 
					handler: function () {
						rows = Ext.getCmp('comprador_form_grid_comprador_cb').getSelection();
						if (rows.length>0) {
							comprador.window_comprador_cb_edit(rows[0].get('id_comprador_cb'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text:'Eliminar',
					handler: function () {
						rows = Ext.getCmp('comprador_form_grid_comprador_cb').getSelection();
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
												id: rows[0].get('id_comprador_cb')
											},
											url:'comprador/deleteCompradorCB',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													st_comprador_cb.reload();
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
				store: st_comprador_cb,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('comprador_form');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('comprador_window_edit').close();
								comprador.st_grid_main.reload();	
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
				text:'Salir',
				handler:function() {
					Ext.getCmp('comprador_window_edit').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'comprador/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						st_comprador_cb.reload({
			    			params: {
			    				id_comprador: records[0].get('id_comprador')
			    			}
			    		});
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