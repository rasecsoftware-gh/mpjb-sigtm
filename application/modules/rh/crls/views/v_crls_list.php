<script>
	crls.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'crls_main_grid',
			region:'center', split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Codigo', dataIndex:'traba_cod', width: 80},
				{text:'DNI', dataIndex:'traba_dni', width: 70},
				{text:'Apellidos y Nombres', dataIndex:'traba_apenom', width: 210},
				{text:'Fecha Nac.', dataIndex:'traba_fecha_naci', width: 80},
				{text:'Direccion', dataIndex:'traba_direccion', width: 200},
				{text:'Telefono', dataIndex:'traba_telefono', width: 80},
				{text:'Ubigeo', dataIndex:'traba_ubigeo', width: 80},
				{text:'Planillas', dataIndex:'planilla_count', width: 70},
				{text:'Servicios', dataIndex:'servicio_count', width: 70},
				{text:'Activo', dataIndex:'traba_activo', width: 60}
			],
			tbar:[{
				text:'Imprimir', handler: function() {
					rows = Ext.getCmp('crls_main_grid').getSelection();
					if (rows.length>0) {
						crls.print_window(rows[0].get('traba_cod'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'->',{
				xtype:'combobox',
				id: 'crls_cb_search_ubig',
				displayField: 'ubigeo_desc',
				valueField: 'ubigeo_id',
				name: 'search_ubig',
				value: 'all',
				store: Ext.create('Ext.data.Store', {
					data : [
						{ubigeo_id: 'all', ubigeo_desc: 'Ubigeo: Todos'},
						{ubigeo_id: 'locumba', ubigeo_desc: 'Solo Locumba'},
						{ubigeo_id: 'notLocumba', ubigeo_desc: 'No Locumba'}
					]
				})
			},{
				xtype:'combobox',
				id: 'crls_cb_search_by',
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
				id: 'crls_search_text',
				enableKeyEvents: true,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('crls_search_bt').click(e);
						}
					}
				}
			},{
				id: 'crls_search_bt',
				text:'Buscar', handler: function() {
					crls.main_store.reload({
						params: {
							search_by: Ext.getCmp('crls_cb_search_by').getValue(),
							search_text: Ext.getCmp('crls_search_text').getValue(),
							search_ubig: Ext.getCmp('crls_cb_search_ubig').getValue(),
						}
					});
				}
			}],
			store: crls.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: crls.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function(ths, record, index, eOpts ) {
					crls.planilla_store.reload({
						params: {
		    				traba_cod: record.get('traba_cod')
		    			}
					});
					Ext.getCmp('crls_servicio_glosa').setValue('');
					crls.servicio_store.reload({
						params: {
		    				traba_cod: record.get('traba_dni')
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
			items: [{
				xtype: 'panel',
				title: 'Record Laboral',
				layout: 'border',
				region: 'west',
				width: 500,
				split: true,
				items: [{
					xtype: 'grid',
					id: 'crls_planilla_grid',
					region: 'center',
					emptyText: 'No tiene periodos registrados',
					store: crls.planilla_store,
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
							Ext.getCmp('crls_planilla_form').loadRecord(record);
						},
						rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
							crls.plani_traba_window(record);
						}
					}
				},{
					xtype: 'form',
					id: 'crls_planilla_form',
					layout: 'form',
					region: 'south',
					height: 150,
					split: true,
					items: [{
						fieldLabel: 'Planilla',
						xtype: 'textfield',
						name: 'plani_num'
					},{
						fieldLabel: 'Descripcion',
						xtype: 'textfield',
						name: 'plani_titulo'
					},{
						fieldLabel: 'Nemonico',
						xtype: 'textfield',
						name: 'nemo_desc'
					},{
						fieldLabel: 'Cargo',
						xtype: 'textfield',
						name: 'plani_traba_cargo'
					},{
						fieldLabel: 'Observacion',
						xtype: 'textfield',
						name: 'plani_glosa'
					}]
				}]
			},{
				xtype: 'panel',
				title: 'Servicios',
				layout: 'border',
				region: 'center',
				items: [{
					xtype: 'grid',
					id: 'crls_servicio_grid',
					region: 'center',
					emptyText: 'No tiene servicios registrados',
					store: crls.servicio_store,
					sortableColumns: false,
					enableColumnHide: false,
					columns:[
						{text:'Año', dataIndex:'os_anio', width: 50},
						{text:'Numero', dataIndex:'os_numero', width: 70},
						{text:'Fecha', dataIndex:'os_fecha', width: 80},
						{text:'Proveedor', dataIndex:'prove_desc', width: 200},
						{text:'Anulado', dataIndex:'os_anulado', width: 60},
						{text:'Total', dataIndex:'os_total', width: 70}
					],
					listeners:{
						select: function(ths, record, index, eOpts ) {
							Ext.getCmp('crls_servicio_glosa').setValue(record.get('os_glosa'));
						}
					}
				},{
					xtype: 'panel',
					title: 'Servicio: Glosa',
					layout: 'border',
					region: 'south',
					height: 160,
					items: [{
						xtype: 'textarea',
						id: 'crls_servicio_glosa',
						region: 'center',
						readOnly: true
					}]
				}]
			}]
		}]
	});

	crls.reload_list = function () {
		crls.main_store.reload();
	};
</script>