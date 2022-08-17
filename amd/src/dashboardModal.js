define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/ajax', 'core/notification', 'core/str'],
    function ($, ModalFactory, ModalEvents, Ajax, Notification, String) {

        var tokenDeleteTrigger = $('.token_delete_button');
        var functionDeleteTrigger = $('.function_delete_button');
        var webserviceDeleteTrigger = $('.webservice_delete_button');
        var webserviceEnableTrigger = $('.webservice_enable_button');
        var webserviceUpdateFilesTrigger = $('.webservice_uploadfiles_button');
        var webserviceDownloadFilesTrigger = $('.webservice_downloadfiles_button');
        var wwwroot = M.cfg.wwwroot;

        // Tokens modal.
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: String.get_string('dashboard_delete_token_title', 'local_wswizard'),
            body: String.get_string('dashboard_delete_token_body', 'local_wswizard'),
            preShowCallback: function (triggerElement, modal) {
                triggerElement = $(triggerElement);
                // Get id from button class.
                let tokenString = triggerElement[0].classList[0];
                let tokenid = tokenString.substr(tokenString.lastIndexOf('tokenid') + 'tokenid'.length);
                modal.params = {'tokenid': parseInt(tokenid)};
                modal.setSaveButtonText(String.get_string('dashboard_modal_delete', 'local_wswizard'));
            },
            large: true,
        }, tokenDeleteTrigger)
            .done(function (modal) {
                modal.getRoot().on(ModalEvents.save, function (e) {
                    e.preventDefault();

                    $.ajax({
                        type: "POST",
                        url: wwwroot + "/local/wswizard/ajax.php?action=deleteToken&id=" + modal.params['tokenid'],
                        dataType: "json",
                        success: function () {
                            window.location.reload();
                        },
                    });
                });
            });

        // Function modal.
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: String.get_string('dashboard_delete_function_title', 'local_wswizard'),
            body: String.get_string('dashboard_delete_function_body', 'local_wswizard'),
            preShowCallback: function (triggerElement, modal) {
                triggerElement = $(triggerElement);
                // Get id from button class.
                let functionIDString = triggerElement[0].classList[0];
                let functionid = functionIDString
                    .substr(functionIDString.lastIndexOf('functionid') + 'functionid'.length);

                let functionNameString = triggerElement[0].classList[1];
                let functionName = functionNameString.substr(
                    functionIDString.lastIndexOf('functionname') + 'functionname'.length + 1);

                modal.params = {'functionid': parseInt(functionid), 'functionname': functionName};
                modal.setSaveButtonText(String.get_string('dashboard_modal_delete', 'local_wswizard'));
            },
            large: true,
        }, functionDeleteTrigger)
            .done(function (modal) {
                modal.getRoot().on(ModalEvents.save, function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: wwwroot + "/local/wswizard/ajax.php?action=deleteFunction&id=" +
                            modal.params['functionid'] + "&functionname=" + modal.params['functionname'],
                        dataType: "json",
                        success: function () {
                            window.location.reload();
                        },
                    });
                });
            });

        // Webservice modal.
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: String.get_string('dashboard_delete_webservice_title', 'local_wswizard'),
            body: String.get_string('dashboard_delete_webservice_body', 'local_wswizard'),
            preShowCallback: function (triggerElement, modal) {
                triggerElement = $(triggerElement);
                // Get id from button class.
                let webserviceIDString = triggerElement[0].classList[0];
                let webserviceid = webserviceIDString.substr(
                    webserviceIDString.lastIndexOf('webserviceid') + 'webserviceid'.length);

                modal.params = {'webserviceid': parseInt(webserviceid)};
                modal.setSaveButtonText(String.get_string('dashboard_modal_delete', 'local_wswizard'));
            },
            large: true,
        }, webserviceDeleteTrigger)
            .done(function (modal) {
                modal.getRoot().on(ModalEvents.save, function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: wwwroot + "/local/wswizard/ajax.php?action=deleteWebservice&id=" + modal.params['webserviceid'],
                        dataType: "json",
                        success: function (resultData) {
                            window.location.reload();
                        },
                    });
                });
            });

        // Enable/disable webservice.
        webserviceEnableTrigger.parent().on('click',function () {
            let webserviceIDString = $(this).children()[0].classList[0];
            let enablewebserviceid = webserviceIDString.substr(
                webserviceIDString.lastIndexOf('enablewebserviceid') + 'enablewebserviceid'.length);
            $.ajax({
                type: "POST",
                url: wwwroot + "/local/wswizard/ajax.php?action=enableWebservice&id=" + enablewebserviceid,
                dataType: "json",
                success: function () {
                },
            });
        });

        webserviceUpdateFilesTrigger.parent().on('click',function () {
            let webserviceIDString = $(this).children()[0].classList[0];
            let webserviceid = webserviceIDString.substr(
                webserviceIDString.lastIndexOf('updateUploadFileswebserviceid') + 'updateUploadFileswebserviceid'.length);
            $.ajax({
                type: "POST",
                url: wwwroot + "/local/wswizard/ajax.php?action=updateUploadFiles&id=" + webserviceid,
                dataType: "json",
                success: function () {
                },
            });
        });

        // Update Download Files for webservice.
        webserviceDownloadFilesTrigger.parent().on('click',function () {
            let webserviceIDString = $(this).children()[0].classList[0];
            let webserviceid = webserviceIDString.substr(
                webserviceIDString.lastIndexOf('downloadfileswebserviceid') + 'downloadfileswebserviceid'.length);
            $.ajax({
                type: "POST",
                url: wwwroot + "/local/wswizard/ajax.php?action=updateDownloadFiles&id=" + webserviceid,
                dataType: "json",
                success: function () {
                },
            });
        });

    });