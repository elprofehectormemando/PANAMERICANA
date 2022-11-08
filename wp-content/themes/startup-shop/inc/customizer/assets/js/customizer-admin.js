/*admin css*/
( function( startup_shop_api ) {

	startup_shop_api.sectionConstructor['startup_shop_upsell'] = startup_shop_api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );
