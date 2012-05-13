window.addEvent('domready', function(){ 
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'quote.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task);
        }
    };
    
    document.id("save_quote").addEvent("click", function(event){
    	Joomla.submitbutton('quote.save');
    });
    
    document.id("cancel_quote").addEvent("click", function(event){
    	Joomla.submitbutton('quote.cancel');
    });
});