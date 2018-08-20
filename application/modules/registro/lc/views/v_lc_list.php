<script>
	lc.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'lc_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Año', dataIndex:'lc_anio', width: 45},
				{text:'Numero', dataIndex:'lc_numero', width: 55},
				{text:'Nombres o Razon Soc.', dataIndex:'contribuyente_nombres', width: 150},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 120},
				{text:'DNI/RUC', dataIndex:'contribuyente_numero_doc', width: 70},
				{text:'Fecha', dataIndex:'lc_fecha', width: 65},
				{text:'Resolucion', dataIndex:'lc_resolucion', width: 70, align: 'left'},
				{
		            xtype: 'actioncolumn',
		            width: 25,
		            items: [{
		                icon: 'tools/icons/page_white_acrobat.png',  // Use a URL in the icon config
		                tooltip: 'Ver constancia en formato PDF',
		                handler: function(grid, rowIndex, colIndex, item, e, record) {
		                    lc.print_window(record);
		                },
		                isDisabled: function (view, rowIndex, colIndex, item, record) {
		                	return !($.trim(record.get('lc_resolucion_pdf')).length > 0);
		                }
		            }]
		        },
		        {text:'Nro. Licencia', dataIndex:'lc_codigo', width: 75, align: 'left'},
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
					lc.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('lc_main_grid').getSelection();
					if (rows.length>0) {
						lc.edit_window(rows[0].get('lc_id'));
					} else {
						Ext.Msg.alert('Error', 'Seleccione un registro');
					}
				}
			},'-',{
				text:'Opciones', 
				menu: [{
					text: 'Generar PDF', 
					handler: function() {
						lc.pdf_generar_window();
					}
				},{
					text: 'Cambiar plantilla', 
					handler: function() {
						lc.plantilla_cambiar_window();
					}
				},'-',{
					text: 'Eliminar', 
					handler: function() {
						lc.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('lc.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('lc_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.lc', rows[0].get('lc_id'));
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'lc_search_by_cb',
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
				id: 'lc_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('lc_search_bt').click(e);
						}
					}
				}
			},{
				id: 'lc_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					lc.reload_list();
				}
			}],
			store: lc.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: lc.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts) {
					if (!lc.form_editing) {
						var f = Ext.getCmp('lc_form');
						f.loadRecord(record);
						Ext.getCmp('lc_form_title_label').setText(lc.title);
						Ext.getCmp('lc_form_lc_id_displayfield').setValue(record.get('lc_id'));
						Ext.getCmp('lc_form_save_bt').hide();
						Ext.getCmp('lc_form_cancel_bt').hide();
						Ext.getCmp('lc_form_contribuyente_id_field').hide();
						Ext.getCmp('lc_form_contribuyente_nomape_field').show();
						Ext.getCmp('lc_form_doc_requisito_grid').enable();
						Ext.getCmp('lc_form_doc_estado_grid').enable();
						lc.doc_requisito_reload_list(record.get('lc_id'));
						lc.doc_estado_reload_list(record.get('lc_id'));
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
					if (!lc.form_editing) {
						lc.edit_window(record.get('lc_id'));
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
				id: 'lc_form',
				url: 'lc/AddOrUpdate',
				layout: 'absolute',
				region: 'north',
				height: 300,
				bodyStyle: {
					//background: '#4c9dd8'
					borderTop: '1px solid silver!important;'
				},
				tbar:[{
					xtype: 'label',
					id: 'lc_form_title_label',
					text: lc.title,
					style: {
						fontWeight: 'bold'
					}
				},'->',{
					text: 'Guardar',
					id: 'lc_form_save_bt',
					hidden: true,
					handler: function () {
						lc.lc_update_window();
					}
				},{
					text: 'Cancelar',
					id: 'lc_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('lc_form_save_bt').hide();
						Ext.getCmp('lc_form_cancel_bt').hide();
						Ext.getCmp('lc_form_contribuyente_id_field').hide();
						Ext.getCmp('lc_form_contribuyente_nomape_field').show();
						Ext.getCmp('lc_form_doc_requisito_grid').enable();
						Ext.getCmp('lc_form_doc_estado_grid').enable();
						lc.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 80,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'lc_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'lc_form_lc_id_field',
					name: 'lc_id'
				},{
					xtype: 'hidden',
					id: 'lc_form_plantilla_id_field',
					name: 'plantilla_id'
				},{
					xtype: 'displayfield',
					id: 'lc_form_lc_id_displayfield',
					fieldLabel: 'ID',
					x: 400, y: 10,
					labelWidth: 25,
					width: 80
				},{
					fieldLabel: 'N&ordm; Reg. y Año',
					id: 'lc_form_lc_numero_field',
    				xtype: 'textfield',
    				name: 'lc_numero',
    				fieldStyle: 'text-align: center;',
    				x: 10, y: 10, width: 160
				},{
					id: 'lc_form_lc_anio_field',
    				xtype: 'textfield',
    				name: 'lc_anio',
    				editable: false,
    				value: '2018',
    				x: 175, y: 10, width: 40
				},{
    				xtype: 'combobox',
    				id: 'lc_form_contribuyente_id_field',
    				name: 'contribuyente_id',
    				fieldLabel: 'Contribuyente',
    				displayField: 'contribuyente_nomape',
    				valueField: 'contribuyente_id',
    				store: lc.contribuyente_store,
    				queryMode: 'remote',
    				triggerAction: 'last', // query
    				minChars: 2,
    				matchFieldWidth: false,
    				x: 10, y: 40, width: 380,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('lc_form_contribuyente_numero_doc_field').setValue(record.get('contribuyente_numero_doc'));
    						Ext.getCmp('lc_form_contribuyente_fecha_nac_field').setValue(record.get('contribuyente_fecha_nac'));
    						if (Ext.getCmp('lc_form_lc_codigo_field').getValue().trim() == '') {
    							Ext.getCmp('lc_form_lc_codigo_field').setValue('KJB'+record.get('contribuyente_numero_doc'));
    						}
				    	}
    				},
    				hidden: true // only for edit
				},{ // only for display
					fieldLabel: 'Contribuyente',
					id: 'lc_form_contribuyente_nomape_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nomape',
    				x: 10, y: 40, width: 380
				},{
					id: 'lc_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				editable: false,
    				x: 395, y: 40, width: 75
				},{
					fieldLabel: 'Fecha Nac.',
					id: 'lc_form_contribuyente_fecha_nac_field',
    				xtype: 'textfield',
    				name: 'contribuyente_fecha_nac',
    				editable: false,
    				x: 320, y: 70, width: 150,
    				labelWidth: 70
				},{
					fieldLabel: 'Fecha Inicio Tramite',
					id: 'lc_form_lc_fecha_field',
    				xtype: 'datefield',
    				name: 'lc_fecha',
    				format: 'd/m/Y',
    				x: 10, y: 70, width: 220,
    				labelWidth: 110
				},{
    				id: 'lc_form_lc_clase_field',
    				xtype: 'combobox',
    				fieldLabel: 'Clase',
    				name: 'lc_clase',
    				displayField: 'desc',
    				valueField: 'id',
    				store: lc.clase_store,
    				queryMode: 'local',
    				x: 10, y: 100, width: 200,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				},{
    				id: 'lc_form_lc_categoria_field',
    				xtype: 'combobox',
    				fieldLabel: 'Categoria',
    				name: 'lc_categoria',
    				displayField: 'desc',
    				valueField: 'id',
    				store: lc.categoria_store,
    				queryMode: 'local',
    				x: 220, y: 100, width: 200,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 70
				},{ 
					fieldLabel: 'N&ordm; Resolucion',
					id: 'lc_form_lc_resolucion_field',
    				xtype: 'textfield',
    				name: 'lc_resolucion',
    				x: 10, y: 130, width: 160
				},{
					fieldLabel: 'Fecha Resolucion',
					id: 'lc_form_lc_resolucion_fecha_field',
    				xtype: 'datefield',
    				name: 'lc_resolucion_fecha',
    				format: 'd/m/Y',
    				x: 180, y: 130, width: 200,
    				labelWidth: 100
				},{
					fieldLabel: 'Fecha Exp.',
					id: 'lc_form_lc_fecha_exp_field',
    				xtype: 'datefield',
    				name: 'lc_fecha_exp',
    				format: 'd/m/Y',
    				x: 10, y: 160, width: 180
				},{
					fieldLabel: 'Fecha Ven.',
					id: 'lc_form_lc_fecha_ven_field',
    				xtype: 'datefield',
    				name: 'lc_fecha_ven',
    				format: 'd/m/Y',
    				x: 210, y: 160, width: 170,
    				labelWidth: 70
				},{ 
					fieldLabel: 'Nro. Licencia',
					id: 'lc_form_lc_codigo_field',
    				xtype: 'textfield',
    				name: 'lc_codigo',
    				x: 10, y: 190, width: 160,
    				maxLength: 11
				},{ 
					fieldLabel: 'Grupo Sang.',
					id: 'lc_form_lc_grupo_s_field',
    				xtype: 'textfield',
    				name: 'lc_grupo_s',
    				x: 180, y: 190, width: 140,
    				labelWidth: 70
				},{ 
					fieldLabel: 'Restricciones',
					id: 'lc_form_lc_restricciones_field',
    				xtype: 'textfield',
    				name: 'lc_restricciones',
    				x: 10, y: 220, width: 380
				},{
					xtype: 'displayfield',
					id: 'lc_form_lc_recibo_validado_flag_displayfield',
					fieldLabel: 'Se ha validado el recibo?',
					name: 'lc_recibo_validado_flag',
					x: 10, y: 250, width: 180,
					labelWidth: 145
				},{
					xtype: 'displayfield',
					id: 'lc_form_plantilla_desc_displayfield',
					fieldLabel: 'Plantilla para generar PDF',
					name: 'plantilla_desc',
					x: 200, y: 250, width: 250,
					labelWidth: 140
				}]
			},{
				xtype: 'panel',
				layout: 'border',
				region: 'center',
				items: [{
					xtype: 'grid',
					id: 'lc_form_doc_requisito_grid',
					region: 'center', 
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: lc.doc_requisito_store,
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
							lc.doc_requisito_add_or_edit();
						}
					},{
						text: '-', 
						tooltip: 'Quitar', tooltipType: 'title',
						handler: function() {
							lc.doc_requisito_delete_window();
						}
					}],
					listeners: {
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							lc.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new lc
				},{
					xtype: 'grid',
					id: 'lc_form_doc_estado_grid',
					region: 'south', 
					height: 170,
					//split:true, 
					//forceFit:true,
					sortableColumns: false,
					enableColumnHide: false,
					store: lc.doc_estado_store,
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
				                    lc.doc_estado_add_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('lc_form').getRecord();
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
				                    lc.doc_estado_delete_window(record);
				                },
				                isDisabled: function (view, rowIndex, colIndex, item, record) {
				                	var doc = Ext.getCmp('lc_form').getRecord();
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
							lc.doc_requisito_add_or_edit();
						},
						hidden: true
					}],
					bbar:[{
						id: 'lc_form_doc_estado_obs_field',
						xtype: 'displayfield',
						fieldLabel: 'Observacion',
						value: ''
					}],
					listeners: {
						select: function (ths, record, index, eOpts) {
							Ext.getCmp('lc_form_doc_estado_obs_field').setValue(record.get('doc_estado_obs'));
						},
						rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts) {
							//lc.doc_requisito_add_or_edit();
						}
					},
					hidden: false // hide on new lc
				}]
			}]
		}]
	});

	lc.reload_list = function (select_id) {
		lc.lc_id_selected = select_id||0;
		//lc.main_store.reload();
		lc.main_store.reload({
			params: {
				search_by: Ext.getCmp('lc_search_by_cb').getValue(),
				search_text: Ext.getCmp('lc_search_text').getValue()
			}
		});
	};

	lc.doc_requisito_reload_list = function (doc_id) {
		lc.doc_requisito_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};

	lc.doc_requisito_add_or_edit = function () {
		var rows = Ext.getCmp('lc_form_doc_requisito_grid').getSelection();
		if (rows.length > 0) {
			record = rows[0];
			if ( record.get('doc_requisito_id') > 0 ) {
				lc.doc_requisito_edit_window();
			} else {
				lc.doc_requisito_add_window();
			}
		} else {
			Ext.Msg.alert('Agregar o Modificar documento', 'Seleccione un registro por favor.');
		}
	};

	lc.doc_estado_reload_list = function (doc_id) {
		lc.doc_estado_store.reload({
			params: {
				doc_id: doc_id
			}
		});
	};
</script>