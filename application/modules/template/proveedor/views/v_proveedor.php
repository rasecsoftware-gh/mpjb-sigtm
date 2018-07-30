<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	proveedor = {};
	// general stores
	proveedor.st_banco = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'proveedor/getBancoList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	proveedor.st_moneda = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'proveedor/getMonedaList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});
	
	proveedor.st_grid_main = Ext.create("Ext.data.Store",{
		proxy:{
			type: 'ajax',
			url: 'proveedor/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 30
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_proveedor_list')?>
<?php echo $this->load->view('v_proveedor_new')?>
<?php echo $this->load->view('v_proveedor_edit')?>
<?php echo $this->load->view('v_proveedor_cb_new')?>
<?php echo $this->load->view('v_proveedor_cb_edit')?>

<script type="text/javascript">

	/* main */

	tab = Ext.getCmp('tab-appProve');
	tab.add(proveedor.grid);
</script>