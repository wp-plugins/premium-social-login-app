// get trim() working in IE
if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

var loginAppHorizontalSharingProviders;
var loginAppVerticalSharingProviders;

function loginAppCheckElement(arr, obj) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == obj) {
            return true;
        }
    }
    return false;
}

window.onload = function () {
    loginAppAdminUI2();
    loginAppHorizontalSharingProviders = jQuery('[name="LoginApp_settings[horizontal_sharing_providers][]"]');
    loginAppVerticalSharingProviders = jQuery('[name="LoginApp_settings[vertical_sharing_providers][]"]');
    loginAppAdminUI();
};

function making_theme_option_ckeckbox_selected(loginAppSharingTheme, type) {
    for (var key in loginAppSharingTheme) {
        if (loginAppSharingTheme[key].checked) {
            if (type == "horizontal") {
                loginAppToggleHorizontalShareTheme(loginAppSharingTheme[key].value);
                break;
            } else {
                loginAppToggleVerticalShareTheme(loginAppSharingTheme[key].value);
            }
        }
    }

}

function set_default_rearrange_providers(loginAppSharingProviders, type) {
    for (var i = 0; i < loginAppSharingProviders.length; i++) {
        if (loginAppSharingProviders[i].checked) {
            loginAppRearrangeProviderList(loginAppSharingProviders[i], type);
        }
    }
}
function loginAppAdminUI() {
    var loginAppHorizontalSharingTheme = jQuery("input[type='radio'][name='LoginApp_settings[horizontalSharing_theme]']:checked");
    var loginAppVerticalSharingTheme = jQuery("input[type='radio'][name='LoginApp_settings[verticalSharing_theme]']:checked");

    making_theme_option_ckeckbox_selected(loginAppHorizontalSharingTheme, "horizontal");
    making_theme_option_ckeckbox_selected(loginAppVerticalSharingTheme, "vertical");
    // if rearrange horizontal sharing icons option is empty, show seleted icons to rearrange
    if (jQuery('[name="LoginApp_settings[horizontal_rearrange_providers][]"]').length == 0) {
        set_default_rearrange_providers(loginAppHorizontalSharingProviders, 'Horizontal');
    }
    // if rearrange vertical sharing icons option is empty, show seleted icons to rearrange
    if (jQuery('[name="LoginApp_settings[vertical_rearrange_providers][]"]').length == 0) {
        set_default_rearrange_providers(loginAppVerticalSharingProviders, 'Vertical');
    }
    // user activate/deactivate toggle
    var loginAppStatusOption = jQuery('[name="LoginApp_settings[LoginApp_enableUserActivation]"]');
    for (var i = 0; i < loginAppStatusOption.length; i++) {
        if (loginAppStatusOption[i].checked && loginAppStatusOption[i].value == '1') {
            jQuery('#loginAppDefaultStatus').css({
                "display": "table-row"
            });
        } else if (loginAppStatusOption[i].checked && loginAppStatusOption[i].value == '0') {
            jQuery('#loginAppDefaultStatus').hide();
        }
    }
    // email required
    var loginAppEmailRequired = jQuery('[name="LoginApp_settings[LoginApp_dummyemail]"]');
    for (var i = 0; i < loginAppEmailRequired.length; i++) {
        if (loginAppEmailRequired[i].checked && loginAppEmailRequired[i].value == 'notdummyemail') {
            jQuery('#loginAppPopupMessage').show();
            jQuery('#loginAppPopupErrorMessage').show();
        } else if (loginAppEmailRequired[i].checked && loginAppEmailRequired[i].value == 'dummyemail') {
            jQuery('#loginAppPopupMessage').hide();
            jQuery('#loginAppPopupErrorMessage').hide();
        }
    }

    // Registration redirection
    var loginAppRegisterRedirection = jQuery('[name="LoginApp_settings[LoginApp_regRedirect]"]');
    for (var i = 0; i < loginAppRegisterRedirection.length; i++) {
        if (loginAppRegisterRedirection[i].checked) {
            jQuery('#loginAppCustomRegistrationUrl').hide();
            if (loginAppRegisterRedirection[i].value == "custom") {
                jQuery('#loginAppCustomRegistrationUrl').show();
            }

        }
    }
    // Hiding social Login position for registration page, if not enabled
    var registrationFormOption = jQuery('#showonregistrationpageyes');
    if (registrationFormOption) {
        if (registrationFormOption.checked) {
            jQuery('#registration_interface').show();
        } else {
            jQuery('#registration_interface').hide();
        }
    }
    // login redirection
    var loginAppLoginRedirection = jQuery('[name="LoginApp_settings[LoginApp_redirect]"]');
    for (var i = 0; i < loginAppLoginRedirection.length; i++) {
        if (loginAppLoginRedirection[i].checked) {
            jQuery('#loginAppCustomLoginUrl').hide();
            if (loginAppLoginRedirection[i].value == "custom") {
                jQuery('#loginAppCustomLoginUrl').show();
            }

        }
    }
    // logout redirection
    var loginAppLogoutRedirection = jQuery('[name="LoginApp_settings[LoginApp_loutRedirect]"]');
    for (var i = 0; i < loginAppLogoutRedirection.length; i++) {
        if (loginAppLogoutRedirection[i].checked) {
            if (loginAppLogoutRedirection[i].value == "homepage") {
                jQuery('#loginAppCustomLogoutUrl').hide();
            } else if (loginAppLogoutRedirection[i].value == "custom") {
                jQuery('#loginAppCustomLogoutUrl').show();
            }
        }
    }
}

