jQuery(document).ready(function ()
{
    jQuery('#cart form .sameaddress input[type=checkbox]').change(function ()
    {
        if (jQuery(this).prop('checked'))
        {
            jQuery('#cart form .shippingaddress').hide();
        }
        else
        {
            jQuery('#cart form .shippingaddress').show();
        }
    });

    jQuery('#cart form .company-link').click(function ()
    {
        jQuery('#cart form .company').toggle('fast');
        return false;
    });

    jQuery('#cart form .sameaddress input[type=checkbox]').change();

});