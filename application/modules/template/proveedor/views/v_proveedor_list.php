<script>
	proveedor.grid = Ext.create('Ext.grid.Panel',{
		id:'proveedor_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'RUC', dataIndex:'ruc_proveedor', width: 120},
			{text:'Descripcion', dataIndex:'desc_proveedor', width: 300},
			{text:'Direccion', dataIndex:'dir_proveedor', width: 150},
			{text:'Telefono', dataIndex:'telefono_proveedor', width: 130},
			{text:'Correo', dataIndex:'correo_proveedor', width: 150},
			{text:'Rep. Legal', dataIndex:'repleg_proveedor', width: 130},
			{text:'Estado', dataIndex:'estado_proveedor', width: 70}
		],
		dockedItems: [{
	        xtype: 'pagingtoolbar',
	        store: proveedor.st_grid_main, // same store GridPanel is using
	        dock: 'bottom',
	        displayInfo: true
	    }],
		tbar:[{
			text:'Nuevo', handler: function() {
				proveedor.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('proveedor_grid_main').getSelection();
				if (rows.length>0) {
					proveedor.window_edit(rows[0].get('id_proveedor'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'proveedor_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'ruc_proveedor', search_desc: 'Por RUC'},
					{search_id: 'desc_proveedor', search_desc: 'Por Descripcion'},
					{search_id: 'repleg_proveedor', search_desc: 'Por Rep. Legal'},
					{search_id: 'estado_proveedor', search_desc: 'Por Estado'}
				]
			})
		},{
			xtype:'textfield',
			id: 'proveedor_search_text'
		},{
			text:'Buscar', handler:function(){
				proveedor.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('proveedor_cb_search_by').getValue(),
						search_text: Ext.getCmp('proveedor_search_text').getValue()
					}
				});
			}
		}],
		store: proveedor.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
</script>