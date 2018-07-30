<script>
	rbs.window_bien_search = function(search_text, selectHandler) {
		var st_sys2009_bien = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'rbs/getSys2009BienList',
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

		var w_config = {
			title: 'Buscar Bienes', 
			modal: true,
			width: 800,
			height: 600, 
			id:'rbs_window_bien_search',
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
					id: 'rbs_bien_search_text',
    				x: 10, y: 5, width: 500,
    				listeners: {
    					specialkey: function(field, e) {
		                    if (e.getKey() == e.ENTER) {
		                    	st_sys2009_bien.reload({
		                    		params: {
		                    			query: field.getValue()
		                    		}
		                    	});
		                    }
		                }
    				}
				},{
					xtype: 'button',
    				id: 'rbs_bien_search_button',
    				text: 'buscar',
				    x: 520, y: 5, width: 100,
				    handler: function () {
				    	st_sys2009_bien.reload({
                    		params: {
                    			query: Ext.getCmp('rbs_bien_search_text').getValue()
                    		}
                    	});
				    }
				}]
			},{
				xtype: 'grid',
				id:'rbs_bien_search_grid',
				region:'center', 
				store: st_sys2009_bien,
				//forceFit:true,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'bs_cod', width: 100},
					{text:'Descripcion', dataIndex:'bs_desc', width: 500},
					{text:'UniMed', dataIndex:'bs_unimed', width: 100}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					},
					rowkeydown: function ( table, record, tr, rowIndex, e, eOpts ) {
						if (e.getKey() == e.ENTER) {
							if (Ext.isDefined(selectHandler)) {
								Ext.defer(selectHandler, 250, null, [record]);
								Ext.getCmp('rbs_window_bien_search').close();
							}
						}
					}
				}
			}],
			buttons: [{
				text: 'Seleccionar', 
				handler: function () {
					rows = Ext.getCmp('rbs_bien_search_grid').getSelection();
					if (rows.length>0) {
						var record = rows[0];
						if (Ext.isDefined(selectHandler)) {
							selectHandler(record);
						}
						Ext.getCmp('rbs_window_bien_search').close();
					} else {
						Ext.Msg.alert('Error','Seleccione un registro primero');
					}
				}
			}],
			listeners: {
				show: function () {
					Ext.getCmp('rbs_bien_search_text').focus();
				}
			}
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		Ext.getCmp('rbs_bien_search_text').setValue(search_text);
		w.show();
	};
</script>