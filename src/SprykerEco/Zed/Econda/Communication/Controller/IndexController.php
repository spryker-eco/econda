<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Econda\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacadeInterface getFacade()
 */
class IndexController extends AbstractController
{
    protected const CATEGORIES = 'categories';
    protected const PRODUCTS = 'products';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function categoryAction(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->getResponse($request, static::CATEGORIES);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function productAction(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->getResponse($request, static::PRODUCTS);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $type
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function getResponse(Request $request, $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $fileContent = $this->getFacade()->getFileContent($type, $this->getLocale($request));

        return $this->streamedResponse(
            function () use ($fileContent) {
                echo $fileContent;
            },
            200,
            ["Content-type" => "text/csv", 'Content-Disposition' => sprintf("attachment; filename='%s.csv'", $type)]
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function getLocale(Request $request): string
    {
        if ($request->query->get('locale') !== null) {
            return $request->query->get('locale');
        }
        return $this->getApplication()['locale'];
    }
}
