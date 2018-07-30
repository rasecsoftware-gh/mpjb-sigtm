<script>
	crls.plani_traba_window = function(record) {
		var w_config = {
			id:'crls_plani_traba_window',
			title:'Detalles de planilla por trabajador', 
			modal: true,
			width: 950,
			height: 650, 
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				region: 'north',
				layout: 'absolute',
				id: 'crls_plani_traba_form',
				height: 260,
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					fieldLabel:'<b>Trabajador</b>',
    				name: 'traba_dni',
				    x: 10, y: 10, width: 180,
				    readOnly: true
				},{
    				name: 'traba_nomape',
    				readOnly: true,
				    x: 195, y: 10, width: 350
				},{
					fieldLabel:'Año, Mes y Planilla',
    				name: 'plani_anio',
    				readOnly: true,
				    x: 10, y: 40, width: 150
				},{
    				name: 'plani_mes',
    				readOnly: true,
				    x: 165, y: 40, width: 35
				},{
    				name: 'plani_num',
    				readOnly: true,
				    x: 205, y: 40, width: 70
				},{
					fieldLabel:'Descripcion',
    				name: 'plani_titulo',
				    x: 10, y: 70, width: 750,
				    readOnly: true
				},{
					fieldLabel:'Tipo Contrato',
    				name: 'tipo_contrato_desc',
				    x: 10, y: 100, width: 600,
				    readOnly: true
				},{
					fieldLabel:'Regimen Lab.',
    				name: 'reglab_desc',
				    x: 10, y: 130, width: 600,
				    readOnly: true
				},{
					fieldLabel:'Nemonico',
    				name: 'nemo_cod',
				    x: 10, y: 160, width: 150,
				    readOnly: true
				},{
    				name: 'nemo_desc',
				    x: 165, y: 160, width: 600,
				    readOnly: true
				},{
					fieldLabel: 'Rem. Neto',
    				name: 'plani_traba_neto',
				    x: 10, y: 190, width: 190,
				    align: 'right',
				    format: '0.00'
				},{
					fieldLabel: 'Dias Lab.',
					labelWidth: 60,
    				name: 'plani_traba_diaslab',
				    x: 230, y: 190, width: 120
				},{
					fieldLabel: 'Cargo',
					labelWidth: 50,
    				name: 'plani_traba_cargo',
				    x: 360, y: 190, width: 250
				},{
					fieldLabel: 'Observacion',
    				name: 'plani_glosa',
				    x: 10, y: 220, width: 600
				}]
			},{
				xtype: 'panel',
				layout: 'border',
				region: 'center',
				split: true,
				items: [{
					xtype: 'grid',
					title: 'Ingresos',
					id: 'crls_plani_traba_ingreso_grid',
					region: 'west',
					width: 300,
					split: true,
					emptyText: 'No tiene ingresos registrados',
					store: crls.plani_traba_ingreso_store,
					sortableColumns: false,
					enableColumnHide: false,
					features: [{
				    	ftype: 'summary'
				    }],
					columns:[
						{text:'Concepto', dataIndex:'tipo_ingreso_abrev', width: 200},
						{
							xtype: 'numbercolumn', text:'Monto', dataIndex:'plani_traba_monto', width: 70, align: 'right', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
				    	}
					]
				},{
					xtype: 'grid',
					title: 'Descuentos',
					id: 'crls_plani_traba_descuento_grid',
					region: 'center',
					emptyText: 'No tiene descuentos registrados',
					store: crls.plani_traba_descuento_store,
					sortableColumns: false,
					enableColumnHide: false,
					features: [{
				    	ftype: 'summary'
				    }],
					columns:[
						{text:'Concepto', dataIndex:'tipo_descuento_abrev', width: 200},
						{
							xtype: 'numbercolumn', text:'Monto', dataIndex:'plani_traba_monto', width: 70, align: 'right', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
				    	}
					]
				},{
					xtype: 'grid',
					title: 'Aportes',
					id: 'crls_plani_traba_aporte_grid',
					region: 'east',
					width: 300,
					split: true,
					emptyText: 'No tiene aportes registrados',
					store: crls.plani_traba_aporte_store,
					sortableColumns: false,
					enableColumnHide: false,
					features: [{
				    	ftype: 'summary'
				    }],
					columns:[
						{text:'Concepto', dataIndex:'tipo_aporte_abrev', width: 200},
						{
							xtype: 'numbercolumn', text:'Monto', dataIndex:'plani_traba_monto', width: 70, align: 'right', format:'0.00', 
							summaryType: 'sum',
							summaryRenderer: function(value, summaryData, dataIndex) {
					        	return '<b>'+Ext.util.Format.number(value, '0.00')+'</b>';
					        }
				    	}
					]
				}]
			}],
			buttons:[{
				text: 'Salir', handler: function() {
					win = Ext.getCmp('crls_plani_traba_window');
					win.close();
				}
			}],
			listeners: {
				show: function () {
					
					Ext.getCmp('crls_plani_traba_form').loadRecord(record);

					crls.plani_traba_ingreso_store.reload({
						params: {
							plani_anio: record.get('plani_anio'),
							plani_cod: record.get('plani_cod'),
		    				traba_cod: record.get('traba_cod')
		    			}
					});
					crls.plani_traba_descuento_store.reload({
						params: {
							plani_anio: record.get('plani_anio'),
							plani_cod: record.get('plani_cod'),
		    				traba_cod: record.get('traba_cod')
		    			}
					});
					crls.plani_traba_aporte_store.reload({
						params: {
							plani_anio: record.get('plani_anio'),
							plani_cod: record.get('plani_cod'),
		    				traba_cod: record.get('traba_cod')
		    			}
					});
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>