<script>
	np.window_edit = function(id) {
		var store_detail = Ext.create("Ext.data.Store", {
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
						var grid = Ext.getCmp('np_form_grid_detail');
						if (Ext.isDefined(grid)) {
							grid.getView().refresh();
						}
					}
				}
			}
		});

		var w_config = {
			title:'Nota de Pedido', 
			modal: true,
			width: 1000,
			height: 600, 
			id:'np_window_edit',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/Update',
				region: 'north',
				layout: 'absolute',
				id: 'np_form',
				height: 190,
				defaultType:'textfield',
				defaults: {
					labelWidth: 135
				},
				items:[{
					xtype: 'hiddenfield',
    				name: 'id_np'
				},{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'nro_np',
    				x: 10, y: 10, width: 250, readOnly: true
				},{
					fieldLabel:'Centro de Costo:',
					xtype: 'combobox',
					store: np.st_frente,
					displayField: 'desc_frente',
    				valueField: 'id_frente',
    				name: 'id_frente',
    				id: 'np_form_cb_frente',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_obra} - {desc_obra}</div><div>{desc_frente}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_obra} - {desc_obra}',
				        '</tpl>'
				    ),
				    x: 10, y: 40, width: 830,
				    readOnly: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('np_form_desc_frente').setValue(record.get('desc_frente'));
				    		Ext.getCmp('np_form_fecha').focus();
				    	}
				    }
				},{
					fieldLabel:'Frente:',
					xtype: 'textfield',
    				name: 'desc_frente',
    				id: 'np_form_desc_frente',
				    x: 10, y: 70, width: 650,
				    readOnly: true
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'np_form_fecha',
					name: 'fecha_np',
					format: 'd/m/Y',
					x: 10, y: 100, width: 250
				},{
					fieldLabel:'Comprador:',
					xtype: 'combobox',
					store: np.st_comprador,
					displayField: 'desc_comprador',
    				valueField: 'id_comprador',
    				name: 'id_comprador',
    				id: 'np_form_cb_comprador',
    				queryMode: 'remote',
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_comprador} - {desc_comprador}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_comprador} - {desc_comprador}',
				        '</tpl>'
				    ),
				    x: 10, y: 130, width: 830,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('np_form_cb_comprador_cb').focus();
				    	}
				    }
				},{
					fieldLabel:'Descripcion:',
					id: 'np_form_desc_np',
					name:'desc_np',
					x: 10, y: 160, width: 650
				}]
			},{
				xtype: 'grid',
				id:'np_form_grid_detail',
				region:'center', 
				//forceFit:true,
				features: [{
			    	ftype: 'summary'
			    }],
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'cod_bs', width: 110},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 250},
					{text:'Cantidad', dataIndex:'cant_det_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'UniMed', dataIndex:'desc_unimed', width: 80},
					{text:'Precio Unit', dataIndex:'preuni_det_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.0000'},
					{
						text:'Total', dataIndex:'tot_det_np', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00', 
						summaryType: 'sum',
						summaryRenderer: function(value, summaryData, dataIndex) {
				        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
				        	//Ext.String.format('{0} student{1}', value, value !== 1 ? 's' : '');
				        }
					},
					{text:'Clasi.Pre', dataIndex:'desc_clapre', width: 80},
					{text:'Observacion', dataIndex:'obs_det_np', width: 80},
					{text:'Requer', dataIndex:'nro_requer', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Opciones de detalle: '
				},{
					text:'Agregar / Importar', 
					handler: function() {
						np.window_detail_new(id, 0);
					}
				},{
					text:'Modificar', 
					handler: function () {
						rows = Ext.getCmp('np_form_grid_detail').getSelection();
						if (rows.length>0) {
							np.window_detail_edit(rows[0].get('id_det_np'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text:'Eliminar',
					handler: function () {
						rows = Ext.getCmp('np_form_grid_detail').getSelection();
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
												id: rows[0].get('id_det_np')
											},
											url:'np/deleteDetail',
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
				store: store_detail,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			tbar:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('np_form');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('np_window_edit').close();
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
				text: 'Imprimir',
				handler: function () {
					var url = "<?=$this->config->item('rpt_server')?>"+'/rpt_np_pdf.jsp?id='+id;
					window.open(url, '_blank');
				}
			},{
				text:'Salir',
				handler:function() {
					Ext.getCmp('np_window_edit').close();
				}
			},'->',{
				xtype: 'displayfield',
				id: 'np_form_desc_estado_np', text: ''
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
						frm = Ext.getCmp('np_form');
						frm.loadRecord(sender.getAt(0));
						Ext.getCmp('np_form_desc_estado_np').setRawValue(records[0].get('desc_estado_np'));
						w.show();
					}
				}
			}
		});
	}
</script>