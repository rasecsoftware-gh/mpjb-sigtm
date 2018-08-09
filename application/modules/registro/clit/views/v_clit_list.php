<script>
	clit.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'clit_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Año', dataIndex:'clit_anio', width: 45},
				{text:'Numero', dataIndex:'clit_numero', width: 55},
				{
		            xtype: 'actioncolumn',
		            width: 25,
		            items: [{
		                icon: 'tools/icons/page_white_acrobat.png',  // Use a URL in the icon config
		                tooltip: 'Ver constancia en formato PDF',
		                handler: function(grid, rowIndex, colIndex, item, e, record) {
		                    clit.print_window(record);
		                },
		                isDisabled: function (view, rowIndex, colIndex, item, record) {
		                	return !($.trim(record.get('clit_pdf')).length > 0);
		                }
		            }]
		        },
				{text:'Nombres o Razon Soc.', dataIndex:'contribuyente_nombres', width: 200},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 150},
				{text:'DNI/RUC', dataIndex:'contribuyente_numero_doc', width: 75},
				{text:'Fecha', dataIndex:'clit_fecha', width: 70},
				{text:'Resultado', dataIndex:'clit_resultado', width: 70, align: 'center'},
				{text:'Estado', dataIndex:'estado_doc_desc', width: 70,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						if (record.get('estado_doc_color') != '') {
							metaData.tdStyle = 'color: ' + record.get('estado_doc_color');
							//value = '<span style="color: '+record.get('estado_doc_color')+';">'+value+'</span>';
						} 
						return value;
					}
				}
			],
			tbar:[{
				text:'Nuevo', 
				handler: function() {
					clit.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('clit_main_grid').getSelection();
					if (rows.length>0) {
						clit.edit_window(rows[0].get('clit_id'));
					} else {
						Ext.Msg.alert('Error', 'Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Generar PDF', 
					handler: function() {
						clit.pdf_generar_window();
					}
				},{
					text: 'Cambiar plantilla', 
					handler: function() {
						clit.plantilla_cambiar_window();
					}
				},'-',{
					text: 'Eliminar', 
					handler: function() {
						clit.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('clit.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('clit_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.clit', rows[0].get('clit_id'));
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'clit_search_by_cb',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				matchFieldWidth: false,
				width: 140,
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'contribuyente', search_desc: 'Por Contribuyente'},
						{search_id: 'numero', search_desc: 'Por Numero de Constancia'},
						{search_id: 'estado', search_desc: 'Por Estado'}
					]
				})
			},{
				xtype: 'textfield',
				id: 'clit_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('clit_search_bt').click(e);
						}
					}
				}
			},{
				id: 'clit_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					clit.reload_list();
				}
			}],
			store: clit.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: clit.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts) {
					if (!clit.form_editing) {
						var f = Ext.getCmp('clit_form');
						f.loadRecord(record);
						Ext.getCmp('clit_form_title_label').setText('Constancia');
						Ext.getCmp('clit_form_clit_id_displayfield').setValue(record.get('clit_id'));
						Ext.getCmp('clit_form_save_bt').hide();
						Ext.getCmp('clit_form_cancel_bt').hide();
						Ext.getCmp('clit_form_contribuyente_id_field').hide();
						Ext.getCmp('clit_form_contribuyente_nomape_field').show();
						Ext.getCmp('clit_form_doc_requisito_grid').enable();
						Ext.getCmp('clit_form_doc_estado_grid').enable();
						clit.doc_requisito_reload_list(record.get('clit_id'));
						clit.doc_estado_reload_list(record.get('clit_id'));
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
					if (!clit.form_editing) {
						clit.edit_window(record.get('clit_id'));
					}
				}
			}
		},{
			xtype: 'panel',
			region: 'east',
			layout: 'border',
			split: true,
			width: 500,
			items: [{
				xtype: 'form',
				id: 'clit_form',
				url: 'clit/AddOrUpdate',
				layout: 'absolute',
				region: 'north',
				height: 235,
				bodyStyle: {
					//background: '#4c9dd8'
					borderTop: '1px solid silver!important;'
				},
				tbar:[{
					xtype: 'label',
					id: 'clit_form_title_label',
					text: 'Constancia',
					style: {
						fontWeight: 'bold'
					}
				},'->',{
					text: 'Guardar',
					id: 'clit_form_save_bt',
					hidden: true,
					handler: function () {
						var operation = Ext.getCmp('clit_form_operation_field').getValue();
						var frm = Ext.getCmp('clit_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									clit.form_editing = false;
									if ( operation == 'new' ) {
										clit.reload_list(action.result.rowid);
									} else {
										clit.reload_list(frm.getRecord().get('clit_id'));
									}
								} else {
									Ext.Msg.alert('Error', action.result.msg);
								}
							},
							failure: function(form, action) {
								frm.unmask();
								Ext.Msg.alert('Guardar', action.result.msg, function () {
									sys_focus(action.result.target_id);
								});
							}
						});
					}
				},{
					text: 'Cancelar',
					id: 'clit_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('clit_form_save_bt').hide();
						Ext.getCmp('clit_form_cancel_bt').hide();
						Ext.getCmp('clit_form_contribuyente_id_field').hide();
						Ext.getCmp('clit_form_contribuyente_nomape_field').show();
						Ext.getCmp('clit_form_doc_requisito_grid').enable();
						Ext.getCmp('clit_form_doc_estado_grid').enable();
						clit.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 80,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'clit_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'clit_form_clit_id_field',
					name: 'clit_id'
				},{
					xtype: 'hidden',
					id: 'clit_form_plantilla_id_field',
					name: 'plantilla_id'
				},{
					xtype: 'displayfield',
					id: 'clit_form_clit_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
					fieldLabel: 'Numero y Año',
					id: 'clit_form_clit_numero_field',
    				xtype: 'textfield',
    				name: 'clit_numero',
    				fieldStyle: 'text-align: center;',
    				x: 10, y: 30, width: 160
				},{
					id: 'clit_form_clit_anio_field',
    				xtype: 'textfield',
    				name: 'clit_anio',
    				editable: false,
    				value: '2018',
    				x: 175, y: 30, width: 40
				},{
    				xtype: 'combobox',
    				id: 'clit_form_contribuyente_id_field',
    				name: 'contribuyente_id',
    				fieldLabel: 'Contribuyente',
    				displayField: 'contribuyente_nomape',
    				valueField: 'contribuyente_id',
    				store: clit.contribuyente_store,
    				queryMode: 'remote',
    				triggerAction: 'last', // query
    				minChars: 2,
    				matchFieldWidth: false,
    				x: 10, y: 60, width: 380,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('clit_form_contribuyente_numero_doc_field').setValue(record.get('contribuyente_numero_doc'));
				    	}
    				},
    				hidden: true // only for edit
				},{ // only for display
					fieldLabel: 'Contribuyente',
					id: 'clit_form_contribuyente_nomape_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nomape',
    				x: 10, y: 60, width: 380
				},{
					id: 'clit_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				editable: false,
    				x: 395, y: 60, width: 75
				},{
					fieldLabel: 'Fecha',
					id: 'clit_form_clit_fecha_field',
    				xtype: 'datefield',
    				name: 'clit_fecha',
    				format: 'd/m/Y',
    				x: 10, y: 90, width: 200
				},{
    				xtype: 'combobox',
    				id: 'clit_form_clit_resultado_field',
    				name: 'clit_resultado',
    				fieldLabel: 'Registra Infraccion de Transito?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: clit.resultado_store,
    				queryMode: 'local',
    				x: 10, y: 120, width: 300,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 170,
    				hidden: false // only for edit
				},{
					xtype: 'displayfield',
					id: 'clit_form_clit_recibo_validado_flag_displayfield',
					fieldLabel: 'Se ha validado el recibo?',
					name: 'clit_recibo_validado_flag',
					x: 10, y: 150,
					width: 30,
					labelWidth: 160
				},{
					xtype: 'displayfield',
					id: 'clit_form_plantilla_desc_displayfield',
					fieldLabel: 'Plantilla para la generacion del documento PDF',
					name: 'plantilla_desc',
					x: 10, y: 180,
					width: 400,
					labelWidth: 250
				}]
			},{
				xtype: 'panel',
				layout: 'border',
				region: 'center',
				items: [{
					xtype: 'grid',
					id: 'clit_form_doc_requisito_grid',
					region: 'center', 
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: clit.doc_requisito_store,
					columns:[
						{text:'Documento', dataIndex: 'tipo_doc_requisito_desc', width: 190,
							renderer: function (value, metaData, record) {
								if ( record.get('doc_requisito_id') == null ) {
									metaData.tdStyle = 'color: silver;';
								} 
								return value;
							}
						},{
				            xtype: 'actioncolumn',
				            width: 25,
				            items: [{
				                icon: 'tools/icons/page_white_acrobat.png',  // Use a URL in the icon config
				                tooltip: 'Ver PDF',
				                handler: function(grid, rowIndex, colIndex, item, e, record) {
				                    window.open('dbfiles/public.doc_requisito/'+record.get('doc_requisito_pdf'), '_blank');
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	return !($.trim(record.get('doc_requisito_pdf')).length > 0);
				                }
				            }]
				        },
						{text:'Fecha', dataIndex: 'doc_requisito_fecha', width: 70},
						{text:'Numero', dataIndex: 'doc_requisito_numero', width: 65},
						{text:'Requer.', dataIndex: 'tipo_doc_requisito_requerido_flag', width: 60, align: 'center'},
						{text:'Presentado', dataIndex: 'doc_requisito_id', width: 70, align: 'center',
							renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
								if (value == null) {
									metaData.tdStyle = 'color: silver;';
									value = 'No';
								} else {
									metaData.tdStyle = 'color: green; font-weight: bold;';
									value = 'Si';
								}
								return value;
							}
						}
					],
					tbar:[{
						xtype: 'label',
						text: 'Documentos requeridos y/o adjuntados'
					},'->',{
						text: 'Agregar o Modifcar', 
						tooltip: 'Agregar o Modificar documento', tooltipType: 'title',
						handler: function() {
							clit.doc_requisito_add_or_edit();
						}
					},{
						text: '-', 
						tooltip: 'Quitar', tooltipType: 'title',
						handler: function() {
							clit.doc_requisito_delete_window();
						}
					}],
					listeners: {
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							clit.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new CLIT
				},{
					xtype: 'grid',
					id: 'clit_form_doc_estado_grid',
					region: 'south', 
					height: 200,
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: clit.doc_estado_store,
					columns:[
						{	
							text:'Estado', dataIndex: 'estado_doc_desc', width: 150,
							renderer: function (value, metaData, record) {
								if ( record.get('doc_estado_id') == null ) {
									metaData.tdStyle = 'color: silver;';
								} else {
									metaData.tdStyle = 'color: '+ record.get('estado_doc_color') +';';
								}
								return value;
							}
						},
						{ text: 'Fecha', dataIndex: 'doc_estado_fecha', width: 130 },
						{ text: 'Usuario', dataIndex: 'doc_estado_usuario', width: 100},
						{
				            xtype: 'actioncolumn',
				            width: 50,
				            items: [{
				                icon: 'tools/icons/accept.png',  // Use a URL in the icon config
				                tooltip: 'Establecer estado', tooltipType: 'title',
				                handler: function(grid, rowIndex, colIndex, item, e, record) {
				                    clit.doc_estado_add_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('clit_form').getRecord();
				                	return !(
				                		record.get('estado_doc_index') > 1 // no es inicial
				                		&& record.get('doc_estado_id') == null // no tiene registro
				                		&& (
				                			parseInt(record.get('estado_doc_index')) == (parseInt(doc.get('estado_doc_index')) + 1) // es siguiente estado
				                			|| record.get('estado_doc_correlativo_flag') == 'N' // o no es correlativo
				                		)
				                	);
				                }
				            },{
				                icon: 'tools/icons/arrow_undo.png',  // Use a URL in the icon config
				                tooltip: 'Cancelar estado', tooltipType: 'title',
				                handler: function(grid, rowIndex, colIndex, item, e, record) {
				                    clit.doc_estado_delete_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('clit_form').getRecord();
				                	return !(
				                		record.get('estado_doc_index') > 1 // no es inicial (el estado incial no se puede revertir)
				                		&& record.get('doc_estado_id') != null // no tiene registro
				                		&& record.get('doc_estado_id') == doc.get('doc_estado_id') // es el estado actual
				                	);
				                }
				            }]
				        }
					],
					tbar:[{
						xtype: 'label',
						text: 'Control de estados'
					},'->',{
						text: 'Continuar', 
						tooltip: 'Modificar documento', tooltipType: 'title',
						handler: function() {
							clit.doc_requisito_add_or_edit();
						},
						hidden: true
					}],
					listeners: {
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							//clit.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new CLIT
				}]
			}]
		}]
	});

	clit.reload_list = function (select_id) {
		clit.clit_id_selected = select_id||0;
		//clit.main_store.reload();
		clit.main_store.reload({
			params: {
				search_by: Ext.getCmp('clit_search_by_cb').getValue(),
				search_text: Ext.getCmp('clit_search_text').getValue()
			}
		});
	};

	clit.doc_requisito_reload_list = function (doc_id) {
		clit.doc_requisito_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};

	clit.doc_requisito_add_or_edit = function () {
		var rows = Ext.getCmp('clit_form_doc_requisito_grid').getSelection();
		if (rows.length > 0) {
			record = rows[0];
			if ( record.get('doc_requisito_id') > 0 ) {
				clit.doc_requisito_edit_window();
			} else {
				clit.doc_requisito_add_window();
			}
		} else {
			Ext.Msg.alert('Agregar o Modificar documento', 'Seleccione un registro por favor.');
		}
	};

	clit.doc_estado_reload_list = function (doc_id) {
		clit.doc_estado_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};
</script>