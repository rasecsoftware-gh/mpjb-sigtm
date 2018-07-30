<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	comprador = {};
	// general stores
	comprador.st_banco = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'comprador/getBancoList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	comprador.st_moneda = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'comprador/getMonedaList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});
	
	comprador.st_grid_main = Ext.create("Ext.data.Store",{
		proxy:{
			type: 'ajax',
			url: 'comprador/getList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_comprador_list')?>
<?php echo $this->load->view('v_comprador_new')?>
<?php echo $this->load->view('v_comprador_edit')?>
<?php echo $this->load->view('v_comprador_cb_new')?>
<?php echo $this->load->view('v_comprador_cb_edit')?>

<script type="text/javascript">

	/* main */

	tab = Ext.getCmp('tab-appComprador');
	tab.add(comprador.grid);
</script>