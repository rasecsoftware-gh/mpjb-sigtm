<script>
	lc.edit_window = function(id) {
		if (lc.form_editing) return;
		lc.form_editing = true;
		var w = Ext.getCmp('lc_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'lc/getRow/'+id,
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
						lc.contribuyente_store.reload({
			    			params: {
			    				query: record.get('contribuyente_numero_doc')
			    			}
			    		});
			    		lc.doc_requisito_reload_list(record.get('lc_id'));
						sys_storeLoadMonitor([
							lc.contribuyente_store,
							lc.doc_requisito_store
						], function () {
							var frm = Ext.getCmp('lc_form');
							frm.loadRecord(record);
							Ext.getCmp('lc_form_title_label').setText('Modificar '+lc.title);
							Ext.getCmp('lc_form_save_bt').show();
							Ext.getCmp('lc_form_cancel_bt').show();
							Ext.getCmp('lc_form_contribuyente_id_field').show();
							Ext.getCmp('lc_form_contribuyente_nomape_field').hide();
							Ext.getCmp('lc_form_doc_requisito_grid').disable();
							Ext.getCmp('lc_form_doc_estado_grid').disable();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('lc', eOpts.getResultSet().getMessage());
						w.unmask();
						lc.form_editing = false;
					}
				}
			}
		});
	};
</script>