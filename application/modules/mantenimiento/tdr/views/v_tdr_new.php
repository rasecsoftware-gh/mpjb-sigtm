<script>
	tdr.new_window = function() {
		if (tdr.form_editing) return;
		tdr.form_editing = true;
		var w = Ext.getCmp('tdr_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'tdr/getNewRow/',
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
						tdr.tipo_permiso_store.reload();
						sys_storeLoadMonitor([tdr.tipo_permiso_store], function () {
			    			var frm = Ext.getCmp('tdr_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('tdr_form_title_label').setText('Nuevo ' + tdr.title);
							Ext.getCmp('tdr_form_save_bt').show();
							Ext.getCmp('tdr_form_cancel_bt').show();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert(tdr.title, eOpts.getResultSet().getMessage());
						w.unmask();
						tdr.form_editing = false;
					}
				}
			}
		});
	};
</script>