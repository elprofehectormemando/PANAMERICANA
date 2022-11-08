( function( api ) {

	// Extends our custom "ebook-store" section.
	api.sectionConstructor['ebook-store'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );