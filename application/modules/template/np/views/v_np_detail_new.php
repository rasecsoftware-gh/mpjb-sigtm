<script>
	np.window_detail_new = function(pid) {
		var st_det_requer = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetRequerList/'+pid,
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
		var w_detail_config = {
			title:'Agregar / Importar detalles', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'np_window_detail',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id:'np_form_detail_grid',
				region:'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text:'NroReq', dataIndex:'nro_requer', width: 65, menuDisabled: true},
					{text:'Codigo', dataIndex:'cod_bs', width: 105, menuDisabled: true},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 290, menuDisabled: true},
					{text:'Cantidad', dataIndex:'cant_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'SALDO', dataIndex:'saldo', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'},
					{text:'UniMed', dataIndex:'desc_unimed', width: 80},
					{text:'Precio Unit', dataIndex:'preuni_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.0000'},
					{
						text:'Total', dataIndex:'tot_det_requer', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.00'
					},
					{xtype: 'checkcolumn', text:'Importar', dataIndex:'importar', width: 80}
				],
				tbar:[{
					xtype: 'label',
					text: 'Filtre los registros segun'
				},{
					xtype:'textfield',
					id: 'np_form_detail_search_text',
					width: 200
				},{
					text:'Buscar', 
					handler: function() {
						st_det_requer.reload({
							params:{
								query: Ext.getCmp('np_form_detail_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = [];
						var st = Ext.getCmp('np_form_detail_grid').store;

						for (var i=0; i<st.count(); i++) {
							if (st.getAt(i).get('importar')) {
								rows.push(st.getAt(i).get('id_det_requer'));
							}
						}
						if (rows.length>0) {
							var values = rows.join();
							//alert(values);
							Ext.Msg.show({
							    title:'Importar registros?',
							    message: 'Realmente desea importar los registros seleccionados?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							   			Ext.Ajax.request({
											params:{
												id_np: pid,
												strlist: values
											},
											url:'np/importDetails',
											success: function (response, opts){
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.getCmp('np_form_grid_detail').store.reload();
													//Ext.getCmp('np_form_detail_grid').store.reload();
													Ext.getCmp('np_window_detail').close();
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
							Ext.Msg.alert('Error','Seleccione al menos un registro');
						}
					}
				},{
					text:'Salir', 
					handler: function () {
						Ext.getCmp('np_window_detail').close();
					}
				}],
				store: st_det_requer,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}]
		};
		var w = Ext.create('Ext.window.Window', w_detail_config);
		w.show();
	}
</script>