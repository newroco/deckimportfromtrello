(function ($, window, document) {
    var url = OC.generateUrl('/apps/deckimportfromtrello/');

    $(document).ready(function () {
        if ($('#dir').length > 0) {
            OCA.Files.fileActions.registerAction({
                name: 'deckimportfromtrello',
                displayName: 'Import to Deck',
                mime: 'application/json',
                order: 1,
                permissions: OC.PERMISSION_ALL,
                type: OCA.Files.FileActions.TYPE_DROPDOWN, // @TODO MUST CHECK THIS.
                icon: OC.imagePath('deckimportfromtrello', 'app.svg'),
                actionHandler: function (filename, context) {
                    importFile(context.$file);
                }
            });
        }
    });

    function importFile($file) {
        var data = {
            fileId: $file.attr('data-id'),
        };

        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function (data) {
               alert('An import job was started, you will receive a notification when it\'s done.');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Something went wrong');
            },
        });
    }
})($, window, document);
