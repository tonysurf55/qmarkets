<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Users;
use App\Entity\Regions;
use App\Entity\Departments;
use App\DataObjects\ResponseDTO;

class SearchController extends BaseController
{
    const CSRF_KEY = 'search-page';
    const INDEX_ACTION = 'app_search_index';
    const SEARCH_ACTION = 'app_search_pattern_index';
    const RUN_INDEX_ACTION = 'app_search_run_index';

    /**
     * Load the Search page
     */
    #[Route('/', name: self::INDEX_ACTION)]
    public function index()
    {
        return $this->render('search/index.html.twig', [
            'regions' => $this->getRegions(),
            'departments' => $this->getDepartments()
        ]);
    }

    /**
     * Search action.
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

        $pattern = $request->get('pattern');
        $filter_region = $request->get('region') ?? null;
        $filter_department = $request->get('department') ?? null;

        try {
            /** @var Users $user */
            $user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->searchUsers($pattern, $filter_region, $filter_department);
        }
        catch(\Exception $ex) {
            return $this->handleError(ex: $ex);
        }

        return (new ResponseDTO())
            ->addParam('pattern', $request->get('pattern'))
            ->addParam('users', $user)
            ->response();
    }

    /**
     * Run the stored procedure which create the keywords and scores tables.
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

        try {
            $this->getDoctrine()
                ->getRepository(Users::class)
                ->runIndex();
        }
        catch(\Exception $ex) {
            return $this->handleError(ex: $ex);
        }

        return (new ResponseDTO())->response();
    }

    /**
     * Get the list of regions
     * @return array
     */
    private function getRegions(): array
    {
        try {
            /** @var Regions[] $regions */
            $regions = $this->getDoctrine()
                ->getRepository(Regions::class)
                ->findAll();
        }
        catch(\Exception $ex) {
            return [];
        }

        return $regions;
    }

    /**
     * Get the list of regions
     * @return array
     */
    private function getDepartments(): array
    {
        try {
            /** @var Departments[] $departments */
            $departments = $this->getDoctrine()
                ->getRepository(Departments::class)
                ->findAll();
        }
        catch(\Exception $ex) {
            return [];
        }

        return $departments;
    }
}