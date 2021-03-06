<?php
namespace Log\Service\Form;

use Interop\Container\ContainerInterface;
use Log\Form\QuickSearchForm;
use Zend\ServiceManager\Factory\FactoryInterface;

class QuickSearchFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new QuickSearchForm(null, $options);
        $urlHelper = $services->get('ViewHelperManager')->get('url');
        $form->setUrlHelper($urlHelper);
        return $form;
    }
}
