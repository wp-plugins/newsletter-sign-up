jQuery(document).ready(function(){
	
	var api_rows = jQuery("tbody.api_rows");
	var form_rows = jQuery("tbody.form_rows");
	var email_id_row = jQuery('tr#email_id_row');
	
	jQuery("#use_api").change(function(){
		if(jQuery(this).attr('checked')) {
			api_rows.show();	
			form_rows.hide();
		} else {
			api_rows.hide();
			form_rows.show();
		}
	});
	
	jQuery("#subscribe_with_name").change(function(){
		if(jQuery(this).attr('checked')) {
			email_id_row.show();	
		} else {
			email_id_row.hide();
		}
	});
	
});
