<script>
	psp.new_window = function() {
		if (psp.form_editing) return;
		psp.form_editing = true;
		var w = Ext.getCmp('psp_form');
		w.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'psp/getNewRow',
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
						sys_storeLoadMonitor([psp.plantilla_store], function () {
			    			var frm = Ext.getCmp('psp_form');
			    			frm.reset();
							frm.loadRecord(record);
							Ext.getCmp('psp_form_title_label').setText('Nuevo Permiso');
							Ext.getCmp('psp_form_save_bt').setText('Guardar y continuar...');
							Ext.getCmp('psp_form_save_bt').show();
							Ext.getCmp('psp_form_cancel_bt').show();
							Ext.getCmp('psp_form_contribuyente_id_field').show();
							Ext.getCmp('psp_form_contribuyente_nomape_field').hide();
							Ext.getCmp('psp_form_doc_requisito_grid').disable();
							Ext.getCmp('psp_form_doc_estado_grid').disable();
							w.unmask();
			    		});
					} else {
						Ext.Msg.alert('Constancia', eOpts.getResultSet().getMessage());
						w.unmask();
						psp.form_editing = false;
					}
				}
			}
		});
	};
</script>