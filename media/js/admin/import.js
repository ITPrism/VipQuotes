window.addEvent('domready', function(){ 
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'import.cancel' || document.formvalidator.isValid(document.id('import-form'))) {
            Joomla.submitform(task, document.getElementById('import-form'));
        }
    };
    
})