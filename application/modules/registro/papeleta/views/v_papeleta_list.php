<script>
	papeleta.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'papeleta_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Numero', dataIndex:'papeleta_numero', width: 60},
				{text:'Fecha', dataIndex:'papeleta_fecha', width: 70},
				{text:'Nombres o Razon social', dataIndex:'contribuyente_nombres', width: 200},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 150},
				{text:'T. Infra.', dataIndex:'tipo_infraccion_desc', width: 70},
				{text:'Cod.Infra.', dataIndex:'papeleta_infraccion_codigo', width: 50},
				{text:'Med.Prevent.', dataIndex:'medida_preventiva_desc', width: 100},
				{text:'Estado', dataIndex:'estado_papeleta_desc', width: 80,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
					    /*switch (value) {
					    	case 'A':
					    		value = '<span style="color: green;">'+value+'</span>';
					    	break;
					    }*/
					    return value;
					}
				}
			],
			tbar:[{
				text:'Nuevo', 
				handler: function() {
					papeleta.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('papeleta_main_grid').getSelection();
					if (rows.length>0) {
						papeleta.edit_window(rows[0].get('papeleta_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Eliminar', 
					handler: function() {
						papeleta.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('registro.papeleta.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('papeleta_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.papeleta', rows[0].get('papeleta_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'papeleta_search_by_cb',
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
				xtype:'textfield',
				id: 'papeleta_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('papeleta_search_bt').click(e);
						}
					}
				}
			},{
				id: 'papeleta_search_bt',
				text:'Consultar', handler: function() {
					papeleta.reload_list();
				}
			}],
			store: papeleta.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: papeleta.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts ) {
					if (!papeleta.form_editing) {
						var f = Ext.getCmp('papeleta_form');
						f.loadRecord(record);
						Ext.getCmp('papeleta_form_papeleta_id_displayfield').setValue(record.get('papeleta_id'));
						Ext.getCmp('papeleta_form_save_bt').hide();
						Ext.getCmp('papeleta_form_cancel_bt').hide();

						Ext.getCmp('papeleta_form_contribuyente_id_field').hide();
						Ext.getCmp('papeleta_form_contribuyente_nomape_field').show();
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts ) {
					if (!papeleta.form_editing) {
						papeleta.edit_window(record.get('papeleta_id'));
					}
				}
			}
		},{
			xtype: 'panel',
			region: 'east',
			layout: 'border',
			split: true,
			width: 470,
			items: [{
				xtype: 'form',
				id: 'papeleta_form',
				url: 'papeleta/AddOrUpdate',
				layout: 'absolute',
				region: 'center',
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					id: 'papeleta_form_title_label',
					xtype: 'label',
					text: 'Papeleta',
					style: {
						fontWeight: 'bold'
					}
				},'-','->',{
					text: 'Guardar',
					id: 'papeleta_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('papeleta_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									papeleta.form_editing = false;
									papeleta.reload_list(action.result.rowid);	
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
					id: 'papeleta_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('papeleta_form_save_bt').hide();
						Ext.getCmp('papeleta_form_cancel_bt').hide();
						papeleta.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 100,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'papeleta_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'papeleta_form_papeleta_id_field',
					name: 'papeleta_id'
				},{
					xtype: 'displayfield',
					id: 'papeleta_form_papeleta_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
					fieldLabel: 'Numero Papeleta',
					id: 'papeleta_form_papeleta_numero_field',
    				xtype: 'textfield',
    				name: 'papeleta_numero',
    				x: 10, y: 30, width: 240
				},{
					fieldLabel: 'Fecha',
					id: 'papeleta_form_papeleta_fecha_field',
    				xtype: 'datefield',
    				name: 'papeleta_fecha',
    				x: 10, y: 60, width: 250
				},{
    				xtype: 'combobox',
    				id: 'papeleta_form_contribuyente_id_field',
    				name: 'contribuyente_id',
    				fieldLabel: 'Contribuyente',
    				displayField: 'contribuyente_nomape',
    				valueField: 'contribuyente_id',
    				store: papeleta.contribuyente_store,
    				queryMode: 'remote',
    				triggerAction: 'last', // query
    				minChars: 2,
    				matchFieldWidth: false,
    				x: 10, y: 90, width: 350,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('papeleta_form_contribuyente_numero_doc_field').setValue(record.get('contribuyente_numero_doc'));
				    	}
    				},
    				hidden: true // only for edit
				},{ // only for display
					fieldLabel: 'Contribuyente',
					id: 'papeleta_form_contribuyente_nomape_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nomape',
    				x: 10, y: 90, width: 350
				},{
					id: 'papeleta_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				editable: false,
    				x: 370, y: 90, width: 75
				},{
    				xtype: 'combobox',
    				id: 'papeleta_form_tipo_infraccion_id_field',
    				name: 'tipo_infraccion_id',
    				fieldLabel: 'Tipo de Infraccion',
    				//fieldStyle: 'color: gray',
    				displayField: 'tipo_infraccion_desc',
    				valueField: 'tipo_infraccion_id',
    				store: papeleta.tipo_infraccion_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 120, width: 210,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {						    		
				    	}
    				}
				},{
					fieldLabel: 'Codigo Infraccion',
					id: 'papeleta_form_papeleta_infraccion_codigo_field',
    				xtype: 'textfield',
    				name: 'papeleta_infraccion_codigo',
    				x: 230, y: 120, width: 150
				},{
    				xtype: 'combobox',
    				id: 'papeleta_form_medida_preventiva_id_field',
    				name: 'medida_preventiva_id',
    				fieldLabel: 'Medida Preventiva',
    				displayField: 'medida_preventiva_desc',
    				valueField: 'medida_preventiva_id',
    				store: papeleta.medida_preventiva_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 150, width: 350,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {						    		
				    	}
    				}
				},{
    				xtype: 'combobox',
    				id: 'papeleta_form_estado_papeleta_id_field',
    				name: 'estado_papeleta_id',
    				fieldLabel: 'Estado',
    				displayField: 'estado_papeleta_desc',
    				valueField: 'estado_papeleta_id',
    				store: papeleta.estado_papeleta_store,
    				queryMode: 'local',
    				matchFieldWidth: false,
    				x: 10, y: 180, width: 250,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
				    	}
    				}
				}]
			}]
		}]
	});

	papeleta.reload_list = function (select_id) {
		papeleta.papeleta_id_selected = select_id||0;
		//papeleta.main_store.reload();
		papeleta.main_store.reload({
			params: {
				search_by: Ext.getCmp('papeleta_search_by_cb').getValue(),
				search_text: Ext.getCmp('papeleta_search_text').getValue()
			}
		});
	};
</script>