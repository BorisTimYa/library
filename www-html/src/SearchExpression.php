<?php


namespace App;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchExpression
{

    private ParameterBagInterface $param;

    function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }

    public function getExpression(Request $request): string
    {
        if ($search = $request->getContent()) {
            $locale = $request->getLocale();
            $locales = explode('|', $this->param->get('app.supported_locales'));
            foreach ($locales as &$loc) {
                if ($loc == $locale) {
                    $loc = "%$search%";
                } else {
                    $loc = '%';
                }
            }

            return implode('|', $locales);
        }

        return '';
    }

}