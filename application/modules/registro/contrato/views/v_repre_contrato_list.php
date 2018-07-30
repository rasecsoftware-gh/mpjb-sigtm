<script>
	contrato.repre_contrato_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'ACTIVO', desc: 'ACTIVO'},
			{id: 'INACTIVO', desc: 'INACTIVO'}
		]
	});

	contrato.repre_contrato_id_selected = 0;
	contrato.repre_contrato_main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getRepreContratoFullList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				if (contrato.repre_contrato_id_selected > 0) {
					Ext.getCmp('repre_contrato_main_grid').getSelectionModel().select(
						contrato.repre_contrato_main_store.getAt(
							contrato.repre_contrato_main_store.find('repre_contrato_id', contrato.repre_contrato_id_selected)
						)
					);
				} else {
					Ext.getCmp('repre_contrato_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});

	contrato.repre_contrato_window = function () {
		var w_config = {
			title: 'Representantes', 
			modal: true,
			width: 650,
			height: 500, 
			id: 'repre_contrato_window',
			layout: 'border',
			items: [{
				xtype: 'grid',
				id:'repre_contrato_main_grid',
				region:'center', split: true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{text:'DNI', dataIndex:'repre_contrato_dni', width: 70},
					{text:'GA', dataIndex:'repre_contrato_gradoacad', width: 50},
					{text:'Nombres', dataIndex:'repre_contrato_apenom', width: 250},
					{text:'Fecha Inicio', dataIndex:'repre_contrato_fecha', width: 80},
					{text:'Fecha Termino', dataIndex:'repre_contrato_fecha_fin', width: 80},
					{text:'Estado', dataIndex:'repre_contrato_estado', width: 70,
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    switch (value) {
						    	case 'INACTIVO':
						    		value = '<span style="color: red;">'+value+'</span>';
						    	break;
						    	case 'ACTIVO':
						    		value = '<span style="color: green;">'+value+'</span>';
						    	break;
						    }
						    return value;
						}
					}
				],
				tbar:[{
					text: 'Nuevo', handler: function() {
						var w = Ext.getCmp('repre_contrato_form');
						w.mask('cargando');
						Ext.create("Ext.data.Store", {
							proxy: {
								type: 'ajax',
								url: 'contrato/getRepreContratoNewRow',
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
							    			var frm = Ext.getCmp('repre_contrato_form');
											frm.loadRecord(record);
											Ext.getCmp('repre_contrato_dni_field').focus();
											w.unmask();
							    		});

									} else {
										Ext.Msg.alert('Representante', eOpts.getResultSet().getMessage());
									}
								}
							}
						});
					}, hidden: <?php echo sys_session_hasRole(array('sa', 'contrato'))?'false':'true'; ?>
				},{
					text: 'Guardar', 
					handler: function() {
						var frm = Ext.getCmp('repre_contrato_form');
						frm.submit({
							success: function(form, action) {
								if (action.result.success) {
									contrato.repre_contrato_reload_list(action.result.rowid);
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
				},'-',{
					text: 'Eliminar', handler: function() {
						var rows = Ext.getCmp('repre_contrato_main_grid').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar Representante',
							    message: 'Realmente desea eliminar el registro seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
										Ext.Ajax.request({
											params:{
												repre_contrato_id: rows[0].get('repre_contrato_id')
											},
											url:'contrato/repreContratoDelete',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Eliminar', result.msg);
													contrato.repre_contrato_reload_list();
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
				store: contrato.repre_contrato_main_store,
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('repre_contrato_form').loadRecord(record);
					}
				}
			},{
				xtype: 'panel',
				region: 'south',
				layout: 'border',
				height: 230,
				split: true,
				items: [{
					xtype: 'form',
					id: 'repre_contrato_form',
					url: 'contrato/repreContratoUpdate',
					layout: 'form',
					region: 'center',
					bodyStyle: {
						//background: '#4c9dd8'
					},
					items: [{
						xtype: 'hidden',
						name: 'repre_contrato_id'
					},{
						fieldLabel: 'DNI',
						id: 'repre_contrato_dni_field',
	    				xtype: 'textfield',
	    				name: 'repre_contrato_dni'
					},{
						fieldLabel: 'Apellidos y Nombres',
						id: 'repre_contrato_apenom_field',
	    				xtype: 'textfield',
	    				name: 'repre_contrato_apenom'
					},{
						fieldLabel: 'Grado Academico',
						id: 'repre_contrato_gradoacad_field',
	    				xtype: 'textfield',
	    				name: 'repre_contrato_gradoacad'
					},{
						fieldLabel: 'Doc. Ref. del Cargo',
						id: 'repre_contrato_docref_field',
	    				xtype: 'textfield',
	    				name: 'repre_contrato_docref'
					},{
						fieldLabel: 'Cargo',
	    				xtype: 'textfield',
	    				id: 'repre_contrato_cargo_field',
	    				name: 'repre_contrato_cargo'
					},{
						fieldLabel: 'Fecha Inicio',
	    				xtype: 'datefield',
	    				id: 'repre_contrato_fecha_field',
	    				name: 'repre_contrato_fecha'
					},{
						fieldLabel: 'Estado',
	    				xtype: 'combobox',
	    				id: 'repre_contrato_estado_field',
	    				name: 'repre_contrato_estado',
	    				displayField: 'desc',
	    				valueField: 'id',
	    				store: contrato.repre_contrato_estado_store,
	    				queryMode: 'local',
	    				matchFieldWidth: false,
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    	}
	    				}
					}]
				}]
			}],
			listeners:{
				show: function () {
					contrato.repre_contrato_reload_list();					
				}
			}
		};

		contrato.repre_contrato_reload_list = function (selected_id) {
			contrato.repre_contrato_id_selected = selected_id||0;
			//contrato.main_store.reload();
			contrato.repre_contrato_main_store.reload({
				params: {
					search_by: ''
				}
			});
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>