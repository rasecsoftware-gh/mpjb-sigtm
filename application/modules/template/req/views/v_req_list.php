<script>
	req.grid = Ext.create('Ext.grid.Panel',{
		id:'req_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		columns:[
			{text:'Numero', dataIndex:'cod_obra_nro_requer', width: 110},
			{text:'Fecha', dataIndex:'fecha_requer', width: 90},
			{text:'Obra', dataIndex:'desc_obra', width: 240},
			{text:'Frente', dataIndex:'desc_frente', width: 200},
			{text:'Descripcion', dataIndex:'desc_requer', width: 150},
			{
				text:'Estado', dataIndex:'desc_estado_requer', width: 100,
				renderer: function (value, metaData, record) {
				    // evaluate the record's `updated` field and if truthy return the value 
				    // from the `newVal` field, else return value
				    var e = record.get('id_estado_requer');
				    if (e == '0') {
				    	metaData.tdStyle = 'color: red';
				    } else if (e == '3') {
				    	metaData.tdStyle = 'color: green';
				    } else if (e == '4') {
				    	metaData.tdStyle = 'color: #20c3ec';
				    } else if (e == '5') {
				    	metaData.tdStyle = 'color: blue';
				    }
				    return value;
				}
			}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				req.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('req_grid_main').getSelection();
				//console.log(rec[0].data);
				if (rows.length>0) {
					req.window_edit(rows[0].get('id_requer'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro a enviar!');
				}
			}
		},{
			text:'Imprimir', handler: function() {
				rows = Ext.getCmp('req_grid_main').getSelection();
				if (rows.length>0) {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_requer_pdf.jsp?id='+rows[0].get('id_requer');
					window.open(url, '_blank');
				} else {
					Ext.Msg.alert('Error','Seleccione un registro a enviar!');
				}	
			}
		},'-',{
			text: 'Aprobar',
			handler: function () {
				rows = Ext.getCmp('req_grid_main').getSelection();
				if (rows.length>0) {
					Ext.Msg.show({
					    title:'Aprobar Requerimiento',
					    message: 'Realmente desea APROBAR el registro seleccionado?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_requer: rows[0].get('id_requer')
									},
									url:'req/Aprobar',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.Msg.alert('Requerimiento', result.msg);
											Ext.getCmp('req_grid_main').store.reload();
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
			text: 'Opciones',
			menu: [{
				text: 'Anular',
				handler: function () {
					rows = Ext.getCmp('req_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Anular Requerimiento',
						    message: 'Realmente desea ANULAR el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											id_requer: rows[0].get('id_requer')
										},
										url:'req/Anular',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Requerimiento', result.msg);
												Ext.getCmp('req_grid_main').store.reload();
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
				text: 'Activar',
				handler: function () {
					rows = Ext.getCmp('req_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Activar requerimiento',
						    message: 'Realmente desea ACTIVAR el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											id_requer: rows[0].get('id_requer')
										},
										url:'req/Activar',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Requerimiento', result.msg);
												Ext.getCmp('req_grid_main').store.reload();
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
			},'-',{
				text: 'Guardar como Atendido',
				handler: function () {
					rows = Ext.getCmp('req_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Guardar como atendido',
						    message: 'Realmente desea guardar como ATENDIDO el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											id_requer: rows[0].get('id_requer')
										},
										url:'req/updateAsAtendido',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Requerimiento', result.msg);
												Ext.getCmp('req_grid_main').store.reload();
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
				text: 'Revaluar Atencion',
				handler: function () {
					rows = Ext.getCmp('req_grid_main').getSelection();
					if (rows.length>0) {
						Ext.Msg.show({
						    title:'Revaluar atencion del requerimiento',
						    message: 'Se verificara al detalle si el requerimiento esta atendido o parcialmente atendido. Realmente desea revaluar el registro seleccionado?',
						    buttons: Ext.Msg.YESNO,
						    icon: Ext.Msg.QUESTION,
						    fn: function(btn) {
						        if (btn === 'yes') {
						   			Ext.Ajax.request({
										params:{
											id_requer: rows[0].get('id_requer')
										},
										url:'req/revaluarAtencion',
										success: function (response, opts){
											var result = Ext.decode(response.responseText);
											if (result.success) {
												Ext.Msg.alert('Requerimiento', result.msg);
												Ext.getCmp('req_grid_main').store.reload();
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
			}]
		},'->',{
			xtype:'combobox',
			id: 'req_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			forceSelection: true,
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'nro_requer', search_desc: 'Por Numero'},
					{search_id: 'cod_obra', search_desc: 'Por Centro de costo'},
					{search_id: 'desc_requer', search_desc: 'Por Descripcion'},
					{search_id: 'id_estado_requer', search_desc: 'Por Estado'}
				]
			}),
			listeners: {
				select: function ( combo, record, eOpts ) {
					if (record.get('search_id')=='id_estado_requer') {
						Ext.getCmp('req_cb_search_text').setHideTrigger(false);
						Ext.getCmp('req_cb_search_text').bindStore(req.st_estado_for_search);
					} else {
						Ext.getCmp('req_cb_search_text').setHideTrigger(true);
						Ext.getCmp('req_cb_search_text').bindStore(req.st_blank);
						Ext.getCmp('req_cb_search_text').setValue('');
					}
				}
			}
		},{
			xtype: 'combobox',
			id: 'req_cb_search_text',
			displayField: 'search_text_desc',
			valueField: 'search_text_id',
			value: '',
			hideTrigger: true,
			queryMode: 'local',
			store: req.st_blank
		},{
			text: 'Buscar', handler: function () {
				req.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('req_cb_search_by').getValue(),
						search_text: Ext.getCmp('req_cb_search_text').getValue()
					}
				});
			}
		}],
		store: req.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ){
			}
		}
	});
</script>