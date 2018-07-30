<script>
	np.grid = Ext.create('Ext.grid.Panel',{
		id:'np_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Numero', dataIndex:'nro_np', width: 60},
			{text:'Fecha', dataIndex:'fecha_np', width: 85},
			{text:'Comprador', dataIndex:'desc_comprador', width: 220},
			{text:'Obra', dataIndex:'obra_cod_desc', width: 300},
			{text:'Frente', dataIndex:'desc_frente', width: 150},
			{text:'Descripcion', dataIndex:'desc_np', width: 100},
			{text:'Estado', dataIndex:'desc_estado_np', width: 100}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				np.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('np_grid_main').getSelection();
				if (rows.length>0) {
					np.window_edit(rows[0].get('id_np'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Imprimir', handler: function() {
				rows = Ext.getCmp('np_grid_main').getSelection();
				if (rows.length>0) {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_np_pdf.jsp?id='+rows[0].get('id_np');
					window.open(url, '_blank');
				} else {
					Ext.Msg.alert('Error','Seleccione un registro a enviar!');
				}
			}
		},{
			text:'Anular',
			handler: function () {
				rows = Ext.getCmp('np_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Anular NP',
					    message: 'Realmente desea ANULAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_np: rows[0].get('id_np')
									},
									url:'np/Anular',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Nota de Pedido', result.msg);
											Ext.getCmp('np_grid_main').store.reload();
										} else {
											Ext.Msg.alert('Error', result.msg);
										}
									},
									failure: function (response, opts){
										Ext.Msg.alert('Error', 'Error en la conexion de datos');
									}
								});         
					        } 
					    }
					});
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Activar',
			handler: function () {
				rows = Ext.getCmp('np_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Activar NP',
					    message: 'Realmente desea ACTIVAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_np: rows[0].get('id_np')
									},
									url:'np/Activar',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Nota de Pedido', result.msg);
											Ext.getCmp('np_grid_main').store.reload();
										} else {
											Ext.Msg.alert('Error', result.msg);
										}
									},
									failure: function (response, opts){
										Ext.Msg.alert('Error', 'Error en la conexion de datos');
									}
								});         
					        } 
					    }
					});
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text: 'Rendicion',
			handler: function () {
				rows = Ext.getCmp('np_grid_main').getSelection();
				if (rows.length>0) {
					np.window_rendicion(rows[0].get('id_np'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'np_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'nro_np', search_desc: 'Por Numero'},
					{search_id: 'id_frente', search_desc: 'Por Centro de costo'},
					{search_id: 'id_comprador', search_desc: 'Por Comprador'},
					{search_id: 'desc_np', search_desc: 'Por Descripcion'}
				]
			})
		},{
			xtype:'textfield',
			id: 'np_search_text'
		},{
			text:'Buscar', handler:function(){
				np.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('np_cb_search_by').getValue(),
						search_text: Ext.getCmp('np_search_text').getValue()
					}
				});
			}
		}],
		store: np.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
	np.reload_list = function () {
		np.st_grid_main.reload({
			params: {
				search_by: Ext.getCmp('np_cb_search_by').getValue(),
				search_text: Ext.getCmp('np_search_text').getValue()
			}
		});
	};
</script>