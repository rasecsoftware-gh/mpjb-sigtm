<script>
	req.window_detail_edit = function(id) {
		req_bs_query_active = false;
		var w_detail_config = {
			title:'Nuevo Detalle Requerimiento', 
			modal: true,
			width: 800,
			height: 320, 
			id:'req_window_detail',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'req/updateDetail',
				region: 'center',
				layout: 'absolute',
				id: 'req_form_detail',
				height: 160,
				defaults: {
					labelWidth: 100
				},
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_det_requer'
				},{
					xtype: 'hiddenfield', name: 'id_requer'
				},{
					xtype: 'hiddenfield', 
					id: 'req_form_detail_id_bs',
					name: 'id_bs'
				},{
					fieldLabel:'Bien/Servicio:',
    				name: 'cod_bs',
    				id: 'req_form_detail_cod_bs',
    				readOnly: true,
				    x: 10, y: 10, width: 200
				},{
    				name: 'desc_bs',
    				id: 'req_form_detail_desc_bs',
				    x: 215, y: 10, width: 550,
				    triggers: {
    					search: {
				            cls: 'x-form-search-trigger',
				            handler: function() {
				                req.window_bs_search(
				                	Ext.getCmp('req_form_detail_desc_bs').getValue(), 
				                	function (r) {
						                Ext.getCmp('req_form_detail_id_bs').setValue(r.get('id_bs'));
										Ext.getCmp('req_form_detail_cod_bs').setValue(r.get('cod_bs'));
										Ext.getCmp('req_form_detail_desc_bs').setValue(r.get('desc_bs'));
							    		Ext.getCmp('req_form_detail_id_unimed').setValue(r.get('id_unimed'));
							    		Ext.getCmp('req_form_detail_desc_unimed').setValue(r.get('desc_unimed'));
							    		Ext.getCmp('req_form_detail_cant_det_requer').focus();
					                }
				                );
				            }
				        }
    				},
    				editable: false
				},{
					xtype: 'hiddenfield', id: 'req_form_detail_id_unimed', name: 'id_unimed'
				},{
					fieldLabel:'Unidad Med.:',
					xtype: 'textfield',
    				name: 'desc_unimed',
    				id: 'req_form_detail_desc_unimed',
				    x: 10, y: 40, width: 350,
				    readOnly: true
				},{
					fieldLabel: 'Cantidad:',
					xtype: 'numberfield',
					id: 'req_form_detail_cant_det_requer',
					name: 'cant_det_requer',
					x: 10, y: 70, width: 220
				},{
					fieldLabel: 'Valor Ref.:',
					xtype: 'numberfield',
					id: 'req_form_detail_preuni_det_requer',
					name: 'preuni_det_requer',
					x: 10, y: 100, width: 220
				},{
					fieldLabel: 'Total:',
					xtype: 'numberfield',
					id: 'req_form_detail_tot_det_requer',
					name: 'tot_det_requer',
					x: 10, y: 130, width: 220
				},{
					fieldLabel:'Clasif. Presupuestal:',
					xtype: 'combobox',
					store: req.st_clapre,
					displayField: 'desc_clapre',
    				valueField: 'id_clapre',
    				name: 'id_clapre',
    				id: 'req_form_detail_cb_clapre',
    				queryMode: 'local',
    				forceSelection: true,
    				editable: false,
				    tpl: Ext.create('Ext.XTemplate',
				        '<ul class="x-list-plain"><tpl for=".">',
				            '<li role="option" class="x-boundlist-item"><div>{cod_clapre} - {desc_clapre}</div></li>',
				        '</tpl></ul>'
				    ),
				    displayTpl: Ext.create('Ext.XTemplate',
				        '<tpl for=".">',
				            '{cod_clapre} - {desc_clapre}',
				        '</tpl>'
				    ),
				    x: 10, y: 160, width: 750,
				    listeners: {
				    	select: function ( combo, record, eOpts ) {
				    		Ext.getCmp('req_form_detail_obs_det_requer').focus();
				    	}
				    }
				},{
					fieldLabel:'Observacion:',
					name:'obs_det_requer',
					id: 'req_form_detail_obs_det_requer',
					x: 10, y: 190, width: 750
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('req_form_detail');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action){
							if (action.result.success) {
								Ext.getCmp('req_window_detail').close();
								Ext.getCmp('req_form_grid_detail').store.reload();	
							} else {
								Ext.Msg.alert('Error', action.result.msg);
							}
						},
						failure:function(form, action){
							Ext.Msg.alert('Error', action.result.msg);
						}
					});
				}
			},{
				text: 'Cancelar', 
				handler: function () {
					Ext.getCmp('req_window_detail').close();
				}
			}]
		};
		
		Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'req/getDetailRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						req.st_clapre_preloading = true;
						req.st_clapre.reload({
							params:{
								id_requer: sender.getAt(0).get('id_requer')
							}
						});

						var w = Ext.create('Ext.window.Window', w_detail_config);
						frm = Ext.getCmp('req_form_detail');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>