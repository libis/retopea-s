<?php
namespace Translations;

use Omeka\Module\AbstractModule;

/**
 * Translations
 *
 * Allows to add specific translations of strings, in particular the hard-coded
 * texts in the theme.
 *
 * @copyright Daniel Berthereau, 2018-2019
 * @license https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
 */
class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
