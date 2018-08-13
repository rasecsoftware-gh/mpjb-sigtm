<script>
	plantilla.new_window = function() {
		if (plantilla.form_editing) return;
		plantilla.form_editing = true;
		var w = Ext.getCmp('plantilla_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'plantilla/getNewRow/',
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
			    			var frm = Ext.getCmp('plantilla_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('plantilla_form_title_label').setText('Nuevo ' + plantilla.title);
							Ext.getCmp('plantilla_form_save_bt').show();
							Ext.getCmp('plantilla_form_cancel_bt').show();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert(plantilla.title, eOpts.getResultSet().getMessage());
						w.unmask();
						plantilla.form_editing = false;
					}
				}
			}
		});
	};
</script>