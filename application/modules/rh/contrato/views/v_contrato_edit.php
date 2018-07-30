<script>
	contrato.edit_window = function(id) {
		var w_config = {
			title:'Contrato', 
			modal: true,
			width: 800,
			height: 700, 
			id:'contrato_edit_window',
			layout: 'border',
			items:[{
				xtype: 'form', 
				bodyStyle: {
					background: 'white',
					padding: '10px'
				},
				url: 'contrato/Update',
				region: 'center',
				layout: 'border',
				id: 'contrato_form',
				defaults: {
					labelWidth: 90
				},
				items:[{
					xtype: 'panel',
					region: 'north',
					layout: 'border',
					height: 100,
					bodyStyle: 'background-color: white;',
					margin: '0 0 5px 0',
					items: [{
						xtype: 'panel',
						region: 'west',
						layout: 'absolute',
						width: 325,
						defaults: {
							labelWidth: 80
						},
						items: [{
							xtype: 'hiddenfield',
							name: 'contrato_id'
						},{
							xtype: 'hiddenfield',
							name: 'contrato_id_parent',
						},{
							//fieldLabel: 'Tipo:',
							xtype: 'combobox',
		    				name: 'tipo_contrato_id',
		    				id: 'contrato_tipo_contrato_combobox',
		    				displayField: 'tipo_contrato_desc',
		    				valueField: 'tipo_contrato_id',
		    				queryMode: 'local',
		    				store: contrato.tipo_contrato_store,
		    				editable: false,
						    x: 10, y: 0, width: 305,
						    forceSelection: true,
						    fieldStyle: 'font-weight: bold;',
						    listeners: {
						    	select: function (combo, record, eOpts) {
						    		contrato.plantilla_store.reload({
						    			params: {
						    				tipo_contrato_id: record.get('tipo_contrato_id'),
						    				plantilla_estado: '%'
						    			}
						    		});
						    		Ext.getCmp('contrato_form_contrato_traba_apenom').focus();
						    	},
						    	change: function (ths, newValue, oldValue, eOpts) {
						    		Ext.select('.c-ctct').hide();
						    		Ext.select('.c-cas').hide();
						    		Ext.select('.c-adenda').hide();
						    		switch (newValue) {
						    			case '01': //CTCT
						    				Ext.select('.c-ctct').show();
						    				break;
						    			case '02': //CAS
						    				Ext.select('.c-cas').show();
						    				break;
						    			case '03': //CAS
						    				Ext.select('.c-adenda').show();
						    				break;
						    		}
						    	}
						    }
						},{
		    				xtype: 'combobox',
		    				id: 'contrato_form_plantilla_id_field',
		    				name: 'plantilla_id',
		    				fieldLabel: 'Plantilla',
		    				fieldStyle: 'color: gray',
		    				labelStyle: 'color: gray',
		    				labelWidth: 45,
		    				displayField: 'plantilla_desc',
		    				valueField: 'plantilla_id',
		    				store: contrato.plantilla_store,
		    				queryMode: 'local',
		    				matchFieldWidth: false,
		    				x: 115, y: 30, width: 200,
		    				editable: false,
		    				listeners: {
		    					select: function(combo, record, eOpts ) {						    		
						    	}
		    				}
						},{
							fieldLabel: 'Año y Numero:',
							xtype: 'displayfield',
		    				name: 'contrato_anio',
		    				x: 10, y: 60, width: 130
						},{
							xtype: 'textfield',
		    				name: 'contrato_numero',
		    				fieldStyle: 'font-weight: bold; text-align: center;',
		    				x: 150, y: 60, width: 70, disabled: false
						}]
					},{
						xtype: 'form',
						region: 'center',
						layout:  'absolute',
						style: {
							borderLeft: '1px solid silver;'
						},
						cls: 'c-adenda',
						defaults: {
							labelStyle: 'color: gray;'
						},
						scrollable: 'y',
						items: [{
							xtype: 'displayfield',
		    				name: 'p_contrato_tipo_numero_anio',
		    				fieldStyle: 'font-weight: bold',
		    				fieldLabel: 'Para',
		    				labelWidth: 30,
		    				labelStyle: 'color: #4285f4;',
		    				value: 'CONTRATO ADMINISTRATIVO DE SERVICIO CAS 34 - 2016',
		    				x: 10, y: 0, width: 370
						},{
							fieldLabel: 'Cargo',
		    				xtype: 'displayfield',
		    				name: 'p_contrato_cargo',
		    				value: 'ESPECIALISTA EN SISTEMAS Y SERVIDORES',
		    				labelWidth: 30,
		    				x: 10, y: 15, width: 400
						},{
							fieldLabel: 'Inicio',
		    				xtype: 'displayfield',
		    				name: 'p_contrato_fecha_inicio',
		    				value: '01/09/2016',
		    				labelWidth: 30,
		    				x: 10, y: 30, width: 100
						},{
							fieldLabel: 'Termino',
		    				xtype: 'displayfield',
		    				name: 'p_contrato_fecha_fin',
		    				value: '31/09/2016',
		    				labelWidth: 40,
		    				x: 120, y: 30, width: 110
						},{
							fieldLabel: 'Emitido el',
		    				xtype: 'displayfield',
		    				name: 'p_contrato_fecha_emision',
		    				value: '28/08/2016',
		    				labelWidth: 50,
		    				x: 250, y: 30, width: 130
						},{
		    				xtype: 'displayfield',
		    				name: 'p_contrato_dependencia',
		    				value: 'ACTIVIDAD MANTENIMIENTO DE LA INFRAESTRUCTURA MUNICIPAL: PALACIO MUNICIPAL Y CASONA TRADICIONAL, SECTOR VILLA LOCUMBA, PROVINCIA JORGE BASADRE',
		    				x: 10, y: 45, width: 430
						}]
					}]
				},{
					xtype: 'panel',
					region: 'north',
					layout:  'absolute',
					height: 75,
					style: {
						borderTop: '1px solid silver;'
					},
					tbar: [{
						xtype: 'label',
						text: 'Entidad y Representante:',
						cls: 'app-section-title'
					}],
					defaults: {
						labelStyle: 'color: gray;'
					},
					items: [{
						xtype: 'displayfield',
	    				name: 'entidad_nombre',
	    				fieldStyle: 'font-weight: bold',
	    				x: 10, y: 0, width: 400
					},{
						fieldLabel: 'RUC:',
	    				xtype: 'displayfield',
	    				name: 'entidad_ruc',
	    				labelWidth: 35,
	    				x: 10, y: 20, width: 100
					},{
						fieldLabel: 'Direccion:',
	    				xtype: 'displayfield',
	    				name: 'entidad_direccion',
	    				labelWidth: 60,
	    				x: 130, y: 20, width: 300
					},{
	    				xtype: 'combobox',
	    				id: 'contrato_form_repre_contrato_id_field',
	    				name: 'repre_contrato_id',
	    				fieldStyle: 'font-weight: bold',
	    				displayField: 'repre_contrato_desc',
	    				valueField: 'repre_contrato_id',
	    				store: contrato.repre_contrato_store,
	    				queryMode: 'local',
	    				matchFieldWidth: false,
	    				x: 444, y: 0, width: 300,
	    				listeners: {
	    					select: function(combo, record, eOpts ) {
					    		Ext.getCmp('contrato_form_repre_contrato_cargo_field').setValue(record.get('repre_contrato_cargo')+'.');
					    	}
	    				}
					},{
	    				xtype: 'displayfield',
	    				id: 'contrato_form_repre_contrato_cargo_field',
	    				name: 'repre_contrato_cargo',
	    				x: 450, y: 20, width: 300
					}]
				},{
					xtype: 'panel',
					region: 'north',
					layout:  'absolute',
					height: 85,
					style: {
						borderTop: '1px solid silver;',
					},
					tbar: [{
						xtype: 'label',
						text: 'Trabajador:',
						cls: 'app-section-title'
					}],
					defaults: {
						labelStyle: 'color: gray;'
					},
					items: [{
						id: 'contrato_form_contrato_traba_cod', xtype: 'hiddenfield', name: 'contrato_traba_cod'
					},{
						id: 'contrato_form_contrato_traba_dni', xtype: 'hiddenfield', name: 'contrato_traba_dni'
					},{
						id: 'contrato_form_contrato_traba_direccion', xtype: 'hiddenfield', name: 'contrato_traba_direccion'
					},{
						id: 'contrato_form_contrato_traba_apenom',
						xtype: 'textfield',
	    				name: 'contrato_traba_apenom',
	    				fieldStyle: 'font-weight: bold',
	    				x: 10, y: 0, width: 300, 
	    				editable: false,
	    				value: null,
	    				emptyText: 'busque un trabajador...',
	    				triggers: {
	    					search: {
					            cls: 'x-form-search-trigger',
					            handler: function() {
					                contrato.trabajador_search_window(
					                	function (r) {
					                		console.info(r);
							                Ext.getCmp('contrato_form_contrato_traba_cod').setValue(r.get('traba_cod'));
							                Ext.getCmp('contrato_form_contrato_traba_apenom').setValue(r.get('traba_apenom'));
							                Ext.getCmp('contrato_form_contrato_traba_dni').setValue(r.get('traba_dni'));
											Ext.getCmp('contrato_form_contrato_traba_ruc').setValue(r.get('traba_ruc'));
								    		Ext.getCmp('contrato_form_contrato_traba_direccion').setValue(r.get('traba_direccion'));
								    		// displayfield's
											Ext.getCmp('contrato_form_traba_dni').setValue(r.get('traba_dni'));
								    		
						                }
					                );
					            }
					        }
	    				}
					},{
						fieldLabel: 'DNI:',
	    				xtype: 'displayfield',
	    				id: 'contrato_form_traba_dni',
	    				name: 'traba_dni',
	    				value: '',
	    				labelWidth: 25,
	    				x: 330, y: 0, width: 80
					},{
						fieldLabel: 'RUC:',
	    				xtype: 'textfield',
	    				id: 'contrato_form_contrato_traba_ruc',
	    				name: 'contrato_traba_ruc',
	    				value: '',
	    				labelWidth: 25,
	    				x: 430, y: 0, width: 150
					},{
						fieldLabel: 'Direccion:',
	    				xtype: 'displayfield',
	    				id: 'contrato_form_traba_direccion',
	    				name: 'contrato_traba_direccion',
	    				value: '', //URBANIZACION LAS BEGONIAS ESTERILES DE CONOSUR MANZANA F LOTE 45',
	    				labelWidth: 60,
	    				x: 10, y: 30, width: 500
					}]
				},,{
					xtype: 'panel',
					region: 'center',
					layout:  'form',
					style: {
						borderTop: '1px solid silver;',
					},
					bodyPadding: '0 0 0 5px',
					tbar: [{
						xtype: 'label',
						text: 'Clausulas del contrato:',
						cls: 'app-section-title'
					}],
					defaults: {
						labelStyle: 'color: gray;'
					},
					items: [{
						fieldLabel: 'Cargo',
	    				xtype: 'textfield',
	    				name: 'contrato_cargo',
	    				value: '', //Especialista en Sistemas y Servidores',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						fieldLabel: 'Dependencia',
	    				xtype: 'textfield',
	    				name: 'contrato_dependencia',
	    				value: '', //GERENCIA DE ADMINISTRACION',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						fieldLabel: 'Area/lugar prestacion',
	    				xtype: 'textfield',
	    				name: 'contrato_area',
	    				value: '', // 'Tecnologias de la Informacion y Comunicaciones',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						fieldLabel: 'Convocatoria',
	    				xtype: 'textfield',
	    				name: 'contrato_convocatoria',
	    				value: '', // Convocatoria CAS 034-2016',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						fieldLabel: 'Nivel Academico',
	    				xtype: 'textfield',
	    				name: 'contrato_nivel_ocupacional',
	    				value: '', //PROFESIONAL',
	    				cls: 'c-ctct',
	    				hidden: true
					},{
						fieldLabel: 'Categoria',
	    				xtype: 'textfield',
	    				name: 'contrato_categoria',
	    				value: '', //SP-B',
	    				cls: 'c-ctct',
	    				hidden: true
					},{
						fieldLabel: 'Tipo de Adenda',
	    				xtype: 'combobox',
	    				name: 'tipo_adenda_id',
	    				cls: 'c-adenda',
	    				displayField: 'tipo_adenda_desc',
	    				valueField: 'tipo_adenda_id',
	    				queryMode: 'local',
	    				store: contrato.tipo_adenda_store,
	    				hidden: true
					},{
						fieldLabel: 'Documento Ref.',
	    				xtype: 'textfield',
	    				name: 'contrato_docref',
	    				value: '', //Informe 034-2016-TIC',
	    				cls: 'c-adenda',
	    				hidden: true
					},{
						fieldLabel: 'Monto (Soles)',
	    				xtype: 'textfield',
	    				name: 'contrato_monto',
	    				value: '', //2750.00',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						fieldLabel: 'Son',
	    				xtype: 'displayfield',
	    				name: 'contrato_monto_letras',
	    				value: '', //DOS MIL SETECIENTOS CINCUENTA CON 00/100 SOLES',
	    				cls: 'c-cas',
	    				hidden: true
					},{
						id: 'contrato_form_contrato_fecha_inicio_field',
						fieldLabel: 'Fecha Inicio',
	    				xtype: 'datefield',
	    				name: 'contrato_fecha_inicio',
	    				format: 'd/m/Y',
	    				cls: 'c-cas c-ctct c-adenda',
	    				hidden: true
					},{
						id: 'contrato_form_contrato_fecha_fin_field',
						fieldLabel: 'Fecha Fin',
	    				xtype: 'datefield',
	    				name: 'contrato_fecha_fin',
	    				format: 'd/m/Y',
	    				cls: 'c-cas c-ctct c-adenda',
	    				hidden: true
					},{
						id: 'contrato_form_contrato_tiempo_field',
						fieldLabel: 'Tiempo',
	    				xtype: 'textfield',
	    				name: 'contrato_tiempo',
	    				value: '', //2 meses',
	    				cls: 'c-adenda',
	    				hidden: true
					},{
						id: 'contrato_form_contrato_fecha_emision_field',
						fieldLabel: 'Fecha Emision',
	    				xtype: 'datefield',
	    				name: 'contrato_fecha_emision',
	    				format: 'd/m/Y',
	    				cls: 'c-cas c-ctct c-adenda',
	    				hidden: true
					}]
				}]
			}],
			buttons:[{
				text:'Guardar', 
				hidden: !<?php echo sys_session_hasRoleToString('rh.contrato.update'); ?>,
				handler: function () {
					var frm = Ext.getCmp('contrato_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('contrato_edit_window').close();
								//contrato.edit_window(action.result.rowid);
								contrato.main_store.reload();	
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Guardar', action.result.msg, function () {
								sys_focus(action.result.target_id);
							});
						}
					});
				}
			},{
				text:'Cancelar', handler: function() {
					win = Ext.getCmp('contrato_edit_window');
					win.close();
				}
			}],
			listeners: {
				show: function () {
					var w = this;
					w.mask('cargando');
					Ext.create("Ext.data.Store", {
						proxy: {
							type: 'ajax',
							url: 'contrato/getRow/'+id,
							reader:{
								type: 'json',
								rootProperty: 'data',
								messageProperty: 'msg'
							}
						},
						autoLoad: true,
						listeners: {
							load: function (sender, records, successful, eOpts) {
								if (successful) {
									var record = sender.getAt(0);
									contrato.repre_contrato_store.reload();
									// load plantilla
									contrato.plantilla_store.reload({
						    			params: {
						    				tipo_contrato_id: record.get('tipo_contrato_id'),
						    				plantilla_estado: '%'
						    			}
						    		});
									sys_storeLoadMonitor([contrato.tipo_contrato_store, contrato.plantilla_store, contrato.repre_contrato_store], function () {
										contrato.tipo_contrato_store.clearFilter();
										contrato.tipo_contrato_store.filterBy(function(r) {
											if (record.get('contrato_id_parent') > 0) { // adenda
												if (r.get('tipo_contrato_id') == '03') {
													return true;
												}
											} else {
												if (r.get('tipo_contrato_id') != '03') {
													return true;
												}
											}
											return false;
										});

										var frm = Ext.getCmp('contrato_form');
										frm.loadRecord(record);
										// cargamos los displayfield's
										Ext.getCmp('contrato_form_traba_dni').setValue(record.get('contrato_traba_dni'));
										// disable's or readonly
										if (record.get('contrato_id_parent') > 0) {
											Ext.getCmp('contrato_form_contrato_traba_ruc').setReadOnly(true);
											Ext.getCmp('contrato_form_contrato_traba_direccion').setReadOnly(true);
										}
										w.unmask();
									});
								} else {
									Ext.Msg.alert('Contrato', eOpts.getResultSet().getMessage());
								}
							}
						}
					});
				}
			}
		};
		Ext.create('Ext.window.Window', w_config).show();
	};
</script>