<script>
	np.window_rendicion = function(id) {
		np.det_rendicion_np_modified = false;
		var st_det_np = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetNPForImportList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
					}
				}
			}
		});

		var st_rendicion_np = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getRendicionNPList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
					}
				}
			}
		});

		var st_det_rendicion_np = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetRendicionNPList',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var g = Ext.getCmp('np_form_rendicion_grid_det_rendicion_np');
						if (Ext.isDefined(g)) {
							g.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Rendicion de Nota de Pedido', 
			modal: true,
			width: 1050,
			height: 700, 
			id:'np_window_rendicion',
			layout: 'border',
			items:[{
				xtype: 'panel',
				layout: 'border',
				region: 'north',
				height: 230,
				split: true,
				items: [{
					xtype:'form', bodyPadding: '0px 10px 0 10px',
					url:'np/updateAsRendido',
					region: 'north',
					layout: 'absolute',
					id: 'np_form_rendicion',
					height: 30,
					defaultType:'textfield',
					defaults: {
						labelWidth: 100
					},
					items:[{
						xtype: 'hiddenfield',
	    				name: 'id_np'
					},{
						fieldLabel: 'Numero:',
						xtype: 'displayfield',
	    				name: 'cod_obra_nro_np',
	    				x: 10, y: 5, width: 250, readOnly: true
					},{
						fieldLabel:'Estado:',
						xtype: 'displayfield',
						id: 'np_form_desc_estado_np',
						name: 'desc_estado_np',
						x: 280, y: 5, width: 120, readOnly: true
					}]
				},{
					xtype: 'grid',
					id:'np_form_rendicion_grid_det_np',
					region:'center', 
					store: st_det_np,
					height: 200,
					sortableColumns: false,
					enableColumnHide: false,
					features: [{
				    	ftype: 'summary'
				    }],
					columns:[
						{xtype: 'rownumberer'},
						{text: 'Codigo', dataIndex:'cod_bs', width: 115},
						{text: 'Bien/Servicio', dataIndex:'desc_bs', width: 310},
						{
							text:'Cant.', dataIndex:'cant_det_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
						},
						{
							text:'Saldo<b>xR</b>', dataIndex:'saldo', width: 90, align: 'right', xtype: 'numbercolumn', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
						},
						{text: 'UniMed', dataIndex:'desc_unimed', width: 100},
						{text: 'PreUnit', dataIndex:'preuni_det_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
						{
							text:'Total', dataIndex:'tot_det_np', width: 90, align: 'right', xtype: 'numbercolumn', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
						},
						{text: 'Comprobante', dataIndex:'comprobante', width: 130},
					],
					tbar:[{
						xtype: 'label',
						text: 'Detalles de NP: '
					},{
						text:'Agregar a comprobante', 
						handler: function () {
							var dnp_rows = Ext.getCmp('np_form_rendicion_grid_det_np').getSelection();
							var rnp_rows = Ext.getCmp('np_form_rendicion_grid_rendicion_np').getSelection();
							if (dnp_rows.length>0 && rnp_rows.length>0) {
								np.window_det_rendicion_np_new(
									rnp_rows[0].get('id_rendicion_np'), 
									dnp_rows[0].get('id_det_np'), 
									rnp_rows[0].get('abrev_tipo_documento_numdoc_rendicion_np')
								);
							} else {
								Ext.Msg.alert('Error','Seleccione un detalle de nota de pedido y un comprobante');
							}
						}
					},'->', 'Saldo xR: Saldo por Rendir'],
					listeners:{
						select: function(ths, record, index, eOpts ) {}
					}
				}]
			},{
				xtype: 'panel',
				layout: 'border',
				region: 'center',
				items:[{
					xtype: 'panel',
					layout: 'border',
					region: 'west',
					width: 350,
					split: true,
					items: [{
						xtype: 'toolbar',
						region: 'north',
						items: [{
							xtype: 'label',
							text: 'Comprobantes de pago:'
						}]
					},{
						xtype: 'grid',
						id:'np_form_rendicion_grid_rendicion_np',
						region:'center', 
						store: st_rendicion_np,
						sortableColumns: false,
						enableColumnHide: false,
						columns:[
							{text: 'Documento', dataIndex:'abrev_tipo_documento_numdoc_rendicion_np', width: 85},
							{text: 'Proveedor', dataIndex:'desc_proveedor', width: 190},
							{text: 'Total', dataIndex:'total', width: 70}

						],
						tbar:[{
							text:'Nuevo', 
							handler: function () {
								np.window_rendicion_np_new(id); // id_np
							}
						},{
							text:'Modificar', 
							handler: function () {
								var rows = Ext.getCmp('np_form_rendicion_grid_rendicion_np').getSelection();
								if (rows.length>0) {
									np.window_rendicion_np_edit(rows[0].get('id_rendicion_np'));
								} else {
									Ext.Msg.alert('Error','Seleccione un registro');
								}
							}
						},{
							text:'Quitar', 
							handler: function () {
								var rows = Ext.getCmp('np_form_rendicion_grid_rendicion_np').getSelection();
								if (rows.length>0) {
									np.window_rendicion_np_delete(rows[0].get('id_rendicion_np'));
								} else {
									Ext.Msg.alert('Error','Seleccione un registro');
								}
							}
						},'-',{
							text:'Imprimir', 
							handler: function () {
								rows = Ext.getCmp('np_form_rendicion_grid_rendicion_np').getSelection();
								if (rows.length>0) {
									var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_rendicion_np_pdf.jsp?id='+rows[0].get('id_rendicion_np');
									window.open(url, '_blank');
								} else {
									Ext.Msg.alert('Error','Seleccione un registro');
								}
							}
						}],
						listeners:{
							select: function(ths, record, index, eOpts ) {
								Ext.getCmp('np_form_rendicion_grid_det_rendicion_np').store.reload({
									params: {
										id_rendicion_np: record.get('id_rendicion_np')
									}
								});
							}
						}
					}]
				},{
					xtype: 'grid',
					id:'np_form_rendicion_grid_det_rendicion_np',
					region:'center', 
					store: st_det_rendicion_np,
					style: 'border-left: 1px solid gray',
					sortableColumns: false,
					enableColumnHide: false,
					features: [{
				    	ftype: 'summary'
				    }],
					columns:[
						{xtype: 'rownumberer'},
						{text: 'Codigo', dataIndex:'cod_bs', width: 115},
						{text: 'Bien/Servicio', dataIndex:'desc_bs', width: 250},
						{text: 'C', dataIndex:'cant_det_rendicion_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
						{text: 'PU', dataIndex:'preuni_det_rendicion_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
						{
							text:'Tot', dataIndex:'tot_det_rendicion_np', width: 90, align: 'right', xtype: 'numbercolumn', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
						}
					],
					tbar:[{
						xtype: 'label',
						text: 'Detalle de comprobante: '
					},{
						text:'Modificar', 
						handler: function () {
							var rows = Ext.getCmp('np_form_rendicion_grid_det_rendicion_np').getSelection();
							if (rows.length>0) {
								np.window_det_rendicion_np_edit(rows[0].get('id_det_rendicion_np'));
							} else {
								Ext.Msg.alert('Error','Seleccione un registro');
							}

						}
					},{
						text:'Quitar', 
						handler: function () {
							var rows = Ext.getCmp('np_form_rendicion_grid_det_rendicion_np').getSelection();
							if (rows.length>0) {
								np.window_det_rendicion_np_delete(rows[0].get('id_det_rendicion_np'));
							} else {
								Ext.Msg.alert('Error','Seleccione un registro');
							}
						}
					}],
					listeners:{
						select: function(ths, record, index, eOpts ) {}
					}
				}]
			}],
			tbar:[{
				text:'Guardar como Rendido', 
				handler: function () {
					Ext.Msg.show({
					    title:'Rendir Nota de Pedido',
					    message: 'Realmente desea Rendir la Nota de pedido?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
								frm = Ext.getCmp('np_form_rendicion');
								frm.submit({
									params: {
										opt: 'opt'
									},
									success: function(form, action) {
										if (action.result.success) {
											Ext.getCmp('np_window_rendicion').close();
											np.st_grid_main.reload();	
										} else {
											Ext.Msg.alert('Error',action.result.msg);
										}
									},
									failure:function(form, action){
										Ext.Msg.alert('Error',action.result.msg);
									}
								});
							}
						}
					});
				}
			},{
				text:'Cancelar Rendicion', 
				handler: function () {
					Ext.Msg.show({
					    title:'Cancelar Rendicion',
					    message: 'Realmente desea cancelar la rendicion?',
					    buttons: Ext.Msg.YESNO,
					    icon: Ext.Msg.QUESTION,
					    fn: function(btn) {
					        if (btn === 'yes') {
					   			Ext.Ajax.request({
									params:{
										id_np: id
									},
									url:'np/cancelarRendicion',
									success: function (response, opts){
										var result = Ext.decode(response.responseText);
										if (result.success) {
											Ext.getCmp('np_window_rendicion').close();
											Ext.getCmp('np_grid_main').store.reload();
											Ext.Msg.alert('Rendicion de Nota de Pedido', result.msg);
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
				}
			},{
				text: 'Imprimir',
				handler: function () {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_np_pdf.jsp?id='+id;
					window.open(url, '_blank');
				}
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('np_window_rendicion').close();
				}
			}],
			listeners: {
				close: function () {
					if (np.det_rendicion_np_modified) {
						np.reload_list();
					}
				}
			}
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('np_form_rendicion');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>