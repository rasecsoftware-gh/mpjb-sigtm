<script>
	cat.edit_window = function(id) {
		if (cat.form_editing) return;
		cat.form_editing = true;
		var w = Ext.getCmp('cat_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'cat/getRow/'+id,
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
						cat.contribuyente_store.reload({
			    			params: {
			    				query: record.get('contribuyente_numero_doc')
			    			}
			    		});
			    		cat.doc_requisito_reload_list(record.get('cat_id'));
						sys_storeLoadMonitor([
							cat.contribuyente_store,
							cat.doc_requisito_store
						], function () {
							var frm = Ext.getCmp('cat_form');
							frm.loadRecord(record);
							Ext.getCmp('cat_form_title_label').setText('Modificar ' + cat.title);
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
						Ext.Msg.alert('cat', eOpts.getResultSet().getMessage());
						w.unmask();
						cat.form_editing = false;
					}
				}
			}
		});
	};
</script>