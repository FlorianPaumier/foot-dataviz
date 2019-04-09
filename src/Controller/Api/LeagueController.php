<?php

namespace App\Controller\Api;

use App\Entity\League;
use App\Form\LeagueType;
use App\Model\Representation\Pagination;
use App\Repository\LeagueRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Rest\Route("/league")
 */
class LeagueController extends ApiController
{
    /**
     * @Rest\Get
     * @Rest\View(serializerGroups={"pagination"}, statusCode=Response::HTTP_OK)
     * @Rest\QueryParam(name="sort", default="id", requirements="name|id")
     * @Rest\QueryParam(name="direction", default="asc", requirements="asc|desc")
     * @Rest\QueryParam(name="page", default=1, requirements="\d+")
     * @Rest\QueryParam(name="limit", default=Pagination::DEFAULT_LIMIT, requirements="\d+")
     * @OA\Get(
     *     path="/league",
     *     tags={"League"},
     *     summary=DESCRIPTION_GET_ALL,
     *     @OA\Parameter(in="query", name="sort", description="field on which the sort is done", @OA\Schema(type="string", enum={"name","id"})),
     *     @OA\Parameter(in="query", name="direction", description="direction of the sort", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(in="query", name="page", description="the page to return", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="limit", description="the number of result per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/League")))
     * )
     */
    public function index(LeagueRepository $leagueRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $qb = $leagueRepository->createQueryBuilder("l");

        return Pagination::paginate($qb, $paginator, $paramFetcher);

    }

    /**
     * @Rest\Get("/{id}")
     * @Rest\View(serializerGroups={"pagination"}, statusCode=Response::HTTP_OK)
     * @OA\Get(
     *     path="/league/{id}",
     *     tags={"League"},
     *     summary=DESCRIPTION_GET,
     *     @OA\Parameter(in="path", name="id", description="Ressource Id", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description=DESCRIPTION_RESPONSE_200, @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/League")))
     * )
     */
    public function show(League $league)
    {
        return $league;
    }
}
