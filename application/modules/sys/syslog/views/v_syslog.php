<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	syslog = {};
	// general stores
	syslog.show_window = function (tablename, rowid) {
		var w_config = {
			id: 'syslog_show_window',
			title: 'Informacio de Registro', 
			modal: true,
			width: 500,
			height: 200, 
			layout: 'border',
			items:[{
				xtype: 'panel', 
				id: 'contrato_anular_form',
				loader: {
					url: 'syslog/View',
					params: {
						tablename: tablename,
						rowid: rowid
					},
					autoLoad: true,
					scripts: true,
					renderer: 'html'
				},
				bodyPadding: 10,
				region: 'center',
				layout: 'auto'
			}],
			listeners: {
				show: function () {
				}
			}
		};

		Ext.create('Ext.window.Window', w_config).show();
	};
</script>