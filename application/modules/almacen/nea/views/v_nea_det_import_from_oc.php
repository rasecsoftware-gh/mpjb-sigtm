<script>
	nea.nea_det_import_from_oc_window = function(nea_id) {
		var sys2009_det_oc_store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'nea/getSys2009DetOCList/'+nea_id,
				reader:{
					type: 'json',
					rootProperty: 'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						//Ext.getCmp('nea_det_import_from_oc_grid').getSelectionModel().selectAll();
					}
				}
			}
		});
		var sys2009_oc_store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'nea/getSys2009OCList/'+nea_id,
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
			title:'Importar bienes desde Ordenes de Compra', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'nea_det_import_from_oc_window',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id: 'nea_det_import_from_oc_oc_grid',
				region: 'east', 
				width: 130,
				split: true,
				defaults: {
					menuDisabled: true
				},
				columns:[
					//{xtype: 'rownumberer'},
					{text:'Anio', dataIndex:'oc_anio', width: 45, menuDisabled: true},
					{text:'Numero', dataIndex:'oc_numero', width: 60, menuDisabled: true}
				],
				tbar:[{
					xtype: 'label',
					text: 'O/C filtrado(s)'
				}],
				store: sys2009_oc_store,
				listeners: {
					select: function(ths, record, index, eOpts ) {
						if (record.get('oc_anio') == '-') {
							sys2009_det_oc_store.clearFilter();
						} else {
							sys2009_det_oc_store.filter('oc_anio_numero', record.get('oc_anio')+'-'+record.get('oc_numero'));
						}
					}
				}
			},{
				xtype: 'grid',
				id:'nea_det_import_from_oc_grid',
				region:'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text:'O/C', dataIndex:'oc_anio_numero', width: 80, menuDisabled: true},
					{text:'Codigo', dataIndex:'bs_cod', width: 100, menuDisabled: true},
					{text:'Descripcion', dataIndex:'bs_desc_obs', width: 220, menuDisabled: true},
					{text:'UniMed', dataIndex:'bs_unimed', width: 75, menuDisabled: true},
					{text:'Cant.', dataIndex:'cantidad', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true},
					{text:'PreUni', dataIndex:'precio', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true},
					{text:'Total', dataIndex:'total', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true},
					{text:'Importado', dataIndex:'importado', width: 75, align: 'center', tooltip: 'Importado', tooltipType: 'title', menuDisabled: true, 
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    switch (value.toUpperCase()) {
						    	case 'SI':
						    		value = '<span style="color: blue;">'+value+'</span>';
						    	break;
						    }
						    return value;
						}
					}
				],
				selModel: {
			    	selType: 'checkboxmodel',
			    	checkOnly: true,
			    	mode: 'MULTI',
			    	toggleOnClick: false
			    },
				tbar:[{
					xtype: 'label',
					text: 'Busque los bienes segun'
				},{
					xtype:'textfield',
					id: 'nea_det_import_from_oc_search_text',
					width: 200,
					enableKeyEvents: true,
					listeners: {
						keypress: function (sender, e, eOpts) {
							if (e.getKey() == e.ENTER) {
								Ext.getCmp('nea_det_import_from_oc_grid_buscar_bt').handler();
							}
						}
					}
				},{
					text:'Buscar', 
					id: 'nea_det_import_from_oc_grid_buscar_bt',
					handler: function() {
						sys2009_det_oc_store.reload({
							params:{
								query: Ext.getCmp('nea_det_import_from_oc_search_text').getValue()
							}
						});
						sys2009_oc_store.reload({
							params:{
								query: Ext.getCmp('nea_det_import_from_oc_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = Ext.getCmp('nea_det_import_from_oc_grid').getSelection();
						var rowid_list = [];
						for (var i=0; i<rows.length; i++) {
							var record = rows[i]; // record is of type grid row
							var row_id = record.get('oc_anio')+'.'+record.get('oc_numero')+'.'+record.get('bs_cod')+'.'+Ext.util.Base64.encode(record.get('oc_det_obs'));
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
							        	var w = Ext.getCmp('nea_det_import_from_oc_window');
										w.mask('importando...');
							   			Ext.Ajax.request({
											params:{
												nea_id: nea_id,
												strlist: values
											},
											url:'nea/importNeaDetFromOC',
											success: function (response, opts) {
												w.unmask();
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Importacion de bienes', result.msg);
													Ext.getCmp('nea_det_import_from_oc_grid').store.reload();
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
						Ext.getCmp('nea_det_import_from_oc_window').close();
					}
				}],
				store: sys2009_det_oc_store,
				emptyText: 'No tiene bienes con ordenes de compra...',
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			listeners: {
				show: function () {
					Ext.getCmp('nea_det_import_from_oc_search_text').focus();
				},
				close: function () {
					Ext.getCmp('nea_det_grid').store.reload();
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_detail_config);
		w.show();

	};
</script>