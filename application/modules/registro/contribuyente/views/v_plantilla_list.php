<script>
	contrato.plantilla_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'A', desc: 'ACTIVO'},
			{id: 'I', desc: 'INACTIVO'}
		]
	});

	contrato.plantilla_id_selected = 0;
	contrato.plantilla_main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'contrato/getPlantillaFullList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				if (contrato.plantilla_id_selected > 0) {
					Ext.getCmp('plantilla_main_grid').getSelectionModel().select(
						contrato.plantilla_main_store.getAt(
							contrato.plantilla_main_store.find('plantilla_id', contrato.plantilla_id_selected)
						)
					);
				} else {
					Ext.getCmp('plantilla_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});

	contrato.plantilla_window = function () {
		var w_config = {
			title: 'Plantillas por Tipo de Contrato', 
			modal: true,
			width: 650,
			height: 500, 
			id: 'plantilla_window',
			layout: 'border',
			items: [{
				xtype: 'grid',
				id:'plantilla_main_grid',
				region:'center', split: true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{text:'Tipo Contrato', dataIndex:'tipo_contrato_abrev', width: 70},
					{text:'Descripcion', dataIndex:'plantilla_desc', width: 180},
					{text:'Archivo', dataIndex:'plantilla_archivo', width: 120},
					{text:'Estado', dataIndex:'plantilla_estado', width: 70,
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
					text: 'Nuevo', handler: function() {
						var w = Ext.getCmp('plantilla_form');
						w.mask('cargando');
						Ext.create("Ext.data.Store", {
							proxy: {
								type: 'ajax',
								url: 'contrato/getPlantillaNewRow',
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
							    			var frm = Ext.getCmp('plantilla_form');
							    			frm.reset();
											frm.loadRecord(record);
											Ext.getCmp('plantilla_desc_field').focus();
											w.unmask();
							    		});

									} else {
										Ext.Msg.alert('Plantilla', eOpts.getResultSet().getMessage());
									}
								}
							}
						});
					}, hidden: <?php echo sys_session_hasRole(array('sa', 'contrato'))?'false':'true'; ?>
				},{
					text: 'Guardar', 
					handler: function() {
						var frm = Ext.getCmp('plantilla_form');
						frm.submit({
							success: function(form, action) {
								if (action.result.success) {
									contrato.plantilla_reload_list(action.result.rowid);
									Ext.Msg.alert('Plantilla', action.result.msg);
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
						var rows = Ext.getCmp('plantilla_main_grid').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar Plantilla',
							    message: 'Realmente desea eliminar el registro seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
										Ext.Ajax.request({
											params:{
												plantilla_id: rows[0].get('plantilla_id')
											},
											url:'contrato/plantillaDelete',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Eliminar', result.msg);
													contrato.plantilla_reload_list();
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
				store: contrato.plantilla_main_store,
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('plantilla_form').reset();
						Ext.getCmp('plantilla_form').loadRecord(record);
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
					id: 'plantilla_form',
					url: 'contrato/plantillaUpdate',
					layout: 'form',
					region: 'center',
					bodyStyle: {
						//background: '#4c9dd8'
					},
					items: [{
						xtype: 'hidden',
						name: 'plantilla_id'
					},{
						fieldLabel: 'Descripcion',
						id: 'plantilla_desc_field',
	    				xtype: 'textfield',
	    				name: 'plantilla_desc'
					},{
						fieldLabel: 'Archivo',
						id: 'plantilla_archivo_link_field',
	    				xtype: 'displayfield',
	    				name: 'plantilla_archivo_link'
					},{
						fieldLabel: 'Subir/Cambiar Archivo',
						id: 'plantilla_file_field',
	    				xtype: 'filefield',
	    				name: 'plantilla_file'
					},{
						fieldLabel: 'Tipo Contrato',
	    				xtype: 'combobox',
	    				id: 'plantilla_tipo_contrato_id_field',
	    				name: 'tipo_contrato_id',
	    				displayField: 'tipo_contrato_desc',
	    				valueField: 'tipo_contrato_id',
	    				store: contrato.tipo_contrato_store,
	    				queryMode: 'local',
	    				matchFieldWidth: false,
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    	}
	    				}
					},{
						fieldLabel: 'Estado',
	    				xtype: 'combobox',
	    				id: 'plantilla_estado_field',
	    				name: 'plantilla_estado',
	    				displayField: 'desc',
	    				valueField: 'id',
	    				store: contrato.plantilla_estado_store,
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
					contrato.plantilla_reload_list();					
				}
			}
		};

		contrato.plantilla_reload_list = function (selected_id) {
			contrato.plantilla_id_selected = selected_id||0;
			//contrato.main_store.reload();
			contrato.plantilla_main_store.reload({
				params: {
					search_by: ''
				}
			});
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>