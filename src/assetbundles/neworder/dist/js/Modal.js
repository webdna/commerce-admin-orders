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
		this.$newOrder = $('#header .btn.add');

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
