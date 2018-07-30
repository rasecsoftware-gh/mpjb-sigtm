<script>
	nea.window_nemo_search = function(search_text, selectHandler) {
		var sys2009_nemo_store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'nea/getSys2009NemoList',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						/*var cb = Ext.getCmp('nea_form_detail_cb_bs');
						if (Ext.isDefined(cb)) {
							cb.bindStore(nea.st_nemo);
						}*/
					}
				}
			}
		});

		var w_config = {
			title:'Buscar Centro de costo (Nemonico)', 
			modal: true,
			width: 800,
			height: 600, 
			id:'nea_window_nemo_search',
			layout: 'border',
			items:[{
				xtype: 'panel', bodyPadding: '0px 10px 0 10px',
				region: 'north',
				layout: 'absolute',
				height: 40,
				defaultType: 'textfield',
				items:[{
					fieldLabel: 'Buscar por:',
					xtype: 'textfield',
					id: 'nea_nemo_search_text',
    				x: 10, y: 5, width: 500,
    				listeners: {
    					specialkey: function(field, e) {
		                    if (e.getKey() == e.ENTER) {
		                    	sys2009_nemo_store.reload({
		                    		params: {
		                    			query: field.getValue()
		                    		}
		                    	})
		                    }
		                }
    				}
				},{
					xtype: 'button',
    				id: 'nea_nemo_search_button',
    				text: 'buscar',
				    x: 520, y: 5, width: 100,
				    handler: function () {
				    	sys2009_nemo_store.reload({
                    		params: {
                    			query: Ext.getCmp('nea_nemo_search_text').getValue()
                    		}
                    	})
				    }
				}]
			},{
				xtype: 'grid',
				id:'nea_nemo_search_grid',
				region:'center', 
				store: sys2009_nemo_store,
				//forceFit:true,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				columns:[
					{xtype:'rownumberer'},
					{text:'Año', dataIndex:'nemo_anio', width: 50},
					{text:'Codigo', dataIndex:'nemo_cod', width: 60},
					{text:'Descripcion', dataIndex:'nemo_desc', width: 550},
					{text:'SF', dataIndex:'nemo_secfun', width: 45}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					},
					rowkeydown: function ( table, record, tr, rowIndex, e, eOpts ) {
						if (e.getKey() == e.ENTER) {
							if (Ext.isDefined(selectHandler)) {
								Ext.defer(selectHandler, 250, null, [record]);
								Ext.getCmp('nea_window_nemo_search').close();
							}
						}
					}
				}
			}],
			buttons: [{
				text: 'Seleccionar',
				handler: function () {
					rows = Ext.getCmp('nea_nemo_search_grid').getSelection();
					if (rows.length>0) {
						var record = rows[0];
						if (Ext.isDefined(selectHandler)) {
							selectHandler(record);
						}
						Ext.getCmp('nea_window_nemo_search').close();
					} else {
						Ext.Msg.alert('Error','Seleccione un registro primero');
					}
				}
			},{
				text: 'Sin Centro de costo', 
				handler: function () {
					Ext.getCmp('nea_window_nemo_search').close();
					if (Ext.isDefined(selectHandler)) {
						selectHandler(null);
					}
				}
			}],
			listeners: {
				show: function () {
					Ext.getCmp('nea_nemo_search_text').focus();
				}
			}
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		Ext.getCmp('nea_nemo_search_text').setValue(search_text);
		w.show();
	}
</script>