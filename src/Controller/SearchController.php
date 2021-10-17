<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    const CSRF_KEY = 'search-page';
    const INDEX_ACTION = 'app_search_index';
    const SEARCH_ACTION = 'app_search_pattern_index';

    /**
    */
    #[Route('/home', name: self::INDEX_ACTION)]
    public function index()
    {
        return $this->render('search/index.html.twig');
    }


    /**
     * Saves the posted data.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/search', name: self::SEARCH_ACTION)]
    public function search(Request $request): Response
    {
        return new JsonResponse(['result' => 'success'], 200);
    }
}