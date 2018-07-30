<script>
	np.window_detail_edit = function(id) {

		var w_detail_config = {
			title:'Detalle de Nota de Pedido', 
			modal: true,
			width: 800,
			height: 300, 
			id:'np_window_detail',
			layout: 'border',
			items:[{
				xtype:'form', bodyPadding: '0px 10px 0 10px',
				url:'np/updateDetail',
				region: 'center',
				layout: 'absolute',
				id: 'np_form_detail',
				height: 160,
				defaultType:'textfield',
				items:[{
					xtype: 'hiddenfield', name: 'id_det_np'
				},{
					xtype: 'hiddenfield', name: 'id_np'
				},{
					fieldLabel:'Bien/Servicio:',
    				name: 'desc_bs',
    				x: 10, y: 10, width: 720, readOnly: true
				},{
					fieldLabel:'Unidad Med.:',
    				name: 'desc_unimed',
    				id: 'np_form_detail_desc_unimed',
				    x: 10, y: 40, width: 220, readOnly: true
				},{
					fieldLabel: 'Cantidad:',
					xtype: 'numberfield',
					id: 'np_form_detail_cant_det_np',
					name: 'cant_det_np',
					x: 10, y: 70, width: 220
				},{
					fieldLabel: 'Precio Unit.:',
					xtype: 'numberfield',
					id: 'np_form_detail_preuni_det_np',
					name: 'preuni_det_np',
					x: 10, y: 100, width: 220
				},{
					fieldLabel: 'Total:',
					xtype: 'numberfield',
					id: 'np_form_detail_tot_det_np',
					name: 'tot_det_np',
					x: 10, y: 130, width: 220, readOnly: true
				},{
					fieldLabel:'Observacion:',
					name:'obs_det_np',
					x: 10, y: 160, width: 720
				}]
			}],
			buttons:[{
				text:'Guardar', 
				handler: function (){
					frm = Ext.getCmp('np_form_detail');
					frm.submit({
						params: {
							opt: 'opt'
						},
						success: function(form, action){
							if (action.result.success) {
								Ext.getCmp('np_window_detail').close();
								Ext.getCmp('np_form_grid_detail').store.reload();	
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
					Ext.getCmp('np_window_detail').close();
				}
			}]
		};
		var store = Ext.create("Ext.data.Store", {
			proxy: {
				type:'ajax',
				url:'np/getDetailRow/'+id,
				reader:{
					type:'json',
					rootProperty:'data'
				}
			},
			autoLoad: true,
			listeners: {
				load: function (sender, records, successful, eOpts) {
					if (successful) {
						var w = Ext.create('Ext.window.Window', w_detail_config);
						frm = Ext.getCmp('np_form_detail');
						frm.loadRecord(sender.getAt(0));
						w.show();
					}
				}
			}
		});
	}
</script>