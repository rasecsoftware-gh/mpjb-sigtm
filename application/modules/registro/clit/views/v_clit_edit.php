<script>
	clit.edit_window = function(id) {
		if (clit.form_editing) return;
		clit.form_editing = true;
		var w = Ext.getCmp('clit_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'clit/getRow/'+id,
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
						clit.contribuyente_store.reload({
			    			params: {
			    				query: record.get('contribuyente_numero_doc')
			    			}
			    		});
			    		clit.doc_requisito_reload_list(record.get('clit_id'));
						sys_storeLoadMonitor([
							clit.contribuyente_store,
							clit.doc_requisito_store
						], function () {
							var frm = Ext.getCmp('clit_form');
							frm.loadRecord(record);
							Ext.getCmp('clit_form_title_label').setText('Modificar Constancia');
							Ext.getCmp('clit_form_save_bt').show();
							Ext.getCmp('clit_form_cancel_bt').show();
							Ext.getCmp('clit_form_contribuyente_id_field').show();
							Ext.getCmp('clit_form_contribuyente_nomape_field').hide();
							Ext.getCmp('clit_form_doc_requisito_grid').enable();
							Ext.getCmp('clit_form_doc_estado_grid').enable();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('clit', eOpts.getResultSet().getMessage());
						w.unmask();
						clit.form_editing = false;
					}
				}
			}
		});
	};
</script>