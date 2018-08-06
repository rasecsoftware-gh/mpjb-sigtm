<script>
	clit.new_window = function() {
		if (clit.form_editing) return;
		clit.form_editing = true;
		var w = Ext.getCmp('clit_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'clit/getNewRow',
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
						sys_storeLoadMonitor([clit.plantilla_store], function () {
			    			var frm = Ext.getCmp('clit_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('clit_form_title_label').setText('Nueva Constancia');
							Ext.getCmp('clit_form_save_bt').setText('Guardar y continuar...');
							Ext.getCmp('clit_form_save_bt').show();
							Ext.getCmp('clit_form_cancel_bt').show();
							Ext.getCmp('clit_form_contribuyente_id_field').show();
							Ext.getCmp('clit_form_contribuyente_nomape_field').hide();
							Ext.getCmp('clit_form_doc_requisito_grid').hide();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert('Constancia', eOpts.getResultSet().getMessage());
						w.unmask();
						clit.form_editing = false;
					}
				}
			}
		});
	};
</script>