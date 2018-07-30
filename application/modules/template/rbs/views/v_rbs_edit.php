<script>
	rbs.window_edit = function(id) {
		var w_config = {
			title:'Modificar Bien', 
			modal: true,
			width: 800,
			height: 280, 
			id:'rbs_window_new',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: 10,
				url:'rbs/Update',
				region: 'center',
				layout: 'absolute',
				id: 'rbs_form',
				defaultType:'textfield',
				defaults: {
					labelWidth: 100
				},
				items:[{
					xtype: 'hidden',
					name: 'oc_det_id',
					id: 'rbs_form_oc_det_id'
				},{
					fieldLabel:'Bien:',
    				name: 'bs_cod',
    				id: 'rbs_form_bs_cod',
    				readOnly: true,
				    x: 10, y: 10, width: 200
				},{
    				name: 'bs_desc',
    				id: 'rbs_form_bs_desc',
				    x: 215, y: 10, width: 550,
				    triggers: {
    					search: {
				            cls: 'x-form-search-trigger',
				            handler: function() {
				                rbs.window_bien_search(
				                	Ext.getCmp('rbs_form_bs_desc').getValue(), 
				                	function (r) {
										Ext.getCmp('rbs_form_bs_cod').setValue(r.get('bs_cod'));
										Ext.getCmp('rbs_form_bs_desc').setValue(r.get('bs_desc'));
							    		Ext.getCmp('rbs_form_bs_unimed').setValue(r.get('bs_unimed'));
					                }
				                );
				            }
				        }
    				},
    				editable: false
				},{
					fieldLabel:'Unidad Med.:',
					xtype: 'textfield',
    				name: 'bs_unimed',
    				id: 'rbs_form_bs_unimed',
				    x: 10, y: 40, width: 250,
				    readOnly: true
				},{
					fieldLabel: 'Caract./Observ.:',
					xtype: 'textfield',
    				name: 'oc_det_obs',
    				id: 'rbs_form_oc_det_obs',
				    x: 10, y: 70, width: 750
				},{
					fieldLabel: 'Cantidad / Saldo:',
					xtype: 'numberfield',
					id: 'rbs_form_oc_det_cantidad',
					name: 'oc_det_cantidad',
					x: 10, y: 100, width: 220
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function () {
					frm = Ext.getCmp('rbs_form');
					frm.submit({
						success: function(form, action) {
							if (action.result.success) {
								Ext.getCmp('rbs_window_new').close();
								rbs.reload_list();
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure: function(form, action) {
							Ext.Msg.alert('Error', action.result.msg);
						}
					});
				}
			},{
				text: 'Cancelar', handler: function() {
					win = Ext.getCmp('rbs_window_new');
					win.close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'rbs/getRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_config);
						frm = Ext.getCmp('rbs_form');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>