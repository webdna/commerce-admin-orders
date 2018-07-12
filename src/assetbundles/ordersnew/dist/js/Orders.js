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

		this.addListener(this.$newOrder, 'click', function(e) {
			e.preventDefault();
			new Craft.Commerce.OrderCreateModal({});
		});
	}
});

Craft.Commerce.OrderCreateModal = Garnish.Modal.extend({
	id: null,
	$email: null,
	$userSelect: null,
	$updateBtn: null,
	$cancelBtn: null,

	init: function(settings) {
		this.id = Math.floor(Math.random() * 1000000000);

		this.setSettings(settings, {
			resizable: false
		});

		var self = this;

		var $form = $('<form class="modal fitted" method="post" accept-charset="UTF-8"/>').appendTo(Garnish.$bod);
		var $body = $('<div class="body"></div>').appendTo($form);
		var $inputs = $('<div class="content">' + '<h2 class="first">' + Craft.t('commerce', 'New Order') + '</h2>' + '</div>').appendTo($body);

		// user select
		this.$userSelect = $('<div id="addUser" class="elementselect"><div class="elements"></div><div class="btn add icon dashed">User</div></div>').appendTo($inputs);

		// email input
		this.$email = $('<div class="field">' + '<div class="heading">' + '<label>' + Craft.t('commerce', 'Email') + '</label>' + '</div>' + '</div>' + '<div class="input ltr">' + '<input type="email" class="text fullwidth" name="email" placeholder="guest email" required>' + '</div>' + '</div>').appendTo($inputs);

		// Error notice area
		this.$error = $('<div class="error"/>').appendTo($inputs);

		// Footer and buttons
		var $footer = $('<div class="footer"/>').appendTo($form);
		var $mainBtnGroup = $('<div class="btngroup right"/>').appendTo($footer);
		this.$cancelBtn = $('<input type="button" class="btn" value="' + Craft.t('commerce', 'Cancel') + '"/>').appendTo($mainBtnGroup);
		this.$updateBtn = $('<input type="button" class="btn submit" value="' + Craft.t('commerce', 'Save') + '"/>').appendTo($mainBtnGroup);

		this.$updateBtn.addClass('disabled');

		new Craft.BaseElementSelectInput({
			id: 'addUser',
			name: 'addUser',
			elementType: 'craft\\elements\\User',
			sources: null,
			criteria: null,
			sourceElementId: null,
			viewMode: 'list',
			limit: 1,
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
		});
		this.base($form, settings);
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
			$tab.append($('<div id="addProduct" class="elementselect"><div class="elements"></div><div class="btn add icon dashed">Add Product</div></div>'));

			new Craft.BaseElementSelectInput({
				id: 'addProduct',
				name: 'addProduct',
				elementType: 'craft\\commerce\\elements\\Variant',
				sources: null,
				criteria: null,
				sourceElementId: null,
				viewMode: 'list',
				limit: null,
				modalStorageKey: null
			});

			$('.infoRow').each(function() {
				var $row = $(this),
					$qty = $row.find('td[data-title="Qty"]'),
					$input = $('<input type="number" min="0" name="qty[' + $row.attr('data-id') + ']" value="' + Number($qty.text()) + '">');

				$qty.empty().append($input);
			});
		}
	}
});
