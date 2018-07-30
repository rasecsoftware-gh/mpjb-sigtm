<script>
	np.window_rendicion = function(id) {
		var st_rendicion_det_np = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetailList/'+id,
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

		var st_np_tipo_documento = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getTipoDocumentoList/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {}
				}
			}
		});

		var w_config = {
			title:'Rendicion de Nota de Pedido', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'np_window_rendicion',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/updateAsRendido',
				region: 'north',
				layout: 'absolute',
				id: 'np_form_rendicion',
				height: 80,
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
    				name: 'nro_np',
    				x: 10, y: 10, width: 250, readOnly: true
				},{
					fieldLabel:'Estado:',
					xtype: 'displayfield',
					id: 'np_form_desc_estado_np',
					name: 'desc_estado_np',
					x: 10, y: 40, width: 300, readOnly: true
				}]
			},{
				xtype: 'grid',
				id:'np_form_rendicion_grid_det_np',
				region:'center', 
				store: st_rendicion_det_np,
				sortableColumns: false,
				enableColumnHide: false,
				features: [{
			    	ftype: 'summary'
			    }],
				columns:[
					{xtype: 'rownumberer'},
					{text: 'Codigo', dataIndex:'cod_bs', width: 115},
					{text: 'Bien/Servicio', dataIndex:'desc_bs', width: 250},
					{text: 'C', dataIndex:'cant_det_np', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text: 'UM', dataIndex:'desc_unimed', width: 80},
					{text: 'PU', dataIndex:'preuni_det_np', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{
						text:'Tot', dataIndex:'tot_det_np', width: 65, align: 'right', xtype: 'numbercolumn', format:'0.00', 
						summaryType: 'sum',
						summaryRenderer: function(value, summaryData, dataIndex) {
				        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
				        }
					},
					{
						text: '<b>Rendicion</b>',
						columns: [
							{text:'C', dataIndex:'cant_final_det_np', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
							{text:'PU', dataIndex:'preuni_final_det_np', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00'},
							{
								text:'T', dataIndex:'tot_final_det_np', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', 
								summaryType: 'sum',
								summaryRenderer: function(value, summaryData, dataIndex) {
						        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
						        }
							},
							{text: 'Proveedor', dataIndex:'desc_proveedor', width: 120}
						]
					}
				],
				tbar:[{
					xtype: 'label',
					text: 'Opciones de Rendicion: '
				},{
					text:'Modificar', 
					handler: function () {
						var rows = Ext.getCmp('np_form_rendicion_grid_det_np').getSelection();
						if (rows.length>0) {
							var id_proveedor = Ext.getCmp('np_form_rendicion_cb_proveedor').getValue();
							np.window_rendicion_detail(rows[0].get('id_det_np'), id_proveedor);
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},'->',{
					fieldLabel:'Usar el Proveedor:',
					labelWidth: 120,
					xtype: 'combobox',
					store: np.st_proveedor,
					displayField: 'desc_proveedor',
    				valueField: 'id_proveedor',
    				name: 'id_proveedor',
    				id: 'np_form_rendicion_cb_proveedor',
    				queryMode: 'remote',
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{ruc_proveedor} - {desc_proveedor}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{ruc_proveedor} - {desc_proveedor}',
				        '</tpl>'
				    ),
				    x: 10, y: 80, width: 650,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {}
				    }
				}],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar como Rendido', 
				handler: function (){
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
									url:'oc/cancelarRendicion',
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
			}]
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