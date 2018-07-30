<script>
	ctl.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'ctl_main_grid',
			region:'center', split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Codigo', dataIndex:'traba_cod', width: 80},
				{text:'DNI', dataIndex:'traba_dni', width: 70},
				{text:'Apellidos y Nombres', dataIndex:'traba_nomape', width: 210},
				{text:'Ubigeo', dataIndex:'traba_ubigeo', width: 80},
				//{text:'RL', dataIndex:'reglab_cod', width: 45},
				//{text:'TC', dataIndex:'sunat_tipo_contrato_cod', width: 45},
				{text:'Activo', dataIndex:'traba_activo', width: 70},
				//{text:'Planillas', dataIndex:'planilla_count', width: 60},
				//{text:'Servicios', dataIndex:'servicio_count', width: 65},
<?php
	$m = date('n'); // mes actual
	$mes = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Set','Oct','Nov','Dic');
	$y = '';
	$w = 45;
	for ($i=1; $i<=13; $i++) {
		if ($m<1) {
			$m = 12;
			$y = ((date('Y')-1)-2000);
			$w = 45 + 7;
		}
		echo "{text:'{$mes[$m]}{$y}', dataIndex:'m{$i}', width: {$w}, 
				renderer: function (value, metaData, record) {
				    // evaluate the record's `updated` field and if truthy return the value 
				    // from the `newVal` field, else return value
				    metaData.tdStyle = 'font-size: 6pt';
				    return value;
				}},\n";
		$m--;
	}
?>				
				{text:'MesesLab', dataIndex:'meses_lab', width: 75}

			],
			tbar:[{
				text: 'Consultar', handler: function() {
					ctl.main_store.reload({
						params: {
							search_opcion: Ext.getCmp('ctl_cb_search_opcion').getValue(),
							search_estado: Ext.getCmp('ctl_cb_search_estado').getValue(),
							search_ubigeo: Ext.getCmp('ctl_cb_search_ubigeo').getValue()
						}
					});
				}
			},'-',{
				text:'Imprimir', handler: function() {
					ctl.print_window();
				}
			},{
				text:'Excel', handler: function(){
					Ext.Ajax.request({
					url: 'ctl/getExcel',
					method: 'POST',
					params: {
						rkey: ctl.rkey,
					},
					success: function(data, response){
						var resp=JSON.parse(data.responseText);
						var a = document.createElement("a");
						a.href = resp.file;
						a.download = resp.name;
						a.target = '_blank';
						document.body.appendChild(a); 
						a.click();
					}
				});
			  }
			},'-',{
				text: 'Imprimir Detallado', handler: function() {
					ctl.print_detalle_window();
				}
			},'->',{
				xtype: 'combobox',
				id: 'ctl_cb_search_opcion',
				displayField: 'opcion_desc',
				valueField: 'opcion_id',
				name: 'search_opcion',
				value: 'pe',
				width: 230,
				store: Ext.create('Ext.data.Store', {
					data : [
						{opcion_id: 'pe', opcion_desc: 'Opcion: Periodo de evaluacion'},
						{opcion_id: 'all', opcion_desc: 'Todos los periodos'},
						{opcion_id: 'gestion', opcion_desc: 'Periodos de Gestion'},
						{opcion_id: 'gestion3s', opcion_desc: 'Periodos de Gestion con 3 Ult. Servicios'}
					]
				})
			},{
				xtype: 'combobox',
				id: 'ctl_cb_search_estado',
				displayField: 'estado_desc',
				valueField: 'estado_id',
				name: 'search_estado',
				value: 'all',
				store: Ext.create('Ext.data.Store', {
					data : [
						{estado_id: 'all', estado_desc: 'Estado: Todos'},
						{estado_id: 'activo', estado_desc: 'Activos'},
						{estado_id: 'noactivo', estado_desc: 'No Activos'}
					]
				})
			},{
				xtype: 'combobox',
				id: 'ctl_cb_search_ubigeo',
				displayField: 'ubigeo_desc',
				valueField: 'ubigeo_id',
				name: 'search_ubigeo',
				value: 'all',
				store: Ext.create('Ext.data.Store', {
					data : [
						{ubigeo_id: 'all', ubigeo_desc: 'Ubigeo: Todos'},
						{ubigeo_id: 'locumba', ubigeo_desc: 'Solo Locumba'},
						{ubigeo_id: 'nolocumba', ubigeo_desc: 'No Locumba'}
					]
				})
			},/*,{
				xtype:'combobox',
				id: 'ctl_cb_search_by',
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
				id: 'ctl_search_text',
				enableKeyEvents: true,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('ctl_search_bt').click(e);
						}
					}
				}
			},{
				id: 'ctl_search_bt',
				text:'Buscar', handler: function() {
					ctl.main_store.reload({
						params: {
							search_by: Ext.getCmp('ctl_cb_search_by').getValue(),
							search_text: Ext.getCmp('ctl_search_text').getValue()
						}
					});
				}
			}*/],
			store: ctl.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: ctl.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function(ths, record, index, eOpts ) {
					ctl.planilla_store.reload({
						params: {
		    				traba_cod: record.get('traba_cod')
		    			}
					});
					Ext.getCmp('ctl_servicio_glosa').setValue('');
					ctl.servicio_store.reload({
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
					id: 'ctl_planilla_grid',
					region: 'center',
					emptyText: 'No tiene periodos registrados',
					store: ctl.planilla_store,
					sortableColumns: false,
					enableColumnHide: false,
					columns:[
						{text:'Año', dataIndex:'plani_anio', width: 45},
						{text:'Mes', dataIndex:'plani_mes', width: 40},
						{text:'Planilla', dataIndex:'plani_num', width: 65},
						{text:'Reg.Lab.', dataIndex:'reglab_abrev', width: 75},
						{text:'Nemo', dataIndex:'nemo_cod', width: 50},
						{text:'DiasLab', dataIndex:'plani_traba_diaslab', width: 60},
						{text:'Bruto', dataIndex:'plani_traba_bruto', width: 70}
					],
					listeners:{
						select: function(ths, record, index, eOpts ) {
							Ext.getCmp('ctl_planilla_form').loadRecord(record);
						},
						rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
							ctl.plani_traba_window(record);
						}
					}
				},{
					xtype: 'form',
					id: 'ctl_planilla_form',
					layout: 'form',
					region: 'south',
					height: 120,
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
					}]
				}]
			},{
				xtype: 'panel',
				title: 'Servicios',
				layout: 'border',
				region: 'center',
				items: [{
					xtype: 'grid',
					id: 'ctl_servicio_grid',
					region: 'center',
					emptyText: 'No tiene servicios registrados',
					store: ctl.servicio_store,
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
							Ext.getCmp('ctl_servicio_glosa').setValue(record.get('os_glosa'));
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
						id: 'ctl_servicio_glosa',
						region: 'center',
						readOnly: true
					}]
				}]
			}]
		}]
	});

	ctl.reload_list = function () {
		ctl.main_store.reload();
	};
</script>