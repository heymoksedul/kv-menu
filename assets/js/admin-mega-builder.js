/**
 * KV Navigation — Mega Menu Builder (admin) with icon picker.
 */
( function( $ ) {
	'use strict';

	var uid = 1000;
	var L = window.kvMegaBuilder || {};

	// Font Awesome 5 Free icon set (solid + brands) used by Elementor.
	var FA_SOLID = ( 'home user users user-tie user-shield cog cogs tools wrench hammer screwdriver toolbox wifi plug ' +
		'bolt bell star heart check check-circle times times-circle ban search envelope envelope-open phone phone-alt ' +
		'mobile-alt map-marker-alt map location-arrow globe globe-americas compass flag calendar calendar-alt clock ' +
		'hourglass-half stopwatch camera image images video film music headphones microphone book book-open ' +
		'graduation-cap chalkboard-teacher briefcase building city industry store store-alt shopping-cart shopping-bag ' +
		'shopping-basket credit-card dollar-sign money-bill-wave coins wallet chart-line chart-bar chart-pie chart-area ' +
		'tachometer-alt rocket lightbulb fire bullhorn gift trophy medal award crown gem thumbs-up thumbs-down ' +
		'comment comments comment-dots paper-plane share share-alt link paperclip lock unlock key shield-alt fingerprint ' +
		'cloud cloud-upload-alt cloud-download-alt download upload sync redo undo copy paste edit pen pencil-alt trash ' +
		'trash-alt save print database server hdd network-wired battery-full desktop laptop tablet-alt keyboard mouse ' +
		'gamepad robot brain atom flask microscope dna heartbeat stethoscope user-md hospital pills capsules syringe ' +
		'first-aid notes-medical tooth eye hand-holding-heart hands-helping seedling leaf tree water tint sun moon ' +
		'snowflake umbrella wind cloud-sun temperature-high paw dog cat futbol dumbbell running biking swimmer utensils ' +
		'coffee pizza-slice hamburger wine-glass birthday-cake ring palette paint-brush magic feather guitar drum film ' +
		'ticket-alt theater-masks chess dice puzzle-piece headset language quote-left hashtag at percent infinity ' +
		'calculator balance-scale gavel landmark university handshake id-card address-book sitemap project-diagram ' +
		'layer-group th th-large th-list list list-ul list-ol columns table expand compress arrows-alt external-link-alt ' +
		'arrow-right arrow-left arrow-up arrow-down angle-right angle-down chevron-right chevron-down plus minus ' +
		'ellipsis-h bars filter sliders-h sort question-circle info-circle exclamation-circle plus-circle truck ' +
		'shipping-fast box boxes warehouse plane car bicycle bus train ship anchor road tag tags bookmark folder ' +
		'folder-open file file-alt file-pdf file-word file-excel cut business-time' ).split( ' ' );

	var FA_BRANDS = ( 'facebook facebook-f twitter instagram linkedin linkedin-in youtube whatsapp telegram telegram-plane ' +
		'github google apple android windows amazon paypal cc-stripe wordpress elementor figma slack discord tiktok ' +
		'pinterest reddit dribbble behance vimeo-v medium' ).split( ' ' );

	function iconList() {
		var out = [];
		FA_SOLID.forEach( function( n ) { out.push( 'fas fa-' + n ); } );
		FA_BRANDS.forEach( function( n ) { out.push( 'fab fa-' + n ); } );
		return out;
	}

	function previewHtml( val ) {
		if ( ! val ) { return ''; }
		if ( /^https?:\/\//.test( val ) ) {
			return '<img src="' + val + '" alt="" />';
		}
		return '<i class="' + val + '"></i>';
	}

	function tabTemplate( tid ) {
		return $( '#kvmega-tab-template' ).html().replace( /__TID__/g, tid );
	}
	function linkTemplate( tid, lid ) {
		return $( '#kvmega-link-template' ).html().replace( /__TID__/g, tid ).replace( /__LID__/g, lid );
	}

	// ---------- Icon picker modal ----------
	var $modal, $grid, $search, $customUrl, currentField = null, gridBuilt = false;

	function buildModal() {
		$modal = $(
			'<div class="kvmega-modal-overlay" style="display:none">' +
				'<div class="kvmega-modal">' +
					'<div class="kvmega-modal-head">' +
						'<input type="text" class="kvmega-icon-search" placeholder="' + ( L.search || 'Search icons...' ) + '">' +
						'<button type="button" class="kvmega-modal-close">&times;</button>' +
					'</div>' +
					'<div class="kvmega-modal-tabs">' +
						'<button type="button" class="kvmega-mtab is-active" data-pane="fa">' + ( L.library || 'Icon Library' ) + '</button>' +
						'<button type="button" class="kvmega-mtab" data-pane="custom">' + ( L.custom || 'Custom SVG / Image' ) + '</button>' +
					'</div>' +
					'<div class="kvmega-modal-body">' +
						'<div class="kvmega-pane kvmega-pane-fa"><div class="kvmega-icon-grid"></div></div>' +
						'<div class="kvmega-pane kvmega-pane-custom" style="display:none">' +
							'<p>' + ( L.custom || 'Custom SVG / Image' ) + '</p>' +
							'<button type="button" class="button kvmega-upload-svg">' + ( L.selectImage || 'Select SVG / Image' ) + '</button>' +
							'<div class="kvmega-custom-url-row">' +
								'<input type="text" class="kvmega-custom-url" placeholder="' + ( L.urlPlaceholder || 'https://...' ) + '">' +
								'<button type="button" class="button button-primary kvmega-use-url">' + ( L.useUrl || 'Use URL' ) + '</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>'
		);
		$( 'body' ).append( $modal );
		$grid = $modal.find( '.kvmega-icon-grid' );
		$search = $modal.find( '.kvmega-icon-search' );
		$customUrl = $modal.find( '.kvmega-custom-url' );

		// Events
		$modal.on( 'click', '.kvmega-modal-close', closeModal );
		$modal.on( 'click', function( e ) { if ( e.target === $modal[0] ) { closeModal(); } } );

		$modal.on( 'click', '.kvmega-mtab', function() {
			$modal.find( '.kvmega-mtab' ).removeClass( 'is-active' );
			$( this ).addClass( 'is-active' );
			var pane = $( this ).data( 'pane' );
			$modal.find( '.kvmega-pane-fa' ).toggle( 'fa' === pane );
			$modal.find( '.kvmega-pane-custom' ).toggle( 'custom' === pane );
		} );

		$modal.on( 'click', '.kvmega-icon-cell', function() {
			applyIcon( $( this ).data( 'icon' ) );
		} );

		$search.on( 'input', function() {
			var q = $( this ).val().toLowerCase();
			$grid.find( '.kvmega-icon-cell' ).each( function() {
				var name = $( this ).data( 'icon' );
				$( this ).toggle( name.indexOf( q ) !== -1 );
			} );
		} );

		$modal.on( 'click', '.kvmega-use-url', function() {
			var v = $.trim( $customUrl.val() );
			if ( v ) { applyIcon( v ); }
		} );

		$modal.on( 'click', '.kvmega-upload-svg', function( e ) {
			e.preventDefault();
			var frame = wp.media( {
				title: L.selectImage || 'Select SVG / Image',
				button: { text: L.useImage || 'Use this image' },
				multiple: false
			} );
			frame.on( 'select', function() {
				var att = frame.state().get( 'selection' ).first().toJSON();
				applyIcon( att.url );
			} );
			frame.open();
		} );
	}

	function buildGrid() {
		if ( gridBuilt ) { return; }
		var html = '';
		iconList().forEach( function( cls ) {
			html += '<button type="button" class="kvmega-icon-cell" data-icon="' + cls + '" title="' + cls + '"><i class="' + cls + '"></i></button>';
		} );
		$grid.html( html );
		gridBuilt = true;
	}

	function openModal( $field ) {
		if ( ! $modal ) { buildModal(); }
		buildGrid();
		currentField = $field;
		$search.val( '' ).trigger( 'input' );
		$customUrl.val( '' );
		$modal.find( '.kvmega-mtab[data-pane="fa"]' ).trigger( 'click' );
		$modal.show();
	}

	function closeModal() {
		if ( $modal ) { $modal.hide(); }
		currentField = null;
	}

	function applyIcon( val ) {
		if ( currentField ) {
			currentField.find( '.kvmega-in-icon' ).val( val );
			currentField.find( '.kvmega-icon-preview' ).html( previewHtml( val ) );
		}
		closeModal();
	}

	// ---------- Init ----------
	$( function() {
		var $builder = $( '.kvmega-builder' );
		if ( ! $builder.length ) { return; }

		$builder.on( 'click', '.kvmega-add-tab', function( e ) {
			e.preventDefault();
			$( '.kvmega-tabs' ).append( tabTemplate( 't' + ( uid++ ) ) );
		} );

		$builder.on( 'click', '.kvmega-remove-tab', function( e ) {
			e.preventDefault();
			if ( window.confirm( 'Remove this tab?' ) ) {
				$( this ).closest( '.kvmega-tab' ).remove();
			}
		} );

		$builder.on( 'click', '.kvmega-toggle-tab', function( e ) {
			e.preventDefault();
			$( this ).closest( '.kvmega-tab' ).find( '.kvmega-tab-body' ).slideToggle( 150 );
		} );

		$builder.on( 'click', '.kvmega-add-link', function( e ) {
			e.preventDefault();
			var tid = $( this ).data( 'tid' );
			$( this ).closest( '.kvmega-tab-body' ).find( '.kvmega-links' ).append( linkTemplate( tid, 'l' + ( uid++ ) ) );
		} );

		$builder.on( 'click', '.kvmega-remove-link', function( e ) {
			e.preventDefault();
			$( this ).closest( '.kvmega-link' ).remove();
		} );

		// Featured image media
		$builder.on( 'click', '.kvmega-media-select', function( e ) {
			e.preventDefault();
			var $wrap = $( this ).closest( '.kvmega-featured' );
			var frame = wp.media( { title: L.selectImage || 'Select Image', button: { text: L.useImage || 'Use this image' }, multiple: false } );
			frame.on( 'select', function() {
				var att = frame.state().get( 'selection' ).first().toJSON();
				$wrap.find( '.kvmega-featured-input' ).val( att.url );
				$wrap.find( '.kvmega-featured-preview' ).html( '<img src="' + att.url + '" alt="" />' );
			} );
			frame.open();
		} );

		$builder.on( 'click', '.kvmega-media-remove', function( e ) {
			e.preventDefault();
			var $wrap = $( this ).closest( '.kvmega-featured' );
			$wrap.find( '.kvmega-featured-input' ).val( '' );
			$wrap.find( '.kvmega-featured-preview' ).empty();
		} );

		// Icon picker button
		$builder.on( 'click', '.kvmega-icon-btn', function( e ) {
			e.preventDefault();
			openModal( $( this ).closest( '.kvmega-icon-field' ) );
		} );
	} );

} )( jQuery );
