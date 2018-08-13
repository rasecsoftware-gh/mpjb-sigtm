<script>
	plantilla.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'plantilla_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Tipo Doc.', dataIndex:'tipo_doc_id', width: 70},
				{text:'Tipo Documento', dataIndex:'tipo_doc_desc', width: 250},
				{text:'Descripcion', dataIndex:'plantilla_desc', width: 200},
				{
		            xtype: 'actioncolumn',
		            width: 30,
		            items: [{
		                icon: 'tools/icons/page_white_word.png',  // Use a URL in the icon config
		                tooltip: 'Descargar archivo de plantilla',
		                handler: function(grid, rowIndex, colIndex, item, e, record) {
		                    window.open('dbfiles/public.plantilla/'+record.get('plantilla_archivo'), '_blank');
		                },
		                isDisabled: function (view, rowIndex, colIndex, item, record) {
		                	return !($.trim(record.get('plantilla_archivo')).length > 0);
		                }
		            }]
		        },
				{text:'Original', dataIndex:'plantilla_original_flag', width: 60, align: 'center'},
				{text:'Estado', dataIndex:'plantilla_estado', width: 70, align: 'center',
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
					    switch (value) {
					    	case 'A':
					    		metaData.tdStyle = 'color: green;';
					    	break;
					    	case 'I':
					    		metaData.tdStyle = 'color: red;';
					    	break;
					    }
					    return value;
					}
				}
			],
			tbar:[{
				text:'Nuevo', 
				handler: function() {
					plantilla.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('plantilla_main_grid').getSelection();
					if (rows.length>0) {
						plantilla.edit_window(rows[0].get('plantilla_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Eliminar', 
					handler: function() {
						plantilla.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('plantilla.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('plantilla_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.plantilla', rows[0].get('plantilla_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'plantilla_search_by_cb',
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
				id: 'plantilla_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('plantilla_search_bt').click(e);
						}
					}
				}
			},{
				id: 'plantilla_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					plantilla.reload_list();
				}
			}],
			store: plantilla.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: plantilla.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts ) {
					if (!plantilla.form_editing) {
						var f = Ext.getCmp('plantilla_form');
						f.loadRecord(record);
						Ext.getCmp('plantilla_form_title_label').setText(plantilla.title);
						Ext.getCmp('plantilla_form_plantilla_id_displayfield').setValue(record.get('plantilla_id'));
						Ext.getCmp('plantilla_form_save_bt').hide();
						Ext.getCmp('plantilla_form_cancel_bt').hide();
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts ) {
					if (!plantilla.form_editing) {
						plantilla.edit_window(record.get('plantilla_id'));
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
				id: 'plantilla_form',
				url: 'plantilla/AddOrUpdate',
				layout: 'absolute',
				region: 'center',
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					id: 'plantilla_form_title_label',
					xtype: 'label',
					text: plantilla.title,
					style: {
						fontWeight: 'bold'
					}
				},'-','->',{
					text: 'Guardar',
					id: 'plantilla_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('plantilla_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									plantilla.form_editing = false;
									plantilla.reload_list(action.result.rowid);	
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
					id: 'plantilla_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('plantilla_form_save_bt').hide();
						Ext.getCmp('plantilla_form_cancel_bt').hide();
						
						plantilla.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 100,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'plantilla_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'plantilla_form_plantilla_id_field',
					name: 'plantilla_id'
				},{
					xtype: 'displayfield',
					id: 'plantilla_form_plantilla_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
    				xtype: 'combobox',
    				id: 'plantilla_form_tipo_doc_id_field',
    				name: 'tipo_doc_id',
    				fieldLabel: 'Tipo de Doc.',
    				//fieldStyle: 'color: gray',
    				displayField: 'tipo_doc_desc',
    				valueField: 'tipo_doc_id',
    				store: plantilla.tipo_doc_store,
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
					id: 'plantilla_form_plantilla_desc_field',
    				xtype: 'textfield',
    				name: 'plantilla_desc',
    				x: 10, y: 60, width: 420
				},{
					fieldLabel: 'Archivo',
					id: 'plantilla_form_plantilla_archivo_field', // 
    				xtype: 'displayfield',
    				name: 'plantilla_archivo',
    				x: 10, y: 90, width: 420
				},{
					fieldLabel: 'Cargar archivo',
					id: 'plantilla_form_plantilla_file_field', // 
    				xtype: 'filefield',
    				name: 'plantilla_file',
    				x: 10, y: 120, width: 420
				},{
					fieldLabel: 'Nota',
					id: 'plantilla_form_plantilla_nota_field',
    				xtype: 'textfield',
    				name: 'plantilla_nota',
    				x: 10, y: 150, width: 420
				},{
    				xtype: 'combobox',
    				id: 'plantilla_form_plantilla_estado_field',
    				name: 'plantilla_estado',
    				fieldLabel: 'Estado',
    				displayField: 'desc',
    				valueField: 'id',
    				store: plantilla.estado_store,
    				queryMode: 'local',
    				x: 10, y: 180, width: 200,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				}]
			}]
		}]
	});

	plantilla.reload_list = function (select_id) {
		plantilla.plantilla_id_selected = select_id||0;
		//plantilla.main_store.reload();
		plantilla.main_store.reload({
			params: {
				search_by: Ext.getCmp('plantilla_search_by_cb').getValue(),
				search_text: Ext.getCmp('plantilla_search_text').getValue()
			}
		});
	};
</script>