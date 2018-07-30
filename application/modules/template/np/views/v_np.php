<script type="text/javascript">
	np = {};
	// stores
	np.st_frente = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'np/getFrenteList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	np.st_comprador = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'np/getCompradorList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	np.st_proveedor = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'np/getProveedorList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false,
		listeners: {
			load: function () {
				var cbp = Ext.getCmp('np_form_rendicion_detail_cb_proveedor');
				if (Ext.isDefined(cbp)) {
					cbp.bindStore(np.st_proveedor);
				}
			}
		}
	});

	np.st_tipo_documento = Ext.create("Ext.data.Store", {
		proxy: {
			type:'ajax',
			url:'np/getTipoDocumentoList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true,
		listeners: {
			load: function (sender, records, successful, eOpts) {
				if (successful) {}
			}
		}
	});
	
	np.st_grid_main = Ext.create("Ext.data.Store",{
		proxy:{
			type: 'ajax',
			url: 'np/getList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true
	});
</script>

<!--  load components -->
<?php echo $this->load->view('v_np_list'); ?>
<?php echo $this->load->view('v_np_new'); ?>
<?php echo $this->load->view('v_np_edit'); ?>
<?php echo $this->load->view('v_np_detail_new'); ?>
<?php echo $this->load->view('v_np_detail_edit'); ?>
<?php echo $this->load->view('v_np_rendicion'); ?>
<?php echo $this->load->view('v_np_rendicion_new'); ?>
<?php echo $this->load->view('v_np_rendicion_edit'); ?>
<?php echo $this->load->view('v_np_rendicion_delete'); ?>
<?php echo $this->load->view('v_np_rendicion_det_new'); ?>
<?php echo $this->load->view('v_np_rendicion_det_edit'); ?>
<?php echo $this->load->view('v_np_rendicion_det_delete'); ?>

<script type="text/javascript">
	tab = Ext.getCmp('tab-appNP');
	tab.add(np.grid);
</script>