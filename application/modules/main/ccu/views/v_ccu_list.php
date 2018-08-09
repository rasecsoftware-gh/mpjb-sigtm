<script>
	ccu.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'panel',
			region: 'center',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id:'ccu_main_grid',
				region:'center', split:true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{text:'Id', dataIndex:'usuario_id', width: 50},
					{text:'Login', dataIndex:'usuario_login', width: 80},
					{text:'Descripcion', dataIndex:'usuario_desc', width: 250},
					{text:'SA', dataIndex:'usuario_sa', width: 35},
					{text:'Estado', dataIndex:'usuario_estado', width: 60,
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
						ccu.usuario_new_window();
					}
				},{
					text: 'Eliminar', 
					handler: function() {
						ccu.usuario_delete_window();
					}
				},'-',,{
					text: 'Permisos&hellip;', 
					handler: function() {
						ccu.permiso_window();
					}
				}],
				store: ccu.main_store,
				dockedItems: [{
			        xtype: 'pagingtoolbar',
			        store: ccu.main_store, // same store GridPanel is using
			        dock: 'bottom',
			        displayInfo: true
			    }],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						ccu.usuario_id_selected = record.get('usuario_id');
						ccu.usuario_new_flag = false;
						Ext.getCmp('ccu_usuario_form').loadRecord(record);
						ccu.usuario_permiso_reload_list(record.get('usuario_id'));
					}
				}
			},{
				xtype: 'form',
				id: 'ccu_usuario_form',
				url: 'ccu/Update', // or Add (new button change this value)
				layout: 'form',
				region: 'south',
				height: 200,
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					id: 'ccu_usuario_update_bt',
					text: 'Guardar', 
					handler: function() {
						var frm = Ext.getCmp('ccu_usuario_form');
						frm.submit({
							url: (ccu.usuario_new_flag?'ccu/Add':'ccu/Update'),
							success: function(form, action) {
								if (action.result.success) {
									Ext.Msg.alert('Usuario', action.result.msg);
									ccu.reload_list(ccu.usuario_id_selected);	
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
				}],
				items: [{
					xtype: 'hidden',
					name: 'usuario_id'
				},{
					fieldLabel: 'Login',
					id: 'ccu_usuario_login_field',
    				xtype: 'textfield',
    				name: 'usuario_login'
				},{
					fieldLabel: 'Descripcion',
					id: 'ccu_usuario_desc_field',
    				xtype: 'textfield',
    				name: 'usuario_desc'
				},{
					fieldLabel: 'Password',
					id: 'ccu_usuario_pw_field',
    				xtype: 'textfield',
    				inputType: 'password',
    				name: 'usuario_pw',
    				disabled: false
				},{
					fieldLabel: 'Es Admin?',
    				xtype: 'combobox',
    				id: 'ccu_usuario_sa_field',
    				name: 'usuario_sa',
    				displayField: 'desc',
    				valueField: 'id',
    				store: ccu.yesno_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				},{
					fieldLabel: 'Estado',
    				xtype: 'combobox',
    				id: 'ccu_usuario_estado_field',
    				name: 'usuario_estado',
    				displayField: 'desc',
    				valueField: 'id',
    				store: ccu.usuario_estado_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				}]
			}]
		},{
			xtype: 'panel',
			region: 'east',
			layout: 'border',
			width: 600,
			split: true,
			items: [{
				xtype: 'grid',
				id: 'ccu_usuario_permiso_grid',
				region: 'center',
				title: 'Permisos del Usuario',
				emptyText: 'No tiene permisos registradas',
				store: ccu.usuario_permiso_store,
				sortableColumns: false,
				enableColumnHide: false,
				tbar:[{
					xtype: 'combobox',
    				id: 'ccu_usuario_permiso_permiso_id_field',
    				displayField: 'permiso_desc',
    				valueField: 'permiso_id',
    				queryMode: 'remote',
    				store: ccu.permiso_store,
    				editable: true,
				    width: 300,
				    forceSelection: false,
				    fieldStyle: 'font-weight: bold;',
				    matchFieldWidth: false,
				    listeners: {
				    	select: function (combo, record, eOpts) {
				    		Ext.getCmp('ccu_usuario_permiso_add_bt').focus();
				    	},
				    	change: function (ths, newValue, oldValue, eOpts) {
				    	}
				    }
				},{
					id: 'ccu_usuario_permiso_add_bt',
					text: 'Agregar', 
					handler: function() {
						ccu.usuario_permiso_add_window();
					}
				},{
					text: 'Quitar', 
					handler: function() {
						ccu.usuario_permiso_delete_window();
					}
				}],
				store: ccu.usuario_permiso_store,
				dockedItems: [{
			        xtype: 'pagingtoolbar',
			        store: ccu.usuario_permiso_store, // same store GridPanel is using
			        dock: 'bottom',
			        displayInfo: true
			    }],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						ccu.usuario_permiso_reload_list(record.get('usuario_id'));
					}
				},
				columns:[
					{text: 'Descripcion', dataIndex: 'permiso_desc', width: 350},
					{text: 'Accion', dataIndex: 'permiso_accion', width: 215}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('ccu_permiso_view_form').loadRecord(record);
					},
					rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
						//ccu.plani_traba_window(record);
					}
				}
			},{
				xtype: 'form',
				id: 'ccu_permiso_view_form',
				layout: 'form',
				region: 'south',
				height: 120,
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[],
				items: [{
					fieldLabel: 'Descripcion',
    				xtype: 'textfield',
    				name: 'permiso_desc',
    				readOnly: true
				},{
					fieldLabel: 'Accion',
    				xtype: 'textfield',
    				name: 'permiso_accion',
    				readOnly: true
				}]
			}]
		}],
		listeners: {
			render: function () {
				ccu.reload_list();
				ccu.permiso_store.reload();
				sys_storeLoadMonitor([ccu.main_store, ccu.permiso_store], function () {
					// nada
				});
			}
		}
	});

	ccu.usuario_new_flag = false;

	ccu.reload_list = function (select_id) {
		ccu.usuario_id_selected = select_id||0;
		//ccu.main_store.reload();
		ccu.main_store.reload({
			params: {
				//search_by: Ext.getCmp('ccu_search_by_cb').getValue(),
				//search_text: Ext.getCmp('ccu_search_text').getValue()
			}
		});
	};
	ccu.usuario_permiso_reload_list = function (usuario_id) {
		ccu.usuario_permiso_store.reload({
			params: {
				usuario_id: usuario_id
			}
		});
	};

</script>