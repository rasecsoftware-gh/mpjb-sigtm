<script>
	notificacion.new_window = function() {
		if (notificacion.form_editing) return;
		notificacion.form_editing = true;
		var w = Ext.getCmp('notificacion_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'notificacion/getNewRow/',
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
						sys_storeLoadMonitor([], function () {
			    			var frm = Ext.getCmp('notificacion_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('notificacion_form_title_label').setText('Nueva '+notificacion.title);
							Ext.getCmp('notificacion_form_save_bt').setText('Guardar');
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