<script>
	geb.grid = Ext.create('Ext.grid.Panel', {
		id:'geb_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Año', dataIndex:'geb_anio', width: 50},
			{text:'Numero', dataIndex:'geb_numero', width: 58},
			{text:'Fecha', dataIndex:'geb_fecha', width: 80},
			{text:'Nemonico', dataIndex:'nemo_cod_desc', width: 350},
			{text:'Area', dataIndex:'area_desc', width: 300},
			{text:'Solicitante', dataIndex:'geb_solicitante', width: 150},
			{text:'Estado', dataIndex:'geb_estado', width: 100,
				renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
				    switch (value) {
				    	case 'ANULADO':
				    		value = '<span style="color: red;">'+value+'</span>';
				    	break;
				    	case 'APROBADO':
				    		value = '<span style="color: green;">'+value+'</span>';
				    	break;
				    }
				    return value;
				}
			},
			{text:'Detalles', dataIndex:'geb_det_count', width: 70, align: 'center',
				renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
				    return '<span style="color: gray;">'+value+'</span>';
				}
			}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				geb.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('geb_grid_main').getSelection();
				if (rows.length>0) {
					geb.window_edit(rows[0].get('geb_id'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Imprimir', handler: function() {
				rows = Ext.getCmp('geb_grid_main').getSelection();
				if (rows.length>0) {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_geb_full_pdf.jsp?id='+rows[0].get('geb_id');
					window.open(url, '_blank');
				} else {
					Ext.Msg.alert('Error', 'Seleccione un registro a enviar!');
				}
			}
		},'-',{
			text: 'Aprobar',
			handler: function () {
				rows = Ext.getCmp('geb_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Aprobar Guia',
					    message: 'Realmente desea APROBAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										geb_id: rows[0].get('geb_id')
									},
									url:'geb/Aprobar',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Guia', result.msg);
											Ext.getCmp('geb_grid_main').store.reload();
										} else {
											Ext.Msg.alert('Guia', result.msg);
										}
									},
									failure: function (response, opts){
										Ext.Msg.alert('Error', 'Error de conexion.');
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
			text: 'Opciones',
			menu: [{
				text: 'Anular',
				handler: function () {
					rows = Ext.getCmp('geb_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Anular',
						    message: 'Realmente desea ANULAR el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											geb_id: rows[0].get('geb_id')
										},
										url:'geb/Anular',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Guia', result.msg);
												Ext.getCmp('geb_grid_main').store.reload();
											} else {
												Ext.Msg.alert('Guia', result.msg);
											}
										},
										failure: function (response, opts){
											Ext.Msg.alert('Error', 'Error en la conexion');
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
					rows = Ext.getCmp('geb_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Activar',
						    message: 'Realmente desea ACTIVAR el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											geb_id: rows[0].get('geb_id')
										},
										url:'geb/Activar',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Guia', result.msg);
												Ext.getCmp('geb_grid_main').store.reload();
											} else {
												Ext.Msg.alert('Guia', result.msg);
											}
										},
										failure: function (response, opts){
											Ext.Msg.alert('Error', 'Error en la conexion.');
										}
									});         
						        } 
						    }
						});
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Cancelar Aprobacion',
				handler: function () {
					rows = Ext.getCmp('geb_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Cancelar aprobacion',
						    message: 'Realmente desea cancelar la aprobacion?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											geb_id: rows[0].get('geb_id')
										},
										url:'geb/cancelarAprobado',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Guia', result.msg);
												Ext.getCmp('geb_grid_main').store.reload();
											} else {
												Ext.Msg.alert('Guia', result.msg);
											}
										},
										failure: function (response, opts){
											Ext.Msg.alert('Error', 'Error en la conexion.');
										}
									});         
						        } 
						    }
						});
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			}]
		},'->',{
			xtype:'combobox',
			id: 'geb_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'numero', search_desc: 'Por Numero'},
					{search_id: 'nemonico', search_desc: 'Por Nemonico'},
					{search_id: 'area', search_desc: 'Por Area'},
					{search_id: 'solicitante', search_desc: 'Por Solicitante'},
					{search_id: 'estado', search_desc: 'Por Estado'},
					{search_id: 'oc', search_desc: 'Por Orden de Compra'}
				]
			})
		},{
			xtype:'textfield',
			id: 'geb_search_text',
			enableKeyEvents: true,
			listeners: {
				keypress: function(sender, e, eOpts) {
					if (e.getKey() == e.ENTER) {
						Ext.getCmp('geb_search_bt').click(e);
					}
				}
			}
		},{
			id: 'geb_search_bt',
			text:'Buscar', handler: function() {
				geb.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('geb_cb_search_by').getValue(),
						search_text: Ext.getCmp('geb_search_text').getValue()
					}
				});
			}
		}],
		store: geb.st_grid_main,
		dockedItems: [{
	        xtype: 'pagingtoolbar',
	        store: geb.st_grid_main, // same store GridPanel is using
	        dock: 'bottom',
	        displayInfo: true
	    }],
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
</script>