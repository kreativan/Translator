# Translator
Translator is ProcessWire module for managing hardcoded and multi-language strings. It will scan front-end files for translatable strings, and add/display them in admin module ui so you can manage them in one place. Module will also detect and create tab for each installed language.

Translator will create text field for each translatable string, but you can also use textarea to add custom translations, one per line like: `translate_this=Translate This`.

Translator can also transform your website in to multi-language with a single click. It will install all required modules and features, and convert all text realated fields to multi-language fieldtypes. There is an option to exclude fields from multi-language conversion...

To activate translator on front-end, you just need to pass translator array to wireLangReplacements() method in your `_init.php` file.

```
$translator = $modules->get('Translator');    
wireLangReplacements($translator->array($user->language->name));
```
