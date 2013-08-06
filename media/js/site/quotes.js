window.addEvent('domready', function(){ 

	// Filter Aithors
	var elementFilterAuthor = document.id("filter_author");
	if(elementFilterAuthor) {
		elementFilterAuthor.addEvent("change", function(event) {
			document.id("vq-filters-form").submit();
		});
	}
	
	// Filter Category
	var elementFilterUser = document.id("filter_category");
	if(elementFilterUser) {
		elementFilterUser.addEvent("change", function(event) {
			document.id("vq-filters-form").submit();
		});
	}
	
	// Filter Users
	var elementFilterUser = document.id("filter_user");
	if(elementFilterUser) {
		elementFilterUser.addEvent("change", function(event) {
			document.id("vq-filters-form").submit();
		});
	}
	
	// Filter Ordering
	var elementFilterOrdering = document.id("filter_ordering");
	if(elementFilterOrdering) {
		elementFilterOrdering.addEvent("change", function(event) {
			document.id("vq-filters-form").submit();
		});
	}
});