jQuery(document).ready(function ($) {
    $('.milesweb-toggle input').on('change', function () {
        const isChecked = $(this).is(':checked');
        const setting = $(this).attr('id');

        $.ajax({
            url: mileswebAjax.ajaxUrl,
            method: 'POST',
            data: {
                action: 'milesweb_save_setting',
                nonce: mileswebAjax.nonce,
                setting: setting,
                value: isChecked,
            },
            success: function (response) {
                if (response.success) {
                    location.reload(); // Refresh the page on success
                } else {
                    // alert('Error: ' + response.data.message);
                }
            },
            error: function () {
                // alert('An unexpected error occurred.');
            },
        });
    });
});
