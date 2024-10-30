var el = wp.element.createElement;
wp.blocks.registerBlockType('clc-products/isbn', {
   title: 'CLC Product via ISBN', // The display title for your block
   icon: 'book', // Toolbar icon can be either using WP Dashicons or custom SVG
   category: 'widgets', // Under which category the block would appear
   attributes: { // The data this block will be storing
      type: { type: 'string', default: 'large' }, // How the product will be shown
      isbn: { type: 'string' }//, // ISBN, Title or Category
      //content: { type: 'array', source: 'children', selector: 'p' } /// Notice box content in p tag
   },
  edit: function(props) {
   // How our block renders in the editor in edit mode
   function updateClcProp( event ) {
      props.setAttributes( { isbn: event.target.value } );
   }
   function updateType( event ) {
      props.setAttributes( { type: event.target.value } );
   }
   return el( 'div',
			  null,
		el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
			el( 'h3',
			{style: {margin: '0'}},
				'CLC Product via ISBN'
			)
		),
	    el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
		  el(
			 'label',
			 null,
			 'ISBN '
		  ),
		  el(
			 'input',
			 {
				type: 'text',
				placeholder: 'ISBN nummer',
				value: props.attributes.isbn,
				onChange: updateClcProp,
				style: { width: '100%' }
			 }
		  )
		),
	    el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
		  el(
			 'label',
			 null,
			 'Type zichtbaarheid '
		  ),
		  el(
			 'select',
			 {
				onChange: updateType,
				value: props.attributes.type,
				style: { width: '100%' }
			 },
			 el("option", {value: "large" }, "Large"),
			 el("option", {value: "small" }, "Small"),
			 el("option", {value: "link" }, "Link"),
			 el("option", {value: "custom_layout_1" }, "Custom layout 1"),
			 el("option", {value: "custom_layout_2" }, "Custom layout 2"),
			 el("option", {value: "custom_layout_3" }, "Custom layout 3")
		  )
		)
   ); // End return
 
},  // End edit()
 
save: function(props) {
   // How our block renders on the frontend
   
   return el( 
	  'div',
      null,
	  '[clc-product isbn="'+props.attributes.isbn+'" type="'+props.attributes.type+'"]'
   ); // End return
 
} // End save()
 
});/**/

wp.blocks.registerBlockType('clc-products/title', {
   title: 'CLC Product via Titel', // The display title for your block
   icon: 'book', // Toolbar icon can be either using WP Dashicons or custom SVG
   category: 'widgets', // Under which category the block would appear
   attributes: { // The data this block will be storing
	  type: { type: 'string', default: 'large' }, // How the product will be shown
      title: { type: 'string' }// ISBN, Title or Category
   },
  edit: function(props) {
   // How our block renders in the editor in edit mode
   function updateClcProp( event ) {
      props.setAttributes( { title: event.target.value } );
   }
   function updateType( event ) {
      props.setAttributes( { type: event.target.value } );
   }
   return el( 'div',
			  null,
		el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
			el( 'h3',
			{style: {margin: '0'}},
				'CLC Product via Titel'
			)
		),
	    el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
		  el(
			 'label',
			 null,
			 'Titel '
		  ),
		  el(
			 'input',
			 {
				type: 'text',
				placeholder: 'Titel',
				value: props.attributes.title,
				onChange: updateClcProp,
				style: { width: '100%' }
			 }
		  )
		),
	    el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
		  el(
			 'label',
			 null,
			 'Type zichtbaarheid '
		  ),
		  el(
			 'select',
			 {
				onChange: updateType,
				value: props.attributes.type,
				style: { width: '100%' }
			 },
			 el("option", {value: "large" }, "Large"),
			 el("option", {value: "small" }, "Small"),
			 el("option", {value: "link" }, "Link"),
			 el("option", {value: "custom_layout_1" }, "Custom layout 1"),
			 el("option", {value: "custom_layout_2" }, "Custom layout 2"),
			 el("option", {value: "custom_layout_3" }, "Custom layout 3")
		  )
		)
   ); // End return
 
},  // End edit()
 
