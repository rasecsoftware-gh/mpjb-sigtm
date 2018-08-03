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
				{text:'Numero', dataIndex:'clit_numero', width: 60},
				{text:'Nombres o Razon Soc.', dataIndex:'contribuyente_nombres', width: 200},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 150},
				{text:'Fecha', dataIndex:'clit_fecha', width: 80},
				{text:'Resultado', dataIndex:'clit_resultado', width: 65},
				{text:'Estado', dataIndex:'estado_doc_desc', width: 80,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						if (record.estado_doc_color != '') {
							value = '<span style="color: '+record.estado_doc_color+';">'+value+'</span>';
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
					text: 'Inactivar', 
					handler: function() {
						clit.inactivar_window();
					}
				},{
					text: 'Activar', 
					handler: function() {
						clit.activar_window();
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
						Ext.getCmp('clit_form_clit_id_displayfield').setValue(record.get('clit_id'));
						Ext.getCmp('clit_form_save_bt').hide();
						Ext.getCmp('clit_form_cancel_bt').hide();
						Ext.getCmp('clit_form_contribuyente_id_field').hide();
						Ext.getCmp('clit_form_contribuyente_nomape_field').show();
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
				height: 200,
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					xtype: 'label',
					text: 'Constancia',
					style: {
						fontWeight: 'bold'
					}
				},'-','->',{
					text: 'Guardar',
					id: 'clit_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('clit_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									clit.form_editing = false;
									clit.main_store.reload(action.result.rowid);	
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
					fieldLabel: 'Resultado',
					id: 'clit_form_clit_resultado_field',
    				xtype: 'textfield',
    				name: 'clit_resultado',
    				x: 10, y: 120, width: 200
				}]
			},{
				xtype: 'grid',
				id: 'clit_form_doc_requisito_grid',
				region: 'center', 
				//split:true, 
				//forceFit:true,
				sortableColumns: false,
				enableColumnHide: false,
				store: clit.doc_requisito_store,
				columns:[
					{text:'Documento', dataIndex: 'tipo_doc_requisito_desc', width: 212},
					{text:'Fecha', dataIndex: 'doc_requisito_fecha', width: 70},
					{text:'Numero', dataIndex: 'doc_requisito_numero', width: 65},
					{text:'Requerido', dataIndex: 'tipo_doc_requisito_requerido_flag', width: 70},
					{text:'Cumple', dataIndex: 'doc_requisito_cumple_flag', width: 65,
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
							if (value == 'S') {
								value = '<span style="color: green;">'+value+'</span>';
							} else {
								return value;	
							}
						}
					}
				],
				tbar:[{
					text: 'Actualizar', 
					handler: function() {
						clit.doc_requisito_reload();
					}
				},{
					text: 'Quitar', 
					handler: function() {
						clit.doc_requisito_delete_window();
					}
				}],
				hidden: false // hide on new CLIT
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

	clit.doc_requisito_reload_list = function () {
		var rows = Ext.getCmp('clit_main_grid').getSelection();
		if (rows.length > 0) {
			var record = rows[0];
			clit.doc_requisito_store.reload({
				params: {
					doc_id: record.get('clit_id')
				}
			});
		}
	};
</script>