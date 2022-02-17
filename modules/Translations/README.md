Translations (module for Omeka S)
=================================

[Translations] is a module for [Omeka S] that allows to add specific
translations of strings, in particular the hard-coded texts in the theme.

In Omeka, the translations are managed with `.po` files in the directory `application/language/`
for the core and in  the directory `language/` of each enabled module. This
module works the same, but you can add any strings yourself in the directory `language/`
of this module, so they will be translated in Omeka.

The translations themselves can be created with a tool like [poedit], or [lokalize],
or any other compliant software or online service.

This is an upgrade of the [plugin Translations] for [Omeka Classic].


Installation
------------

Uncompress files and rename module folder `Translations`.

See general end user documentation for [installing a module].


Usage
-----

### Preparation of templates

Simply update the translations files in the directory `language/` of the
module: the main one `template.pot` and each translation like `fr.po` and `fr.mo`.
You can add any language, but respect the names of the files (see `application/language/`)
and copy the two files `.po` and `.mo` for each language.

Quick hack to create the base `template.pot` file automatically:

- copy your theme inside the directory `modules`;
- then run this command from the Omeka root (some packages like `xgettext` should
  be installed first: see Omeka documentation):
```sh
# Create main template.
gulp i18n:module:template --module-name=my-theme

# If your editor can’t compile .po files, you can run this command after translating:
gulp i18n:module:compile --module-name=my-theme
```
- then copy all the files inside `language` into the directory `language` of
  this module;
- finally, remove the theme from the directory `modules`.

Warning: this process overrides existing templates, so save them first.

### Activation

In some cases, a cache may be used. In that case, restart the web server or
simply remove all files starting with `omeka_i18n_cache` in the temp directory
of the web server, usually `/tmp`, or `/tmp/systemd-private-xxx/tmp`. Or wait
some hours for the automatic refresh of the translations.

Anyway, when the translations are updated, you should uninstall and reinstall
the plugin in the config panel of Omeka. The translations will be automatically
available.

### Use of translations

Of course, all strings should be called via the controller plugin or the view
helper `translate`:
```php
// Example in a view (phtml file).
echo $this->translate('my text');

// Or for better performance:
$translate = $this->plugin('translate');
echo $translate('my text');
```


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitHub.


License
-------

This module is published under the [CeCILL v2.1] licence, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.


Copyright
---------

* Copyright Daniel Berthereau, 2018-2019 (see [Daniel-KM] on GitHub)


[Translations]: https://github.com/Daniel-KM/Omeka-S-module-Translations
[Omeka S]: https://omeka.org/s
[plugin Translations]: https://github.com/Daniel-KM/Omeka-plugin-Translations
[Omeka Classic]: https://omeka.org/classic
[poedit]: https://poedit.net
[lokalize]: https://www.kde.org/applications/development/lokalize
[plugin issues]: https://github.com/Daniel-KM/Omeka-plugin-Translations/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
