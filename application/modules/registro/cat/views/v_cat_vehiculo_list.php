<script>
	cat.cat_vehiculo_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'CORRECTO'},
			{id: 'OBSERVADO'}
		]
	});

	cat.cat_vehiculo_id_selected = 0;
	cat.cat_vehiculo_main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cat/getVehiculoList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				if (cat.cat_vehiculo_id_selected > 0) {
					Ext.getCmp('cat_vehiculo_main_grid').getSelectionModel().select(
						cat.cat_vehiculo_main_store.getAt(
							cat.cat_vehiculo_main_store.find('cat_vehiculo_id', cat.cat_vehiculo_id_selected)
						)
					);
				} else {
					Ext.getCmp('cat_vehiculo_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});

	cat.cat_vehiculo_list_window = function (doc_id) {
		var w_config = {
			title: 'Vehiculos', 
			modal: true,
			width: 950,
			height: 500, 
			id: 'cat_vehiculo_list_window',
			layout: 'border',
			items: [{
				xtype: 'grid',
				id:'cat_vehiculo_main_grid',
				region:'center', 
				split: true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Cat.', dataIndex:'cat_vehiculo_categoria', width: 40},
					{text:'Marca', dataIndex:'cat_vehiculo_marca', width: 100},
					{text:'Modelo', dataIndex:'cat_vehiculo_modelo', width: 80},
					{text:'Placa', dataIndex:'cat_vehiculo_placa', width: 65},
					{text:'Conductor', dataIndex:'cat_vehiculo_conductor_nomape', width: 150},
					{text:'Estado', dataIndex:'cat_vehiculo_estado', width: 80,
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    switch (value) {
						    	case 'OBSERVADO':
						    		metaData.tdStyle = 'color: orange;';
						    	break;
						    	case 'CORRECTO':
						    		metaData.tdStyle = 'color: green;';
						    	break;
						    }
						    return value;
						}
					}
				],
				tbar:[{
					text: 'Nuevo', handler: function() {
						var w = Ext.getCmp('cat_vehiculo_form');
						w.mask('cargando');
						Ext.create("Ext.data.Store", {
							proxy: {
								type: 'ajax',
								url: 'cat/getVehiculoNewRow',
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
							    			var frm = Ext.getCmp('cat_vehiculo_form');
							    			frm.reset();
											frm.loadRecord(record);
											Ext.getCmp('cat_vehiculo_form_title_label').setText('Nuevo Vehiculo');
											w.unmask();
							    		});

									} else {
										Ext.Msg.alert('cat_vehiculo', eOpts.getResultSet().getMessage());
									}
								}
							}
						});
					}
				},'-',{
					text: 'Eliminar', handler: function() {
						var rows = Ext.getCmp('cat_vehiculo_main_grid').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar datos del vehiculo',
							    message: 'Realmente desea eliminar el vehiculo seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
										Ext.Ajax.request({
											params:{
												cat_vehiculo_id: rows[0].get('cat_vehiculo_id')
											},
											url:'cat/cat_vehiculoDelete',
											success: function (response, opts) {
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Eliminar', result.msg);
													cat.cat_vehiculo_reload_list();
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
				store: cat.cat_vehiculo_main_store,
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('cat_vehiculo_form').reset();
						Ext.getCmp('cat_vehiculo_form_title_label').setText('Datos del Vehiculo');
						Ext.getCmp('cat_vehiculo_form').loadRecord(record);
					}
				}
			},{
				xtype: 'panel',
				region: 'east',
				layout: 'border',
				width: 350,
				split: true,
				items: [{
					xtype: 'form',
					id: 'cat_vehiculo_form',
					url: 'cat/addOrUpdateVehiculo',
					layout: 'form',
					region: 'center',
					bodyStyle: {
						//background: '#4c9dd8'
						borderTop: '1px solid silver!important;'
					},
					tbar:[{
						xtype: 'label',
						id: 'cat_vehiculo_form_title_label',
						text: 'Datos del Vehiculo',
						style: {
							fontWeight: 'bold'
						}
					},{
						id: 'cat_vehiculo_form_update_bt',
						text: 'Guardar', 
						handler: function() {
							var frm = Ext.getCmp('cat_vehiculo_form');
							frm.submit({
								success: function(form, action) {
									if (action.result.success) {
										cat.cat_vehiculo_reload_list(action.result.rowid);
										Ext.Msg.alert('Vehiculo', action.result.msg);
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
						id: 'cat_vehiculo_form_cat_vehiculo_id_field',
						name: 'cat_vehiculo_id'
					},{
						xtype: 'hidden',
						id: 'cat_vehiculo_form_cat_id_field',
						name: 'cat_id',
						value: doc_id
					},{
						xtype: 'hidden',
						id: 'cat_vehiculo_form_operation_field',
						name: 'operation'
					},{
						fieldLabel: 'Categoria',
						id: 'cat_vehiculo_form_cat_vehiculo_categoria_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_categoria'
					},{
						fieldLabel: 'Marca',
						id: 'cat_vehiculo_form_cat_vehiculo_marca_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_marca'
					},{
						fieldLabel: 'Modelo',
						id: 'cat_vehiculo_form_cat_vehiculo_modelo_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_modelo'
					},{
						fieldLabel: 'Color',
						id: 'cat_vehiculo_form_cat_vehiculo_color_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_color'
					},{
						fieldLabel: 'Placa',
						id: 'cat_vehiculo_form_cat_vehiculo_placa_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_placa'
					},{
						fieldLabel: 'Nro. Tarjeta Prop.',
						id: 'cat_vehiculo_form_cat_vehiculo_ntp_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_ntp'
					},{
						fieldLabel: 'Conductor',
						id: 'cat_vehiculo_form_cat_vehiculo_conductor_nomape_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_conductor_nomape'
					},{
						fieldLabel: 'Conductor DNI',
						id: 'cat_vehiculo_form_cat_vehiculo_conductor_dni_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_conductor_dni'
					},{
						fieldLabel: 'Conductor Nro. Licencia',
						id: 'cat_vehiculo_form_cat_vehiculo_conductor_nlc_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_conductor_nlc'
					},{
						fieldLabel: 'Estado Registro',
	    				xtype: 'combobox',
	    				id: 'cat_vehiculo_form_cat_vehiculo_estado_field',
	    				name: 'cat_vehiculo_estado',
	    				displayField: 'id',
	    				valueField: 'id',
	    				store: cat.cat_vehiculo_estado_store,
	    				queryMode: 'local',
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    	}
	    				}
					},{
						fieldLabel: 'Observacion',
						id: 'cat_vehiculo_form_cat_vehiculo_observacion_field',
	    				xtype: 'textfield',
	    				name: 'cat_vehiculo_observacion'
					}]
				}]
			}],
			listeners:{
				show: function () {
					cat.cat_vehiculo_reload_list();					
				}
			}
		};

		cat.cat_vehiculo_reload_list = function (selected_id) {
			cat.cat_vehiculo_id_selected = selected_id||0;
			//cat.main_store.reload();
			cat.cat_vehiculo_main_store.reload({
				params: {
					doc_id: doc_id
				}
			});
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>