save: function(props) {
   // How our block renders on the frontend
   
   return el( 
	  'div',
      null,
	  '[clc-product title="'+props.attributes.title+'" type="'+props.attributes.type+'"]'
   ); // End return
 
} // End save()
 
});/**/

wp.blocks.registerBlockType('clc-products/cat', {
   title: 'CLC Product via Categorie', // The display title for your block
   icon: 'book', // Toolbar icon can be either using WP Dashicons or custom SVG
   category: 'widgets', // Under which category the block would appear
   attributes: { // The data this block will be storing
	  type: { type: 'string', default: 'large' }, // How the product will be shown
      cat: { type: 'string' },// ISBN, Title or Category
	  amount: { type: 'int' },// ISBN, Title or Category
	  visible: {type: 'int'}
   },
  edit: function(props) {
   // How our block renders in the editor in edit mode
   function updateClcProp( event ) {
      props.setAttributes( { cat: event.target.value } );
   }
   function updateType( event ) {
      props.setAttributes( { type: event.target.value } );
   }
   function updateAmount( event ) {
      props.setAttributes( { amount: event.target.value } );
   }
   function updateVisible( event ) {
      props.setAttributes( { visible: event.target.value } );
   }
   return el( 'div',
			  null,
		el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
			el( 'h3',
			{style: {margin: '0'}},
				'CLC Producten via Categorie'
			)
		),
		el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
			el( 'a',
				{href: 'https://clcnederland.com/affiliate/affiliate-categorie',
				target: '_blank'},
				'Bekijk onze categorieen'
			)
		),
	    el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
		  el(
			 'label',
			 null,
			 'Categorie '
		  ),
		  el(
			 'input',
			 {
				type: 'text',
				placeholder: 'Categorie',
				value: props.attributes.cat,
				onChange: updateClcProp,
				style: { width: '100%' }
			 }
		  )
		),
		el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
		  el(
			 'label',
			 null,
			 'Aantal producten '
		  ),
		  el(
			 'input',
			 {
				type: 'number',
				placeholder: 'Amount',
				value: props.attributes.amount,
				onChange: updateAmount,
				style: { width: '100%' }
			 }
		  )
		),
		el( 'div',
          {
			 className: 'wp-block-shortcode',
			 style: {paddingBottom: '0'}
		  },
		  el(
			 'label',
			 null,
			 'Type zichtbaarheid '
		  ),
		  el(
			 'select',
			 {
				onChange: updateType,
				value: props.attributes.type,
				style: { width: '100%' }
			 },
			 el("option", {value: "large" }, "Large"),
			 el("option", {value: "carousel_large" }, "Large (Carousel)"),
			 el("option", {value: "small" }, "Small"),
			 el("option", {value: "carousel_small" }, "Small (Carousel)"),
			 el("option", {value: "page_small" }, "Small (Rows)"),
			 el("option", {value: "link" }, "Link"),
			 el("option", {value: "custom_layout_1" }, "Custom layout 1"),
			 el("option", {value: "custom_layout_2" }, "Custom layout 2"),
			 el("option", {value: "custom_layout_3" }, "Custom layout 3")
		  )
	   ),
	   el( 'div',
          {
			 className: 'wp-block-shortcode'
		  },
		  el(
			 'label',
			 null,
			 'Aantal producten per rij '
		  ),
		  el(
			 'input',
			 {
				type: 'number',
				placeholder: 'Visible',
				value: props.attributes.visible,
				onChange: updateVisible,
				style: { width: '100%' }
			 }
		  )
		)
	); // End return
 
},  // End edit()
 
save: function(props) {
   // How our block renders on the frontend
   
   return el( 
	  'div',
      null,
	  '[clc-product cat="'+props.attributes.cat+'" type="'+props.attributes.type+'" amount="'+props.attributes.amount+'"]'
   ); // End return
 
} // End save()
 
});/**/