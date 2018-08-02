# DVZ Theme Options

Provides variables based on whitelisted cookie parameters to templates and modifies theme properties.

### Requirements

- MyBB 1.8.x
- PHP >= 7.0

### 1. Theme Definitions

Theme-specific options can be placed in a `theme-name-lowercase.json` file in the `inc/plugins/dvz_theme_options/themes/` directory in a JSON format:

```json
{
  "scheme": {
    "light": [],
    "dark": {
      "stylesheets": [
        "dip-in-black.css"
      ],
      "editortheme": "dip-in-black.css"
    }
  },
  "menu-position": {
    "top": [],
    "side": [],
  }
}
```

The first-level keys specify option names, and second-level keys their possible cases (`light` or `dark` for `scheme`, and `top` or `side` for `menu-position`). The value for each case can be either an empty array to simply provide whitelisting for option cases or contain properties that modify theme options:

- _array_ `stylesheets`

  Appends additional stylesheets associated with the current theme to `{$stylesheets}`.

- _string_ `editortheme`

  Name of the editor theme file that overwrites the `{$theme['editortheme']}` variable.

Theme definitions are cached &mdash; after making changes deactivate and activate the plugin (or set `dvzThemeOptions\DEVELOPMENT_MODE` to `1 ` to bypass caching).

### 2. Passing Initial Values to the Client

The plugin compares cookie values against the whitelist and provides a JSON-encoded object, available for templates. The values can passed to JavaScript for further processing by appending the code in the `headerinclude` template (e.g. inside `<script>` with other MyBB variables):

```javascript
	// DVZ Theme Options
	var themeOptionCases = '{$themeOptionCasesJson}';
```
The variable will produce an object similar to `{"scheme":"light","menu-position":"top"}`.



### 3. Managing Options Client-Side

Include the following library in you theme's JavaScript files: [**ThemeOptions.js**](ThemeOptions.js).

**API:**

- _bool_ **active**

  Indicating whether the plugin is activated &mdash; `false` values should cause the theme to hide interface related to switching options. The library's behavior in this condition is undefined.

- _void_ **setOptionCase(** _string_ option , _string_ optionCase **)**

  Sets a new `optionCase` value for `option`.

- _string|undefined_ **getOptionCase(** _string_ option **)**

  Retrieves the case value for specified `option`.

- _void_ **fetchApplyProperties(** [ _function_ callback ] **)**

  Performs an asynchronous request to the server to receive parsed property values passed to optional callback function `callback`:

  - `stylesheets`, an array of appended extra stylesheets,
  - `stylesheetsRendered`, extra `<link>` tags for stylesheets to be updated within the `<head>` section,
  - `editortheme`, the case-overwritten value,
  - `optionCases`, an array of updated case values for specified options.

  The `<link>` stylesheet references are updated automatically once the function is executed.

Cookies in the format similar to `theme_options[theme-name-lowercase][menu-position]` will be modified using `themeOptions.setOptionCase()` and attached to future requests.



Once the document is loaded an object can be initialized with the lowercase theme name (same as the `.json` file) and used:

```javascript
$(function () {
    themeOptions = new ThemeOptions('theme-name-lowercase');

    // make sure the plugin is installed
    if (themeOptions.active) {

        // set scheme to dark
        themeOptions.setOptionCase('scheme', 'dark');

        // repaint the page with appended CSS
        themeOptions.fetchApplyProperties(function(data) {
            console.log('Page painted ' + data['optionCases']['scheme']);
        });
    }
});
```



### 4. Raw Case Values in Templates

The option case values are provided in a globally available `$themeOptionCases` array. Therefore,  an option called `menu-position` can be used in MyBB templates like so:

```html
<div class="menu menu--$themeOptionCases['menu-position']}">
	This is the {$themeOptionCases['menu-position']} menu.
</div>
```
