<script>
	nea.window_edit = function(id) {
		var nea_det_store = Ext.create("Ext.data.Store", {
			fields: ['nea_det_id','nea_id','orden_numero','orden_anio','bs_cod','bs_desc','bs_unimed','nea_det_cantidad','nea_det_obs'],
			proxy: {
				type:'ajax',
				url:'nea/getNeaDetList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				},
				writer: {
	                type: "json",
	                encode: true,
	                writeAllFields: true,
	                rootProperty: "data"
	            }
			},
			autoLoad: false,
			autoSync: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var grid = Ext.getCmp('nea_det_grid');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Nota de Entrada a Almacen', 
			modal: true,
			width: 1000,
			height: 630, 
			id:'nea_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'nea/Update',
				region: 'north',
				layout: 'absolute',
				id: 'nea_form',
				height: 220,
				defaultType:'textfield',
				defaults: {
					labelWidth: 90
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'nea_id'
				},{
					fieldLabel: 'Año y Numero:',
					xtype: 'displayfield',
    				name: 'nea_anio',
    				x: 10, y: 10, width: 140,
    				fieldStyle: {
    					fontWeight: 'bold'
    				}
    				//labelStyle: 'background-color: silver;'
				},{
					xtype: 'displayfield',
    				name: 'nea_numero',
    				x: 150, y: 10, width: 70,
    				fieldStyle: {
    					fontWeight: 'bold'
    				}
				},{
					fieldLabel:'Tipo:',
					xtype: 'displayfield',
    				name: 'tipo_nea_desc',
				    x: 10, y: 40, width: 400
				},{
					fieldLabel:'Procedencia:',
					xtype: 'combobox',
					store: nea.procedencia_store,
					displayField: 'nea_procedencia',
    				valueField: 'nea_procedencia',
    				name: 'nea_procedencia',
    				id: 'nea_procedencia_field',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{nea_procedencia}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{nea_procedencia}',
				        '</tpl>'
				    ),
				    x: 10, y: 70, width: 630,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    	}
				    }
				},{
					fieldLabel: 'Centro de costo:',
					xtype: 'displayfield',
					id: 'nea_nemo_anio_cod_desc_field',
    				name: 'nemo_anio_cod_desc',
    				x: 10, y: 100, width: 750
				},{
					xtype: 'button',
					text: 'Cambiar',
					x: 790, y: 100,
					handler: function () {
						nea.window_nemo_search(
		                	'', 
		                	function (r) {
		                		Ext.Msg.show({
								    title:'Cambiar Centro de costo?',
								    message: 'Realmente deseas cambiar el Centro de costo y GUARDAR?',
								    buttons: Ext.Msg.YESNO,
								    icon: Ext.Msg.QUESTION,
								    fn: function(btn) {
								        if (btn === 'yes') {
					                		var w = Ext.getCmp('nea_window_edit');
					                		w.mask();
					                		var params = {
					                			nea_id: id
					                		};
					                		if (r == null) {
					                			params.nemo_anio = '';
					                			params.nemo_cod = '';
					                			params.nemo_anio_cod_desc = '';
					                		} else {
					                			params.nemo_anio = r.get('nemo_anio');
					                			params.nemo_cod = r.get('nemo_cod');
					                			params.nemo_desc = r.get('nemo_desc');
					                			params.nemo_secfun = r.get('nemo_secfun');
					                			params.nemo_meta = r.get('nemo_meta');
					                			params.nemo_anio_cod_desc = params.nemo_anio + ' - ' + params.nemo_cod + ' | ' + params.nemo_desc;
					                		}
					                		Ext.Ajax.request({
												params: params,
												url:'nea/changeNemo',
												success: function (response, opts) {
													w.unmask();
													var result = Ext.decode(response.responseText);
													if (result.success) {
														Ext.Msg.alert('Cambiar Centro de costo', result.msg);
														Ext.getCmp('nea_nemo_anio_cod_desc_field').setValue(params.nemo_anio_cod_desc);
													} else {
														Ext.Msg.alert('Error', result.msg);
													}
												},
												failure: function (response, opts){
													w.unmask();
													Ext.Msg.alert('Error', 'Error en la conexion.');
												}
											});
										}
									}
								});
			                }
		                );
					}
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'nea_fecha_field',
					name: 'nea_fecha',
					format: 'd/m/Y',
					x: 10, y: 140, width: 185
				},{
					fieldLabel:'Observacion:',
					id: 'nea_observacion_field',
					name:'nea_observacion',
					x: 10, y: 170, width: 940
				}]
			},{
				xtype: 'grid',
				id:'nea_det_grid',
				region:'center', 
				//forceFit:true,
				/*features: [{
			    	ftype: 'summary'
			    }],*/
			    sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'bs_cod', width: 110},
					{text:'Descripcion', dataIndex:'bs_desc', width: 200},
					{text:'UniMed', dataIndex:'bs_unimed', width: 80},
					{text:'Cantidad', dataIndex:'nea_det_cantidad', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00',
						editor: {
			                xtype: 'numberfield',
			                allowBlank: false
			            }
					},
					{text:'PrecioUnit', dataIndex:'nea_det_precio', width: 85, align: 'right', xtype: 'numbercolumn', format:'0.00',
						editor: {
			                xtype: 'numberfield',
			                allowBlank: false
			            }
					},
					{text:'Total', dataIndex:'nea_det_total', width: 85, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'Observacion', dataIndex:'nea_det_obs', width: 150,
						editor: {
			                xtype: 'textfield',
			                allowBlank: true
			            }
			        },
					{text:'O/C', dataIndex:'oc_anio_numero', width: 110}
				],
				tbar:[{
					xtype: 'label',
					text: 'Agregar desde '
				},{
					text:'Ordenes de Compra', 
					handler: function() {
						nea.nea_det_import_from_oc_window(id);
					}
				},{
					text:'Catalogo de Bienes', 
					handler: function() {
						nea.nea_det_import_from_cb_window(id);
					}
				},'-',{
					text:'Eliminar',
					handler: function () {
						var rows = Ext.getCmp('nea_det_grid').getSelection();
						if (rows.length>0) {
							nea.nea_det_delete_window(rows[0].get('nea_det_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}],
				store: nea_det_store,
				plugins: {
			        ptype: 'rowediting',
			        clicksToEdit: 2,
			        autoCancel: false,
			        saveBtnText: 'Aceptar',
			        listeners: {
			        	edit: function (editor, context, eOpts) {
			        		console.info(context); 
			        		Ext.Ajax.request({
								params:{
									nea_det_id: context.record.get('nea_det_id'),
									nea_det_cantidad: context.newValues.nea_det_cantidad,
									nea_det_precio: context.newValues.nea_det_precio,
									nea_det_obs: context.newValues.nea_det_obs
								},
								url:'nea/updateNeaDet',
								success: function (response, opts) {
									var result = Ext.decode(response.responseText);
									if (result.success) {
										nea_det_store.commitChanges();
										nea_det_store.reload();
									} else {
										nea_det_store.rejectChanges();
										Ext.Msg.alert('Error', result.msg);
									}
								},
								failure: function (response, opts) {
									Ext.Msg.alert('Error', 'Error en la conexion de datos');
								}
							});
			        	}
			        }
			    },
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('nea_form');
					frm.submit({
						success: function (form, action) {
							if (action.result.success) {
								Ext.getCmp('nea_window_edit').close();
								nea.main_store.reload();	
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function (form, action){
							Ext.Msg.alert('Guardar', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Imprimir',
				menu: [{
					text: 'Original',
					handler: function () {
						nea.print_window(id, 'ORIGINAL');
					}
				},'-',{
					text: 'Control',
					handler: function () {
						nea.print_window(id, 'CONTROL');
					}
				},{
					text: 'Usuario',
					handler: function () {
						nea.print_window(id, 'USUARIO');
					}
				}]
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('nea_window_edit').close();
				}
			}],
			listeners: {
				show: function (w, eOpts) {
					w.mask('cargando');
					Ext.create("Ext.data.Store", {
						proxy: {
							type:'ajax',
							url:'nea/getRow/'+id,
							reader:{
								type:'json',
								rootProperty:'data'
							}
						},
						autoLoad: true,
						listeners: {
							load: function (sender, records, successful, eOpts) {
								w.unmask();
								if (successful && records.length > 0) {
									frm = Ext.getCmp('nea_form');
									frm.loadRecord(records[0]);
									nea_det_store.reload();
								}
							}
						}
					});
				}
			}
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>