<script>
	ccu.usuario_new_window = function() {
		var f = Ext.getCmp('ccu_usuario_form');
		f.mask('cargando');
		Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'ccu/getNewRow/',
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
		    			f.loadRecord(record);
						f.unmask();
						ccu.usuario_new_flag = true;
					} else {
						Ext.Msg.alert('Usuario', eOpts.getResultSet().getMessage());
					}
				}
			}
		});
	};
</script>