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

    // Style file input
    jQuery('#jform_image').fileinput({
        showPreview: false,
        showUpload: false,
        browseClass: "btn btn-success",
        browseLabel: Joomla.JText._('COM_VIPQUOTES_PICK_IMAGE'),
        browseIcon: '<span class="glyphicon glyphicon-picture"></span> ',
        removeClass: "btn btn-danger",
        removeLabel: Joomla.JText._('COM_VIPQUOTES_REMOVE'),
        layoutTemplates: {
            main1:
            "<div class=\'input-group {class}\'>\n" +
            "   <div class=\'input-group-btn\'>\n" +
            "       {browse}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>"
        }
    });
});