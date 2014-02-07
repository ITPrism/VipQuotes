jQuery(document).ready(function() { 
	
    Joomla.submitbutton = function(task){
        if (task == 'import.cancel' || document.formvalidator.isValid(document.id('import-form'))) {
            Joomla.submitform(task, document.getElementById('import-form'));
        }
    };
    
    jQuery('.fileupload').fileuploadstyle();
    
});