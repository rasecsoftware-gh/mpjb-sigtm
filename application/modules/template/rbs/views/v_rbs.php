<script type="text/javascript">
	/********************* generamos el name espace ***************************/
	rbs = {};
	// general stores
	
	rbs.st_grid_main = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'rbs/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: true,
		pageSize: 50,
		listeners: {
			load: function () {
				Ext.getCmp('rbs_grid_main').getSelectionModel().selectAll();
			}
		}
	});
</script>

<!--  cargamos los componentes -->
<?php echo $this->load->view('v_rbs_list'); ?>
<?php echo $this->load->view('v_rbs_new'); ?>
<?php echo $this->load->view('v_rbs_edit'); ?>
<?php echo $this->load->view('v_rbs_bien_search'); ?>

<script type="text/javascript">
	var tab = Ext.getCmp('tab-rbs');
	tab.add(rbs.grid);
</script>