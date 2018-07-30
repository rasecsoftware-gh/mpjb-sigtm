<script>
	req.window_edit = function(id) {
		var store_detail = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'req/getDetailList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var g = Ext.getCmp('req_form_grid_detail');
						if (Ext.isDefined(g)) {
							g.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Requerimiento', 
			modal: true,
			width: 1000,
			height: 600, 
			id:'req_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'req/Update',
				region: 'north',
				layout: 'absolute',
				id: 'req_form_edit',
				height: 160,
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_requer'
				},{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_requer',
    				x: 10, y: 0, width: 220,
    				readOnly: true
				},{
					fieldLabel:'Centro de Costo:',
					xtype: 'combobox',
					displayField: 'desc_obra',
    				valueField: 'cod_obra',
    				name: 'cod_obra',
    				id: 'req_form_cb_obra',
    				queryMode: 'remote',
    				store: req.st_obra,
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_obra} - {desc_obra}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_obra} - {desc_obra}',
				        '</tpl>'
				    ),
				    x: 10, y: 30, width: 820,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		req.st_frente.reload({
				    			params: {
				    				cod_obra: record.get('cod_obra')
				    			}
				    		});
				    		Ext.getCmp('req_form_cb_frente').focus();
				    	}
				    }
				},{
					fieldLabel:'Frente:',
					xtype: 'combobox',
    				name: 'id_frente',
    				id: 'req_form_cb_frente',
    				displayField: 'desc_frente',
    				valueField: 'id_frente',
				    x: 10, y: 60, width: 620,
				    forceSelection: true,
				    queryMode: 'local',
				    store: req.st_frente,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('req_from_fecha_requer').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					name: 'fecha_requer',
					id: 'req_from_fecha_requer',
					format: 'd/m/Y',
					x: 10, y: 90, width: 220
				},{
					fieldLabel:'Descripcion:',
					name:'desc_requer',
					x: 10, y: 120, width: 620
				}]
			},{
				xtype: 'grid',
				id:'req_form_grid_detail',
				region:'center', 
				//forceFit:true,
				store: store_detail,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				features: [{
			    	ftype: 'summary'
			    }],
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'cod_bs', width: 100},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 290},
					{text:'Cant', dataIndex:'cant_det_requer', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'UM', dataIndex:'desc_unimed', width: 80},
					{text:'V.Ref', dataIndex:'preuni_det_requer', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{
						text:'Total', dataIndex:'tot_det_requer', width: 65, align: 'right', xtype: 'numbercolumn', format:'0.00', 
						summaryType: 'sum',
						summaryRenderer: function(value, summaryData, dataIndex) {
				        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
				        	//Ext.String.format('{0} student{1}', value, value !== 1 ? 's' : '');
				        }
					},
					{text:'Clasif.Presu.', dataIndex:'desc_clapre', width: 120},
					{text:'Observacion', dataIndex:'obs_det_requer', width: 80},
					{text:'Estado', dataIndex:'desc_estado_det_requer', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Opciones de detalle: '
				},{
					text:'Agregar', 
					handler: function() {
						req.window_detail_new(id, 0);
					}
				},{
					text:'Modificar', 
					handler: function () {
						rows = Ext.getCmp('req_form_grid_detail').getSelection();
						if (rows.length>0) {
							req.window_detail_edit(rows[0].get('id_det_requer'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text:'Eliminar',
					handler: function () {
						rows = Ext.getCmp('req_form_grid_detail').getSelection();
						if (rows.length>0) {
							Ext.Msg.show({
							    title:'Eliminar detalle?',
							    message: 'Realmente desea eliminar el detalle seleccionado?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							   			Ext.Ajax.request({
											params:{
												id: rows[0].get('id_det_requer')
											},
											url:'req/deleteDetail',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													store_detail.reload();
												} else {
													Ext.Msg.alert('Error', result.msg);
												}
											},
											failure: function (response, opts){
												Ext.Msg.alert('Error', 'Error en la conexion de datos');
											}
										});         
							        } else {
							            console.log('Cancel pressed');
							        } 
							    }
							});
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				}],
				bbar:[{
					xtype: 'label',
					id: 'req_form_grid_detail_estado',
					text: '-'
				}],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						Ext.getCmp('req_form_grid_detail_estado').setText(record.get('desc_estado_det_requer'));
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('req_form_edit');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('req_window_edit').close();
								req.st_grid_main.reload();	
							} else {
								Ext.Msg.alert('Error',action.result.msg);
							}
						},
						failure:function(form, action){
							Ext.Msg.alert('Error',action.result.msg);
						}
					});
				}
			},{
				text: 'Imprimir',
				handler: function () {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_requer_pdf.jsp?id='+id;
					window.open(url, '_blank');
				}
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('req_window_edit').close();
				}
			},'->',{
				xtype: 'displayfield',
				id: 'req_form_desc_estado_requer', text: ''
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'req/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful && records.length > 0) {
						req.st_obra_preloading = true;
						req.st_obra.reload({
							params: {
								'cod_obra': records[0].get('cod_obra')
							}
						});

						req.st_frente_preloading = true;
						req.st_frente.reload({
							params: {
								'cod_obra': records[0].get('cod_obra')
							}
						});
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('req_form_edit');
						frm.loadRecord(records[0]);
						Ext.getCmp('req_form_desc_estado_requer').setRawValue(records[0].get('desc_estado_requer'));
						w.show();
					} else {
						Ext.Msg.alert('Error', 'Registro no encontrado.');
					}
				}
			}
		});
	}
</script>