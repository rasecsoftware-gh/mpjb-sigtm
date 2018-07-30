<script>
	nea.nea_det_import_from_cb_window = function(nea_id) {
		var sys2009_bien_store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'nea/getSys2009BienList/'+nea_id,
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
			title: 'Agregar desde el catalogo de bienes', 
			modal: true,
			width: 800,
			height: 500, 
			id: 'nea_det_import_from_cb_window',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id: 'net_det_import_from_cb_grid',
				region: 'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text: 'Codigo', dataIndex: 'bs_cod', width: 100, menuDisabled: true},
					{text: 'Bien', dataIndex: 'bs_desc', width: 400, menuDisabled: true},
					{text: 'UniMed', dataIndex: 'bs_unimed', width: 80, menuDisabled: true},
					{text: 'Importado', dataIndex: 'importado', width: 80, align: 'center', tooltip: 'Importado', tooltipType: 'title', menuDisabled: true, 
						renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
						    if (value.toUpperCase() == 'SI') {
						    	value = '<span style="color: blue;">'+value+'</span>';
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
					text: 'Busque registros segun'
				},{
					xtype:'textfield',
					id: 'net_det_import_from_cb_search_text',
					width: 200,
					enableKeyEvents: true,
					listeners: {
						keypress: function (sender, e, eOpts) {
							if (e.getKey() == e.ENTER) {
								sys2009_bien_store.reload({
									params:{
										query: Ext.getCmp('net_det_import_from_cb_search_text').getValue()
									}
								});		
							}
						}
					}
				},{
					text:'Buscar', 
					handler: function() {
						sys2009_bien_store.reload({
							params:{
								query: Ext.getCmp('net_det_import_from_cb_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = Ext.getCmp('net_det_import_from_cb_grid').getSelection();
						var rowid_list = [];
						for (var i=0; i<rows.length; i++) {
							var record = rows[i]; // record is of type grid row
							var row_id = record.get('bs_cod');
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
							        	var w = Ext.getCmp('nea_det_import_from_cb_window');
										w.mask('importando...');
							   			Ext.Ajax.request({
											params:{
												nea_id: nea_id,
												strlist: values
											},
											url:'nea/importNeaDetFromCB',
											success: function (response, opts) {
												w.unmask();
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Importacion de bienes', result.msg);
													Ext.getCmp('net_det_import_from_cb_grid').store.reload();
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
						Ext.getCmp('nea_det_import_from_cb_window').close();
					}
				}],
				store: sys2009_bien_store,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			listeners: {
				show: function () {
					//sys2009_bien_store.reload();
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