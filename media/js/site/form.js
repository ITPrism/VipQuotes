jQuery(document).ready(function() {

	Joomla.submitbutton = function(task) {
		
		if (( task == 'form.save' || task == "form.save2new" ) && document.formvalidator.isValid(document.id('adminForm'))) {
			if (tinyMCE.get("jform_quote").isHidden()) {
				tinyMCE.get("jform_quote").show()
			}; 
			tinyMCE.get("jform_quote").save();			
			Joomla.submitform(task);
		} else {
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
		
	}
	
});