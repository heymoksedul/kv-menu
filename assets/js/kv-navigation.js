/**
 * KV Navigation — Frontend Handler
 * Sticky + hide-on-scroll, mobile drawer, drilldown submenus, accessibility.
 */
( function( $ ) {
	'use strict';

	var KVNavigation = function( $scope ) {
		var $wrapper  = $scope.find( '.kv-navigation-wrapper' );
		if ( ! $wrapper.length ) {
			return;
		}
		var $toggle   = $scope.find( '.kv-nav-toggle-btn' );
		var $close    = $scope.find( '.kv-nav-close-btn' );
		var $back     = $scope.find( '.kv-nav-back-btn' );
		var $panel    = $scope.find( '.kv-nav-panel' );
		var $overlay  = $scope.find( '.kv-nav-overlay' );
		var $body     = $( 'body' );

		// ---------- Helpers ----------
		function isDrawerMode() {
			return $toggle.is( ':visible' );
		}

		function updateBack() {
			var active = $panel.find( '.sub-menu.is-active, .kv-nav-mega-panel.is-active, .kv-mega-pane.is-active' ).length;
			if ( active > 0 ) {
				$back.show();
				$wrapper.addClass( 'kv-nav--submenu-open' );
			} else {
				$back.hide();
				$wrapper.removeClass( 'kv-nav--submenu-open' );
			}
		}

		function openDrawer() {
			$wrapper.addClass( 'kv-nav--open' );
			$toggle.attr( 'aria-expanded', 'true' ).attr( 'aria-label', 'Close menu' );
			$body.addClass( 'kv-nav-locked' );
			// Focus first focusable item in the panel.
			var $first = $panel.find( 'a, button' ).filter( ':visible' ).first();
			if ( $first.length ) {
				$first.trigger( 'focus' );
			}
		}

		function closeDrawer() {
			$wrapper.removeClass( 'kv-nav--open' );
			$toggle.attr( 'aria-expanded', 'false' ).attr( 'aria-label', 'Open menu' );
			$body.removeClass( 'kv-nav-locked' );
			$panel.find( '.sub-menu, .kv-nav-mega-panel, .kv-mega-pane' ).removeClass( 'is-active' );
			updateBack();
		}

		// ---------- Toggle ----------
		$toggle.on( 'click', function( e ) {
			e.preventDefault();
			if ( $wrapper.hasClass( 'kv-nav--open' ) ) {
				closeDrawer();
			} else {
				openDrawer();
			}
		} );

		$close.on( 'click', function( e ) {
			e.preventDefault();
			closeDrawer();
			$toggle.trigger( 'focus' );
		} );

		$overlay.on( 'click', function() {
			closeDrawer();
		} );

		// ---------- Back button ----------
		$back.on( 'click', function( e ) {
			e.preventDefault();
			var $active = $panel.find( '.sub-menu.is-active, .kv-nav-mega-panel.is-active, .kv-mega-pane.is-active' );
			if ( $active.length ) {
				$active.last().removeClass( 'is-active' );
			}
			updateBack();
		} );

		// ---------- Drilldown (parent item click / keyboard in drawer) ----------
		$scope.find( '.kvn-menu .menu-item-has-children > a, .kvn-menu .menu-item-has-mega-menu > a' ).on( 'click', function( e ) {
			if ( ! isDrawerMode() ) {
				return; // desktop: allow normal hover dropdown / link
			}
			e.preventDefault();
			var $sub = $( this ).siblings( '.sub-menu, .kv-nav-mega-panel' ).first();
			if ( $sub.length ) {
				$sub.addClass( 'is-active' );
				updateBack();
				var $firstLink = $sub.find( 'a, button' ).filter( ':visible' ).first();
				if ( $firstLink.length ) {
					$firstLink.trigger( 'focus' );
				}
			}
		} );

		// ---------- Native mega: tab switch (desktop) / drilldown (mobile) ----------
		function activateDesktopTab( $tab ) {
			var $mega = $tab.closest( '.kv-mega' );
			$mega.find( '.kv-mega-tab' ).removeClass( 'is-active' );
			$tab.addClass( 'is-active' );
			$mega.find( '.kv-mega-pane' ).removeClass( 'kv-mega-pane--current' );
			$mega.find( '[id="' + $tab.attr( 'data-target' ) + '"]' ).addClass( 'kv-mega-pane--current' );
		}

		$scope.find( '.kv-mega-tab' ).on( 'click', function( e ) {
			e.preventDefault();
			var $tab = $( this );
			if ( isDrawerMode() ) {
				var $pane = $tab.closest( '.kv-mega' ).find( '[id="' + $tab.attr( 'data-target' ) + '"]' );
				$pane.addClass( 'is-active' );
				updateBack();
				var $fl = $pane.find( 'a' ).filter( ':visible' ).first();
				if ( $fl.length ) {
					$fl.trigger( 'focus' );
				}
			} else {
				activateDesktopTab( $tab );
			}
		} );

		$scope.find( '.kv-mega-tab' ).on( 'mouseenter', function() {
			if ( ! isDrawerMode() ) {
				activateDesktopTab( $( this ) );
			}
		} );

		$scope.find( '.kv-mega-tab' ).on( 'keydown', function( e ) {
			if ( 'Enter' === e.key || ' ' === e.key || 13 === e.keyCode || 32 === e.keyCode ) {
				e.preventDefault();
				$( this ).trigger( 'click' );
			}
		} );

		// ---------- Keyboard: ESC closes drawer, focus trap ----------
		$( document ).on( 'keydown.kvnav', function( e ) {
			if ( ! $wrapper.hasClass( 'kv-nav--open' ) ) {
				return;
			}
			if ( 'Escape' === e.key || 27 === e.keyCode ) {
				closeDrawer();
				$toggle.trigger( 'focus' );
				return;
			}
			if ( 'Tab' === e.key || 9 === e.keyCode ) {
				var $focusable = $panel.add( $toggle ).find( 'a, button' ).filter( ':visible' );
				if ( ! $focusable.length ) {
					return;
				}
				var first = $focusable.first()[0];
				var last  = $focusable.last()[0];
				if ( e.shiftKey && document.activeElement === first ) {
					e.preventDefault();
					last.focus();
				} else if ( ! e.shiftKey && document.activeElement === last ) {
					e.preventDefault();
					first.focus();
				}
			}
		} );

		// ---------- Reset on resize back to desktop ----------
		$( window ).on( 'resize.kvnav', function() {
			if ( ! isDrawerMode() ) {
				closeDrawer();
			}
		} );

		// ---------- Sticky + hide-on-scroll ----------
		if ( $wrapper.hasClass( 'kv-nav-sticky-enabled' ) ) {
			var offset      = parseInt( $wrapper.attr( 'data-sticky-offset' ), 10 ) || 80;
			var hideOnDown  = $wrapper.attr( 'data-hide-on-scroll' ) === '1';
			var lastScroll  = 0;
			var ticking     = false;

			var onScroll = function() {
				var y = window.pageYOffset || document.documentElement.scrollTop;

				if ( y > offset ) {
					$wrapper.addClass( 'kv-header-sticky' );
				} else {
					$wrapper.removeClass( 'kv-header-sticky' ).removeClass( 'kv-header-hidden' );
				}

				if ( hideOnDown && y > offset ) {
					if ( y > lastScroll && y > offset + 60 ) {
						$wrapper.addClass( 'kv-header-hidden' );
					} else {
						$wrapper.removeClass( 'kv-header-hidden' );
					}
				}

				lastScroll = y <= 0 ? 0 : y;
				ticking = false;
			};

			$( window ).on( 'scroll.kvnav', function() {
				if ( ! ticking ) {
					window.requestAnimationFrame( onScroll );
					ticking = true;
				}
			} );
			onScroll();
		}
	};

	// Bind: prefer Elementor frontend hooks, fall back to DOM ready.
	function boot() {
		if ( window.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/kv-navigation.default', function( $scope ) {
				KVNavigation( $scope );
			} );
		}
	}

	$( window ).on( 'elementor/frontend/init', boot );

	// Fallback for non-Elementor rendering contexts.
	$( function() {
		if ( ! window.elementorFrontend ) {
			$( '.kv-navigation-wrapper' ).each( function() {
				KVNavigation( $( this ).closest( '.elementor-widget, body' ) );
			} );
		}
	} );

} )( jQuery );
