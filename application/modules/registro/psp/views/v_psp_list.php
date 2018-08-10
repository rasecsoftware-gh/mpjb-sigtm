<script>
	psp.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'psp_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Año', dataIndex:'psp_anio', width: 45},
				{text:'Numero', dataIndex:'psp_numero', width: 55},
				{text:'Nombres o Razon Soc.', dataIndex:'contribuyente_nombres', width: 200},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 150},
				{text:'DNI/RUC', dataIndex:'contribuyente_numero_doc', width: 75},
				{text:'Fecha', dataIndex:'psp_fecha', width: 70},
				{text:'Resolucion', dataIndex:'psp_resolucion', width: 70, align: 'left'},
				{
		            xtype: 'actioncolumn',
		            width: 25,
		            items: [{
		                icon: 'tools/icons/page_white_acrobat.png',  // Use a URL in the icon config
		                tooltip: 'Ver resolucion en formato PDF',
		                handler: function(grid, rowIndex, colIndex, item, e, record) {
		                    psp.print_window(record);
		                },
		                isDisabled: function (view, rowIndex, colIndex, item, record) {
		                	return !($.trim(record.get('psp_pdf_resolucion')).length > 0);
		                }
		            }]
		        },
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
					psp.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('psp_main_grid').getSelection();
					if (rows.length>0) {
						psp.edit_window(rows[0].get('psp_id'));
					} else {
						Ext.Msg.alert('Error', 'Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Generar PDF', 
					handler: function() {
						psp.pdf_generar_window();
					}
				},{
					text: 'Cambiar plantilla', 
					handler: function() {
						psp.plantilla_cambiar_window();
					}
				},'-',{
					text: 'Eliminar', 
					handler: function() {
						psp.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('psp.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('psp_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.psp', rows[0].get('psp_id'));
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'psp_search_by_cb',
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
						{search_id: 'numero', search_desc: 'Por Numero de Documento'},
						{search_id: 'resolucion', search_desc: 'Por Resolucion'},
						{search_id: 'estado', search_desc: 'Por Estado'}
					]
				})
			},{
				xtype: 'textfield',
				id: 'psp_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('psp_search_bt').click(e);
						}
					}
				}
			},{
				id: 'psp_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					psp.reload_list();
				}
			}],
			store: psp.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: psp.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts) {
					if (!psp.form_editing) {
						var f = Ext.getCmp('psp_form');
						f.loadRecord(record);
						Ext.getCmp('psp_form_title_label').setText('Constancia');
						Ext.getCmp('psp_form_psp_id_displayfield').setValue(record.get('psp_id'));
						Ext.getCmp('psp_form_save_bt').hide();
						Ext.getCmp('psp_form_cancel_bt').hide();
						Ext.getCmp('psp_form_contribuyente_id_field').hide();
						Ext.getCmp('psp_form_contribuyente_nomape_field').show();
						Ext.getCmp('psp_form_doc_requisito_grid').enable();
						Ext.getCmp('psp_form_doc_estado_grid').enable();
						psp.doc_requisito_reload_list(record.get('psp_id'));
						psp.doc_estado_reload_list(record.get('psp_id'));
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
					if (!psp.form_editing) {
						psp.edit_window(record.get('psp_id'));
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
				id: 'psp_form',
				url: 'psp/AddOrUpdate',
				layout: 'absolute',
				region: 'north',
				height: 330,
				bodyStyle: {
					//background: '#4c9dd8'
					borderTop: '1px solid silver!important;'
				},
				tbar:[{
					xtype: 'label',
					id: 'psp_form_title_label',
					text: 'Permiso de Servicio Publico',
					style: {
						fontWeight: 'bold'
					}
				},'->',{
					text: 'Guardar',
					id: 'psp_form_save_bt',
					hidden: true,
					handler: function () {
						var operation = Ext.getCmp('psp_form_operation_field').getValue();
						var frm = Ext.getCmp('psp_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									psp.form_editing = false;
									if ( operation == 'new' ) {
										psp.reload_list(action.result.rowid);
									} else {
										psp.reload_list(frm.getRecord().get('psp_id'));
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
					id: 'psp_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('psp_form_save_bt').hide();
						Ext.getCmp('psp_form_cancel_bt').hide();
						Ext.getCmp('psp_form_contribuyente_id_field').hide();
						Ext.getCmp('psp_form_contribuyente_nomape_field').show();
						Ext.getCmp('psp_form_doc_requisito_grid').enable();
						Ext.getCmp('psp_form_doc_estado_grid').enable();
						psp.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 80,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'psp_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'psp_form_psp_id_field',
					name: 'psp_id'
				},{
					xtype: 'hidden',
					id: 'psp_form_plantilla_id_field',
					name: 'plantilla_id'
				},{
					xtype: 'displayfield',
					id: 'psp_form_psp_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
					fieldLabel: 'Numero y Año',
					id: 'psp_form_psp_numero_field',
    				xtype: 'textfield',
    				name: 'psp_numero',
    				fieldStyle: 'text-align: center;',
    				x: 10, y: 30, width: 160
				},{
					id: 'psp_form_psp_anio_field',
    				xtype: 'textfield',
    				name: 'psp_anio',
    				editable: false,
    				value: '2018',
    				x: 175, y: 30, width: 40
				},{
    				xtype: 'combobox',
    				id: 'psp_form_contribuyente_id_field',
    				name: 'contribuyente_id',
    				fieldLabel: 'Contribuyente',
    				displayField: 'contribuyente_nomape',
    				valueField: 'contribuyente_id',
    				store: psp.contribuyente_store,
    				queryMode: 'remote',
    				triggerAction: 'last', // query
    				minChars: 2,
    				matchFieldWidth: false,
    				x: 10, y: 60, width: 380,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('psp_form_contribuyente_numero_doc_field').setValue(record.get('contribuyente_numero_doc'));
				    	}
    				},
    				hidden: true // only for edit
				},{ // only for display
					fieldLabel: 'Contribuyente',
					id: 'psp_form_contribuyente_nomape_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nomape',
    				x: 10, y: 60, width: 380
				},{
					id: 'psp_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				editable: false,
    				x: 395, y: 60, width: 75
				},{
					fieldLabel: 'Fecha',
					id: 'psp_form_psp_fecha_field',
    				xtype: 'datefield',
    				name: 'psp_fecha',
    				format: 'd/m/Y',
    				x: 10, y: 90, width: 200
				},{
					fieldLabel: 'Fecha de Inicio',
					id: 'psp_form_psp_fecha_inicio_field',
    				xtype: 'datefield',
    				name: 'psp_fecha_inicio',
    				format: 'd/m/Y',
    				x: 10, y: 120, width: 200,
    				labelWidth: 95
				},{
					fieldLabel: 'Fecha de Termino',
					id: 'psp_form_psp_fecha_fin_field',
    				xtype: 'datefield',
    				name: 'psp_fecha_fin',
    				format: 'd/m/Y',
    				x: 10, y: 150, width: 200,
    				labelWidth: 95
				},{
					id: 'psp_form_psp_ruta_field',
    				xtype: 'textfield',
    				fieldLabel: 'Ruta',
    				name: 'psp_ruta',
    				x: 10, y: 180, width: 380
				},{
					id: 'psp_form_psp_resolucion_field',
    				xtype: 'textfield',
    				fieldLabel: 'Resolucion',
    				name: 'psp_resolucion',
    				x: 10, y: 210, width: 380
				},{
					xtype: 'displayfield',
					id: 'clit_form_clit_recibo_validado_flag_displayfield',
					fieldLabel: 'Se ha validado el recibo?',
					name: 'clit_recibo_validado_flag',
					x: 10, y: 240, width: 30,
					labelWidth: 160
				},{
					xtype: 'displayfield',
					id: 'psp_form_plantilla_desc_displayfield',
					fieldLabel: 'Plantilla para la generacion del documento PDF',
					name: 'plantilla_desc',
					x: 10, y: 270,
					width: 400,
					labelWidth: 250
				}]
			},{
				xtype: 'panel',
				layout: 'border',
				region: 'center',
				items: [{
					xtype: 'grid',
					id: 'psp_form_doc_requisito_grid',
					region: 'center', 
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: psp.doc_requisito_store,
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
							psp.doc_requisito_add_or_edit();
						}
					},{
						text: '-', 
						tooltip: 'Quitar', tooltipType: 'title',
						handler: function() {
							psp.doc_requisito_delete_window();
						}
					}],
					listeners: {
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							psp.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new psp
				},{
					xtype: 'grid',
					id: 'psp_form_doc_estado_grid',
					region: 'south', 
					height: 200,
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: psp.doc_estado_store,
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
				                    psp.doc_estado_add_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('psp_form').getRecord();
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
				                    psp.doc_estado_delete_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('psp_form').getRecord();
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
							psp.doc_requisito_add_or_edit();
						},
						hidden: true
					}],
					listeners: {
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							//psp.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new psp
				}]
			}]
		}]
	});

	psp.reload_list = function (select_id) {
		psp.psp_id_selected = select_id||0;
		//psp.main_store.reload();
		psp.main_store.reload({
			params: {
				search_by: Ext.getCmp('psp_search_by_cb').getValue(),
				search_text: Ext.getCmp('psp_search_text').getValue()
			}
		});
	};

	psp.doc_requisito_reload_list = function (doc_id) {
		psp.doc_requisito_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};

	psp.doc_requisito_add_or_edit = function () {
		var rows = Ext.getCmp('psp_form_doc_requisito_grid').getSelection();
		if (rows.length > 0) {
			record = rows[0];
			if ( record.get('doc_requisito_id') > 0 ) {
				psp.doc_requisito_edit_window();
			} else {
				psp.doc_requisito_add_window();
			}
		} else {
			Ext.Msg.alert('Agregar o Modificar documento', 'Seleccione un registro por favor.');
		}
	};

	psp.doc_estado_reload_list = function (doc_id) {
		psp.doc_estado_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};
</script>