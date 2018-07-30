<script>
	comprador.grid = Ext.create('Ext.grid.Panel',{
		id:'comprador_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Codigo', dataIndex:'cod_comprador', width: 120},
			{text:'Descripcion', dataIndex:'desc_comprador', width: 400},
			{text:'Estado', dataIndex:'estado_comprador', width: 80}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				comprador.window_new();
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('comprador_grid_main').getSelection();
				if (rows.length>0) {
					comprador.window_edit(rows[0].get('id_comprador'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'comprador_cb_search_by',
			displayField: 'search_desc',
			valueField: 'search_id',
			name: 'search_by',
			value: 'all',
			store: Ext.create('Ext.data.Store', {
				data : [
					{search_id: 'all', search_desc: 'Busqueda General'},
					{search_id: 'cod_comprador', search_desc: 'Por Codigo'},
					{search_id: 'desc_comprador', search_desc: 'Por Descripcion'},
					{search_id: 'estado_comprador', search_desc: 'Por Estado'}
				]
			})
		},{
			xtype:'textfield',
			id: 'comprador_search_text'
		},{
			text:'Buscar', handler:function(){
				comprador.st_grid_main.reload({
					params: {
						search_by: Ext.getCmp('comprador_cb_search_by').getValue(),
						search_text: Ext.getCmp('comprador_search_text').getValue()
					}
				});
			}
		}],
		store: comprador.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
</script>