<script>
	cheque.grid = Ext.create('Ext.grid.Panel', {
		id:'cheque_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Año', dataIndex:'ano_eje', width: 50},
			{text:'Expediente', dataIndex:'expediente', width: 100},
			{text:'Nro. C/P', dataIndex:'num_doc_cp', width: 100},
			{text:'RUC', dataIndex:'ruc', width: 90},
			{text:'Proveedor', dataIndex:'proveedor', width: 200},
			{text:'Nro. Cheque', dataIndex:'num_doc', width: 100},
			{text:'Fecha', dataIndex:'fecha_doc', width: 100},
			{text:'Nombre/Girado', dataIndex:'nombre', width: 200},
			{text:'Monto', dataIndex:'monto', width: 120, align: 'right', xtype: 'numbercolumn', format:'0.00'},
			{text:'Estado', dataIndex:'estado_envio', width: 100, align: 'center',
				renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
				    switch (value) {
				    	case 'P':
				    		value = '<span style="color: gray;">'+value+'</span>';
				    	break;
				    	case 'A':
				    		value = '<span style="color: green;">'+value+'</span>';
				    	break;
				    	case 'T':
				    		value = '<span style="color: orange;">'+value+'</span>';
				    	break;
				    	default: 
				    		value = '<span style="color: black;">'+value+'</span>';
				    }
				    return value;
				}
			}
		],
		tbar:[{
			xtype:'combobox',
			id: 'cheque_ano_eje_cb',
			displayField: 'ano_eje_desc',
			valueField: 'ano_eje_id',
			name: 'ano_eje',
			value: '2017',
			store: Ext.create('Ext.data.Store', {
				data : [
					{ano_eje_id: '2017', ano_eje_desc: 'Año 2017'},
					{ano_eje_id: '2016', ano_eje_desc: 'Año 2016'},
					{ano_eje_id: '2015', ano_eje_desc: 'Año 2015'}
				]
			})
		},'-',{
			text:'Imprimir', handler: function() {
				var rows = Ext.getCmp('cheque_grid_main').getSelection();
				if (rows.length>0) {
					cheque.print_window(rows[0].get('expediente_documento_id'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'cheque_cb_search_by',
			fieldLabel: 'Buscar',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'expediente',
			labelWidth: 60,
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'expediente', search_desc: 'Por Expediente'},
					{search_id: 'all', search_desc: 'Busq. General'}
				]
			})
		},{
			xtype:'textfield',
			id: 'cheque_search_text',
			enableKeyEvents: true,
			listeners: {
				keypress: function(sender, e, eOpts) {
					if (e.getKey() == e.ENTER) {
						Ext.getCmp('cheque_search_bt').click(e);
					}
				}
			}
		},{
			id: 'cheque_search_bt',
			text:'Buscar', handler: function() {
				cheque.main_store.reload({
					params: {
						ano_eje: Ext.getCmp('cheque_ano_eje_cb').getValue(),
						search_by: Ext.getCmp('cheque_cb_search_by').getValue(),
						search_text: Ext.getCmp('cheque_search_text').getValue()
					}
				});
			}
		}],
		store: cheque.main_store,
		dockedItems: [{
	        xtype: 'pagingtoolbar',
	        store: cheque.main_store, // same store GridPanel is using
	        dock: 'bottom',
	        displayInfo: true
	    }],
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
</script>