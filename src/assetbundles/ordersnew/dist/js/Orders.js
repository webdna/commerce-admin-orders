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
		var $iframe = $('<iframe src="/admin/commerce-admin-orders/orders/new" style="width:100%; height:100%;"></iframe>').appendTo($container);

		/*var $form = $('<form class="modal elementselectormodal" method="post" accept-charset="UTF-8"/>').appendTo(Garnish.$bod);
		var $body = $('<div class="body"></div>').appendTo($form);
		var $inputs = $('<div class="content">' + '<h2 class="first">' + Craft.t('commerce', 'New Order') + '</h2>' + '</div>').appendTo($body);

		// product select
		this.$productSelect = $('<div id="addProduct" class="elementselect"><div class="elements"></div><div class="btn add icon dashed">Product</div></div>').appendTo($inputs);

		//product list
		this.$orderDetails = $('<div class="order-details pane"></div>');
		this.$productList = $('<table id="" class="data fullwidth collapsible"></table>').appendTo(this.$orderDetails);
		$('<thead><tr><th scope="col">Item</th><th scope="col">Note</th><th scope="col">Price</th><th scope="col">Qty</th><th scope="col"></th><th scope="col"></th><th scope="col"></th></tr></thead>').appendTo(this.$productList);
		$('<tbody></tbody>').appendTo(this.$productList);
*/
		/* {% for lineItem in order.lineItems %}

                    {% set info = [
                        { label: "Description", value: lineItem.description },
                        { label: "Tax Category", value: lineItem.taxCategory.name },
                        { label: "Shipping Category", value: lineItem.shippingCategory.name },
                        { label: "Price", value: lineItem.price|currency(order.currency) },
                        { label: "Sale Amount", value: lineItem.saleAmount|currency(order.currency) },
                        { label: "Sale Price", value: lineItem.salePrice|currency(order.currency) },
                        { label: "Quantity", value: lineItem.qty },
                        { label: "Sub-total", value: lineItem.subtotal|currency(order.currency) },
                        { label: "Total (with adjustments)", value: lineItem.total|currency(order.currency) },
                    ] %}

                    <tr class="infoRow" data-id="{{ lineItem.id }}" data-info="{{ info|json_encode }}">
                        <td data-title="{{ 'Item'|t('commerce') }}">
                            {% if lineItem.purchasable %}
                                {% if lineItem.purchasable.cpEditUrl %}
                                    <a class="purchasable-link"
                                       href="{{ lineItem.purchasable.cpEditUrl }}">{{ lineItem.description }}</a>
                                {% else %}
                                    <span class="description">{{ lineItem.description }}</span>
                                {% endif %}
                            {% else %}
                                <span class="description">{{ lineItem.description }}</span>
                            {% endif %}
                            <br><span class="code">{{ lineItem.sku }}</span>
                            {% if lineItem.options|length %}
                                <a class="fieldtoggle first last"
                                   data-target="info-{{ lineItem.id }}">{{ "Options"|t('commerce') }}</a>
                                <span id="info-{{ lineItem.id }}"
                                      class="hidden">
                                {% for key, option in lineItem.options %}
                                    {{ key|t('commerce') }}: {% if option is iterable %}
                                    <code>{{ option|json_encode|raw }}</code>{% else %}{{ option }}{% endif %}
                                <br>
                                {% endfor %}
                                    </span>
                            {% endif %}
                        </td>
                        <td data-title="{{ 'Note'|t('commerce') }}">
                            {% if lineItem.note %}{{ lineItem.note|nl2br }}{% endif %}
                        </td>
                        <td data-title="{{ 'Price'|t('commerce') }}">
                            {{ lineItem.salePrice|currency(order.currency) }}
                        </td>
                        <td data-title="{{ 'Qty'|t('commerce') }}">
                            {{ lineItem.qty }}
                        </td>
                        <td></td>
                        <td data-title="{{ 'Sub-total'|t('commerce') }}">
                            <span class="right">{{ lineItem.subtotal|currency(order.currency) }}</span>
                        </td>
                        <td>
                                <span class="tableRowInfo" data-icon="info"
                                      href="#"></span>
                        </td>
                    </tr>
                    {% for adjustment in lineItem.adjustments %}
                        <tr>
                            <td></td>
                            <td>
                                <strong>{{ adjustment.type|title|t('commerce') }} {{ "Adjustment"|t('commerce') }}</strong><br>{{ adjustment.name|title }}
                                <span class="info"><strong>{{ adjustment.type|title|t('commerce') }} {{ "Adjustment"|t('commerce') }}</strong><br> {{ adjustment.description }}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="right">{{ adjustment.amount|currency(order.currency) }}</span>
                            </td>
                            <td></td>
                        </tr>
                    {% endfor %}
				{% endfor %} */

		// <tr>
		//     <td></td>
		//     <td></td>
		//     <td></td>
		//     <td></td>
		//     <td><strong>{{ "Items Total (with adjustments)" }}</strong></td>
		//     <td>
		//         <span class="right">{{ order.itemTotal|currency(order.currency) }}</span>
		//     </td>
		//     <td></td>
		// </tr>

		// {% for adjustment in order.orderAdjustments %}
		//     <tr>
		//         <td>
		//             <strong>{{ adjustment.type|title|t('commerce') }} {{ "Adjustment"|t('commerce') }}</strong><br>{{ adjustment.name|title }}
		//             <span class="info"><strong>{{ adjustment.type|title|t('commerce') }} {{ "Adjustment"|t('commerce') }}</strong><br> {{ adjustment.description }}</span>
		//         </td>
		//         <td></td>
		//         <td></td>
		//         <td></td>
		//         <td></td>
		//         <td>
		//             <span class="right">{{ adjustment.amount|currency(order.currency) }}</span>
		//         </td>
		//         <td></td>
		//     </tr>
		// {% endfor %}
		// <tr>
		//     <td></td>
		//     <td>
		//         {% if order.isPaid and order.totalPrice > 0 %}
		//             <div class="paidLogo">
		//                 <span>{{ 'PAID'|t('commerce') }}</div>
		//         {% endif %}
		//     </td>
		//     <td></td>
		//     <td></td>
		//     <td><h2>{{ "Total Price"|t('commerce') }}</h2></td>
		//     <td>
		//         <h2 class="right">{{ order.totalPrice|currency(order.currency) }}</h2>
		//     </td>
		//     <td></td>
		// </tr>

		// email input
		/*this.$email = $('<div class="field">' + '<div class="heading">' + '<label>' + Craft.t('commerce', 'Email') + '</label>' + '</div>' + '</div>' + '<div class="input ltr">' + '<input type="email" class="text fullwidth" name="email" placeholder="guest email" required>' + '</div>' + '</div>').appendTo($inputs);

		// Error notice area
		this.$error = $('<div class="error"/>').appendTo($inputs);

		// Footer and buttons
		var $footer = $('<div class="footer"/>').appendTo($form);
		var $mainBtnGroup = $('<div class="btngroup right"/>').appendTo($footer);
		this.$cancelBtn = $('<input type="button" class="btn" value="' + Craft.t('commerce', 'Cancel') + '"/>').appendTo($mainBtnGroup);
		this.$updateBtn = $('<input type="button" class="btn submit" value="' + Craft.t('commerce', 'Save') + '"/>').appendTo($mainBtnGroup);

		this.$updateBtn.addClass('disabled');

		new Craft.BaseElementSelectInput({
			id: 'addProduct',
			name: 'addProduct',
			elementType: 'craft\\commerce\\elements\\Variant',
			sources: null,
			criteria: null,
			sourceElementId: null,
			viewMode: 'list',
			limit: null,
			modalStorageKey: null,
			onSelectElements: function() {
				self.$email.find('input').prop('disabled', true);
				self.$updateBtn.removeClass('disabled');
				$(window).trigger('resize');
			},
			onRemoveElements: function() {
				self.$email.find('input').prop('disabled', false);
				self.$updateBtn.addClass('disabled');
				$(window).trigger('resize');
			}
		});

		this.addListener(this.$cancelBtn, 'click', 'hide');
		this.addListener(this.$updateBtn, 'click', function(ev) {
			ev.preventDefault();
			if (!$(ev.target).hasClass('disabled')) {
				this.createOrder();
			}
		});
		this.addListener(this.$email.find('input'), 'input', function(e) {
			self.$updateBtn.removeClass('disabled');
		});*/
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
