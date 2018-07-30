<script>
	contrato.panel = Ext.create('Ext.Panel', {
		region: 'center',
		layout: 'border',
		items: [{
			xtype: 'grid',
			id:'contrato_main_grid',
			region:'center', split:true, 
			//forceFit:true,
			sortableColumns: false,
			enableColumnHide: false,
			columns:[
				{text:'Año', dataIndex:'contrato_anio', width: 50},
				{text:'Tipo', dataIndex:'tipo_contrato_abrev', width: 60},
				{text:'Numero', dataIndex:'contrato_numero', width: 60},
				{text:'DNI', dataIndex:'contrato_traba_dni', width: 70},
				{text:'Trabajador', dataIndex:'contrato_traba_apenom', width: 200},
				//{text:'Dependencia', dataIndex:'contrato_dependencia', width: 230},
				//{text:'Cargo', dataIndex:'contrato_cargo', width: 120},
				{text:'Inicio', dataIndex:'contrato_fecha_inicio', width: 80},
				{text:'Fin', dataIndex:'contrato_fecha_fin', width: 80},
				{text:'F. Emision', dataIndex:'contrato_fecha_emision', width: 80},
				{text:'F. Entrega', dataIndex:'contrato_fecha_entrega', width: 80},
				{text:'Estado', dataIndex:'contrato_estado', width: 80,
					renderer: function (value, metaData, record, rowIndex, colIndex, store, view) {
					    switch (value) {
					    	case 'ANULADO':
					    		value = '<span style="color: red;">'+value+'</span>';
					    	break;
					    	case 'EMITIDO':
					    		value = '<span style="color: green;">'+value+'</span>';
					    	break;
					    	case 'ENTREGADO':
					    		value = '<span style="color: blue;">'+value+'</span>';
					    	break;
					    }
					    return value;
					}
				}
			],
			tbar:[{
				text:'Nuevo', handler: function() {
					contrato.new_window();
				}
			},{
				text:'Modificar', handler: function() {
					var rows = Ext.getCmp('contrato_main_grid').getSelection();
					if (rows.length>0) {
						contrato.edit_window(rows[0].get('contrato_id'));
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},{
				text:'Ver/Generar PDF', handler: function() {
					var rows = Ext.getCmp('contrato_main_grid').getSelection();
					if (rows.length>0) {
						contrato.print_window(rows[0]);
					} else {
						Ext.Msg.alert('Error','Seleccione un registro');
					}
				}
			},'-',{
				text: 'Opciones', 
				hidden: !<?php echo sys_session_hasRoleToString('rh.contrato'); ?>,
				menu: [{
					text: 'Emitir', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.emitir_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Entregar', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.entregar_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},'-',{
					text: 'Cancelar Emitido', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.cancelar_emitido_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Cancelar Entregado', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.cancelar_entregado_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},'-',{
					text: 'Anular', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.anular_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Cancelar Anulado', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.cancelar_anulado_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},{
					text: 'Cancelar Generado', handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.cancelar_generado_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					}
				},'-',{
					text: 'Re-Generar PDF', 
					handler: function() {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.regenerar_pdf_window(rows[0]);
						} else {
							Ext.Msg.alert('Error', 'Seleccione un registro');
						}
					},
					hidden: !<?php echo sys_session_hasRoleToString('sa'); ?>
				},{
					text: 'Generar PDFs&hellip;', 
					handler: function() {
						contrato.generar_pdfs_window();
					},
					hidden: !<?php echo sys_session_hasRoleToString('sa'); ?>
				}]
			},'Mostrar:',{
				xtype: 'combobox',
				id: 'contrato_tipo_contrato_id_cb',
				displayField: 'tipo_contrato_desc',
				valueField: 'tipo_contrato_id',
				name: 'tipo_contrato_id',
				value: '', // ctct
				store: contrato.tipo_contrato_parent_store,
				matchFieldWidth: false,
				forceSelection: true,
				editable: false,
				width: 180,
				listeners: {
					select: function(ths, record, index, eOpts ) {
						contrato.reload_list();
					}
				}
			},'-',{
				text: 'Representantes&hellip;', handler: function() {
					contrato.repre_contrato_window();
				}
			},{
				text: 'Plantillas&hellip;', handler: function() {
					contrato.plantilla_window();
				}
			},'->',{
				xtype: 'combobox',
				id: 'contrato_search_by_cb',
				displayField: 'search_desc',
				valueField: 'search_id',
				name: 'search_by',
				value: 'all',
				matchFieldWidth: false,
				width: 140,
				store: Ext.create('Ext.data.Store', {
					data : [
						{search_id: 'all', search_desc: 'Busqueda General'},
						{search_id: 'numero', search_desc: 'Por Numero de Contrato'},
						//{search_id: 'trabajador', search_desc: 'Por DNI, Apellidos y Nombres del Trabajador'},
						//{search_id: 'dependencia', search_desc: 'Por Dependencia / Area'},
						{search_id: 'estado', search_desc: 'Por Estado'}
					]
				})
			},{
				xtype:'textfield',
				id: 'contrato_search_text',
				enableKeyEvents: true,
				width: 140,
				listeners: {
					keypress: function(sender, e, eOpts) {
						if (e.getKey() == e.ENTER) {
							Ext.getCmp('contrato_search_bt').click(e);
						}
					}
				}
			},{
				id: 'contrato_search_bt',
				text:'Buscar/Actualizar', handler: function() {
					contrato.reload_list();
				}
			}],
			store: contrato.main_store,
			dockedItems: [{
		        xtype: 'pagingtoolbar',
		        store: contrato.main_store, // same store GridPanel is using
		        dock: 'bottom',
		        displayInfo: true
		    }],
			listeners:{
				select: function(ths, record, index, eOpts ) {
					//Ext.getCmp('contrato_info_form').loadRecord(record);
					Ext.getCmp('contrato_info_desc').setValue(
						record.get('tipo_contrato_desc')+' N° '+record.get('contrato_numero')+'-'+record.get('contrato_anio')
						+' | '+record.get('contrato_traba_apenom')
					);
					contrato.adenda_reload_list(record.get('contrato_id'));
				}
			}
		},{
			xtype: 'panel',
			region: 'south',
			layout: 'border',
			height: 400,
			split: true,
			items: [{
				xtype: 'form',
				id: 'contrato_info_form',
				layout: 'absolute',
				region: 'north',
				height: 30,
				bodyStyle: {
					background: '#4c9dd8'
				},
				items: [{
					xtype: 'displayfield',
					fieldLabel: 'ADENDAS:',
					id: 'contrato_info_desc',
					value: '',
					x: 10, y: 5,
					fieldStyle: {
						color: 'white',
						fontWeight: 'bold'
					},
					labelWidth: 70,
					labelStyle: 'color: white; font-weight: bold',
				}]
			},{
				xtype: 'grid',
				id: 'contrato_adenda_grid',
				region: 'center',
				//title: 'Adendas Contractuales',
				emptyText: 'No tiene adendas registradas',
				store: contrato.adenda_store,
				sortableColumns: false,
				enableColumnHide: false,
				tbar: [{
					text: 'Nuevo', 
					//hidden: <?php echo sys_session_hasRoleToString('rh.contrato.new'); ?>,
					handler: function () {
						var rows = Ext.getCmp('contrato_main_grid').getSelection();
						if (rows.length>0) {
							contrato.new_window(rows[0].get('contrato_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},{
					text: 'Modificar',
					handler: function () {
						var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
						if (rows.length>0) {
							contrato.edit_window(rows[0].get('contrato_id'));
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},'-',{
					text: 'Ver/Generar PDF', handler: function() {
						var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
						if (rows.length>0) {
							contrato.print_window(rows[0]);
						} else {
							Ext.Msg.alert('Error','Seleccione un registro');
						}
					}
				},'-',{
					text: 'Opciones', 
					hidden: !<?php echo sys_session_hasRoleToString('rh.contrato'); ?>,
					menu: [{
						text: 'Emitir', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.emitir_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},{
						text: 'Entregar', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.entregar_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},'-',{
						text: 'Cancelar Emitido', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.cancelar_emitido_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},{
						text: 'Cancelar Entregado', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.cancelar_entregado_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},'-',{
						text: 'Anular', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.anular_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},{
						text: 'Cancelar Anulado', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.cancelar_anulado_window(rows[0], true);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},{
						text: 'Cancelar Generado', handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.cancelar_generado_window(rows[0]);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						}
					},'-',{
						text: 'Re-Generar PDF', 
						handler: function() {
							var rows = Ext.getCmp('contrato_adenda_grid').getSelection();
							if (rows.length>0) {
								contrato.regenerar_pdf_window(rows[0]);
							} else {
								Ext.Msg.alert('Error', 'Seleccione un registro');
							}
						},
						hidden: !<?php echo sys_session_hasRoleToString('sa'); ?>
					}]
				}],
				columns:[
					{text: 'Tipo', dataIndex:'tipo_contrato_desc', width: 130},
					{text: 'Año', dataIndex:'contrato_anio', width: 60},
					{text: 'Numero', dataIndex:'contrato_numero', width: 70},
					{text: 'Inicio', dataIndex:'contrato_fecha_inicio', width: 90},
					{text: 'Termino', dataIndex:'contrato_fecha_fin', width: 90},
					{text: 'Documento Ref.', dataIndex:'contrato_docref', width: 150},
					{text: 'Estado', dataIndex:'contrato_estado', width: 100}
				],
				listeners:{
					select: function(ths, record, index, eOpts ) {
					},
					rowdblclick: function(ths, record, tr, rowIndex, e, eOpts) {
						//contrato.plani_traba_window(record);
					}
				}
			}]
		}]
	});

	contrato.reload_list = function (select_id) {
		contrato.select_contrato_id = select_id||0;
		//contrato.main_store.reload();
		contrato.main_store.reload({
			params: {
				search_by: Ext.getCmp('contrato_search_by_cb').getValue(),
				search_text: Ext.getCmp('contrato_search_text').getValue(),
				tipo_contrato_id: Ext.getCmp('contrato_tipo_contrato_id_cb').getValue()
			}
		});
	};
	contrato.adenda_reload_list = function (contrato_id) {
		contrato.adenda_store.reload({
			params: {
				contrato_id_parent: contrato_id
			}
		});
	};
</script>