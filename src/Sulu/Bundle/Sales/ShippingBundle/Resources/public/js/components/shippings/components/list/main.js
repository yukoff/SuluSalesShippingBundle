/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

define(['sulusalesshipping/util/sidebar'], function(Sidebar) {

    'use strict';

    var bindCustomEvents = function() {
            // navigate to edit contact
            this.sandbox.on('husky.datagrid.item.click', function(id) {
                // get data for sidebar via controller
                this.sandbox.emit('salesorder.orders.sidebar.getData', {
                    data: id,
                    callback: function(contact, account) {
                        this.sandbox.emit(
                            'sulu.sidebar.set-widget',
                            '/admin/widget-groups/order-info?contact=' + contact + '&account=' + account
                        );
                    }.bind(this)
                });
            }, this);

            // delete clicked
            this.sandbox.on('sulu.list-toolbar.delete', function() {
                this.sandbox.emit('husky.datagrid.items.get-selected', function(ids) {
                    this.sandbox.emit('sulu.salesshipping.shipping.delete', ids);
                }.bind(this));
            }, this);

            // add clicked
            this.sandbox.on('sulu.list-toolbar.add', function() {
                this.sandbox.emit('sulu.salesshipping.shipping.new');
            }, this);
        },

        datagridAction = function(id) {
            this.sandbox.emit('sulu.salesshipping.shipping.load', id);
        },

        datagridClick = function(id) {
            this.sandbox.emit('sulu.salesshipping.shipping.sidebar.load', id);
        };

    return {
        view: true,

        layout: {
            content: {
                width: 'max'
            },
            sidebar: {
                width: 'fixed',
                cssClasses: 'sidebar-padding-50'
            }
        },

        header: {
            title: 'salesshipping.shippings.title',
            noBack: true,
            breadcrumb: [
                {title: 'navigation.sales'},
                {title: 'salesshipping.shippings.title'}
            ]
        },

        templates: ['/admin/shipping/template/shipping/list'],

        initialize: function() {
            this.render();
            bindCustomEvents.call(this);
        },

        render: function() {
            this.sandbox.dom.html(this.$el, this.renderTemplate('/admin/shipping/template/shipping/list'));

            // init list-toolbar and datagrid
            this.sandbox.sulu.initListToolbarAndList.call(this, 'shippingsFields', '/admin/api/shippings/fields',
                {
                    el: this.$find('#list-toolbar-container'),
                    instanceName: 'shippings',
                    inHeader: true,
                    template: []
                },
                {
                    el: this.sandbox.dom.find('#shippings-list', this.$el),
                    url: '/admin/api/shippings?flat=true',
                    searchInstanceName: 'shippings',
                    searchFields: ['number', 'account', 'contact'],
                    resultKey: 'shippings',
                    actionCallback: datagridAction.bind(this),
                    clickCallback: datagridClick.bind(this)
                }
            );

            Sidebar.initForList(this.sandbox);
        }
    };
});
