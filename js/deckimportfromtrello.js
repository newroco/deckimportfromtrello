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
        OCP.Toast.info('Board import started.');

        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function (data) {
                if(data.boardUrl){
                    OCP.Toast.info('<a href="' + data.boardUrl + '">Board was imported successfully</a>',{'isHTML':true});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                let message = '';
                if(jqXHR.responseJSON &&  jqXHR.responseJSON.message){
                    message = jqXHR.responseJSON.message;
                }
                OCP.Toast.error('Something went wrong: ' + message);
            },
        });
    }
})($, window, document);
