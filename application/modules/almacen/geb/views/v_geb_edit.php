<script>
	geb.window_edit = function(id) {
		var st_geb_det = Ext.create("Ext.data.Store", {
			fields: ['geb_det_id','geb_id','orden_numero','orden_anio','bs_cod','bs_desc','bs_unimed','geb_det_cantidad','geb_det_obs'],
			proxy: {
				type:'ajax',
				url:'geb/getGebDetList/'+id,
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
						var grid = Ext.getCmp('geb_form_grid_geb_det');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Guia de entrega de bienes', 
			modal: true,
			width: 1000,
			height: 630, 
			id:'geb_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'geb/Update',
				region: 'north',
				layout: 'absolute',
				id: 'geb_form',
				height: 220,
				defaultType:'textfield',
				defaults: {
					labelWidth: 90
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'geb_id'
				},,{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'geb_numero',
    				x: 10, y: 10, width: 200, readOnly: true, editable: false
				},{
					fieldLabel: 'Centro de Costo:',
					id: 'geb_form_nemo_anio',
    				name: 'nemo_anio',
    				x: 10, y: 40, width: 140, readOnly: true
				},{
					id: 'geb_form_nemo_cod',
    				name: 'nemo_cod',
    				x: 155, y: 40, width: 40, readOnly: true
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_nemo_secfun',
    				name: 'nemo_secfun'
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_nemo_meta',
    				name: 'nemo_meta'
				},{
					id: 'geb_form_nemo_desc',
    				name: 'nemo_desc',
				    x: 200, y: 40, width: 550,
				    triggers: {
    					search: {
				            cls: 'x-form-search-trigger',
				            handler: function() {
				                geb.window_nemo_search(
				                	Ext.getCmp('geb_form_nemo_desc').getValue(), 
				                	function (r) {
				                		if (st_geb_det.count()>0) {
				                			Ext.Msg.alert('Cambiar Centro de Costo', 'No es posible cambiar de Centro de costo si la Guia tiene bienes registrados.');
				                			return;
				                		}
				                		Ext.getCmp('geb_form_nemo_anio').setValue(r.get('nemo_anio'));
						                Ext.getCmp('geb_form_nemo_cod').setValue(r.get('nemo_cod'));
										Ext.getCmp('geb_form_nemo_desc').setValue(r.get('nemo_desc'));
										Ext.getCmp('geb_form_nemo_secfun').setValue(r.get('nemo_secfun'));
										Ext.getCmp('geb_form_nemo_meta').setValue(r.get('nemo_meta'));
							    		Ext.getCmp('geb_form_cb_area').focus();
					                }
				                );
				            }
				        }
    				},
    				editable: false
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_area_cod',
    				name: 'area_cod'
				},{
					fieldLabel:'Area destino:',
					xtype: 'combobox',
    				name: 'area_desc',
    				id: 'geb_form_cb_area',
    				displayField: 'area_desc',
    				valueField: 'area_desc',
    				queryMode: 'local',
    				anyMatch: true,
    				store: geb.st_sys2009_area,
				    x: 10, y: 70, width: 730,
				    forceSelection: false,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('geb_form_area_cod').setValue(record.get('area_cod'));
				    		Ext.getCmp('geb_form_geb_fecha').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'geb_form_geb_fecha',
					name: 'geb_fecha',
					format: 'd/m/Y',
					x: 10, y: 100, width: 250
				},{
					fieldLabel:'Solicitante:',
					xtype: 'combobox',
					store: geb.st_solicitante,
					displayField: 'geb_solicitante',
    				valueField: 'geb_solicitante',
    				name: 'geb_solicitante',
    				id: 'geb_form_geb_solicitante',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{geb_solicitante}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{geb_solicitante}',
				        '</tpl>'
				    ),
				    x: 10, y: 130, width: 530,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('geb_form_geb_desc').focus();
				    	}
				    }
				},{
					fieldLabel:'Observacion:',
					id: 'geb_form_geb_desc',
					name:'geb_desc',
					x: 10, y: 160, width: 730
				}]
			},{
				xtype: 'grid',
				id:'geb_form_grid_geb_det',
				region:'center', 
				//forceFit:true,
				/*features: [{
			    	ftype: 'summary'
			    }],*/
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'bs_cod', width: 110},
					{text:'Descripcion', dataIndex:'bs_desc', width: 350},
					{text:'Observacion', dataIndex:'geb_det_obs', width: 160},
					{text:'UniMed', dataIndex:'bs_unimed', width: 80},
					{text:'Cant.', dataIndex:'geb_det_cantidad', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00',
						editor: {
			                xtype: 'numberfield',
			                allowBlank: false
			            }
					},
					{text:'Orden', dataIndex:'orden_anio_numero', width: 110}
				],
				tbar:[{
					xtype: 'label',
					text: 'Opciones de detalle: '
				},{
					text:'Agregar / Importar', 
					handler: function() {
						geb.window_geb_det_import(id);
					}
				},{
					text:'Importar desde saldos', 
					handler: function() {
						geb.window_geb_det_import_saldo(id);
					}
				}/*,{
					text:'Modificar', 
					handler: function () {
						var rows = Ext.getCmp('geb_form_grid_geb_det').getSelection();
						if (rows.length>0) {
							geb.window_geb_det_edit(rows[0].get('geb_det_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}*/,{
					text:'Eliminar',
					handler: function () {
						var rows = Ext.getCmp('geb_form_grid_geb_det').getSelection();
						if (rows.length>0) {
							geb.window_geb_det_delete(rows[0].get('geb_det_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}],
				store: st_geb_det,
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
									geb_det_id: context.record.get('geb_det_id'),
									geb_det_cantidad: context.newValues.geb_det_cantidad
								},
								url:'geb/updateGebDet',
								success: function (response, opts) {
									var result = Ext.decode(response.responseText);
									if (result.success) {
										st_geb_det.commitChanges();
									} else {
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
					frm = Ext.getCmp('geb_form');
					frm.submit({
						success: function (form, action) {
							if (action.result.success) {
								Ext.getCmp('geb_window_edit').close();
								geb.st_grid_main.reload();	
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
				handler: function () {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_geb_full_pdf.jsp?id='+id;
					window.open(url, '_blank');
				}
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('geb_window_edit').close();
				}
			}],
			listeners: {
				show: function (w, eOpts) {
					w.mask('cargando');
					Ext.create("Ext.data.Store", {
						proxy: {
							type:'ajax',
							url:'geb/getRow/'+id,
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
									frm = Ext.getCmp('geb_form');
									frm.loadRecord(records[0]);
									st_geb_det.reload();
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