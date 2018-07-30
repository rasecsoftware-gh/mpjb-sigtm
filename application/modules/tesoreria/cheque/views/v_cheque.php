<script type="text/javascript">
	cheque = {};
	// stores
	
	cheque.main_store = Ext.create("Ext.data.Store", {
		proxy:{
			type: 'ajax',
			url: 'cheque/getList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				totalProperty: 'total'
			}
		},
		autoLoad: false,
		pageSize: 30
	});

	
</script>

<?php echo $this->load->view('v_cheque_list'); ?>
<?php echo $this->load->view('v_cheque_print'); ?>
	
<script>
	/*************************** main ************************/
	tab = Ext.getCmp('tab-cheque');
	tab.add(cheque.grid);
</script>