<script>
	estado_doc.new_window = function() {
		if (estado_doc.form_editing) return;
		estado_doc.form_editing = true;
		var w = Ext.getCmp('estado_doc_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'estado_doc/getNewRow/',
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
			    			var frm = Ext.getCmp('estado_doc_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('estado_doc_form_title_label').setText('Nuevo ' + estado_doc.title);
							Ext.getCmp('estado_doc_form_save_bt').show();
							Ext.getCmp('estado_doc_form_cancel_bt').show();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert(estado_doc.title, eOpts.getResultSet().getMessage());
						w.unmask();
						estado_doc.form_editing = false;
					}
				}
			}
		});
	};
</script>