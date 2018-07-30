<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	clapre = {};
	// general stores
	clapre.st_obra = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'clapre/getObraList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});
	
	clapre.st_grid_main = Ext.create("Ext.data.Store",{
		proxy:{
			type: 'ajax',
			url: 'clapre/getList',
			reader: {
				type: 'json',
				rootProperty: 'data'
			}
		},
		autoLoad: true
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_clapre_list')?>
<?php echo $this->load->view('v_clapre_new')?>
<?php echo $this->load->view('v_clapre_edit')?>
<?php echo $this->load->view('v_clapre_delete')?>
<?php //echo $this->load->view('v_clapre_cb_edit')?>

<script type="text/javascript">

	/* main */

	tab = Ext.getCmp('tab-appCP');
	tab.add(clapre.grid);
</script>