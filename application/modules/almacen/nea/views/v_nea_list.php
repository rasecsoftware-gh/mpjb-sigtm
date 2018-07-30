<script>
	nea.grid = Ext.create('Ext.grid.Panel', {
		id:'nea_grid_main',
		region:'center', 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Año', dataIndex:'nea_anio', width: 50},
			{text:'Numero', dataIndex:'nea_numero', width: 58},
			{text:'Fecha', dataIndex:'nea_fecha', width: 80},
			{text:'Tipo', dataIndex:'tipo_nea_desc', width: 100},
			{text:'Nemonico', dataIndex:'nemo_cod_desc', width: 300},
			{text:'Estado', dataIndex:'nea_estado', width: 80,
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
			{text:'Items', dataIndex:'nea_det_count', width: 70, align: 'center',
				renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
				    return '<span style="color: gray;">'+value+'</span>';
				}
			},
			{text:'Total', dataIndex:'nea_total', xtype: 'numbercolumn', width: 70, align: 'right', format: '0.00'}
		],
		tbar:[{
			xtype: 'label',
			id: 'nea_anio_list_label',
			style: {
				fontWeight: 'bold'
			},
			text: nea.anio
		},{
			text:'Nuevo', handler: function() {
				nea.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('nea_grid_main').getSelection();
				if (rows.length>0) {
					nea.window_edit(rows[0].get('nea_id'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text: 'Imprimir',
			menu: [{
				text: 'Original',
				handler: function () {
					rows = Ext.getCmp('nea_grid_main').getSelection();
					if (rows.length>0) {
						nea.print_window(rows[0].get('nea_id'), 'ORIGINAL');
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Control',
				handler: function () {
					rows = Ext.getCmp('nea_grid_main').getSelection();
					if (rows.length>0) {
						nea.print_window(rows[0].get('nea_id'), 'CONTROL');
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}	
				}
			},{
				text: 'Usuario',
				handler: function () {
					rows = Ext.getCmp('nea_grid_main').getSelection();
					if (rows.length>0) {
						nea.print_window(rows[0].get('nea_id'), 'USUARIO');
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			}]
		},'-',{
			text: 'Aprobar',
			handler: function () {
				rows = Ext.getCmp('nea_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Aprobar NEA',
					    message: 'Realmente desea APROBAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										nea_id: rows[0].get('nea_id')
									},
									url:'nea/Aprobar',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('NEA', result.msg);
											Ext.getCmp('nea_grid_main').store.reload();
										} else {
											Ext.Msg.alert('NEA', result.msg);
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
					rows = Ext.getCmp('nea_grid_main').getSelection();
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
											nea_id: rows[0].get('nea_id')
										},
										url:'nea/Anular',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('NEA', result.msg);
												Ext.getCmp('nea_grid_main').store.reload();
											} else {
												Ext.Msg.alert('NEA', result.msg);
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
						Ext.Msg.alert('Error', 'Seleccione un registro');
					}
				}
			},{
				text:'Cancelar Anulacion',
				handler: function () {
					rows = Ext.getCmp('nea_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Activar',
						    message: 'Realmente desea Cancelar la Anulacion del registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											nea_id: rows[0].get('nea_id')
										},
										url:'nea/cancelarAnulado',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('NEA', result.msg);
												Ext.getCmp('nea_grid_main').store.reload();
											} else {
												Ext.Msg.alert('NEA', result.msg);
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
					rows = Ext.getCmp('nea_grid_main').getSelection();
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
											nea_id: rows[0].get('nea_id')
										},
										url:'nea/cancelarAprobado',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('NEA', result.msg);
												Ext.getCmp('nea_grid_main').store.reload();
											} else {
												Ext.Msg.alert('NEA', result.msg);
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
				text: 'Cambiar Año',
				menu: [{
					text: '<?php echo date('Y');?>',
					handler: function () {
						nea.change_year_window('<?php echo date('Y');?>');
					}
				},'-',{
					text: '<?php echo date('Y')-1;?>',
					handler: function () {
						nea.change_year_window('<?php echo date('Y')-1;?>');
					}
				},{
					text: '<?php echo date('Y')-2;?>',
					handler: function () {
						nea.change_year_window('<?php echo date('Y')-2;?>');
					}
				}]
			}]
		},'->',{
			xtype:'combobox',
			id: 'nea_cb_search_by',
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
					{search_id: 'estado', search_desc: 'Por Estado'}
				]
			})
		},{
			xtype:'textfield',
			id: 'nea_search_text',
			enableKeyEvents: true,
			listeners: {
				keypress: function(sender, e, eOpts) {
					if (e.getKey() == e.ENTER) {
						Ext.getCmp('nea_search_bt').click(e);
					}
				}
			}
		},{
			id: 'nea_search_bt',
			text:'Buscar', handler: function() {
				nea.main_store.reload({
					params: {
						search_by: Ext.getCmp('nea_cb_search_by').getValue(),
						search_text: Ext.getCmp('nea_search_text').getValue()
					}
				});
			}
		}],
		store: nea.main_store,
		dockedItems: [{
	        xtype: 'pagingtoolbar',
	        store: nea.main_store, // same store GridPanel is using
	        dock: 'bottom',
	        displayInfo: true
	    }],
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
</script>