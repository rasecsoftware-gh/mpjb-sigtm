<script>
	geb.window_geb_det_import = function(geb_id) {
		var st_sys2009_det_oc = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'geb/getSys2009DetOCList/'+geb_id,
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
		var st_sys2009_oc = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'geb/getSys2009OCList/'+geb_id,
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
			title:'Agregar / Importar detalles', 
			modal: true,
			width: 1000,
			height: 500, 
			id:'geb_window_geb_det_import',
			layout: 'border',
			items:[{
				xtype: 'grid',
				id: 'geb_form_geb_det_import_grid_oc',
				region: 'west', 
				width: 165,
				split: true,
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					{text:'Anio', dataIndex:'oc_anio', width: 45, menuDisabled: true},
					{text:'Orden', dataIndex:'oc_numero', width: 70, menuDisabled: true}
				],
				tbar:[{
					xtype: 'textfield',
					id: 'geb_form_geb_det_import_oc_search_text',
					width: 80
				},{
					text:'Buscar', 
					handler: function() {
						st_sys2009_oc.reload({
							params:{
								query: Ext.getCmp('geb_form_geb_det_import_oc_search_text').getValue()
							}
						});
					}
				}],
				store: st_sys2009_oc,
				listeners: {
					select: function(ths, record, index, eOpts ) {
						st_sys2009_det_oc.reload({
							params:{
								orden_anio: record.get('oc_anio'),
								orden_numero: record.get('oc_numero'),
								query: Ext.getCmp('geb_form_geb_det_import_search_text').getValue()
							}
						});
					}
				}
			},{
				xtype: 'grid',
				id:'geb_form_geb_det_import_grid',
				region:'center', 
				//features: [{ftype: 'summary'}],
				defaults: {
					menuDisabled: true
				},
				columns:[
					{xtype: 'rownumberer'},
					/*{text:'Anio', dataIndex:'orden_anio', width: 45, menuDisabled: true},
					{text:'Orden', dataIndex:'orden_numero', width: 70, menuDisabled: true},*/
					{text:'Codigo', dataIndex:'bs_cod', width: 100, menuDisabled: true},
					{text:'Bien', dataIndex:'bs_desc', width: 210, menuDisabled: true},
					{text:'Observacion', dataIndex:'obs', width: 170, menuDisabled: true},
					{text:'Cant.', dataIndex:'cantidad', width: 70, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true},
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
					{text:'Saldo', dataIndex:'saldo', width: 60, align: 'right', xtype: 'numbercolumn', format:'0.00', menuDisabled: true}
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
					id: 'geb_form_geb_det_import_search_text',
					width: 200,
					enableKeyEvents: true,
					listeners: {
						keypress: function (sender, e, eOpts) {
							if (e.getKey() == e.ENTER) {
								st_sys2009_det_oc.reload({
									params:{
										query: Ext.getCmp('geb_form_geb_det_import_search_text').getValue()
									}
								});		
							}
						}
					}
				},{
					text:'Buscar', 
					handler: function() {
						st_sys2009_det_oc.reload({
							params:{
								query: Ext.getCmp('geb_form_geb_det_import_search_text').getValue()
							}
						});
					}
				},'->',{
					xtype: 'label',
					text: 'Seleccione uno o varios registros para'
				},{
					text:'Importar',
					handler: function () {
						var rows = Ext.getCmp('geb_form_geb_det_import_grid').getSelection();
						var rowid_list = [];
						for (var i=0; i<rows.length; i++) {
							var record = rows[i]; // record is of type grid row
							var row_id = record.get('oc_anio')+'.'+record.get('oc_numero')+'.'+record.get('bs_cod')+'.'+Ext.util.Base64.encode(record.get('obs'));
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
							        	var w = Ext.getCmp('geb_window_geb_det_import');
										w.mask('importando...');
							   			Ext.Ajax.request({
											params:{
												geb_id: geb_id,
												strlist: values
											},
											url:'geb/importGebDetFromOC',
											success: function (response, opts) {
												w.unmask();
												var result = Ext.decode(response.responseText);
												if (result.success) {
													Ext.Msg.alert('Importacion de bienes', result.msg);
													Ext.getCmp('geb_form_geb_det_import_grid').store.reload();
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
						Ext.getCmp('geb_window_geb_det_import').close();
					}
				}],
				store: st_sys2009_det_oc,
				listeners:{
					select: function(ths, record, index, eOpts ) {
					}
				}
			}],
			listeners: {
				show: function () {
					st_sys2009_oc.reload();
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