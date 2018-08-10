<script>
	psp.psp_vehiculo_estado_store = Ext.create('Ext.data.Store', {
		data : [
			{id: 'CORRECTO'},
			{id: 'OBSERVADO'}
		]
	});

	psp.psp_vehiculo_id_selected = 0;
	psp.psp_vehiculo_main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'psp/getVehiculoList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				if (psp.psp_vehiculo_id_selected > 0) {
					Ext.getCmp('psp_vehiculo_main_grid').getSelectionModel().select(
						psp.psp_vehiculo_main_store.getAt(
							psp.psp_vehiculo_main_store.find('psp_vehiculo_id', psp.psp_vehiculo_id_selected)
						)
					);
				} else {
					Ext.getCmp('psp_vehiculo_main_grid').getSelectionModel().selectAll();
				}
			}
		}
	});

	psp.psp_vehiculo_list_window = function (doc_id) {
		var w_config = {
			title: 'Vehiculos', 
			modal: true,
			width: 950,
			height: 500, 
			id: 'psp_vehiculo_list_window',
			layout: 'border',
			items: [{
				xtype: 'grid',
				id:'psp_vehiculo_main_grid',
				region:'center', 
				split: true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Cat.', dataIndex:'psp_vehiculo_categoria', width: 40},
					{text:'Marca', dataIndex:'psp_vehiculo_marca', width: 100},
					{text:'Modelo', dataIndex:'psp_vehiculo_modelo', width: 80},
					{text:'Placa', dataIndex:'psp_vehiculo_placa', width: 65},
					{text:'Conductor', dataIndex:'psp_vehiculo_conductor_nomape', width: 150},
					{text:'Estado', dataIndex:'psp_vehiculo_estado', width: 80,
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
						var w = Ext.getCmp('psp_vehiculo_form');
						w.mask('cargando');
						Ext.create("Ext.data.Store", {
							proxy: {
								type: 'ajax',
								url: 'psp/getVehiculoNewRow',
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
							    			var frm = Ext.getCmp('psp_vehiculo_form');
							    			frm.reset();
											frm.loadRecord(record);
											Ext.getCmp('psp_vehiculo_form_title_label').setText('Nuevo Vehiculo');
											w.unmask();
							    		});

									} else {
										Ext.Msg.alert('psp_vehiculo', eOpts.getResultSet().getMessage());
									}
								}
							}
						});
					}
				},'-',{
					text: 'Eliminar', handler: function() {
						var rows = Ext.getCmp('psp_vehiculo_main_grid').getSelection();
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
												psp_vehiculo_id: rows[0].get('psp_vehiculo_id')
											},
											url:'psp/psp_vehiculoDelete',
											success: function (response, opts) {
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Eliminar', result.msg);
													psp.psp_vehiculo_reload_list();
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
				store: psp.psp_vehiculo_main_store,
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('psp_vehiculo_form').reset();
						Ext.getCmp('psp_vehiculo_form_title_label').setText('Datos del Vehiculo');
						Ext.getCmp('psp_vehiculo_form').loadRecord(record);
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
					id: 'psp_vehiculo_form',
					url: 'psp/addOrUpdateVehiculo',
					layout: 'form',
					region: 'center',
					bodyStyle: {
						//background: '#4c9dd8'
						borderTop: '1px solid silver!important;'
					},
					tbar:[{
						xtype: 'label',
						id: 'psp_vehiculo_form_title_label',
						text: 'Datos del Vehiculo',
						style: {
							fontWeight: 'bold'
						}
					},{
						id: 'psp_vehiculo_form_update_bt',
						text: 'Guardar', 
						handler: function() {
							var frm = Ext.getCmp('psp_vehiculo_form');
							frm.submit({
								success: function(form, action) {
									if (action.result.success) {
										psp.psp_vehiculo_reload_list(action.result.rowid);
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
						id: 'psp_vehiculo_form_psp_vehiculo_id_field',
						name: 'psp_vehiculo_id'
					},{
						xtype: 'hidden',
						id: 'psp_vehiculo_form_psp_id_field',
						name: 'psp_id',
						value: doc_id
					},{
						xtype: 'hidden',
						id: 'psp_vehiculo_form_operation_field',
						name: 'operation'
					},{
						fieldLabel: 'Categoria',
						id: 'psp_vehiculo_form_psp_vehiculo_categoria_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_categoria'
					},{
						fieldLabel: 'Marca',
						id: 'psp_vehiculo_form_psp_vehiculo_marca_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_marca'
					},{
						fieldLabel: 'Modelo',
						id: 'psp_vehiculo_form_psp_vehiculo_modelo_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_modelo'
					},{
						fieldLabel: 'Color',
						id: 'psp_vehiculo_form_psp_vehiculo_color_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_color'
					},{
						fieldLabel: 'Placa',
						id: 'psp_vehiculo_form_psp_vehiculo_placa_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_placa'
					},{
						fieldLabel: 'Nro. Tarjeta Prop.',
						id: 'psp_vehiculo_form_psp_vehiculo_ntp_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_ntp'
					},{
						fieldLabel: 'Conductor',
						id: 'psp_vehiculo_form_psp_vehiculo_conductor_nomape_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_conductor_nomape'
					},{
						fieldLabel: 'Conductor DNI',
						id: 'psp_vehiculo_form_psp_vehiculo_conductor_dni_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_conductor_dni'
					},{
						fieldLabel: 'Conductor Nro. Licencia',
						id: 'psp_vehiculo_form_psp_vehiculo_conductor_nlc_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_conductor_nlc'
					},{
						fieldLabel: 'Estado Registro',
	    				xtype: 'combobox',
	    				id: 'psp_vehiculo_form_psp_vehiculo_estado_field',
	    				name: 'psp_vehiculo_estado',
	    				displayField: 'id',
	    				valueField: 'id',
	    				store: psp.psp_vehiculo_estado_store,
	    				queryMode: 'local',
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    	}
	    				}
					},{
						fieldLabel: 'Observacion',
						id: 'psp_vehiculo_form_psp_vehiculo_observacion_field',
	    				xtype: 'textfield',
	    				name: 'psp_vehiculo_observacion'
					}]
				}]
			}],
			listeners:{
				show: function () {
					psp.psp_vehiculo_reload_list();					
				}
			}
		};

		psp.psp_vehiculo_reload_list = function (selected_id) {
			psp.psp_vehiculo_id_selected = selected_id||0;
			//psp.main_store.reload();
			psp.psp_vehiculo_main_store.reload({
				params: {
					doc_id: doc_id
				}
			});
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>