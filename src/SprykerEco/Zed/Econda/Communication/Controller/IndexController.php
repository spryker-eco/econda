<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Econda\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @method \SprykerEco\Zed\Econda\Business\EcondaFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Econda\Persistence\EcondaQueryContainerInterface getQueryContainer()
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
    public function categoryAction(Request $request): StreamedResponse
    {
        return $this->getResponse($request, static::CATEGORIES);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function productAction(Request $request): StreamedResponse
    {
        return $this->getResponse($request, static::PRODUCTS);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $type
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function getResponse(Request $request, $type): StreamedResponse
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
