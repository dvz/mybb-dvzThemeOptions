// DVZ Theme Options
function ThemeOptions(themeId)
{
    try {
        themeOptionCases = JSON.parse(themeOptionCases);
        this.active = true;
    } catch (e) {
        this.active = false;
    }

    this.setOptionCase = function(option, optionCase)
    {
        // set manually to save name with sqaure brackets
        document.cookie = cookiePrefix + 'theme_options[' + themeId + '][' + option + ']=' + optionCase + '; ' +
            'domain=' + cookieDomain + '; ' +
            'path=' + cookiePath + '; ' +
            'max-age=31536000; ' +
            (cookieSecureFlag == true ? 'secure; ' : '');

        themeOptionCases[option] = optionCase;
    };

    this.getOptionCase = function(option)
    {
        return themeOptionCases[option];
    };

    this.fetchApplyProperties = function(callback)
    {
        $.getJSON(rootpath + '/xmlhttp.php', {
            action: 'theme_options_get_properties',
        }, function (data) {
            $('head link[rel="stylesheet"][data-theme-options="true"]').remove();
            $('head').append(data['stylesheetsRendered']);

            themeOptionCases = data['optionCases'];

            if (typeof callback === 'function') {
                callback(data);
            }
        });
    };
};
