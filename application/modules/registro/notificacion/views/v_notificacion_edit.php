<script>
	notificacion.edit_window = function(id) {
		if (notificacion.form_editing) return;
		notificacion.form_editing = true;
		var w = Ext.getCmp('notificacion_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'notificacion/getRow/'+id,
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
						notificacion.contribuyente_store.reload({
							params: {
								query: record.get('contribuyente_numero_doc')
							}
						});
						sys_storeLoadMonitor([
							notificacion.contribuyente_store,
							notificacion.papeleta_store
						], function () {
							var frm = Ext.getCmp('notificacion_form');
							frm.loadRecord(record);
							notificacion.papeleta_store.reload(); // reload with current form values
							Ext.getCmp('notificacion_form_title_label').setText('Modificar '+notificacion.title);
							Ext.getCmp('notificacion_form_save_bt').show();
							Ext.getCmp('notificacion_form_cancel_bt').show();
							Ext.getCmp('notificacion_form_contribuyente_id_field').show();
							Ext.getCmp('notificacion_form_contribuyente_nomape_field').hide();
							Ext.getCmp('notificacion_form_papeleta_id_field').show();
							Ext.getCmp('notificacion_form_papeleta_numero_field').hide();
							w.unmask();
						});
					} else {
						Ext.Msg.alert('notificacion', eOpts.getResultSet().getMessage());
						w.unmask();
						notificacion.form_editing = false;
					}
				}
			}
		});
	};
</script>