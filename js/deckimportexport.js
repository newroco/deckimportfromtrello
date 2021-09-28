(function ($, window, document) {
    var url = OC.generateUrl('/apps/deckimportexport/');

    $(document).ready(function () {
        if ($('#dir').length > 0) {
            OCA.Files.fileActions.registerAction({
                name: 'deckimportexport',
                displayName: 'Import to Deck',
                mime: 'application/json',
                order: 1,
                permissions: OC.PERMISSION_ALL,
                type: OCA.Files.FileActions.TYPE_DROPDOWN, // @TODO MUST CHECK THIS.
                icon: OC.imagePath('deckimportexport', 'app.svg'),
                actionHandler: function (filename, context) {
                    importFile(context.$file);
                }
            });
        }
    });

    function importFile($file) {
        var data = {
            id: $file.attr('data-id'),
        };

        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function (data) {
               //
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Something went wrong');
            },
        });
    }
})($, window, document);
