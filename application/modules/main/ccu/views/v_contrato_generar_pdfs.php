<script>
	contrato.generar_pdfs_window = function() {
		var store = Ext.create("Ext.data.Store", {
			proxy:{
				type: 'ajax',
				url: 'contrato/getListForGenPDF',
				reader: {
					type: 'json',
					rootProperty: 'data'
				}
			},
			autoLoad: false,
			listeners: {
				load: function (ths, records) {
					var total = Ext.getCmp('contrato_generar_pdfs_total_field');
					if (Ext.isDefined(total)) {
						total.setValue(records.length);
						Ext.getCmp('contrato_generar_pdfs_total_gen_field').setValue(records.length);
					}
				}
			}
		});
		var w_config = {
			id: 'contrato_generar_pdfs_window',
			title: 'Generar PDFs', 
			modal: true,
			width: 400,
			height: 370, 
			layout: 'border',
			items:[{
				xtype: 'form', 
				bodyPadding: 10,
				region: 'center',
				layout: 'form',
				id: 'contrato_generar_pdfs_form',
				defaultType: 'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hiddenfield',
					name: 'contrato_id'
				},{
					fieldLabel: 'Año',
					id: 'contrato_generar_pdfs_contrato_anio_field',
					xtype: 'combobox',
    				displayField: 'desc',
					valueField: 'id',
    				store: Ext.create('Ext.data.Store', {
						data: [
							{id: '2018', desc: '2018'},
							{id: '2017', desc: '2017'}
						]
					}),
    				listeners:{
    					select: function (combo, record, eOpts) {
    					}
    				}
				},{
					id: 'contrato_generar_pdfs_tipo_contrato_id_field',
					fieldLabel: 'Tipo Contrato',
					xtype: 'combobox',
    				displayField: 'tipo_contrato_desc',
    				valueField: 'tipo_contrato_id',
    				queryMode: 'local',
    				store: contrato.tipo_contrato_store,
    				editable: false,
				    forceSelection: true,
				    fieldStyle: 'font-weight: bold;'
				},{
					xtype: 'button',
    				text: 'Consultar',
    				handler: function () {
    					store.reload({
    						params: {
    							contrato_anio: Ext.getCmp('contrato_generar_pdfs_contrato_anio_field').getValue(),
    							tipo_contrato_id: Ext.getCmp('contrato_generar_pdfs_tipo_contrato_id_field').getValue()
    						}
    					});
    				}
				},{
					id: 'contrato_generar_pdfs_total_field',
					xtype: 'displayfield',
					fieldLabel: 'Total registros encontrados',
					value: '0',
				    fieldStyle: {
				    	fontWeight: 'bold'
				    }
				},{
					id: 'contrato_generar_pdfs_size_field',
					xtype: 'textfield',
					fieldLabel: 'Tamaño Lote Procesar',
					value: '10'
				},{
					id: 'contrato_generar_pdfs_total_gen_field',
					xtype: 'textfield',
					fieldLabel: 'Total a Procesar',
					value: '50'
				},{
					xtype: 'button',
    				text: 'Generar',
    				handler: function () {
    					if (store.getCount() > 0) {
    						Ext.Msg.show({
							    title: 'Generar PDFs',
							    message: 'Realmente desea generar los PDFs?',
							    buttons: Ext.Msg.YESNO,
							    icon: Ext.Msg.QUESTION,
							    fn: function(btn) {
							        if (btn === 'yes') {
							        	var size = parseInt(Ext.getCmp('contrato_generar_pdfs_size_field').getValue());
							        	var total = parseInt(Ext.getCmp('contrato_generar_pdfs_total_gen_field').getValue());
							        	var estado_field = Ext.getCmp('contrato_generar_pdfs_message_field');
							        	var generado_field = Ext.getCmp('contrato_generar_pdfs_generado_count_field');
							        	var error_field = Ext.getCmp('contrato_generar_pdfs_error_count_field');
							        	var generados = 0;
							        	var errores = 0;
							        	var iterator = 0;
							        	if (total <= store.getCount()) {
							        		if (size <= total) {
							        			function generar_lote() {
							        				estado_field.setValue('Generando');
							        				var request_list = [];
							        				var hasta = (iterator+size);
							        				//console.log('i:'+iterator);
							        				//console.log('h:'+hasta);
							        				for (var i=iterator; i<hasta; i++) {
								        				if (i < total) {
								        					var record = store.getAt(i);
								        					var r = Ext.Ajax.request({
																params:{
																	contrato_id: record.get('contrato_id')
																},
																url:'contrato/regenerarPDF',
																success: function (response, opts){
																	var result = Ext.decode(response.responseText);
																	if (result.success) {
																		generados++;
																		estado_field.setValue(estado_field.getValue()+'.');
																		generado_field.setValue(generados);
																	} else {
																		errores++;
																		estado_field.setValue(estado_field.getValue()+'.');
																		error_field.setValue(errores);
																	}
																},
																failure: function (response, opts){
																	errores++;
																	estado_field.setValue(estado.getValue()+'.');
																	error_field.setValue(errores);
																},
																timeout: 5*60*1000 // 5m
															});
															request_list.push(r);
															iterator++;
								        				} else {
								        					break;
								        				}
									        		}
									        		sys_storeLoadMonitor(request_list, function () {
									        			if (iterator<total) {
									        				generar_lote();	
									        			} else {
									        				estado_field.setValue('Terminado');
									        			}
									        		});
							        			};
							        			generar_lote();
							        		} else {
							        			Ext.Msg.alert('Error', 'El tamaño del lote no puede ser mayor al total a generar.');
							        		}
							        	} else {
							        		Ext.Msg.alert('Error', 'El total a generar no puede ser mayo al total de registros disponibles.');
							        	}
							        } else {
							            console.log('Cancel pressed');
							        } 
							    }
							});
    					}
    				}
				},{
					id: 'contrato_generar_pdfs_generado_count_field',
					xtype: 'displayfield',
					fieldLabel: 'Generados',
					value: '0'
				},{
					id: 'contrato_generar_pdfs_error_count_field',
					xtype: 'displayfield',
					fieldLabel: 'Error',
					value: '0'
				},{
					id: 'contrato_generar_pdfs_message_field',
					xtype: 'displayfield',
					fieldLabel: 'Estado',
					value: '...'
				}]
			}],
			listeners: {
				show: function () {					
					
				}
			}
		};

		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>