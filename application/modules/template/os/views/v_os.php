<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	os = {};
</script>

<!--  cargamos los componentes -->
<?php //echo $this->load->view('grid_combus')?>
<?php //echo $this->load->view('grid_ubica')?>

<script type="text/javascript">
	/*************************** stores **********************/
	os.st_obra = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'os/getObraList',
			params: {
				anio_eje: '2015',
			},
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false
	});

	os.st_frente = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'os/getFrenteList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false
	});

	os.st_proveedor = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'os/getProveedorList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	os.st_regimen_igv = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'os/getRegimenIgvList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	os.st_regimen_igv_det = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'os/getRegimenIgvDetList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});
	

	os.st_grid_main = Ext.create("Ext.data.Store",{
		proxy:{
			type: 'ajax',
			url: 'os/getList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true
	});

	/*************************** window **********************/	
	os.window_new = function() {
		var w_config = {
			title:'Nueva Orden de Servicio', 
			modal: true,
			width: 800,
			height: 310, 
			id:'os_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'os/Add',
				region: 'center',
				layout: 'absolute',
				id: 'os_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 135
				},
				items:[{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_orden',
    				x: 10, y: 10, width: 250, disabled: true
				},{
					fieldLabel:'Centro de Costo:',
					xtype: 'combobox',
					store: os.st_obra,
					displayField: 'desc_obra',
    				valueField: 'cod_obra',
    				name: 'cod_obra',
    				id: 'os_form_cb_obra',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_obra} - {desc_obra}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_obra} - {desc_obra}',
				        '</tpl>'
				    ),
				    x: 10, y: 40, width: 720,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		os.st_frente.reload({
				    			params: {
				    				cod_obra: record.get('cod_obra')
				    			}
				    		});
				    		Ext.getCmp('os_form_cb_frente').focus();
				    	}
				    }
				},{
					fieldLabel:'Frente:',
					xtype: 'combobox',
    				name: 'id_frente',
    				id: 'os_form_cb_frente',
    				displayField: 'desc_frente',
    				valueField: 'id_frente',
    				queryMode: 'local',
    				store: os.st_frente,
				    x: 10, y: 70, width: 620,
				    forceSelection: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('os_form_fecha').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'os_form_fecha',
					name: 'fecha_orden',
					format: 'd/m/Y',
					x: 10, y: 100, width: 250
				},{
					fieldLabel:'Proveedor:',
					xtype: 'combobox',
					store: os.st_proveedor,
					displayField: 'desc_proveedor',
    				valueField: 'id_proveedor',
    				name: 'id_proveedor',
    				id: 'os_form_cb_proveedor',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{ruc_proveedor} - {desc_proveedor}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{ruc_proveedor} - {desc_proveedor}',
				        '</tpl>'
				    ),
				    x: 10, y: 130, width: 750,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('os_form_desc_orden').focus();
				    	}
				    }
				},{
					fieldLabel:'Descripcion:',
					id: 'os_form_desc_orden',
					name:'desc_orden',
					x: 10, y: 160, width: 650
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('os_form');
					frm.submit({
						params: {
							id_tipo_orden: 'OS'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('os_window_new').close();
								os.window_edit(action.result.rowid);
								os.st_grid_main.reload();	
							} else {
								Ext.Msg.alert('Error',action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Error',action.result.msg);
						}
					});
				}
			},{
				text:'Cancelar',handler:function() {
					win = Ext.getCmp('os_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'os/getNewRow',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('os_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}

	os.window_edit = function(id) {
		var store_detail = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'os/getDetailList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var grid = Ext.getCmp('os_form_grid_detail');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Orden de Servicio', 
			modal: true,
			width: 1000,
			height: 600, 
			id:'os_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'os/Update',
				region: 'north',
				layout: 'absolute',
				id: 'os_form',
				height: 220,
				defaultType:'textfield',
				defaults: {
					labelWidth: 135
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_orden'
				},{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_orden',
    				x: 10, y: 10, width: 250, readOnly: true
				},{
					fieldLabel:'Centro de Costo:',
    				name: 'obra_cod_desc',
    				id: 'os_form_obra_cod_desc',
    				x: 10, y: 40, width: 720
				},{
					fieldLabel:'Frente:',
					xtype: 'combobox',
    				name: 'id_frente',
    				id: 'os_form_cb_frente',
    				displayField: 'desc_frente',
    				valueField: 'id_frente',
    				queryMode: 'local',
    				store: os.st_frente,
				    x: 10, y: 70, width: 620,
				    forceSelection: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('os_form_fecha').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'os_form_fecha',
					name: 'fecha_orden',
					format: 'd/m/Y',
					x: 10, y: 100, width: 250
				},{
					fieldLabel:'Proveedor:',
					xtype: 'combobox',
					store: os.st_proveedor,
					displayField: 'desc_proveedor',
    				valueField: 'id_proveedor',
    				name: 'id_proveedor',
    				id: 'os_form_cb_proveedor',
    				queryMode: 'remote',
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{ruc_proveedor} - {desc_proveedor}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{ruc_proveedor} - {desc_proveedor}',
				        '</tpl>'
				    ),
				    x: 10, y: 130, width: 830,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('os_form_desc_orden').focus();
				    	}
				    }
				},{
					fieldLabel:'Descripcion:',
					id: 'os_form_desc_orden',
					name:'desc_orden',
					x: 10, y: 160, width: 650
				},{
					fieldLabel:'Regimen IGV:',
					xtype: 'combobox',
    				name: 'id_regimen_igv',
    				id: 'os_form_cb_regimen_igv',
    				displayField: 'desc_regimen_igv',
    				valueField: 'id_regimen_igv',
    				queryMode: 'local',
    				store: os.st_regimen_igv,
				    x: 10, y: 190, width: 300,
				    forceSelection: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('os_form_cb_regimen_igv_det').setValue('');
				    		os.st_regimen_igv_det.filter([{property: 'id_regimen_igv', value: record.get('id_regimen_igv')}]);
				    		Ext.getCmp('os_form_cb_regimen_igv_det').focus();
				    	}
				    }
				},{
					fieldLabel:'Porcentaje (%):',
					xtype: 'combobox',
					store: os.st_regimen_igv_det,
					displayField: 'desc',
    				valueField: 'val_regimen_igv_det',
    				name: 'val_regimen_igv_det',
    				id: 'os_form_cb_regimen_igv_det',
    				queryMode: 'local',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{desc_regimen_igv_det} {val_regimen_igv_det} %</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{val_regimen_igv_det}',
				        '</tpl>'
				    ),
				    x: 350, y: 190, width: 300,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    	}
				    }
				}]
			},{
				xtype: 'grid',
				id:'os_form_grid_detail',
				region:'center', 
				//forceFit:true,
				features: [{
			    	ftype: 'summary'
			    }],
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'cod_bs', width: 110},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 300},
					{text:'Cantidad', dataIndex:'cant_det_orden', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'UniMed', dataIndex:'desc_unimed', width: 80},
					{text:'Precio Unit', dataIndex:'preuni_det_orden', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.0000'},
					{
						text:'Total', dataIndex:'tot_det_orden', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00', 
						summaryType: 'sum',
						summaryRenderer: function(value, summaryData, dataIndex) {
				        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
				        	//Ext.String.format('{0} student{1}', value, value !== 1 ? 's' : '');
				        }
					},
					{text:'Clasif.Presu', dataIndex:'desc_clapre', width: 80},
					{text:'Requer', dataIndex:'nro_requer', width: 80},
					{text:'Observacion', dataIndex:'obs_det_orden', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Opciones de detalle: '
				},{
					text:'Agregar / Importar', 
					handler: function() {
						os.window_detail_new(id, 0);
					}
				},{
					text:'Modificar', 
					handler: function () {
						rows = Ext.getCmp('os_form_grid_detail').getSelection();
						if (rows.length>0) {
							os.window_detail_edit(rows[0].get('id_det_orden'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text:'Eliminar',
					handler: function () {
						rows = Ext.getCmp('os_form_grid_detail').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar detalle?',
							    message: 'Realmente desea eliminar el detalle seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							   			Ext.Ajax.request({
											params:{
												id: rows[0].get('id_det_orden')
											},
											url:'os/deleteDetail',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													store_detail.reload();
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
				store: store_detail,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('os_form');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('os_window_edit').close();
								os.st_grid_main.reload();	
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
				text: 'Imprimir',
				handler: function () {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_np_pdf.jsp?id='+id;
					window.open(url, '_blank');
				}
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('os_window_edit').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'os/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						os.st_frente.reload({
							params: {
								'cod_obra': records[0].get('cod_obra')
							}
						});
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('os_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}

	os.window_detail_new = function(pid) {
		var st_det_requer = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'os/getDetRequerList/'+pid,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
					}
				}
			}
		});
		var w_detail_config = {
			title:'Agregar / Importar detalles', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'os_window_detail',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id:'os_form_detail_grid',
				region:'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text:'NroReq', dataIndex:'nro_requer', width: 65, menuDisabled: true},
					{text:'Codigo', dataIndex:'cod_bs', width: 105, menuDisabled: true},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 290, menuDisabled: true},
					{text:'Cantidad', dataIndex:'cant_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'SALDO', dataIndex:'saldo', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'UniMed', dataIndex:'desc_unimed', width: 80},
					{text:'Precio Unit', dataIndex:'preuni_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.0000'},
					{
						text:'Total', dataIndex:'tot_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'
					},
					{xtype: 'checkcolumn', text:'Importar', dataIndex:'importar', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Filtre los registros segun'
				},{
					xtype:'textfield',
					id: 'os_form_detail_search_text',
					width: 200
				},{
					text:'Buscar', 
					handler: function() {
						st_det_requer.reload({
							params:{
								query: Ext.getCmp('os_form_detail_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = [];
						var st = Ext.getCmp('os_form_detail_grid').store;

						for (var i=0; i<st.count(); i++) {
							if (st.getAt(i).get('importar')) {
								rows.push(st.getAt(i).get('id_det_requer'));
							}
						}
						if (rows.length>0) {
							var values = rows.join();
							//alert(values);
							Ext.Msg.show({
							    title:'Importar registros?',
							    message: 'Realmente desea importar los registros seleccionados?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							   			Ext.Ajax.request({
											params:{
												id_orden: pid,
												strlist: values
											},
											url:'os/importDetails',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.getCmp('os_form_grid_detail').store.reload();
													//Ext.getCmp('os_form_detail_grid').store.reload();
													Ext.getCmp('os_window_detail').close();
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
							Ext.Msg.alert('Error','Seleccione al menos un registro');
						}
					}
				},{
					text:'Salir', 
					handler: function () {
						Ext.getCmp('os_window_detail').close();
					}
				}],
				store: st_det_requer,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}]
		};
		var w = Ext.create('Ext.window.Window', w_detail_config);
		w.show();
	}

	os.window_detail_edit = function(id) {

		var w_detail_config = {
			title:'Detalle de Orden', 
			modal: true,
			width: 800,
			height: 300, 
			id:'os_window_detail',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'os/updateDetail',
				region: 'center',
				layout: 'absolute',
				id: 'os_form_detail',
				height: 160,
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_det_orden'
				},{
					xtype: 'hiddenfield', name: 'id_orden'
				},{
					fieldLabel:'Bien/Servicio:',
    				name: 'desc_bs',
    				x: 10, y: 10, width: 720, readOnly: true
				},{
					fieldLabel:'Unidad Med.:',
    				name: 'desc_unimed',
    				id: 'os_form_detail_desc_unimed',
				    x: 10, y: 40, width: 220, readOnly: true
				},{
					fieldLabel: 'Cantidad:',
					xtype: 'numberfield',
					id: 'os_form_detail_cant_det_orden',
					name: 'cant_det_orden',
					x: 10, y: 70, width: 220
				},{
					fieldLabel: 'Precio Unit.:',
					xtype: 'numberfield',
					id: 'os_form_detail_preuni_det_orden',
					name: 'preuni_det_orden',
					x: 10, y: 100, width: 220
				},{
					fieldLabel: 'Total:',
					xtype: 'numberfield',
					id: 'os_form_detail_tot_det_orden',
					name: 'tot_det_orden',
					x: 10, y: 130, width: 220, readOnly: true
				},{
					fieldLabel:'Observacion:',
					name:'obs_det_orden',
					x: 10, y: 160, width: 720
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('os_form_detail');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action){
							if (action.result.success) {
								Ext.getCmp('os_window_detail').close();
								Ext.getCmp('os_form_grid_detail').store.reload();	
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
					Ext.getCmp('os_window_detail').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'os/getDetailRow/'+id,
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
						frm = Ext.getCmp('os_form_detail');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}

	/*************************** main ************************/
	os.grid = Ext.create('Ext.grid.Panel',{
		id:'os_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Numero', dataIndex:'nro_orden', width: 60},
			{text:'Fecha', dataIndex:'fecha_orden', width: 85},
			{text:'Proveedor', dataIndex:'proveedor_ruc_desc', width: 220},
			{text:'Obra', dataIndex:'obra_cod_desc', width: 300},
			{text:'Frente', dataIndex:'desc_frente', width: 150},
			{text:'Descripcion', dataIndex:'desc_orden', width: 100},
			{text:'Estado', dataIndex:'desc_estado_orden', width: 100}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				os.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('os_grid_main').getSelection();
				if (rows.length>0) {
					os.window_edit(rows[0].get('id_orden'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Imprimir', handler: function() {
				rows = Ext.getCmp('os_grid_main').getSelection();
				if (rows.length>0) {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_os_pdf.jsp?id='+rows[0].get('id_orden');
					window.open(url, '_blank');
				} else {
					Ext.Msg.alert('Error','Seleccione un registro a enviar!');
				}
			}
		},{
			text:'Anular',
			handler: function () {
				rows = Ext.getCmp('os_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Anular ORDEN',
					    message: 'Realmente desea ANULAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_orden: rows[0].get('id_orden')
									},
									url:'os/Anular',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Orden de servicio', result.msg);
											Ext.getCmp('os_grid_main').store.reload();
										} else {
											Ext.Msg.alert('Error', result.msg);
										}
									},
									failure: function (response, opts){
										Ext.Msg.alert('Error', 'Error en la conexion de datos');
									}
								});         
					        } 
					    }
					});
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Activar',
			handler: function () {
				rows = Ext.getCmp('os_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Activar ORDEN',
					    message: 'Realmente desea ACTIVAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_orden: rows[0].get('id_orden')
									},
									url:'os/Activar',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Orden de servicio', result.msg);
											Ext.getCmp('os_grid_main').store.reload();
										} else {
											Ext.Msg.alert('Error', result.msg);
										}
									},
									failure: function (response, opts){
										Ext.Msg.alert('Error', 'Error en la conexion de datos');
									}
								});         
					        } 
					    }
					});
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'os_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'nro_orden', search_desc: 'Por Numero'},
					{search_id: 'id_frente', search_desc: 'Por Centro de costo'},
					{search_id: 'id_proveedor', search_desc: 'Por Proveedor'},
					{search_id: 'desc_orden', search_desc: 'Por Descripcion'}
				]
			})
		},{
			xtype:'textfield',
			id: 'os_search_text'
		},{
			text:'Buscar', handler:function(){
				os.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('os_cb_search_by').getValue(),
						search_text: Ext.getCmp('os_search_text').getValue()
					}
				});
			}
		}],
		store: os.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});

	tab = Ext.getCmp('tab-appOS');
	tab.add(os.grid);
</script>