// prepare rearrange provider list
function loginAppRearrangeProviderList(elem, sharingType) {
    var ul = jQuery('#loginRadius' + sharingType + 'Sortable');
    if (elem.checked) {

        var listItem = jQuery('<li />')
            .addClass('lrshare_iconsprite32 lrshare_' + elem.value.toLowerCase())
            .attr({
                id: 'loginRadius' + sharingType + "LI" + elem.value,
                title: elem.value
            });
        // append hidden field
        var provider = jQuery('<input>')
            .attr({
                type: 'hidden',
                name: 'LoginApp_settings[' + sharingType.toLowerCase() + '_rearrange_providers][]',
                value: elem.value
            });
        listItem.append(provider);
        ul.append(listItem);

    } else {
        if (jQuery('#loginRadius' + sharingType + 'LI' + elem.value)) {
            jQuery('#loginRadius' + sharingType + 'LI' + elem.value).remove();
        }
    }
}
// limit maximum number of providers selected in horizontal sharing

function loginAppSharingLimit(elem, key) {
    var sharingProviders = loginAppHorizontalSharingProviders;
    var errorDiv = jQuery('#loginAppHorizontalSharingLimit');
    if (key == 'vertical') {
        sharingProviders = loginAppVerticalSharingProviders;
        var errorDiv = jQuery('#loginAppVerticalSharingLimit');
    }

    var checkCount = 0;
    for (var i = 0; i < sharingProviders.length; i++) {
        if (sharingProviders[i].checked) {
            // count checked providers
            checkCount++;
            if (checkCount >= 10) {
                elem.checked = false;
                errorDiv.show();
                setTimeout(function () {
                    errorDiv.hide();
                }, 2000);
                return;
            }
        }
    }
}

// show/hide options according to the selected horizontal sharing theme
function loginAppToggleHorizontalShareTheme(theme) {

    jQuery('#login_app_horizontal_sharing_providers_container').hide();
    jQuery('#login_app_horizontal_rearrange_container').hide();
    jQuery('#login_app_horizontal_counter_providers_container').hide();
    jQuery('#login_app_horizontal_providers_container').hide();
    var displayArray = [];

    switch (theme) {
        case '32' || '16':
            displayArray[0] = 'login_app_horizontal_rearrange_container';
            displayArray[1] = 'login_app_horizontal_sharing_providers_container';
            displayArray[2] = 'login_app_horizontal_providers_container';
            break;

        case 'counter_vertical' || 'counter_horizontal':
            displayArray[0] = 'login_app_horizontal_counter_providers_container';
            displayArray[1] = 'login_app_horizontal_providers_container';
            break;

        default:
            break;
    }
    for (i = 0; i < displayArray.length; i++) {
        jQuery('#' + displayArray[i]).show();
    }
}

// display options according to the selected counter theme
function loginAppToggleVerticalShareTheme(theme) {

    jQuery('#login_app_vertical_rearrange_container').hide();
    jQuery('#login_app_vertical_sharing_providers_container').hide();
    jQuery('#login_app_vertical_counter_providers_container').hide();

    var displayVerticalArray = [];
    switch (theme) {
        case '32' || '16':
            displayVerticalArray[0] = 'login_app_vertical_rearrange_container';
            displayVerticalArray[1] = 'login_app_vertical_sharing_providers_container';
            break;

        case 'counter_vertical' || 'counter_horizontal':
            displayVerticalArray[0] = 'login_app_vertical_counter_providers_container';
            break;

    }
    for (i = 0; i < displayVerticalArray.length; i++) {
        jQuery('#' + displayVerticalArray[i]).show();
    }
}

