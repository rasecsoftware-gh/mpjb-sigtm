<script>
	psp.edit_window = function(id) {
		if (psp.form_editing) return;
		psp.form_editing = true;
		var w = Ext.getCmp('psp_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'psp/getRow/'+id,
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
						psp.contribuyente_store.reload({
			    			params: {
			    				query: record.get('contribuyente_numero_doc')
			    			}
			    		});
			    		psp.doc_requisito_reload_list(record.get('psp_id'));
						sys_storeLoadMonitor([
							psp.contribuyente_store,
							psp.doc_requisito_store
						], function () {
							var frm = Ext.getCmp('psp_form');
							frm.loadRecord(record);
							Ext.getCmp('psp_form_title_label').setText('Modificar ' + psp.title);
							Ext.getCmp('psp_form_save_bt').show();
							Ext.getCmp('psp_form_cancel_bt').show();
							Ext.getCmp('psp_form_contribuyente_id_field').show();
							Ext.getCmp('psp_form_contribuyente_nomape_field').hide();
							Ext.getCmp('psp_form_doc_requisito_grid').disable();
							Ext.getCmp('psp_form_doc_estado_grid').disable();
							Ext.getCmp('psp_form_psp_vehiculo_panel').disable();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('psp', eOpts.getResultSet().getMessage());
						w.unmask();
						psp.form_editing = false;
					}
				}
			}
		});
	};
</script>