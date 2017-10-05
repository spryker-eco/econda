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

    const CATEGORIES = 'categories';
    const PRODUCTS = 'products';

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function categoryAction(Request $request)
    {
        $response = $this->getFacade()->getFileContent(self::CATEGORIES, $this->getLocale($request));

        return $this->streamedResponse(
            function () use ($response) {
                echo $response;
            },
            200,
            ["Content-type" => "text/csv", 'Content-Disposition' => 'attachment; filename="categories.csv"']
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function productAction(Request $request)
    {
        $response = $this->getFacade()->getFileContent(self::PRODUCTS, $this->getLocale($request));

        return $this->streamedResponse(
            function () use ($response) {
                echo $response;
            },
            200,
            ["Content-type" => "text/csv", 'Content-Disposition' => 'attachment; filename="products.csv"']
        );
    }

    /**
     * @return string
     */
    protected function getLocale(Request $request)
    {
        if ($request->query->get('locale') !== null) {
            return $request->query->get('locale');
        }
        return $this->getApplication()['locale'];
    }

}
