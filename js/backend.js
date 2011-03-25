jQuery(document).ready(function(){
	
	var field_form_action = jQuery("#ns_form_action");
	var field_email_id = jQuery("#ns_email_id");
	var field_name_id = jQuery('#ns_name_id');
	var aweber_row = jQuery("#ns_aweber_options");
	var phplist_row = jQuery("#ns_phplist_options");
	var email_id_row = jQuery("#ns_email_id_row");
	var service = jQuery("#ns_email_service").val();
	
	jQuery("#ns_email_service").change(function(){
		ns_update_options();
	});
	
	jQuery("#ns_subscribe_with_name").change(function(){
		if(jQuery(this).attr('checked')) {
			email_id_row.show();
		} else {
			email_id_row.hide();
		}
	});
	
	function ns_update_options()
	{
	service = jQuery("#ns_email_service").val();
	switch(service)
	{
			
			case 'mailchimp':
				field_form_action.attr('readOnly',false).val('');
				field_email_id.val('EMAIL').attr('readOnly',true);
				field_name_id.val('NAME');
				aweber_row.hide();
				phplist_row.hide();
			break;
			
			case 'aweber':
				field_form_action.val('http://www.aweber.com/scripts/addlead.pl').attr('readOnly',true);
				field_email_id.val('email').attr('readOnly',true);
				field_name_id.val('');
				phplist_row.hide();
				aweber_row.show();
			break;
			
			case 'icontact':
				field_form_action.attr('readOnly',false).val('');
				field_email_id.val('fields_email').attr('readOnly',true);
				field_name_id.val('');
				aweber_row.hide();
				phplist_row.hide();
			break;
			
			case 'ymlp':
				field_form_action.attr('readOnly',false).val('');
				field_email_id.val('YMP0').attr('readOnly',true);
				field_name_id.val('');
				aweber_row.hide();
				phplist_row.hide();
			break;
			
			case 'phplist':
				field_form_action.attr('readOnly',false).val('');
				field_email_id.val('').attr('readOnly',false);
				field_name_id.val('');
				aweber_row.hide();
				phplist_row.show();
			break;
			
			case 'other':
				field_form_action.attr('readOnly',false).val('');
				field_email_id.val('').attr('readOnly',false);
				field_name_id.val('');
				aweber_row.hide();
				phplist_row.hide();
			break;
		
	}
	}
	
});

