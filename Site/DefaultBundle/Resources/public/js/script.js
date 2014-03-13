function testPlaceholder()
{
    var test = document.createElement('input');
    if (!('placeholder' in test))
    {
        jQuery('.nolabel label').show();
    }
    else
    {
        jQuery('.nolabel label').each(function (index, item)
        {
            var jItem = jQuery(item);
            if (jItem.hasClass('required'))
            {
                jQuery('#' + jItem.prop('for'), jItem.parents('form')).prop('placeholder', jItem.html().replace(/<\/?[^>]+>/gi, '').replace(/\s{2,}/g, ' ').replace('&nbsp;', ' ').replace('&amp;', '&').trim()
                );
            }
        });
    }
}

jQuery(document).ready(function ()
{
    testPlaceholder();
});