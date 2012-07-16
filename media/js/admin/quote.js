window.addEvent('domready', function(){ 
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'quote.cancel' || document.formvalidator.isValid(document.id('quote-form'))) {
            Joomla.submitform(task, document.getElementById('quote-form'));
        }
    };
})