// assign update code function onchange event of elements
function loginAppAttachFunction(elems) {
    for (var i = 0; i < elems.length; i++) {
        elems[i].onchange = loginAppToggleTheme;
    }
}

function loginAppGetChecked(elems) {
    var checked = [];
    // loop over all
    for (var i = 0; i < elems.length; i++) {
        if (elems[i].checked) {
            checked.push(elems[i].value);
        }
    }
    return checked;
}
jQuery(document).ready(function () {
    jQuery("#loginAppHorizontalSortable, #loginAppVerticalSortable").sortable({
        revert: true
    });

    function hideAndShowCustomUrlBox(element, inputBoxName) {
        if (element.is(':checked') && element.val() == "custom") {
            jQuery('#' + inputBoxName).show();
        } else {
            jQuery('#' + inputBoxName).hide();
        }
    }

    function display_element(elem, elementToShow) {
        if (elem.is(":checked")) {
            jQuery('#' + elementToShow).show();
        }
    };

    function hide_element(elem, elementToHide) {
        if (elem.is(":checked")) {
            jQuery('#' + elementToHide).hide();
        }
    }

    jQuery(".horizontalCounters").click(function () {
        jQuery("#login_app_horizontal_counter_providers_container,#login_app_horizontal_providers_container").show();
        jQuery("#login_app_horizontal_rearrange_container,#login_app_horizontal_sharing_providers_container").hide();
    });

    jQuery('.horizontalSharingThemesTop').click(function () {
        jQuery("#login_app_horizontal_rearrange_container,#login_app_horizontal_sharing_providers_container,#login_app_horizontal_providers_container").show();
        jQuery("#login_app_horizontal_counter_providers_container").hide();

    });

    jQuery(".horizontalSharingSingle").click(function () {
        jQuery("#login_app_horizontal_providers_container,#login_app_horizontal_rearrange_container").hide();
    });


    jQuery('.verticalSharingThemesTop').click(function () {
        jQuery("#login_app_vertical_rearrange_container,#login_app_vertical_sharing_providers_container").show();
        jQuery("#login_app_vertical_counter_providers_container").hide();
    });

    jQuery("#login_app_sharing_vertical_16").click(function () {
        jQuery("#login_app_vertical_rearrange_container,#login_app_vertical_sharing_providers_container").show();
        jQuery("#login_app_vertical_counter_providers_container").hide();
    });

    jQuery(".verticalCounters").click(function () {
        jQuery("#login_app_vertical_counter_providers_container").show();
        jQuery("#login_app_vertical_rearrange_container,#login_app_vertical_sharing_providers_container").hide();
    });


    jQuery("#show_horizontal_theme_content").click(function () {
        jQuery("#login_app_horizontal").show();
        jQuery("#login_app_vertical").hide();
    });

    jQuery("#show_vertical_theme_content").click(function () {
        jQuery("#login_app_horizontal").hide();
        jQuery("#login_app_vertical").show();
    });


    jQuery('#showonregistrationpageyes').click(function () {
        display_element(jQuery(this), 'registration_interface');

    });
    jQuery('#showonregistrationpageno').click(function () {
        hide_element(jQuery(this), 'registration_interface');

    });
    jQuery('#controlActivationYes').click(function () {
        display_element(jQuery(this), 'loginAppDefaultStatus');

    });
    jQuery('#controlActivationNo').click(function () {
        hide_element(jQuery(this), 'loginAppDefaultStatus');

    });
    jQuery('#dummyMailYes').click(function () {
        jQuery('#loginAppPopupMessage').show();
        jQuery('#loginAppPopupErrorMessage').show();

    });
    jQuery('#dummyMailNo').click(function () {
        jQuery('#loginAppPopupMessage').hide();
        jQuery('#loginAppPopupErrorMessage').hide();

    });


    jQuery('.loginRedirectionRadio').click(function () {
        hideAndShowCustomUrlBox(jQuery(this), 'loginAppCustomLoginUrl');

    });

    jQuery('.registerRedirectionRadio').click(function () {
        hideAndShowCustomUrlBox(jQuery(this), 'loginAppCustomRegistrationUrl');

    });

    jQuery('.logoutRedirectionRadio').click(function () {
        hideAndShowCustomUrlBox(jQuery(this), 'loginAppCustomLogoutUrl');

    });

});