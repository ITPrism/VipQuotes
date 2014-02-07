jQuery(document).ready(function() { 
	
    Joomla.submitbutton = function(task){
        if (task == 'tab.cancel' || document.formvalidator.isValid(document.id('tab-form'))) {
            Joomla.submitform(task, document.getElementById('tab-form'));
        }
    };
    
});