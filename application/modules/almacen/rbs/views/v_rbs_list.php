<script>
	rbs.grid = Ext.create('Ext.grid.Panel', {
		id:'rbs_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Codigo', dataIndex:'bs_cod', width: 100},
			{text:'Descripcion', dataIndex:'bs_desc', width: 300},
			{text:'Obs', dataIndex:'oc_det_obs', width: 200},
			{text:'UniMed', dataIndex:'bs_unimed', width: 80},
			{text:'Cantidad', dataIndex:'oc_det_cantidad', width: 60},
			{text:'Saldo', dataIndex:'oc_det_saldo', width: 70},
			{text:'O/C', dataIndex:'oc_anio_numero', width: 150}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				rbs.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('rbs_grid_main').getSelection();
				if (rows.length>0) {
					rbs.window_edit(rows[0].get('oc_det_id'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'rbs_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'bs_cod', search_desc: 'Por Codigo'},
					{search_id: 'bs_desc', search_desc: 'Por Descripcion'}
				]
			})
		},{
			xtype:'textfield',
			id: 'rbs_search_text',
			enableKeyEvents: true,
			listeners: {
				keypress: function(sender, e, eOpts) {
					if (e.getKey() == e.ENTER) {
						Ext.getCmp('rbs_search_bt').click(e);
					}
				}
			}
		},{
			id: 'rbs_search_bt',
			text:'Buscar', handler: function() {
				rbs.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('rbs_cb_search_by').getValue(),
						search_text: Ext.getCmp('rbs_search_text').getValue()
					}
				});
			}
		}],
		store: rbs.st_grid_main,
		dockedItems: [{
	        xtype: 'pagingtoolbar',
	        store: rbs.st_grid_main, // same store GridPanel is using
	        dock: 'bottom',
	        displayInfo: true
	    }],
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
	rbs.reload_list = function () {
		rbs.st_grid_main.reload();
	};
</script>