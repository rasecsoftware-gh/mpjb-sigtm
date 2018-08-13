<script>
	cat.plantilla_cambiar_window = function() {
		var rows = Ext.getCmp('cat_main_grid').getSelection();
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
			id: 'cat_plantilla_cambiar_window',
			title: 'Cambiar plantilla', 
			modal: true,
			width: 450,
			height: 230, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				url: 'cat/cambiarPlantilla',
				bodyPadding: 10,
				region: 'center',
				layout: 'absolute',
				id: 'cat_plantilla_cambiar_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'cat_id',
					value: record.get('cat_id'),
				},{
    				xtype: 'combobox',
    				id: 'cat_plantilla_cambiar_form_plantilla_id_field',
    				name: 'plantilla_id',
    				fieldLabel: 'Plantilla',
    				displayField: 'plantilla_desc',
    				valueField: 'plantilla_id',
    				store: cat.plantilla_store,
    				queryMode: 'local',
    				x: 10, y: 10, width: 400,
    				editable: false,
    				listeners: {
    					select: function(combo, record, eOpts ) {
    						Ext.getCmp('cat_plantilla_cambiar_form_plantilla_archivo_field').setValue(record.get('plantilla_archivo'));
    						Ext.getCmp('cat_plantilla_cambiar_form_plantilla_nota_field').setValue(record.get('plantilla_nota'));
				    	}
    				}
				},{
					id: 'cat_plantilla_cambiar_form_plantilla_archivo_field',
					xtype: 'displayfield',
					fieldLabel: 'Archivo fuente',
				    x: 10, y: 40, width: 400
				},{
					id: 'cat_plantilla_cambiar_form_plantilla_nota_field',
					xtype: 'displayfield',
					fieldLabel: 'Descripcion Adic.',
				    x: 10, y: 70, width: 400
				}]
			}],
			buttons:[{
				text: 'Cambiar', handler: function() {
					var frm = Ext.getCmp('cat_plantilla_cambiar_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('cat_plantilla_cambiar_window').close();
								cat.reload_list(record.get('cat_id'));
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
					Ext.getCmp('cat_plantilla_cambiar_window').close();
				}
			}],
			listeners: {
				show: function () {
					var w = Ext.getCmp('cat_plantilla_cambiar_form');
					w.mask('cargando');					
					cat.plantilla_store.reload();
					sys_storeLoadMonitor([cat.plantilla_store], function () {
						Ext.getCmp('cat_plantilla_cambiar_form_plantilla_id_field').setValue(record.get('plantilla_id'));
						var plantilla = Ext.getCmp('cat_plantilla_cambiar_form_plantilla_id_field').getSelection();
						Ext.getCmp('cat_plantilla_cambiar_form_plantilla_archivo_field').setValue(plantilla.get('plantilla_archivo'));
    					Ext.getCmp('cat_plantilla_cambiar_form_plantilla_nota_field').setValue(plantilla.get('plantilla_nota'));
						w.unmask();
		    		});
				}
			}
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>