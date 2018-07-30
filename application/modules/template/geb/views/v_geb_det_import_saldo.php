<script>
	geb.window_geb_det_import_saldo = function(geb_id) {
		var st_oc_det_saldo = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'geb/getOCDetSaldoList/'+geb_id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
					}
				}
			}
		});
		var w_detail_config = {
			title:'Agregar / Importar desde saldos', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'geb_window_geb_det_import_saldo',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id:'geb_form_geb_det_import_saldo_grid',
				region:'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'bs_cod', width: 100, menuDisabled: true},
					{text:'Bien', dataIndex:'bs_desc', width: 210, menuDisabled: true},
					{text:'Observacion', dataIndex:'oc_det_obs', width: 170, menuDisabled: true},
					{text:'Cant.', dataIndex:'oc_det_cantidad', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true},
					{text:'UniMed', dataIndex:'bs_unimed', width: 75, menuDisabled: true},
					{text:'Importado', dataIndex:'importado', width: 50, align: 'center', tooltip: 'Importado', tooltipType: 'title', menuDisabled: true, 
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    switch (value.toUpperCase()) {
						    	case 'SI':
						    		value = '<span style="color: blue;">'+value+'</span>';
						    	break;
						    }
						    return value;
						}
					},
					{text:'Saldo', dataIndex:'oc_det_saldo', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true}
				],
				selModel: {
			    	selType: 'checkboxmodel',
			    	checkOnly: true,
			    	mode: 'MULTI',
			    	toggleOnClick: false
			    },
				tbar:[{
					xtype: 'label',
					text: 'Filtre los registros segun'
				},{
					xtype:'textfield',
					id: 'geb_form_geb_det_import_saldo_search_text',
					width: 200,
					enableKeyEvents: true,
					listeners: {
						keypress: function (sender, e, eOpts) {
							if (e.getKey() == e.ENTER) {
								st_oc_det_saldo.reload({
									params:{
										query: Ext.getCmp('geb_form_geb_det_import_saldo_search_text').getValue()
									}
								});		
							}
						}
					}
				},{
					text:'Buscar', 
					handler: function() {
						st_oc_det_saldo.reload({
							params:{
								query: Ext.getCmp('geb_form_geb_det_import_saldo_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = Ext.getCmp('geb_form_geb_det_import_saldo_grid').getSelection();
						var rowid_list = [];
						for (var i=0; i<rows.length; i++) {
							var record = rows[i]; // record is of type grid row
							var row_id = record.get('oc_det_id');
							rowid_list.push(row_id);
						}
						if (rows.length>0) {
							var values = rowid_list.join(); // ',' default separator
							//alert(values);
							Ext.Msg.show({
							    title:'Importacion de bienes',
							    message: 'Realmente desea importar los registros seleccionados?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							        	var w = Ext.getCmp('geb_window_geb_det_import_saldo');
										w.mask('importando...');
							   			Ext.Ajax.request({
											params:{
												geb_id: geb_id,
												strlist: values
											},
											url:'geb/importGebDetFromSaldo',
											success: function (response, opts) {
												w.unmask();
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Importacion de bienes', result.msg);
													Ext.getCmp('geb_form_geb_det_import_saldo_grid').store.reload();
												} else {
													Ext.Msg.alert('Error', result.msg);
												}
											},
											failure: function (response, opts){
												w.unmask();
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
				},'-',{
					text:'Finalizar', 
					handler: function () {
						Ext.getCmp('geb_window_geb_det_import_saldo').close();
					}
				}],
				store: st_oc_det_saldo,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			listeners: {
				show: function () {
					st_oc_det_saldo.reload();
				},
				close: function () {
					Ext.getCmp('geb_form_grid_geb_det').store.reload();
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_detail_config);
		w.show();

	};
</script>