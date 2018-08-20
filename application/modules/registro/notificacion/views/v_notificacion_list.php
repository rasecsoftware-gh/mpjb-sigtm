<script>
	notificacion.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'notificacion_main_grid',
			region: 'center', 
			//split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Numero', dataIndex:'notificacion_numero', width: 60},
				{text:'Fecha', dataIndex:'notificacion_fecha', width: 70},
				{text:'Nombres o Razon social', dataIndex:'contribuyente_nombres', width: 180},
				{text:'Apellidos', dataIndex:'contribuyente_apellidos', width: 120},
				{text:'Acto Admin.', dataIndex:'notificacion_acto_administrativo', width: 150},
				{text:'Acta Fecha', dataIndex:'notificacion_acta_fecha', width: 70},
				{text:'Se nego a recibir', dataIndex:'notificacion_acta_snar', width: 70},
				{text:'Se nego a firmar', dataIndex:'notificacion_acta_snaf', width: 70},
				{text:'Se dejo bajo puerta', dataIndex:'notificacion_acta_sdbp', width: 70}
			],
			tbar:[{
				text:'Nuevo', 
				handler: function() {
					notificacion.new_window();
				}
			},{
				text:'Modificar', 
				handler: function() {
					var rows = Ext.getCmp('notificacion_main_grid').getSelection();
					if (rows.length>0) {
						notificacion.edit_window(rows[0].get('notificacion_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				menu: [{
					text: 'Eliminar', 
					handler: function() {
						notificacion.delete_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('registro.notificacion.delete'); ?>,
				},'-',{
					text: 'Ver informacion de registro', 
					handler: function() {
						var rows = Ext.getCmp('notificacion_main_grid').getSelection();
						if (rows.length>0) {
							syslog.show_window('public.notificacion', rows[0].get('notificacion_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}]
			},'->',{
				xtype: 'combobox',
				id: 'notificacion_search_by_cb',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				matchFieldWidth: false,
				width: 140,
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'acto', search_desc: 'Por Acto administrativo'}
					]
				})
			},{
				xtype:'textfield',
				id: 'notificacion_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('notificacion_search_bt').click(e);
						}
					}
				}
			},{
				id: 'notificacion_search_bt',
				text:'Consultar', handler: function() {
					notificacion.reload_list();
				}
			}],
			store: notificacion.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: notificacion.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function (ths, record, index, eOpts ) {
					if (!notificacion.form_editing) {
						var f = Ext.getCmp('notificacion_form');
						f.loadRecord(record);
						Ext.getCmp('notificacion_form_notificacion_id_displayfield').setValue(record.get('notificacion_id'));
						Ext.getCmp('notificacion_form_save_bt').hide();
						Ext.getCmp('notificacion_form_cancel_bt').hide();

						Ext.getCmp('notificacion_form_contribuyente_id_field').hide();
						Ext.getCmp('notificacion_form_contribuyente_nomape_field').show();
					}
				},
				rowdblclick: function ( ths, record, tr, rowIndex, e, eOpts ) {
					if (!notificacion.form_editing) {
						notificacion.edit_window(record.get('notificacion_id'));
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
				id: 'notificacion_form',
				url: 'notificacion/AddOrUpdate',
				layout: 'absolute',
				region: 'center',
				bodyStyle: {
					//background: '#4c9dd8'
				},
				tbar:[{
					id: 'notificacion_form_title_label',
					xtype: 'label',
					text: notificacion.title,
					style: {
						fontWeight: 'bold'
					}
				},'-','->',{
					text: 'Guardar',
					id: 'notificacion_form_save_bt',
					hidden: true,
					handler: function () {
						var frm = Ext.getCmp('notificacion_form');
						frm.mask('guardando');
						frm.submit({
							success: function(form, action) {
								frm.unmask();
								if (action.result.success) {
									notificacion.form_editing = false;
									notificacion.reload_list(action.result.rowid);	
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
					id: 'notificacion_form_cancel_bt',
					hidden: true,
					handler: function () {
						Ext.getCmp('notificacion_form_save_bt').hide();
						Ext.getCmp('notificacion_form_cancel_bt').hide();
						notificacion.form_editing = false;
					}
				}],
				defaults: {
					labelWidth: 110,
					labelStyle: 'color: gray'
				},
				items: [{
					xtype: 'hidden',
					id: 'notificacion_form_operation_field',
					name: 'operation',
					value: 'edit'
				},{
					xtype: 'hidden',
					id: 'notificacion_form_notificacion_id_field',
					name: 'notificacion_id'
				},{
					xtype: 'displayfield',
					id: 'notificacion_form_notificacion_id_displayfield',
					fieldLabel: 'ID',
					x: 10, y: 0,
					width: 200
				},{
					fieldLabel: 'Numero Notificacion',
					id: 'notificacion_form_notificacion_numero_field',
    				xtype: 'textfield',
    				name: 'notificacion_numero',
    				x: 10, y: 30, width: 240
				},{
					fieldLabel: 'Fecha',
					id: 'notificacion_form_notificacion_fecha_field',
    				xtype: 'datefield',
    				name: 'notificacion_fecha',
    				x: 10, y: 60, width: 250
				},{
    				xtype: 'combobox',
    				id: 'notificacion_form_contribuyente_id_field',
    				name: 'contribuyente_id',
    				fieldLabel: 'Contribuyente',
    				displayField: 'contribuyente_nomape',
    				valueField: 'contribuyente_id',
    				store: notificacion.contribuyente_store,
    				queryMode: 'remote',
    				triggerAction: 'last', // query
    				minChars: 2,
    				matchFieldWidth: false,
    				x: 10, y: 90, width: 350,
    				editable: true,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('notificacion_form_contribuyente_numero_doc_field').setValue(record.get('contribuyente_numero_doc'));
				    	}
    				},
    				hidden: true // only for edit
				},{ // only for display
					fieldLabel: 'Contribuyente',
					id: 'notificacion_form_contribuyente_nomape_field',
    				xtype: 'textfield',
    				name: 'contribuyente_nomape',
    				x: 10, y: 90, width: 350
				},{
					id: 'notificacion_form_contribuyente_numero_doc_field',
    				xtype: 'textfield',
    				name: 'contribuyente_numero_doc',
    				editable: false,
    				x: 370, y: 90, width: 75
				},{
					fieldLabel: 'Acto Administrativo',
					id: 'notificacion_form_notificacion_acto_administrativo_field',
    				xtype: 'textfield',
    				name: 'notificacion_acto_administrativo',
    				x: 10, y: 120, width: 400,
    				maxLength: 100
				},{
					fieldLabel: 'Acta - Fecha',
					id: 'notificacion_form_notificacion_acta_fecha_field',
    				xtype: 'datefield',
    				name: 'notificacion_acta_fecha',
    				x: 10, y: 150, width: 250
				},{
    				xtype: 'combobox',
    				id: 'notificacion_form_notificacion_acta_snar_field',
    				name: 'notificacion_acta_snar',
    				fieldLabel: 'Se nego a recibir?',
    				//fieldStyle: 'color: gray',
    				displayField: 'desc',
    				valueField: 'id',
    				store: notificacion.yesno_store,
    				queryMode: 'local',
    				x: 10, y: 180, width: 210,
    				editable: false
				},{
    				xtype: 'combobox',
    				id: 'notificacion_form_notificacion_acta_snaf_field',
    				name: 'notificacion_acta_snaf',
    				fieldLabel: 'Se nego a firmar?',
    				//fieldStyle: 'color: gray',
    				displayField: 'desc',
    				valueField: 'id',
    				store: notificacion.yesno_store,
    				queryMode: 'local',
    				x: 10, y: 210, width: 210,
    				editable: false
				},{
    				xtype: 'combobox',
    				id: 'notificacion_form_notificacion_acta_sdbp_field',
    				name: 'notificacion_acta_sdbp',
    				fieldLabel: 'Se dejo bajo puerta?',
    				//fieldStyle: 'color: gray',
    				displayField: 'desc',
    				valueField: 'id',
    				store: notificacion.yesno_store,
    				queryMode: 'local',
    				x: 10, y: 240, width: 210,
    				editable: false
				}]
			}]
		}]
	});

	notificacion.reload_list = function (select_id) {
		notificacion.notificacion_id_selected = select_id||0;
		//notificacion.main_store.reload();
		notificacion.main_store.reload({
			params: {
				search_by: Ext.getCmp('notificacion_search_by_cb').getValue(),
				search_text: Ext.getCmp('notificacion_search_text').getValue()
			}
		});
	};
</script>