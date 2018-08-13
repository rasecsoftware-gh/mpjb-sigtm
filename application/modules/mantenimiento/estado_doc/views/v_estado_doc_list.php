<script>
	estado_doc.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'estado_doc_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Tipo Doc.', dataIndex:'tipo_doc_id', width: 75},
				{text:'Descripcion', dataIndex:'estado_doc_desc', width: 200,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
					    metaData.tdStyle = 'color: '+record.get('estado_doc_color')+';';
					    return value;
					}
				},
				{text:'VRR', dataIndex:'estado_doc_requisito_requerido_flag', width: 60},
				{text:'Correlativo', dataIndex:'estado_doc_correlativo_flag', width: 70},
				{text:'Generar PDF', dataIndex:'estado_doc_generar_pdf_flag', width: 90},
				{text:'Modificar', dataIndex:'estado_doc_modificar_flag', width: 60},
				{text:'Final', dataIndex:'estado_doc_final_flag', width: 60},
				{text:'Orden', dataIndex:'estado_doc_index', width: 50}
			],
			tbar:[{
				text:'Nuevo', 
				handler: function() {
					estado_doc.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('estado_doc_main_grid').getSelection();
					if (rows.length>0) {
						estado_doc.edit_window(rows[0].get('estado_doc_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Eliminar', 
					handler: function() {
						estado_doc.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('estado_doc.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('estado_doc_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.estado_doc', rows[0].get('estado_doc_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'estado_doc_search_by_cb',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				matchFieldWidth: false,
				width: 140,
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'estado', search_desc: 'Por Estado'}
					]
				})
			},{
				xtype: 'textfield',
				id: 'estado_doc_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('estado_doc_search_bt').click(e);
						}
					}
				}
			},{
				id: 'estado_doc_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					estado_doc.reload_list();
				}
			}],
			store: estado_doc.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: estado_doc.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts ) {
					if (!estado_doc.form_editing) {
						var f = Ext.getCmp('estado_doc_form');
						f.loadRecord(record);
						Ext.getCmp('estado_doc_form_title_label').setText(estado_doc.title);
						Ext.getCmp('estado_doc_form_estado_doc_id_displayfield').setValue(record.get('estado_doc_id'));
						Ext.getCmp('estado_doc_form_save_bt').hide();
						Ext.getCmp('estado_doc_form_cancel_bt').hide();
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts ) {
					if (!estado_doc.form_editing) {
						estado_doc.edit_window(record.get('estado_doc_id'));
					}
				}
			}
		},{
			xtype: 'panel',
			region: 'east',
			layout: 'border',
			split: true,
			width: 450,
			items: [{
				xtype: 'form',
				id: 'estado_doc_form',
				url: 'estado_doc/AddOrUpdate',
				layout: 'absolute',
				region: 'center',
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					id: 'estado_doc_form_title_label',
					xtype: 'label',
					text: estado_doc.title,
					style: {
						fontWeight: 'bold'
					}
				},'-','->',{
					text: 'Guardar',
					id: 'estado_doc_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('estado_doc_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									estado_doc.form_editing = false;
									estado_doc.reload_list(action.result.rowid);	
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
					id: 'estado_doc_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('estado_doc_form_save_bt').hide();
						Ext.getCmp('estado_doc_form_cancel_bt').hide();
						
						estado_doc.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 100,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'estado_doc_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'estado_doc_form_estado_doc_id_field',
					name: 'estado_doc_id'
				},{
					xtype: 'displayfield',
					id: 'estado_doc_form_estado_doc_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_tipo_doc_id_field',
    				name: 'tipo_doc_id',
    				fieldLabel: 'Tipo de Doc.',
    				//fieldStyle: 'color: gray',
    				displayField: 'tipo_doc_desc',
    				valueField: 'tipo_doc_id',
    				store: estado_doc.tipo_doc_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 30, width: 420,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {						    		
				    	}
    				}
				},{
					fieldLabel: 'Descripcion',
					id: 'estado_doc_form_estado_doc_desc_field',
    				xtype: 'textfield',
    				name: 'estado_doc_desc',
    				x: 10, y: 60, width: 420
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_modificar_flag_field',
    				name: 'estado_doc_modificar_flag',
    				fieldLabel: 'Permite modificar el registro?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.yes_no_store,
    				queryMode: 'local',
    				x: 10, y: 90, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 160
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_generar_pdf_flag_field',
    				name: 'estado_doc_generar_pdf_flag',
    				fieldLabel: 'Permite generar documento (PDF)?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.yes_no_store,
    				queryMode: 'local',
    				x: 10, y: 120, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 185
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_requisito_requerido_flag_field',
    				name: 'estado_doc_requisito_requerido_flag',
    				fieldLabel: 'Verificar requisitos requeridos?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.yes_no_store,
    				queryMode: 'local',
    				x: 10, y: 150, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 160
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_correlativo_flag_field',
    				name: 'estado_doc_correlativo_flag',
    				fieldLabel: 'Depende de un estado anterior o es inicial (correlativo)?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.yes_no_store,
    				queryMode: 'local',
    				x: 10, y: 180, width: 340,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 285
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_final_flag_field',
    				name: 'estado_doc_final_flag',
    				fieldLabel: 'Es un estado final?',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.yes_no_store,
    				queryMode: 'local',
    				x: 10, y: 210, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				},
    				labelWidth: 160
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_color_field',
    				name: 'estado_doc_color',
    				fieldLabel: 'Color',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.color_store,
    				queryMode: 'local',
    				x: 10, y: 240, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				},{
    				xtype: 'combobox',
    				id: 'estado_doc_form_estado_doc_index_field',
    				name: 'estado_doc_index',
    				fieldLabel: 'Orden',
    				displayField: 'desc',
    				valueField: 'id',
    				store: estado_doc.index_store,
    				queryMode: 'local',
    				x: 10, y: 270, width: 200,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				}]
			}]
		}]
	});

	estado_doc.reload_list = function (select_id) {
		estado_doc.estado_doc_id_selected = select_id||0;
		//estado_doc.main_store.reload();
		estado_doc.main_store.reload({
			params: {
				search_by: Ext.getCmp('estado_doc_search_by_cb').getValue(),
				search_text: Ext.getCmp('estado_doc_search_text').getValue()
			}
		});
	};
</script>