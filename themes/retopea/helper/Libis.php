<?php
namespace OmekaTheme\Helper;
use Zend\View\Helper\AbstractHelper;
class Libis extends AbstractHelper
{
    public function getLanguage(){
      $languages = array(
                      'fr' => 'Français',
                      'en'=> 'English',
                      'nl'=> 'Nederlands'
                    );

      $path =  $_SERVER["REQUEST_URI"];
      $parts = explode('/', $path);
      $lang = $parts[2];
      return $lang;
    }

    public function localeSwitcher()
    {
        $output = array();
        array_push($output, '<ul id="locale_switcher">');
        $localeUrl = '';
        $locales = array('fr','tr','en');
        foreach ($locales as $language) {
            array_push($output, '<li>');
            array_push($output, '<a href="' . $localeUrl . '">');
            array_push($output, $language);
            //array_push($output, '<img src="' . $this->view->assetUrl($imageSrc) . '" alt="' . $language . '" />');
            array_push($output, '</a>');
            array_push($output, '</li>');
            }
        array_push($output, '</ul>');
        /*
        $frontController = Zend_Controller_Front::getInstance();
        $locales = $frontController->getParam('locales');
        $request = $frontController->getRequest();
        $baseUrl = $request->getBaseUrl();
        $path = '/' . trim($request->getPathInfo(), '/\\');
        if (count($locales) > 0) {
            $locale = Zend_Registry::get('Zend_Locale');
            $localeLanguage = $locale->getLanguage();
            $defaultLocaleLanguage = array_keys($locale->getDefault());
            $defaultLocaleLanguage = $defaultLocaleLanguage[0];
            array_push($output, '<ul id="locale_switcher">');
            foreach ($locales as $language) {
                /*$imageSrc  = 'img/i18n_';
                $imageSrc .= $language . '_' . ($localeLanguage == $language ? 'on' : 'off');
                $imageSrc .= '.gif';
                */
                /*
                $urlLanguage = $defaultLocaleLanguage == $language
                    ? ''
                    : '/' . $language;
                if (strlen($baseUrl) === 0) {
                    $localeUrl = $urlLanguage . $path;
                } else {
                    $localeUrl = preg_replace('/^' . preg_quote($baseUrl, '/') . '\/?/',
                        $urlLanguage . '/', $path);
                }

                array_push($output, '<li>');
                array_push($output, '<a href="' . $localeUrl . '">');
                array_push($output, $language);
                //array_push($output, '<img src="' . $this->view->assetUrl($imageSrc) . '" alt="' . $language . '" />');
                array_push($output, '</a>');
                array_push($output, '</li>');
            }
            array_push($output, '</ul>');

        }
        */
        return join('', $output);
    }
}
