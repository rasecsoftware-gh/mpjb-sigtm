<script>
	nea.window_new = function() {
		var w_config = {
			title:'Nueva NEA', 
			modal: true,
			width: 800,
			height: 320, 
			id:'nea_window_new',
			layout: 'border',
			items:[{
				xtype:'form', 
				bodyPadding: 10,
				url:'nea/Add',
				region: 'center',
				layout: 'absolute',
				id: 'nea_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 90
				},
				items:[{
					fieldLabel: 'Año y Numero:',
					xtype: 'displayfield',
    				name: 'nea_anio',
    				x: 10, y: 10, width: 140
				},{
					xtype: 'textfield',
    				name: 'nea_numero',
    				x: 160, y: 10, width: 70, disabled: true
				},{
					fieldLabel:'Tipo:',
					xtype: 'combobox',
    				name: 'tipo_nea_id',
    				id: 'nea_tipo_nea_combobox',
    				displayField: 'tipo_nea_desc',
    				valueField: 'tipo_nea_id',
    				queryMode: 'local',
    				store: nea.tipo_nea_store,
				    x: 10, y: 40, width: 400,
				    forceSelection: true,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('nea_procedencia_field').focus();
				    	}
				    }
				},{
					fieldLabel:'Procedencia:',
					xtype: 'combobox',
					store: nea.procedencia_store,
					displayField: 'nea_procedencia',
    				valueField: 'nea_procedencia',
    				name: 'nea_procedencia',
    				id: 'nea_procedencia_field',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{nea_procedencia}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{nea_procedencia}',
				        '</tpl>'
				    ),
				    x: 10, y: 70, width: 630,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('nea_nemo_desc_field').focus();
				    	}
				    }
				},{
					fieldLabel: 'Centro de costo:',
					id: 'nea_nemo_anio_field',
    				name: 'nemo_anio',
    				x: 10, y: 100, width: 140, readOnly: true
				},{
					id: 'nea_nemo_cod_field',
    				name: 'nemo_cod',
    				x: 155, y: 100, width: 40, readOnly: true
				},{
					xtype: 'hiddenfield',
					id: 'nea_nemo_secfun_field',
    				name: 'nemo_secfun'
				},{
					xtype: 'hiddenfield',
					id: 'nea_nemo_meta_field',
    				name: 'nemo_meta'
				},{
					id: 'nea_nemo_desc_field',
    				name: 'nemo_desc',
				    x: 200, y: 100, width: 550,
				    triggers: {
    					search: {
				            cls: 'x-form-search-trigger',
				            handler: function() {
				                nea.window_nemo_search(
				                	Ext.getCmp('nea_nemo_desc_field').getValue(), 
				                	function (r) {
				                		if (r == null) {
				                			Ext.getCmp('nea_nemo_anio_field').setValue('');
							                Ext.getCmp('nea_nemo_cod_field').setValue('');
							                Ext.getCmp('nea_nemo_secfun_field').setValue('');
							                Ext.getCmp('nea_nemo_meta_field').setValue('');
							                Ext.getCmp('nea_nemo_desc_field').setValue('');
				                		} else {
				                			Ext.getCmp('nea_nemo_anio_field').setValue(r.get('nemo_anio'));
							                Ext.getCmp('nea_nemo_cod_field').setValue(r.get('nemo_cod'));
											Ext.getCmp('nea_nemo_desc_field').setValue(r.get('nemo_desc'));
											Ext.getCmp('nea_nemo_secfun_field').setValue(r.get('nemo_secfun'));
											Ext.getCmp('nea_nemo_meta_field').setValue(r.get('nemo_meta'));
				                		}
							    		Ext.getCmp('nea_fecha_field').focus();
					                }
				                );
				            }
				        },
				        clear: {
				        	cls: 'x-form-clear-trigger',
				            weight: 2, 
				            handler: function() {
				            	Ext.getCmp('nea_nemo_anio_field').setValue('');
				                Ext.getCmp('nea_nemo_cod_field').setValue('');
				                Ext.getCmp('nea_nemo_secfun_field').setValue('');
				                Ext.getCmp('nea_nemo_meta_field').setValue('');
				                Ext.getCmp('nea_nemo_desc_field').setValue('');
				            }
				        }
    				},
    				editable: false
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'nea_fecha_field',
					name: 'nea_fecha',
					format: 'd/m/Y',
					x: 10, y: 130, width: 250
				},{
					fieldLabel:'Observacion:',
					id: 'nea_observacion_field',
					name: 'nea_observacion',
					x: 10, y: 160, width: 730
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('nea_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('nea_window_new').close();
								nea.window_edit(action.result.rowid);
								nea.main_store.reload();	
							} else {
								Ext.Msg.alert('Error',action.result.msg);
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
				text:'Cancelar', handler:function() {
					win = Ext.getCmp('nea_window_new');
					win.close();
				}
			}],
			listeners: {
				show: function () {
					Ext.create("Ext.data.Store", {
						proxy: {
							type: 'ajax',
							url: 'nea/getNewRow',
							reader:{
								type: 'json',
								rootProperty: 'data'
							}
						},
						autoLoad: true,
						listeners: {
							load: function (sender, records, successful, eOpts) {
								if (successful) {
									frm = Ext.getCmp('nea_form');
									frm.loadRecord(sender.getAt(0));
								}
							}
						}
					});
				}
			}
		};
		var w = Ext.create('Ext.window.Window', w_config);
		w.show();
	};
</script>