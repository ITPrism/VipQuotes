jQuery(document).ready(function() {

	Joomla.submitbutton = function(task) {
		
		if (( task == 'authorform.save' || task == "authorform.save2new" ) && document.formvalidator.isValid(document.id('adminForm'))) {

			if (tinyMCE.get("jform_bio").isHidden()) {
				tinyMCE.get("jform_bio").show()
			}

			tinyMCE.get("jform_bio").save();
			Joomla.submitform(task);

		} else {
			alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
		}
	}

    jQuery("#js-vq-authorform-save2new").on("click", function(event){
        event.preventDefault();

        if (confirm(Joomla.JText._('COM_VIPQUOTES_ADD_AUTHOR_QUESTION'))) {
            Joomla.submitbutton('authorform.save2new');
        }
    });

    jQuery('.fileupload').fileuploadstyle();
});