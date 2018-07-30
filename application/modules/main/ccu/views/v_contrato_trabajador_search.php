<script>
	contrato.trabajador_search_window = function(selectHandler) {
		var trabajador_store = Ext.create("Ext.data.Store", {
			proxy: {
				type: 'ajax',
				url: 'contrato/getTrabaList',
				reader: {
					type: 'json',
					rootProperty: 'data',
					totalProperty: 'total'
				}
			},
			autoLoad: false,
			pageSize: 500,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						/*var cb = Ext.getCmp('contrato_form_detail_cb_bs');
						if (Ext.isDefined(cb)) {
							cb.bindStore(contrato.st_trabajador);
						}*/
					}
				}
			}
		});

		var w_config = {
			title:'Buscar Trabajador', 
			modal: true,
			width: 700,
			height: 550, 
			id: 'contrato_trabajador_search_window',
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
					id: 'contrato_trabajador_search_text',
    				x: 10, y: 5, width: 500,
    				listeners: {
    					specialkey: function(field, e) {
		                    if (e.getKey() == e.ENTER) {
		                    	trabajador_store.reload({
		                    		params: {
		                    			query: field.getValue()
		                    		}
		                    	})
		                    }
		                }
    				}
				},{
					xtype: 'button',
    				id: 'contrato_trabajador_search_button',
    				text: 'buscar',
				    x: 520, y: 5, width: 100,
				    handler: function () {
				    	trabajador_store.reload({
                    		params: {
                    			query: Ext.getCmp('contrato_trabajador_search_text').getValue()
                    		}
                    	})
				    }
				}]
			},{
				xtype: 'grid',
				id:'contrato_trabajador_search_grid',
				region:'center', 
				store: trabajador_store,
				//forceFit:true,
				enableColumnMove: false, enableColumnHide: false, sortableColumns: false,
				columns:[
					{xtype: 'rownumberer', width: 40},
					{text: 'Codigo', dataIndex: 'traba_cod', width: 80},
					{text: 'DNI', dataIndex: 'traba_dni', width: 70},
					{text: 'Descripcion', dataIndex: 'traba_nomape', width: 300},
					{text: 'RUC', dataIndex: 'traba_ruc', width: 90},
					{text: 'Activo', dataIndex: 'traba_activo', width: 70}
				],
				dockedItems: [{
			        xtype: 'pagingtoolbar',
			        store: trabajador_store, // same store GridPanel is using
			        dock: 'bottom',
			        displayInfo: true
			    }],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					},
					rowkeydown: function(table, record, tr, rowIndex, e, eOpts ) {
						if (e.getKey() == e.ENTER) {
							if (Ext.isDefined(selectHandler)) {
								Ext.defer(selectHandler, 250, null, [record]);
								Ext.getCmp('contrato_trabajador_search_window').close();
							}
						}
					},
					rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
						if (Ext.isDefined(selectHandler)) {
							Ext.defer(selectHandler, 250, null, [record]);
							Ext.getCmp('contrato_trabajador_search_window').close();
						}
					}
				}
			}],
			buttons: [{
				text: 'Seleccionar',
				handler: function () {
					rows = Ext.getCmp('contrato_trabajador_search_grid').getSelection();
					if (rows.length>0) {
						var record = rows[0];
						if (Ext.isDefined(selectHandler)) {
							selectHandler(record);
						}
						Ext.getCmp('contrato_trabajador_search_window').close();
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			}],
			listeners: {
				show: function () {
					Ext.getCmp('contrato_trabajador_search_text').focus();
				}
			}
		};
		
		var w = Ext.create('Ext.window.Window', w_config);
		//Ext.getCmp('contrato_trabajador_search_text').setValue(search_text);
		w.show();
	}
</script>