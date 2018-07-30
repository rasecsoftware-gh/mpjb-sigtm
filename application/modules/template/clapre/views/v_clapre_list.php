<script>
	clapre.grid = Ext.create('Ext.grid.Panel',{
		id:'clapre_grid_main',
		region:'center', split:true, 
		//forceFit:true,
		sortableColumns: false,
		enableColumnHide: false,
		columns:[
			{text:'Codigo', dataIndex:'cod_clapre', width: 120},
			{text:'Descripcion', dataIndex:'desc_clapre', width: 500},
			{text:'Estado', dataIndex:'estado_clapre', width: 80}
		],
		tbar:[{
			text:'Nuevo', handler: function() {
				if (Ext.getCmp('clapre_cb_obra').getSelectedRecord()!=null) {
					clapre.window_new(Ext.getCmp('clapre_cb_obra').getValue());
				} else {
					Ext.Msg.alert('Error','Seleccione una obra primero ¬¬');
				}
				
			}
		},{
			text:'Modificar', handler: function() {
				rows = Ext.getCmp('clapre_grid_main').getSelection();
				if (rows.length>0) {
					clapre.window_edit(rows[0].get('id_clapre'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},{
			text:'Eliminar', handler: function() {
				rows = Ext.getCmp('clapre_grid_main').getSelection();
				if (rows.length>0) {
					clapre.window_delete(rows[0].get('id_clapre'));
				} else {
					Ext.Msg.alert('Error','Seleccione un registro');
				}
			}
		},'->',{
			xtype:'combobox',
			id: 'clapre_cb_obra',
			displayField: 'cod_desc_obra',
			valueField: 'cod_obra',
			store: clapre.st_obra,
			forceSelection: true,
			width: 600,
			listeners: {
				select: function (combo, record, e) {
					clapre.reload_list();
				}
			}
		}],
		store: clapre.st_grid_main,
		listeners:{
			select: function(ths, record, index, eOpts ) {
			}
		}
	});
	clapre.reload_list = function () {
		clapre.st_grid_main.reload({
			params: {
				cod_obra: Ext.getCmp('clapre_cb_obra').getValue()
			}
		});
	};
</script>