<script>
	contribuyente.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'contribuyente_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'TP', dataIndex:'tipo_persona_id', width: 50},
				{text:'TD', dataIndex:'tipo_doc_identidad_desc', width: 60},
				{text:'Num. Doc.', dataIndex:'contribuyente_numero_doc', width: 70},
				{text:'Nombres o Razon social', dataIndex:'contribuyente_nombres', width: 230},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 150},
				{text:'Direccion', dataIndex:'contribuyente_direccion', width: 150},
				{text:'Estado', dataIndex:'contribuyente_estado', width: 50,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
					    switch (value) {
					    	case 'A':
					    		value = '<span style="color: green;">'+value+'</span>';
					    	break;
					    	case 'I':
					    		value = '<span style="color: red;">'+value+'</span>';
					    	break;
					    }
					    return value;
					}
				}
			],
			tbar:[{
				text:'Nuevo', handler: function() {
					contribuyente.new_window();
				}
			},{
				text:'Modificar', handler: function() {
					var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
					if (rows.length>0) {
						contribuyente.edit_window(rows[0].get('contribuyente_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				hidden: true, //!<?php echo sys_session_hasRoleToString('rh.contribuyente'); ?>,
				menu: [{
					text: 'Emitir', handler: function() {
						var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
						if (rows.length>0) {
							contribuyente.emitir_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Entregar', handler: function() {
						var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
						if (rows.length>0) {
							contribuyente.entregar_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},'-',{
					text: 'Cancelar Emitido', handler: function() {
						var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
						if (rows.length>0) {
							contribuyente.cancelar_emitido_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Cancelar Entregado', handler: function() {
						var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
						if (rows.length>0) {
							contribuyente.cancelar_entregado_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},'-',{
					text: 'Re-Generar PDF', 
					handler: function() {
						var rows = Ext.getCmp('contribuyente_main_grid').getSelection();
						if (rows.length>0) {
							contribuyente.regenerar_pdf_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					},
					hidden: !<?php echo sys_session_hasRoleToString('sa'); ?>
				},{
					text: 'Generar PDFs&hellip;', 
					handler: function() {
						contribuyente.generar_pdfs_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('sa'); ?>
				}]
			},'-',{
				text: 'Plantillas&hellip;', 
				hidden: true,
				handler: function() {
					contribuyente.plantilla_window();
				}
			},'->',{
				xtype: 'combobox',
				id: 'contribuyente_search_by_cb',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				matchFieldWidth: false,
				width: 140,
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'tp', search_desc: 'Por Tipo de Persona'},
						{search_id: 'tdi', search_desc: 'Por Tipo Doc. Identidad'},
						{search_id: 'numero_doc', search_desc: 'Por Numero Doc.'},
						{search_id: 'estado', search_desc: 'Por Estado'}
					]
				})
			},{
				xtype:'textfield',
				id: 'contribuyente_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('contribuyente_search_bt').click(e);
						}
					}
				}
			},{
				id: 'contribuyente_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					contribuyente.reload_list();
				}
			}],
			store: contribuyente.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: contribuyente.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function(ths, record, index, eOpts ) {
					if (!contribuyente.form_editing) {
						var f = Ext.getCmp('contribuyente_form');
						f.loadRecord(record);
					}
				}
			}
		},{
			xtype: 'panel',
			region: 'east',
			layout: 'border',
			split: true,
			width: 400,
			items: [{
				xtype: 'form',
				id: 'contribuyente_form',
				url: 'contribuyente/AddOrUpdate',
				layout: 'absolute',
				region: 'center',
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					xtype: 'label',
					text: 'Contribuyente'
				},'->',{
					text: 'Guardar',
					id: 'contribuyente_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('contribuyente_form');
						frm.submit({
							success: function(form, action) {
								if (action.result.success) {
									contribuyente.main_store.reload(action.result.rowid);	
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
				},{
					text: 'Cancelar',
					id: 'contribuyente_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('contribuyente_form_save_bt').hide();
						Ext.getCmp('contribuyente_form_cancel_bt').hide();
					}
				}],
				defaults: {
					labelWidth: 120,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'contribuyente_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'displayfield',
					fieldLabel: 'ID',
					id: 'contribuyente_form_contribuyente_id_field',
					name: 'contribuyente_id',
					value: '',
					x: 10, y: 0,
					width: 200
				},{
    				xtype: 'combobox',
    				id: 'contribuyente_form_tipo_persona_id_field',
    				name: 'tipo_persona_id',
    				fieldLabel: 'Tipo de Persona',
    				//fieldStyle: 'color: gray',
    				displayField: 'tipo_persona_desc',
    				valueField: 'tipo_persona_id',
    				store: contribuyente.tipo_persona_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 30, width: 210,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {						    		
				    	}
    				}
				},{
    				xtype: 'combobox',
    				id: 'contribuyente_form_tipo_doc_identidad_id_field',
    				name: 'tipo_doc_identidad_id',
    				fieldLabel: 'Tipo Documento',
    				//fieldStyle: 'color: gray',
    				displayField: 'tipo_doc_identidad_desc',
    				valueField: 'tipo_doc_identidad_id',
    				store: contribuyente.tipo_doc_identidad_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 60, width: 240,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {						    		
				    	}
    				}
				},{
					fieldLabel: 'Numero Doc.',
					id: 'contribuyente_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				x: 10, y: 90, width: 240
				},{
					fieldLabel: 'Nombres o Razon soc.',
					id: 'contribuyente_form_contribuyente_nombres_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nombres',
    				x: 10, y: 120, width: 380
				},{
					fieldLabel: 'Apellidos',
					id: 'contribuyente_form_contribuyente_apellidos_field',
    				xtype: 'textfield',
    				name: 'contribuyente_apellidos',
    				x: 10, y: 150, width: 380
				},{
					fieldLabel: 'Departam. / Provinc.',
					id: 'contribuyente_form_ubigeo_departamento_provincia_field',
    				xtype: 'displayfield',
    				name: 'ubigeo_departamento_provincia',
    				value: 'Departamento / Provincia',
    				x: 10, y: 180, width: 380
				},{
    				xtype: 'combobox',
    				id: 'contribuyente_form_ubigeo_id_field',
    				name: 'ubigeo_id',
    				fieldLabel: 'Distrito',
    				displayField: 'ubigeo_distrito',
    				valueField: 'ubigeo_id',
    				store: contribuyente.ubigeo_store,
    				queryMode: 'remote',
    				matchFieldWidth: false,
    				x: 10, y: 210, width: 380,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('contribuyente_form_ubigeo_departamento_provincia_field').setValue(
    							record.get('ubigeo_departamento') + ' / ' + record.get('ubigeo_provincia')
    						);
				    	}
    				}
				},{
					fieldLabel: 'Direccion',
					id: 'contribuyente_form_contribuyente_direccion_field',
    				xtype: 'textfield',
    				name: 'contribuyente_direccion',
    				x: 10, y: 240, width: 380
				},{
					fieldLabel: 'Telefono',
					id: 'contribuyente_form_contribuyente_telefono_field',
    				xtype: 'textfield',
    				name: 'contribuyente_telefono',
    				x: 10, y: 270, width: 250
				},{
					fieldLabel: 'E-mail',
					id: 'contribuyente_form_contribuyente_email_field',
    				xtype: 'textfield',
    				name: 'contribuyente_email',
    				x: 10, y: 300, width: 380
				},{
					fieldLabel: 'Observacion',
					id: 'contribuyente_form_contribuyente_observacion_field',
    				xtype: 'textfield',
    				name: 'contribuyente_observacion',
    				x: 10, y: 330, width: 380
				}]
			}]
		}]
	});

	contribuyente.reload_list = function (select_id) {
		contribuyente.contribuyente_id_selected = select_id||0;
		//contribuyente.main_store.reload();
		contribuyente.main_store.reload({
			params: {
				search_by: Ext.getCmp('contribuyente_search_by_cb').getValue(),
				search_text: Ext.getCmp('contribuyente_search_text').getValue()
			}
		});
	};
</script>