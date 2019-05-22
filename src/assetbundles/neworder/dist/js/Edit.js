/**
 * Commerce Admin Orders plugin for Craft CMS
 *
 * Orders Field JS
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   CommerceAdminOrders
 * @since     1.0.0
 */

var adminOrderTabs = Garnish.Base.extend({
	$selectedTab: null,

	init: function($parent) {
		console.log('here');
		this.$selectedTab = null;

		var $tabs = $parent.find('#tabs > ul > li');
		var tabs = [];
		var tabWidths = [];
		var totalWidth = 0;
		var i, a, href;

		for (i = 0; i < $tabs.length; i++) {
			tabs[i] = $($tabs[i]);
			tabWidths[i] = tabs[i].width();
			totalWidth += tabWidths[i];

			// Does it link to an anchor?
			a = tabs[i].children('a');
			href = a.attr('href');
			if (href && href.charAt(0) === '#') {
				this.addListener(a, 'click', function(ev) {
					ev.preventDefault();
					this.selectTab(ev.currentTarget);
				});

				if (encodeURIComponent(href.substr(1)) === document.location.hash.substr(1)) {
					this.selectTab(a);
				}
			}

			if (!this.$selectedTab && a.hasClass('sel')) {
				this.$selectedTab = a;
			}
		}

		// Now set their max widths
		for (i = 0; i < $tabs.length; i++) {
			tabs[i].css('max-width', (100 * tabWidths[i]) / totalWidth + '%');
		}
	},

	selectTab: function(tab) {
		var $tab = $(tab);

		if (this.$selectedTab) {
			if (this.$selectedTab.get(0) === $tab.get(0)) {
				return;
			}
			this.deselectTab();
		}

		$tab.addClass('sel');
		var href = $tab.attr('href');
		$(href).removeClass('hidden');
		if (typeof history !== 'undefined') {
			history.replaceState(undefined, undefined, href);
		}
		Garnish.$win.trigger('resize');
		// Fixes Redactor fixed toolbars on previously hidden panes
		Garnish.$doc.trigger('scroll');
		this.$selectedTab = $tab;
	},

	deselectTab: function() {
		if (!this.$selectedTab) {
			return;
		}

		this.$selectedTab.removeClass('sel');
		if (this.$selectedTab.attr('href').charAt(0) === '#') {
			$(this.$selectedTab.attr('href')).addClass('hidden');
		}
		this.$selectedTab = null;
	}
});

$(document).ready(function() {
	if ($('body').hasClass('commerceordersindex')) {
		new Craft.Commerce.OrderCreate();
	}

	if ($('#adminOrders')[0]) {
		//if ($('#order-completionStatus')[0]) {
		var $form = $('#main-form'),
			$adminOrders = $('#adminOrders'),
			$orderDetails = $('.order-details'),
			editPage = $('#order-completionStatus')[0];

		$form.removeAttr('data-confirm-unload');

		if (editPage) {
			$orderDetails.find('table thead tr').append($('<th scope="col" class="thin"></th>'));
			$orderDetails.find('.infoRow').each(function() {
				var $row = $(this),
					$qty = $row.find('td[data-title="Qty"]'),
					$input = $('<input type="number" min="0" name="lineItems[' + $row.attr('data-id') + '][qty]" value="' + Number($qty.text()) + '">');
				$('<td class="thin"><a class="delete icon" data-id="' + $row.attr('data-id') + '" title="Delete" role="button"></a></td>').appendTo($row);

				$qty.empty().append($input);
			});

			var tabs = new adminOrderTabs($adminOrders);

			$('#cartControls .submit').on('click', function(e) {
				e.preventDefault();
				$orderDetails.addClass('loading');
				$form.find('input[name=action]').val('commerce/cart/update-cart');
				$form.append($('<input type="hidden" name="redirect" value="' + $form.attr('data-saveshortcut-redirect') + '">'));
				$form.submit();
			});
		}

		$adminOrders.find('.tab').each(function() {
			var $tab = $(this),
				type = $tab.find('.elementindex').data('type');

			Craft.elementIndex = Craft.createElementIndex(type, $tab, {
				context: 'index',
				storageKey: 'elementindex.' + type,
				criteria: Craft.defaultIndexCriteria
			});
		});

		$form.on('click', '.infoRow a.delete', function(e) {
			e.preventDefault();
			$('<input type="hidden" name="lineItems[' + $(this).data('id') + '][remove]" value="1">').appendTo($form);

			if (editPage) {
				$form.find('input[name=action]').val('commerce/cart/update-cart');
				$form.append($('<input type="hidden" name="redirect" value="' + $form.attr('data-saveshortcut-redirect') + '">'));
			}
			$form.submit();
		});
		$adminOrders.on('click', '.atc', function(e) {
			e.preventDefault();

			var $this = $(this);
			(purchasableId = $this.attr('data-id')), (qty = $adminOrders.find('[name="adminOrderQty[' + purchasableId + ']"]').val());

			if (qty > -1) {
				$orderDetails.addClass('loading');

				$.ajax({
					type: 'POST',
					dataType: 'json',
					headers: {
						'X-CSRF-Token': $('[name="CRAFT_CSRF_TOKEN"]').val()
					},
					url: '',
					data: {
						action: 'commerce-admin-orders/orders/add-to-cart',
						purchasableId: purchasableId,
						adminOrderQty: qty,
						orderId: $('input[name="orderId"]').val()
					},
					success: function(data) {
						$orderDetails.empty();
						$orderDetails.html(data.html);
						$orderDetails.removeClass('loading');

						var notification = '<div class="notification notice">Product added to cart</div>';
						$('#notifications-wrapper #notifications').html(notification);
						$('#notifications-wrapper #notifications .notification')
							.delay(2000)
							.fadeOut(400);

						$.each($('.tableRowInfo'), function() {
							new Craft.Commerce.TableRowAdditionalInfoIcon(this);
						});
					}
				});
			}
		});
		//}
	}
});
