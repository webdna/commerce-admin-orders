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

if (typeof Craft.Commerce === typeof undefined) {
	Craft.Commerce = {};
}

Craft.Commerce.OrderCreate = Garnish.Base.extend({
	init: function(settings) {
		this.setSettings(settings);

		this.$newOrder = $('<a href="#" class="btn submit">New Order</a>');
		$('#header').append(this.$newOrder);

		var modal = null;

		this.addListener(this.$newOrder, 'click', function(e) {
			e.preventDefault();
			if (modal) {
				modal.show();
			} else {
				modal = new Craft.Commerce.OrderCreateModal({});
			}
		});
	}
});

Craft.Commerce.OrderCreateModal = Garnish.Modal.extend({
	id: null,
	$email: null,
	$productSelect: null,
	$updateBtn: null,
	$cancelBtn: null,
	$orderDetails: null,
	$productList: null,

	init: function(settings) {
		this.id = Math.floor(Math.random() * 1000000000);

		this.setSettings(settings, {
			resizable: false
		});

		var self = this;

		var $container = $('<div class="modal "></div>').appendTo(Garnish.$bod);
		var $iframe = $('<iframe src="' + Craft.getCpUrl('commerce-admin-orders/orders/new') + '" style="width:100%; height:100%;"></iframe>').appendTo($container);

		this.base($container, settings);
	},
	createOrder: function() {
		var data = {
			email: this.$email.find('input').val(),
			userId: this.$userSelect.find('input').val()
		};

		Craft.postActionRequest('commerce-admin-orders/orders/save-order', data, function(response) {
			if (response.success) {
				document.location = '/admin/commerce/orders/' + response.orderId;
			} else {
				alert(response.error);
			}
		});
	}
});

$(document).ready(function() {
	if ($('body').hasClass('commerceordersindex')) {
		new Craft.Commerce.OrderCreate();
	}
	if ($('body').hasClass('commerceordersedit')) {
		var $tab = $('#orderDetailsTab');

		if ($('#order-completionStatus')[0]) {
			$('#main-form').removeAttr('data-confirm-unload');

			$tab.append($('<div id="order-extras" class="flex"><div></div><div><input type="text" name="couponCode" class="text" value="" placeholder="Coupon Code"> <a class="btn submit" data-id="update">Update</a></div></div>'));

			$tab.find('[data-id="update"]').on('click', function(e) {
				e.preventDefault();
				$('.order-details').addClass('loading');
				$('input[name=action]').val('commerce/cart/update-cart');

				$('#main-form')
					.append($('<input type="hidden" name="redirect" value="' + $('#main-form').attr('data-saveshortcut-redirect') + '">'))
					.submit();
			});

			$tab.find('table thead tr').append($('<th scope="col" class="thin"></th>'));

			$('.infoRow').each(function() {
				var $row = $(this),
					$qty = $row.find('td[data-title="Qty"]'),
					$input = $('<input type="number" min="0" name="lineItems[' + $row.attr('data-id') + '][qty]" value="' + Number($qty.text()) + '">');
				$('<td class="thin"><a class="delete icon" data-id="' + $row.attr('data-id') + '" title="Delete" role="button"></a></td>').appendTo($row);

				$qty.empty().append($input);
			});

			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '',
				data: {
					action: 'commerce-admin-orders/orders/get-admin-variants'
				},
				success: function(data) {
					$tab.append(data.html);

					Craft.elementIndex = Craft.createElementIndex('kuriousagency\\commerce\\adminorders\\elements\\AdminVariant', $('#admin-variants'), {
						context: 'index',
						storageKey: 'elementindex.kuriousagency\\commerce\\adminorders\\elements\\AdminVariant',
						criteria: Craft.defaultIndexCriteria
					});
				}
			});

			$.ajax({
				type: 'POST',
				dataType: 'json',
				headers: {
					'X-CSRF-Token': $('[name="CRAFT_CSRF_TOKEN"]').val()
				},
				url: '',
				data: {
					action: 'commerce-admin-orders/orders/get-order-number-by-id',
					orderId: $('input[name="orderId"]').val()
				},
				success: function(data) {
					$('#main-form').append($('<input type="hidden" name="orderNumber" value="' + data.orderNumber + '">'));
				}
			});

			$tab.on('click', '.atc', function(e) {
				e.preventDefault();

				var $this = $(this);
				(purchasableId = $this.attr('data-id')), (qty = $tab.find('[name="adminOrderQty[' + purchasableId + ']"]').val());

				$('.order-details').addClass('loading');

				if (qty > 0) {
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
							$('.order-details').empty();
							$('.order-details').html(data.html);
							$('.order-details').removeClass('loading');

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

			$tab.on('click', '.infoRow a.delete', function(e) {
				e.preventDefault();
				console.log($(this).data('id'));
				$('.order-details').addClass('loading');
				$tab.find('[name="lineItems[' + $(this).data('id') + '][qty]"]').val('0');
				$('input[name=action]').val('commerce/cart/update-cart');
				$('#main-form')
					.append($('<input type="hidden" name="redirect" value="' + $('#main-form').attr('data-saveshortcut-redirect') + '">'))
					.submit();
			});
		}
	}
});
