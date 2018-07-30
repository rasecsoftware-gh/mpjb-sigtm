<script>
	asistencia.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'asistencia_main_grid',
			region:'center', split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Codigo', dataIndex:'traba_cod', width: 80},
				{text:'DNI', dataIndex:'traba_dni', width: 70},
				{text:'Nombres y Apellidos', dataIndex:'traba_nomape', width: 210},
				{text:'Fecha Nac.', dataIndex:'traba_fecha_naci', width: 80},
				{text:'Direccion', dataIndex:'traba_direccion', width: 200},
				{text:'Telefono', dataIndex:'traba_telefono', width: 80},
				{text:'Planillas', dataIndex:'planilla_count', width: 70},
				{text:'Servicios', dataIndex:'servicio_count', width: 70},
				{text:'Activo', dataIndex:'traba_activo', width: 60}
			],
			tbar:[{
				text:'Imprimir', handler: function() {
					rows = Ext.getCmp('asistencia_main_grid').getSelection();
					if (rows.length>0) {
						asistencia.print_window(rows[0].get('traba_cod'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'->',{
				xtype:'combobox',
				id: 'asistencia_cb_search_by',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'traba_dni', search_desc: 'Por DNI'},
						{search_id: 'traba_desc', search_desc: 'Por Descripcion'}
					]
				})
			},{
				xtype:'textfield',
				id: 'asistencia_search_text',
				enableKeyEvents: true,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('asistencia_search_bt').click(e);
						}
					}
				}
			},{
				id: 'asistencia_search_bt',
				text:'Buscar', handler: function() {
					asistencia.main_store.reload({
						params: {
							search_by: Ext.getCmp('asistencia_cb_search_by').getValue(),
							search_text: Ext.getCmp('asistencia_search_text').getValue()
						}
					});
				}
			}],
			store: asistencia.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: asistencia.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function(ths, record, index, eOpts ) {
					asistencia.marcacion_store.reload({
						params: {
		    				traba_dni: record.get('traba_dni'),
		    				fecha_ini: Ext.getCmp('asistencia_fecha_ini').getRawValue(),
		    				fecha_fin: Ext.getCmp('asistencia_fecha_fin').getRawValue()
		    			}
					});
				}
			}
		},{
			xtype: 'panel',
			region: 'south',
			layout: 'border',
			height: 400,
			split: true,
			tbar: [{
				xtype: 'datefield',
				id: 'asistencia_fecha_ini',
				format: 'd/m/Y',
				value: '<?php echo date('d/m/Y'); ?>'
			},{
				xtype: 'datefield',
				id: 'asistencia_fecha_fin',
				format: 'd/m/Y',
				value: '<?php echo date('d/m/Y'); ?>'
			}],
			items: [{
				xtype: 'grid',
				id: 'asistencia_marcacion_grid',
				region: 'west',
				emptyText: 'No tiene marcaciones registrados',
				store: asistencia.marcacion_store,
				sortableColumns: false,
				enableColumnHide: false,
				width: 300,
				columns:[
					{text:'Biometrico', dataIndex:'sensorid', width: 80},
					{text:'Fecha y Hora', dataIndex:'checktime', width: 150}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						//Ext.getCmp('asistencia_planilla_form').loadRecord(record);
					},
					rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
						//asistencia.plani_traba_window(record);
					}
				}
			},{
				xtype: 'grid',
				id: 'asistencia_asistencia_grid',
				region: 'center',
				emptyText: 'No tiene informacion registrados',
				store: asistencia.asistencia_store,
				sortableColumns: false,
				enableColumnHide: false,
				columns:[
					{text:'Año', dataIndex:'plani_anio', width: 45},
					{text:'Mes', dataIndex:'plani_mes', width: 40},
					{text:'Planilla', dataIndex:'plani_num', width: 65},
					{text:'Reg.Lab.', dataIndex:'reglab_abrev', width: 75},
					{text:'Nemo', dataIndex:'nemo_cod', width: 50},
					{text:'SIAF', dataIndex:'plani_siaf', width: 50},
					{text:'DiasLab', dataIndex:'plani_traba_diaslab', width: 60},
					{text:'Bruto', dataIndex:'plani_traba_bruto', width: 70, xtype: 'numbercolumn', format:'0.00'}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
						//Ext.getCmp('asistencia_planilla_form').loadRecord(record);
					},
					rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
						//asistencia.plani_traba_window(record);
					}
				}
			}]
		}]
	});

	asistencia.reload_list = function () {
		asistencia.main_store.reload();
	};
</script>