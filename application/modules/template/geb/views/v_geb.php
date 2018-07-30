<script type="text/javascript">
	geb = {};
	// stores
	geb.st_sys2009_area = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'geb/getSys2009AreaList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: true
	});

	geb.st_solicitante = Ext.create("Ext.data.Store",{
		proxy:{
			type:'ajax',
			url:'geb/getSolicitanteList',
			reader:{
				type:'json',
				rootProperty:'data'
			}
		},
		autoLoad: false
	});
	
	geb.st_grid_main = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'geb/getList',
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

<?php echo $this->load->view('v_geb_list'); ?>
<?php echo $this->load->view('v_geb_new'); ?>
<?php echo $this->load->view('v_geb_edit'); ?>
<?php echo $this->load->view('v_geb_nemo_search'); ?>
<?php echo $this->load->view('v_geb_det_import'); ?>
<?php echo $this->load->view('v_geb_det_import_saldo'); ?>
<?php //echo $this->load->view('v_geb_det_edit'); ?>
<?php echo $this->load->view('v_geb_det_delete'); ?>
	
<script>
	/*************************** main ************************/
	tab = Ext.getCmp('tab-geb');
	tab.add(geb.grid);
</script>