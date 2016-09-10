var app = {

    baseUrl: location.protocol + "//" + location.hostname + (location.port && ":" + location.port) + "/",

    init: function () {
        console.info('app initialized!');

        this.onDocumentLoad();
    },

    onDocumentLoad: function () {
        //app.debug.log('abc', 'log');
        //app.debug.error('error');
        //app.debug.info('info');
        //app.debug.warn('Warn');
    },

    formatAmount: function (amount, commas, precision) {
        var num = parseFloat(amount);
        if (precision !== 'undefined') {
            var num = parseFloat(amount).toFixed(precision);
        }

        if (typeof commas != 'undefined') {
            num = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        return num;
    },

    floatReplace: function ($amount) {
        return $amount ? parseFloat($amount.replace(/,/g, "")) : '';
    },

    ajax: function (url, type, data, onSuccess, onError) {
        jQuery.ajax({
            url: url,
            type: type,
            data: data,
            dataType: 'json',
            beforeSend: function () {
                app.showLoader();
            },
            complete: function () {
                app.hideLoader();
            },
            success: onSuccess,
            error: onError
        });

        return;
    },

    showLoader: function () {
        $('#loader').fadeIn();
        $('body').css('overflow', 'hidden'); // lock scroll bar
        //$.blockUI({message: app.loadingMsg});
    },

    hideLoader: function () {
        $('#loader').fadeOut();
        $('body').css('overflow', 'visible'); // unlock scroll bar
        //$.unblockUI();
    },

    loadingMsg: '<h2>Please wait...</h2>',


    show: function (el) {
        $(el).show();
        return false;
    },

    hide: function (el) {
        $(el).hide();
        return false;
    },

    scrollTo: function (div, offset, speed, callback) {
        var speed = speed || 'slow';
        var offset = offset || '150'; // Default offset of header if any.
        var callback = callback || function () {
            };

        if ($(div).length > 0) {
            $('html, body').stop().animate({scrollTop: ($(div).offset().top - offset)}, speed, 'swing', callback);
            return $(div);
        }
        return false;
    },

};

app.setDebug = function (isDebug) {
    if (isDebug) {
        app.debug = {
            //log: window.console.log.bind(window.console, '%s: %s'),
            log: window.console.log.bind(window.console, 'log: %s'),
            error: window.console.error.bind(window.console, 'error: %s'),
            info: window.console.info.bind(window.console, 'info: %s'),
            warn: window.console.warn.bind(window.console, 'warn: %s')
        };
    } else {
        var __no_op = function () {
        };

        app.debug = {
            log: __no_op,
            error: __no_op,
            warn: __no_op,
            info: __no_op
        }
    }
}

app.setDebug(true);

$(function () {

    app.init();

    //region Datatable
    var dataTableSelector = '.data-table';
    var myTable = $(dataTableSelector).DataTable({
        "order": [
            [1, "desc"]
        ],
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }, {
            "width": "2%",
            "targets": 0
        }, {
            "width": "10%",
            "targets": 2
        }],
        "initComplete": function (settings, json) {
            //app.debug.log('from initComplete' + settings);
        },
        "drawCallback": function (settings) {
            //app.debug.log('from drawCallback' + settings);
        },
        "rowCallback": function (row, data, index) {
            // app.debug.log('from rowCallback' + index);
        },
        "stateLoadParams": function (settings, data) {
            // app.debug.log('from stateLoadParams' + data);
        },
        "footerCallback": function (tfoot, data, start, end, display) {
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        }
    });

    // $(dataTableSelector).each(function () {
    //     app.debug.log($(this).attr('id'));
    // });

    // $('.data-table tbody').on( 'click', 'tr', function () {
    //     if ( $(this).hasClass('selected') ) {
    //         $(this).removeClass('selected');
    //     }
    //     else {
    //         myTable.$('tr.selected').removeClass('selected');
    //         $(this).addClass('selected');
    //     }
    // } );

    /**
     * Download selected files
     *
     * @param  {[type]} e){                     e.preventDefault();               var currentSectionId [description]
     * @return {[type]}      [description]
     */
    $('.btn-download-all').on('click', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> download selected files (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var selectedFiles = [];
        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
            if (this.checked) {
                app.debug.log($(this).val());
                selectedFiles.push($(this).val());
            }
        });

        app.debug.info(selectedFiles);

        if (selectedFiles.length > 0) {
            if (confirm('Are you sure, you want to download selected files?')) {

                app.ajax($('body').data('remote-file'), 'POST', {
                    action: 'download',
                    file: selectedFiles,
                    key: $('body').data('key')
                }, function (response) {

                    app.debug.log('Remote Response ', response, response.data.length);

                    if (response.response === true) {

                        app.ajax($('body').data('source-file'), 'POST', {
                            action: 'download',
                            file: selectedFiles,
                            encrypted_data: response.data
                        }, function (sourceResponse) {
                            app.debug.log('inner ajax res', sourceResponse);

                            if (sourceResponse.response === true) {
                                /** If success hide selected rows */
                                $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
                                    if (this.checked) {
                                        //$(this).parents('tr').fadeOut();
                                        myTable.rows('.selected').remove().draw();
                                        $('#' + sectionId + ' .toggle-checkboxes').prop('checked', false); // uncheck parent checkbox
                                    }
                                });
                            }

                            $('#' + sectionId + ' div.alert-box').show();
                            $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(sourceResponse.msg);
                            $('#' + sectionId + ' div.alert-box').scrollThisTo();

                        });

                    }
                });
            }
        } else {
            alert('Please select some files');
            return false;
        }

    });

    /**
     * Upload selected files
     *
     * @param  {[type]} e){                     e.preventDefault();               var currentSectionId [description]
     * @return {[type]}      [description]
     */
    $('.btn-upload-all').on('click', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> upload selected files (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var selectedFiles = [];
        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
            if (this.checked) {
                app.debug.log($(this).val());
                selectedFiles.push($(this).val());
            }
        });

        if (selectedFiles.length > 0) {
            if (confirm('Are you sure, you want to upload selected files?')) {

                app.ajax($('body').data('source-file'), 'POST', {
                    action: 'upload',
                    file: selectedFiles,
                    key: $('body').data('key')
                }, function (response) {
                    app.debug.log('response ', response);
                    if (response.response === true) {

                        /** If success hide selected rows */
                        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function (a, b) {
                            if (this.checked) {
                                //$(this).parents('tr').fadeOut();
                                myTable.rows('.selected').remove().draw();
                                $('#' + sectionId + ' .toggle-checkboxes').prop('checked', false); // uncheck parent checkbox
                            }
                        });

                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();

                    }
                }, function (xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.response === false) {
                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').addClass('alert-danger').html(response.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();
                    }
                });
            }
        } else {
            alert('Please select some files');
            return false;
        }

    });

    $('.btn-delete-remote-all').on('click', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> delete selected remote files (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var selectedFiles = [];
        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
            if (this.checked) {
                selectedFiles.push($(this).val());
            }
        });

        if (selectedFiles.length > 0) {
            if (confirm('Are you sure, you want to delete selected files from remote?')) {

                app.debug.info(selectedFiles);

                app.ajax($('body').data('remote-file'), 'POST', {
                    action: 'delete-remote',
                    file: selectedFiles,
                    key: $('body').data('key')
                }, function (response) {

                    if (response.response === true) {
                        /** If success hide selected rows */
                        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
                            if (this.checked) {
                                //$(this).parents('tr').fadeOut();
                                myTable.rows('.selected').remove().draw();
                                $('#' + sectionId + ' .toggle-checkboxes').prop('checked', false); // uncheck parent checkbox
                            }
                        });

                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();

                    }

                }, function (xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    console.log(response);
                    if (response.response === false) {
                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').addClass('alert-danger').html(response.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();
                    }
                });

            }
        } else {
            alert('Please select some files');
            return false;
        }
    });

    $('.btn-delete-source-all').on('click', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> delete selected source files (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var selectedFiles = [];
        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
            if (this.checked) {
                selectedFiles.push($(this).val());
            }
        });

        if (selectedFiles.length > 0) {
            if (confirm('Are you sure, you want to delete selected files from source?')) {

                app.debug.info(selectedFiles);

                app.ajax($('body').data('source-file'), 'POST', {
                    action: 'delete-source',
                    file: selectedFiles,
                }, function (response) {

                    if (response.response === true) {
                        /** If success hide selected rows */
                        $.each($('#' + currentSectionId + ' tbody input[type="checkbox"]'), function () {
                            if (this.checked) {
                                //$(this).parents('tr').fadeOut();
                                myTable.rows('.selected').remove().draw();
                                $('#' + sectionId + ' .toggle-checkboxes').prop('checked', false); // uncheck parent checkbox
                            }
                        });

                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();

                    }

                });

            }
        } else {
            alert('Please select some files');
            return false;
        }
    });

    $('.data-table tbody').on('click', '.btn-delete-source', function (e) {
        e.preventDefault();

        var el = $(this);

        if (confirm('Are you sure, you want to delete file from source?')) {
            var currentSectionId = el.parents('div.section').data('id');
            app.debug.info('clicked -> delete a source file (' + currentSectionId + ')');
            var sectionId = currentSectionId.replace('-files', '');

            var file = el.parents('tr').addClass('selected').find('input').val();

            app.ajax($('body').data('source-file'), 'POST', {
                action: 'delete-source',
                file: file,
                key: $('body').data('key')
            }, function (response) {
                if (response.response === true) {
                    //el.parents('tr').fadeOut();
                    myTable.row('.selected').remove().draw(false);

                    $('#' + sectionId + ' div.alert-box').show();
                    $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                    $('#' + sectionId + ' div.alert-box').scrollThisTo();
                }
            });
        }

    });

    $('.data-table tbody').on('click', '.btn-delete-remote', function (e) {
        e.preventDefault();

        var el = $(this);

        if (confirm('Are you sure, you want to delete file from remote?')) {
            var currentSectionId = el.parents('div.section').data('id');
            app.debug.info('clicked -> delete a source file (' + currentSectionId + ')');
            var sectionId = currentSectionId.replace('-files', '');

            var file = el.parents('tr').addClass('selected').find('input').val();

            app.ajax($('body').data('remote-file'), 'POST', {
                action: 'delete-remote',
                file: file,
                key: $('body').data('key')
            }, function (response) {
                if (response.response === true) {
                    //el.parents('tr').fadeOut();
                    myTable.row('.selected').remove().draw(false);

                    $('#' + sectionId + ' div.alert-box').show();
                    $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                    $('#' + sectionId + ' div.alert-box').scrollThisTo();
                }
            }, function (xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                console.log(response);
                if (response.response === false) {
                    $('#' + sectionId + ' div.alert-box').show();
                    $('#' + sectionId + ' div.alert').removeClass('alert-success').addClass('alert-danger').html(response.msg);
                    $('#' + sectionId + ' div.alert-box').scrollThisTo();
                }
            });
        }

    });

    /**
     * Upload a file to the remote-location
     *
     * @param  {[type]} e)        {                                  e.preventDefault();        var currentSectionId [description]
     * @param  {[type]} dataType: 'json'        [description]
     * @param  {[type]} success:  function      (response)    {                                                      app.debug.log('response ', response);                if (response.response [description]
     * @return {[type]}           [description]
     */
    $('.data-table tbody').on('click', '.btn-upload', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> upload a file (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var el = $(this);
        var file = el.parents('tr').addClass('selected').find('input').val();

        app.ajax($('body').data('source-file'), 'POST', {
            action: 'upload', file: file
        }, function (response) {
            app.debug.log('response ', response);
            if (response.response === true) {
                //el.parents('tr').fadeOut();
                myTable.row('.selected').remove().draw(false);

                $('#' + sectionId + ' div.alert-box').show();
                $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(response.msg);
                $('#' + sectionId + ' div.alert-box').scrollThisTo();
            }
        }, function (xhr, status, error) {
            var response = JSON.parse(xhr.responseText);
            if (response.response === false) {
                $('#' + sectionId + ' div.alert-box').show();
                $('#' + sectionId + ' div.alert').addClass('alert-danger').html(response.msg);
                $('#' + sectionId + ' div.alert-box').scrollThisTo();
            }
        });
    });

    $('.data-table tbody').on('click', '.btn-download', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> download a file (' + currentSectionId + ')');
        var sectionId = currentSectionId.replace('-files', '');

        var key = $('body').data('key');
        var el = $(this);
        var file = el.parents('tr').addClass('selected').find('input').val();

        app.ajax($('body').data('remote-file'), 'POST', {
            action: 'download',
            file: file,
            key: key
        }, function (response) {
            app.debug.log('response ', response);

            if (response.response === true) {
                app.ajax($('body').data('source-file'), 'POST', {
                    action: 'download',
                    file: file,
                    encrypted_data: response.data
                }, function (sourceResponse) {
                    app.debug.log('inner ajax res', sourceResponse);

                    if (sourceResponse.response === true) {
                        //el.parents('tr').fadeOut();
                        myTable.row('.selected').remove().draw(false);

                        $('#' + sectionId + ' div.alert-box').show();
                        $('#' + sectionId + ' div.alert').removeClass('alert-danger').addClass('alert-success').html(sourceResponse.msg);
                        $('#' + sectionId + ' div.alert-box').scrollThisTo();
                    }
                });
            }
        });

    });
    //endregion


    /**
     * Copy the current file path into clipboard
     */
    $('.data-table tbody').on('click', '.btn-copy', function (e) {
        e.preventDefault();

        var currentSectionId = $(this).parents('div.section').data('id');
        app.debug.info('clicked -> copy file path (' + currentSectionId + ')');

        var el = $(this);

        // Change the row color which has been copied just to remember
        el.parents('tr').css({
            'background': '#D0FDDC',
            'border-bottom': '1px solid #e9e9e9'
        });

        // Copy the text into clipboard
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(el.parents('tr').find('input').val()).select();
        document.execCommand("copy");
        $temp.remove();
    });

    $('.config').click(function (e) {
        e.preventDefault();
        $('.compare-table').fadeToggle();
    });

    $('a.tooltip').tooltip({
        rounded: true
    });

    $('table').toggleCheckboxes();

    $('[data-toggle="tooltip"]').tooltip();
});