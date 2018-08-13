<script>
	cat.new_window = function() {
		if (cat.form_editing) return;
		cat.form_editing = true;
		var w = Ext.getCmp('cat_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'cat/getNewRow',
				reader:{
					type: 'json',
					rootProperty: 'data',
					messageProperty: 'msg'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var record = sender.getAt(0);
						sys_storeLoadMonitor([cat.plantilla_store], function () {
			    			var frm = Ext.getCmp('cat_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('cat_form_title_label').setText('Nueva ' + cat.title);
							Ext.getCmp('cat_form_save_bt').setText('Guardar y continuar...');
							Ext.getCmp('cat_form_save_bt').show();
							Ext.getCmp('cat_form_cancel_bt').show();
							Ext.getCmp('cat_form_contribuyente_id_field').show();
							Ext.getCmp('cat_form_contribuyente_nomape_field').hide();
							Ext.getCmp('cat_form_doc_requisito_grid').disable();
							Ext.getCmp('cat_form_doc_estado_grid').disable();
							Ext.getCmp('cat_form_cat_vehiculo_panel').disable();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert(cat.title, eOpts.getResultSet().getMessage());
						w.unmask();
						cat.form_editing = false;
					}
				}
			}
		});
	};
</script>