<script>
	ccu.permiso_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'ACTIVO'},
			{id: 'I', desc: 'INACTIVO'}
		]
	});

	ccu.permiso_id_selected = 0;
	ccu.permiso_main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'ccu/getPermisoList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				if (ccu.permiso_id_selected > 0) {
					Ext.getCmp('permiso_main_grid').getSelectionModel().select(
						ccu.permiso_main_store.getAt(
							ccu.permiso_main_store.find('permiso_id', ccu.permiso_id_selected)
						)
					);
				} else {
					Ext.getCmp('permiso_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});

	ccu.permiso_window = function () {
		var w_config = {
			title: 'Permisos del sistema', 
			modal: true,
			width: 650,
			height: 500, 
			id: 'permiso_window',
			layout: 'border',
			items: [{
				xtype: 'grid',
				id: 'permiso_main_grid',
				region: 'center', split: true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{text: 'Id', dataIndex: 'permiso_id', width: 40},
					{text: 'Descripcion', dataIndex: 'permiso_desc', width: 330},
					{text: 'Accion', dataIndex: 'permiso_accion', width: 170},
					{text: 'Estado', dataIndex: 'permiso_estado', width: 70,
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    switch (value) {
						    	case 'I':
						    		value = '<span style="color: red;">'+value+'</span>';
						    	break;
						    	case 'A':
						    		value = '<span style="color: green;">'+value+'</span>';
						    	break;
						    }
						    return value;
						}
					}
				],
				tbar:[{
					text: 'Nuevo', 
					handler: function() {
						var w = Ext.getCmp('permiso_form');
						w.mask('cargando');
						Ext.create("Ext.data.Store", {
							proxy: {
								type: 'ajax',
								url: 'ccu/getPermisoNewRow',
								reader:{
									type: 'json',
									rootProperty: 'data',
									messageProperty: 'msg'
								}
							},
							autoLoad: true,
							listeners: {
								load: function (sender, records, successful, eOpts) {
									if (successful) {
										var record = sender.getAt(0);
										sys_storeLoadMonitor([], function () {
							    			var frm = Ext.getCmp('permiso_form');
							    			frm.reset();
											frm.loadRecord(record);
											Ext.getCmp('permiso_desc_field').focus();
											ccu.permiso_new_flag = true;
											w.unmask();
							    		});

									} else {
										Ext.Msg.alert('Permiso', eOpts.getResultSet().getMessage());
									}
								}
							}
						});
					}
				},'-',{
					text: 'Eliminar', handler: function() {
						var rows = Ext.getCmp('permiso_main_grid').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar permiso',
							    message: 'Realmente desea eliminar el registro seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
										Ext.Ajax.request({
											params:{
												permiso_id: rows[0].get('permiso_id')
											},
											url:'ccu/permisoDelete',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Eliminar', result.msg);
													ccu.permiso_reload_list();
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
				store: ccu.permiso_main_store,
				listeners:{
					select: function (ths, record, index, eOpts ) {
						var f = Ext.getCmp('permiso_form'); 
						f.reset(); 
						f.loadRecord(record);
						ccu.permiso_new_flag = false;
					},
					rowdblclick: function (ths, record, tr, rowIndex, e, eOpts ) {
						var f = Ext.getCmp('permiso_form'); 
						f.reset(); 
						f.loadRecord(record);
						ccu.permiso_new_flag = false;
					}
				}
			},{
				xtype: 'panel',
				region: 'south',
				layout: 'border',
				height: 180,
				split: true,
				items: [{
					xtype: 'form',
					id: 'permiso_form',
					url: 'ccu/permisoUpdate', // ccu/permisoAdd
					layout: 'form',
					region: 'center',
					bodyStyle: {
						//background: '#4c9dd8'
					},
					items: [{
						xtype: 'hidden',
						name: 'permiso_id'
					},{
						fieldLabel: 'Descripcion',
						id: 'permiso_desc_field',
	    				xtype: 'textfield',
	    				name: 'permiso_desc'
					},{
						fieldLabel: 'Accion',
						id: 'permiso_accion_field',
	    				xtype: 'textfield',
	    				name: 'permiso_accion'
					},{
						fieldLabel: 'Estado',
	    				xtype: 'combobox',
	    				id: 'permiso_estado_field',
	    				name: 'permiso_estado',
	    				displayField: 'desc',
	    				valueField: 'id',
	    				store: ccu.permiso_estado_store,
	    				queryMode: 'local',
	    				matchFieldWidth: false,
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    	}
	    				}
					}],
					tbar:[{
						text: 'Guardar', 
						handler: function() {
							var frm = Ext.getCmp('permiso_form');
							frm.submit({
								url: (ccu.permiso_new_flag?'ccu/permisoAdd':'ccu/permisoUpdate'),
								success: function(form, action) {
									if (action.result.success) {
										ccu.permiso_reload_list(action.result.rowid);
										Ext.Msg.alert('Permiso', action.result.msg);
									} else {
										Ext.Msg.alert('Error', action.result.msg);
									}
								},
								failure: function(form, action) {
									Ext.Msg.alert('Guardar', action.result.msg, function () {
										sys_focus(action.result.target_id);
									});
								}
							});
						}
					}]
				}]
			}],
			listeners:{
				show: function () {
					ccu.permiso_reload_list();					
				}
			}
		};

		ccu.permiso_new_flag = false;

		ccu.permiso_reload_list = function (selected_id) {
			ccu.permiso_id_selected = selected_id||0;
			//ccu.main_store.reload();
			ccu.permiso_main_store.reload({
				params: {
					search_by: ''
				}
			});
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>