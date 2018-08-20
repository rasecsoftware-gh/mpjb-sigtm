<script>
	papeleta.edit_window = function(id) {
		if (papeleta.form_editing) return;
		papeleta.form_editing = true;
		var w = Ext.getCmp('papeleta_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'papeleta/getRow/'+id,
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
						papeleta.contribuyente_store.reload({
							params: {
								query: record.get('contribuyente_numero_doc')
							}
						});
						sys_storeLoadMonitor([
							papeleta.contribuyente_store
						], function () {
							var frm = Ext.getCmp('papeleta_form');
							frm.loadRecord(record);
							Ext.getCmp('papeleta_form_title_label').setText('Modificar '+papeleta.title);
							Ext.getCmp('papeleta_form_save_bt').show();
							Ext.getCmp('papeleta_form_cancel_bt').show();
							Ext.getCmp('papeleta_form_contribuyente_id_field').show();
							Ext.getCmp('papeleta_form_contribuyente_nomape_field').hide();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('Papeleta', eOpts.getResultSet().getMessage());
						w.unmask();
						papeleta.form_editing = false;
					}
				}
			}
		});
	};
</script>