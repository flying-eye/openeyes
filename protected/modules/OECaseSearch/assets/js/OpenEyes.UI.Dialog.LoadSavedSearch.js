/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

(function (exports, Util) {
    'use strict';

    // Base Dialog.
    var Dialog = exports;

    function LoadSavedSearchDialog(options) {
        options = $.extend(true, {}, LoadSavedSearchDialog._defaultOptions, options);

        this.userId = options.user_id;
        this.userSearches = options.all_searches;
        Dialog.call(this, options);
    }

    Util.inherits(Dialog, LoadSavedSearchDialog);

    LoadSavedSearchDialog._defaultOptions = {
        destroyOnClose: false,
        title: '',
        popupContentClass: 'oe-popup-content popup-search-query',
        modal: true,
        width: null,
        minHeight: 400,
        maxHeight: 400,
        dialogClass: 'dialog oe-load-saved-search-popup',
        selector: '#load-saved-search-template',
    };

    // selectors for finding and hooking into various of the key elements.
    var selectors = {
        currentUserSearchTemplate: '#all-search-template',
        searchContentTemplate: '#search-contents-template',
        otherUserTemplate: '#other-user-item-template',
        otherUserSearchTemplate: '#other-user-search-item-template',
        otherUserSearchItem: '#other-user-search-list li',
        searchContentItem: '#search-content-template',
        currentUserSearchList: '.searches button.js-show-query',
        deleteSearch: '.searches .trash',
        otherUserList: '#other-user-list li',
        otherUserSearchList: '#other-user-search-list',
        searchContentsList: '.show-query table tbody',
        loadSearchButton: '.searches td button.js-use-query'
    };
    LoadSavedSearchDialog.prototype.selectedSearchId = 0;

    /**
     * Manage all the provided option data into required internal data structures for initialisation.
     */
    LoadSavedSearchDialog.prototype.create = function () {
        var self = this;

        // parent initialisation
        LoadSavedSearchDialog._super.prototype.create.call(self);

        self.setupEventHandlers();
    };

    LoadSavedSearchDialog.prototype.getCurrentUserSearchesList = function() {
        return this.userSearches;
    };

    /**
     *
     * @param options
     * @returns {string}
     */
    LoadSavedSearchDialog.prototype.getContent = function (options) {
        return this.compileTemplate({
            selector: options.selector,
            data: {
                allSearches: this.getCurrentUserSearchesList(),
            }
        });
    };

    /**
     * Setup all the interaction event hooks for clicking and updating form elements in the dialog.
     */
    LoadSavedSearchDialog.prototype.setupEventHandlers = function () {
        var self = this;

        self.content.on('click', selectors.currentUserSearchList, function (e) {
            e.preventDefault();
            self.content.find(selectors.currentUserSearchList).removeClass('selected');
            $(this).addClass('selected');
            self.selectedSearchId = $(this).data('id');
            self.updateSearchContentsList();
        });

        self.content.on('click', selectors.deleteSearch, function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.ajax({
                url: '/OECaseSearch/caseSearch/deleteSearch/' + id,
                success: function() {
                    $(e.target).closest('tr').remove();
                },
                error: function() {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to delete saved search - Unexpected error occurred.'
                    }).open();
                }
            });
        });

        self.content.on('click', selectors.loadSearchButton, function(e) {
            e.preventDefault();
            // Add the search parameters to the screen.
            self.selectedSearchId = $(this).data('id');
            $.ajax({
                url: '/OECaseSearch/caseSearch/loadSearch/' + self.selectedSearchId,
                type: 'GET',
                success: function (response) {
                    // Append the dynamic parameter HTML before the first fixed parameter.
                    let $tableBody = $('#param-list tbody');
                    let $params = $(response).find('tr.parameter');

                    $($tableBody).find('.parameter').remove();
                    //$($tableBody).find('.fixed-parameter').remove();
                    $('#param-list tbody').append($params);

                    // Execute the search.
                    $('form#search-form').submit();
                },
                error: function() {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to load saved search criteria.'
                    }).open();
                },
                complete: $('.oe-popup-wrap').remove()
            });
        });
    };

    LoadSavedSearchDialog.prototype.updateSearchContentsList = function () {
        var self = this;
        $.ajax({
            url: '/OECaseSearch/caseSearch/loadSearch/' + self.selectedSearchId + '?preview=1',
            type: 'GET',
            success: function (response) {
                $(selectors.searchContentsList).empty();
                $(selectors.searchContentsList).html(
                    self.compileTemplate({
                        selector: selectors.searchContentTemplate,
                        data: {
                            searchContents: response,
                        }
                    })
                );
            },
            error: function() {
                new OpenEyes.UI.Dialog.Alert({
                    content: 'Unable to fetch saved search content.'
                }).open();
            }
        });
    };

    LoadSavedSearchDialog.prototype.updateOtherUserSearchesList = function () {
        var self = this;
        $.ajax({
            url: '/OECaseSearch/caseSearch/getSearchesByUser/' + self.selectedUserId,
            type: 'GET',
            success: function (response) {
                self.otherUserSearchItems = JSON.parse(response);
                $(selectors.otherUserSearchList).append(
                    self.compileTemplate({
                        selector: selectors.otherUserSearchTemplate,
                        data: {
                            otherUserSearches: self.otherUserSearchItems,
                        }
                    })
                );
            },
            error: function() {
                new OpenEyes.UI.Dialog.Alert({
                    content: 'Unable to fetch saved searches for selected user.'
                }).open();
            }
        });
    };

    exports.LoadSavedSearch = LoadSavedSearchDialog;
}(OpenEyes.UI.Dialog, OpenEyes.Util));
