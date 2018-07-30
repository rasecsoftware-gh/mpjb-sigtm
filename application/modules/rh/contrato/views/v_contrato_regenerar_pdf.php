<script>
	contrato.regenerar_pdf_window = function(record) {
		var w_config = {
			title: 'Re-Generar PDF', 
			modal: true,
			width: 400,
			height: 200, 
			id: 'contrato_regenerar_pdf_window',
			padding: 10,
			listeners: {
				show: function () {
					w.mask('Re-generando el PDF...');
					Ext.Ajax.request({
						params:{
							contrato_id: record.get('contrato_id')
						},
						url:'contrato/regenerarPDF',
						success: function (response, opts){
							var result = Ext.decode(response.responseText);
							if (result.success) {
								w.setHtml(result.msg);
								w.unmask();
							} else {
								Ext.Msg.alert('Error', result.msg);
							}
						},
						failure: function (response, opts){
							Ext.Msg.alert('Error', 'Error en la conexion.');
						},
						timeout: 5*60*1000 // 5m
					});
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>