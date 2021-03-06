<script>
	contribuyente.edit_window = function(id) {
		if (contribuyente.form_editing) return;
		contribuyente.form_editing = true;
		var w = Ext.getCmp('contribuyente_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'contribuyente/getRow/'+id,
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
						// load plantilla
						contribuyente.ubigeo_store.reload({
			    			params: {
			    				query: record.get('ubigeo_id')
			    			}
			    		});
						sys_storeLoadMonitor([
							contribuyente.tipo_persona_store, 
							contribuyente.tipo_doc_identidad_store, 
							contribuyente.ubigeo_store
						], function () {
							var frm = Ext.getCmp('contribuyente_form');
							frm.loadRecord(record);
							Ext.getCmp('contribuyente_form_save_bt').show();
							Ext.getCmp('contribuyente_form_cancel_bt').show();
							Ext.getCmp('contribuyente_form_ubigeo_id_field').show();
							Ext.getCmp('contribuyente_form_ubigeo_distrito_field').hide();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('contribuyente', eOpts.getResultSet().getMessage());
						w.unmask();
						contribuyente.form_editing = false;
					}
				}
			}
		});
	};
</script>