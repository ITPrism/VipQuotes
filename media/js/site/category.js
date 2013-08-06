window.addEvent('domready', function(){ 

	// Filter Ordering
	var elementFilterOrdering = document.id("filter_ordering");
	if(elementFilterOrdering) {
		elementFilterOrdering.addEvent("change", function(event) {
			document.id("vq-filters-form").submit();
		});
	}
})