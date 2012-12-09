window.addEvent('domready', function(){ 
    Joomla.submitbutton = function(task){
        if (task == 'author.cancel' || document.formvalidator.isValid(document.id('author-form'))) {
            Joomla.submitform(task, document.getElementById('author-form'));
        }
    };
})