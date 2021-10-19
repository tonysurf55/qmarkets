<?php
namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\DataObjects\ResponseDTO;
use App\Controller\BaseController;

class SearchController extends BaseController
{
    const CSRF_KEY = 'search-page';
    const INDEX_ACTION = 'app_search_index';
    const SEARCH_ACTION = 'app_search_pattern_index';
    const RUN_INDEX_ACTION = 'app_search_run_index';

    /**
    */
    #[Route('/', name: self::INDEX_ACTION)]
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
        if ($errorResponse = $this->validateCsrf(self::CSRF_KEY, $request->get('token'), 11023491013, true)) {
            return $errorResponse;
        }

        try {
            /** @var Users $user */
            $user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->searchUsers();
//                ->findBy(['uid' => 1]);
        }
        catch(\Exception $ex) {
            return $this->handleError(ex: $ex);
        }

        return (new ResponseDTO())
            ->addParam('pattern', $request->get('pattern'))
            ->addParam('user', $user)
            ->response();
    }


    /**
     * Saves the posted data.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/run-index', name: self::RUN_INDEX_ACTION)]
    public function runIndex(Request $request): Response
    {
        if ($errorResponse = $this->validateCsrf(self::CSRF_KEY, $request->get('token'), 11023491014, true)) {
            return $errorResponse;
        }

//        try {
//            /** @var Users $user */
//            $user = $this->getDoctrine()
//                ->getRepository(Users::class)
//                ->searchUsers();
////                ->findBy(['uid' => 1]);
//        }
//        catch(\Exception $ex) {
//            return $this->handleError(ex: $ex);
//        }

        return (new ResponseDTO())
//            ->addParam('pattern', $request->get('pattern'))
//            ->addParam('user', $user)
            ->response();
    }
}