<script>
	clit.plantilla_cambiar_window = function() {
		var rows = Ext.getCmp('clit_main_grid').getSelection();
		var record = null;
		if (rows.length > 0) {
			record = rows[0];
			if ( record.get('estado_doc_final_flag') == 'S' ) {
				Ext.Msg.alert('Cambiar Plantilla', 'No es posible modificar la plantilla en el estado actual.');
				return false;	
			}
		} else {
			Ext.Msg.alert('Error', 'Seleccione un documento por favor.');
			return false;
		} 
		var w_config = {
			id: 'clit_plantilla_cambiar_window',
			title: 'Cambiar plantilla', 
			modal: true,
			width: 450,
			height: 230, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'clit/cambiarPlantilla',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'clit_plantilla_cambiar_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'clit_id',
					value: record.get('clit_id'),
				},{
    				xtype: 'combobox',
    				id: 'clit_plantilla_cambiar_form_plantilla_id_field',
    				name: 'plantilla_id',
    				fieldLabel: 'Plantilla',
    				displayField: 'plantilla_desc',
    				valueField: 'plantilla_id',
    				store: clit.plantilla_store,
    				queryMode: 'local',
    				x: 10, y: 10, width: 400,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('clit_plantilla_cambiar_form_plantilla_archivo_field').setValue(record.get('plantilla_archivo'));
    						Ext.getCmp('clit_plantilla_cambiar_form_plantilla_nota_field').setValue(record.get('plantilla_nota'));
				    	}
    				}
				},{
					id: 'clit_plantilla_cambiar_form_plantilla_archivo_field',
					xtype: 'displayfield',
					fieldLabel: 'Archivo fuente',
				    x: 10, y: 40, width: 400
				},{
					id: 'clit_plantilla_cambiar_form_plantilla_nota_field',
					xtype: 'displayfield',
					fieldLabel: 'Descripcion Adic.',
				    x: 10, y: 70, width: 400
				}]
			}],
			buttons:[{
				text: 'Cambiar', handler: function() {
					var frm = Ext.getCmp('clit_plantilla_cambiar_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('clit_plantilla_cambiar_window').close();
								clit.reload_list(record.get('clit_id'));
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Cambiar Plantilla', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text: 'Salir', handler: function() {
					Ext.getCmp('clit_plantilla_cambiar_window').close();
				}
			}],
			listeners: {
				show: function () {
					var w = Ext.getCmp('clit_plantilla_cambiar_form');
					w.mask('cargando');					
					clit.plantilla_store.reload();
					sys_storeLoadMonitor([clit.plantilla_store], function () {
						Ext.getCmp('clit_plantilla_cambiar_form_plantilla_id_field').setValue(record.get('plantilla_id'));
						var plantilla = Ext.getCmp('clit_plantilla_cambiar_form_plantilla_id_field').getSelection();
						Ext.getCmp('clit_plantilla_cambiar_form_plantilla_archivo_field').setValue(plantilla.get('plantilla_archivo'));
    					Ext.getCmp('clit_plantilla_cambiar_form_plantilla_nota_field').setValue(plantilla.get('plantilla_nota'));
						w.unmask();
		    		});
				}
			}
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>