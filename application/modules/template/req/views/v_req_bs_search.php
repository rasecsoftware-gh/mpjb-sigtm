<script>
	req.window_bs_search = function(search_text, selectHandler) {
		var st_bieser = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'req/getBSList',
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						/*var cb = Ext.getCmp('req_form_detail_cb_bs');
						if (Ext.isDefined(cb)) {
							cb.bindStore(req.st_bieser);
						}*/
					}
				}
			}
		});

		var w_config = {
			title:'Busqueda de bienes y servicios', 
			modal: true,
			width: 800,
			height: 600, 
			id:'req_window_bs_search',
			layout: 'border',
			items:[{
				xtype:'panel', bodyPadding: '0px 10px 0 10px',
				region: 'north',
				layout: 'absolute',
				height: 40,
				defaultType:'textfield',
				items:[{
					fieldLabel: 'Buscar por:',
					xtype: 'textfield',
					id: 'req_bs_search_text',
    				x: 10, y: 5, width: 500,
    				listeners: {
    					specialkey: function(field, e) {
		                    if (e.getKey() == e.ENTER) {
		                    	st_bieser.reload({
		                    		params: {
		                    			query: field.getValue()
		                    		}
		                    	})
		                    }
		                }
    				}
				},{
					xtype: 'button',
    				id: 'req_bs_search_button',
    				text: 'buscar',
				    x: 520, y: 5, width: 100,
				    handler: function () {
				    	st_bieser.reload({
                    		params: {
                    			query: Ext.getCmp('req_bs_search_text').getValue()
                    		}
                    	})
				    }
				}]
			},{
				xtype: 'grid',
				id:'req_bs_search_grid',
				region:'center', 
				store: st_bieser,
				//forceFit:true,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				columns:[
					{xtype: 'rownumberer'},
					{text:'Codigo', dataIndex:'cod_bs', width: 110},
					{text:'Bien/Servicio', dataIndex:'desc_bs', width: 450},
					{text:'UM', dataIndex:'desc_unimed', width: 100},
					{text:'V.Ref', dataIndex:'valref_bs', width: 80, align: 'right', xtype: 'numbercolumn', format:'0.0000'}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					},
					rowkeydown: function ( table, record, tr, rowIndex, e, eOpts ) {
						if (e.getKey() == e.ENTER) {
							if (Ext.isDefined(selectHandler)) {
								Ext.defer(selectHandler, 250, null, [record]);
								Ext.getCmp('req_window_bs_search').close();
							}
						}
					}
				}
			}],
			buttons: [{
				text: 'Seleccionar', 
				handler: function () {
					rows = Ext.getCmp('req_bs_search_grid').getSelection();
					if (rows.length>0) {
						var record = rows[0];
						if (Ext.isDefined(selectHandler)) {
							selectHandler(record);
						}
						Ext.getCmp('req_window_bs_search').close();
					} else {
						Ext.Msg.alert('Error','Seleccione un registro primero');
					}
				}
			}]
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		Ext.getCmp('req_bs_search_text').setValue(search_text);
		w.show();
	}
</script>