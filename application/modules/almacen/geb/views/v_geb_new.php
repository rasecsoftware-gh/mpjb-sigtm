<script>
	geb.window_new = function() {
		var w_config = {
			title:'Nueva Guia', 
			modal: true,
			width: 800,
			height: 320, 
			id:'geb_window_new',
			layout: 'border',
			items:[{
				xtype:'form', 
				bodyPadding: 10,
				url:'geb/Add',
				region: 'center',
				layout: 'absolute',
				id: 'geb_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 90
				},
				items:[{
					fieldLabel: 'Año:',
					xtype: 'displayfield',
    				name: 'geb_anio',
    				x: 10, y: 10, width: 180
				},{
					fieldLabel: 'Numero:',
					xtype: 'textfield',
    				name: 'geb_numero',
    				x: 10, y: 40, width: 200, disabled: true
				},{
					fieldLabel: 'Centro de Costo:',
					id: 'geb_form_nemo_anio',
    				name: 'nemo_anio',
    				x: 10, y: 70, width: 140, readOnly: true
				},{
					id: 'geb_form_nemo_cod',
    				name: 'nemo_cod',
    				x: 155, y: 70, width: 40, readOnly: true
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_nemo_secfun',
    				name: 'nemo_secfun'
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_nemo_meta',
    				name: 'nemo_meta'
				},{
					id: 'geb_form_nemo_desc',
    				name: 'nemo_desc',
				    x: 200, y: 70, width: 550,
				    triggers: {
    					search: {
				            cls: 'x-form-search-trigger',
				            handler: function() {
				                geb.window_nemo_search(
				                	Ext.getCmp('geb_form_nemo_desc').getValue(), 
				                	function (r) {
				                		Ext.getCmp('geb_form_nemo_anio').setValue(r.get('nemo_anio'));
						                Ext.getCmp('geb_form_nemo_cod').setValue(r.get('nemo_cod'));
										Ext.getCmp('geb_form_nemo_desc').setValue(r.get('nemo_desc'));
										Ext.getCmp('geb_form_nemo_secfun').setValue(r.get('nemo_secfun'));
										Ext.getCmp('geb_form_nemo_meta').setValue(r.get('nemo_meta'));
							    		Ext.getCmp('geb_form_cb_area').focus();
					                }
				                );
				            }
				        }
    				},
    				editable: false
				},{
					xtype: 'hiddenfield',
					id: 'geb_form_area_cod',
    				name: 'area_cod'
				},{
					fieldLabel:'Area destino:',
					xtype: 'combobox',
    				name: 'area_desc',
    				id: 'geb_form_cb_area',
    				displayField: 'area_desc',
    				valueField: 'area_desc',
    				queryMode: 'local',
    				anyMatch: true,
    				store: geb.st_sys2009_area,
				    x: 10, y: 100, width: 730,
				    forceSelection: false,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('geb_form_area_cod').setValue(record.get('area_cod'));
				    		Ext.getCmp('geb_form_geb_fecha').focus();
				    	}
				    }
				},{
					fieldLabel: 'Fecha:',
					xtype: 'datefield',
					id: 'geb_form_geb_fecha',
					name: 'geb_fecha',
					format: 'd/m/Y',
					x: 10, y: 130, width: 250
				},{
					fieldLabel:'Solicitante:',
					xtype: 'combobox',
					store: geb.st_solicitante,
					displayField: 'geb_solicitante',
    				valueField: 'geb_solicitante',
    				name: 'geb_solicitante',
    				id: 'geb_form_geb_solicitante',
    				queryMode: 'remote',
    				// Template for the dropdown menu.
				    // Note the use of the "x-list-plain" and "x-boundlist-item" class,
				    // this is required to make the items selectable.
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{geb_solicitante}</div></li>',
				        '</tpl></ul>'
				    ),
				    // template for the content inside text field
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{geb_solicitante}',
				        '</tpl>'
				    ),
				    x: 10, y: 160, width: 530,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('geb_form_geb_desc').focus();
				    	}
				    }
				},{
					fieldLabel:'Observacion:',
					id: 'geb_form_geb_desc',
					name:'geb_desc',
					x: 10, y: 190, width: 730
				}]
			}],
			buttons:[{
				text:'Guardar y continuar', 
				handler: function () {
					frm = Ext.getCmp('geb_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('geb_window_new').close();
								geb.window_edit(action.result.rowid);
								geb.st_grid_main.reload();	
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
					win = Ext.getCmp('geb_window_new');
					win.close();
				}
			}],
			listeners: {
				show: function () {
					Ext.create("Ext.data.Store", {
						proxy: {
							type:'ajax',
							url:'geb/getNewRow',
							reader:{
								type:'json',
								rootProperty:'data'
							}
						},
						autoLoad: true,
						listeners: {
							load: function (sender, records, successful, eOpts) {
								if (successful) {
									frm = Ext.getCmp('geb_form');
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