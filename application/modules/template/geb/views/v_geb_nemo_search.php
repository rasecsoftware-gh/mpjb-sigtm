<script>
	geb.window_nemo_search = function(search_text, selectHandler) {
		var st_sys2009_nemo = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'geb/getSys2009NemoList',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						/*var cb = Ext.getCmp('geb_form_detail_cb_bs');
						if (Ext.isDefined(cb)) {
							cb.bindStore(geb.st_nemo);
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
			id:'geb_window_nemo_search',
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
					id: 'geb_nemo_search_text',
    				x: 10, y: 5, width: 500,
    				listeners: {
    					specialkey: function(field, e) {
		                    if (e.getKey() == e.ENTER) {
		                    	st_sys2009_nemo.reload({
		                    		params: {
		                    			query: field.getValue()
		                    		}
		                    	})
		                    }
		                }
    				}
				},{
					xtype: 'button',
    				id: 'geb_nemo_search_button',
    				text: 'buscar',
				    x: 520, y: 5, width: 100,
				    handler: function () {
				    	st_sys2009_nemo.reload({
                    		params: {
                    			query: Ext.getCmp('geb_nemo_search_text').getValue()
                    		}
                    	})
				    }
				}]
			},{
				xtype: 'grid',
				id:'geb_nemo_search_grid',
				region:'center', 
				store: st_sys2009_nemo,
				//forceFit:true,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				columns:[
					{xtype: 'rownumberer'},
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
								Ext.getCmp('geb_window_nemo_search').close();
							}
						}
					}
				}
			}],
			buttons: [{
				text: 'Seleccionar', 
				handler: function () {
					rows = Ext.getCmp('geb_nemo_search_grid').getSelection();
					if (rows.length>0) {
						var record = rows[0];
						if (Ext.isDefined(selectHandler)) {
							selectHandler(record);
						}
						Ext.getCmp('geb_window_nemo_search').close();
					} else {
						Ext.Msg.alert('Error','Seleccione un registro primero');
					}
				}
			}],
			listeners: {
				show: function () {
					Ext.getCmp('geb_nemo_search_text').focus();
				}
			}
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		Ext.getCmp('geb_nemo_search_text').setValue(search_text);
		w.show();
	}
</script>