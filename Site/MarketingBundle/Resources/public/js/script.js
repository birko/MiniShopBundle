jQuery(document).ready(function ()
{
    jQuery('form.contact-form').submit(function () {
        jQuery(this).parent().load(jQuery(this).attr('action'), jQuery(this).serializeArray(), function (data, textStatus, jqXHR) {
            if (testPlaceholder) {
                testPlaceholder();
            }
        });
        return false;
    });
});