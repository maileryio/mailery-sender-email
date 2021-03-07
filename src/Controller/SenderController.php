<?php

namespace Mailery\Sender\Email\Controller;

use Mailery\Widget\Search\Form\SearchForm;
use Mailery\Widget\Search\Model\SearchByList;
use Mailery\Sender\Email\Search\SenderSearchBy;
use Mailery\Sender\Email\Filter\SenderFilter;
use Mailery\Sender\Email\Repository\SenderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Brand\BrandLocatorInterface;
use Mailery\Sender\Email\Form\SenderForm;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Http\Method;
use Mailery\Sender\Email\Service\SenderCrudService;

class SenderController
{
    private const PAGINATION_INDEX = 10;

    /**
     * @var SenderRepository
     */
    private SenderRepository $senderRepo;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param BrandLocatorInterface $brandLocator
     * @param SenderRepository $senderRepo
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        BrandLocatorInterface $brandLocator,
        SenderRepository $senderRepo
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);
        $searchBy = $queryParams['searchBy'] ?? null;
        $searchPhrase = $queryParams['search'] ?? null;

        $searchForm = (new SearchForm())
            ->withSearchByList(new SearchByList([
                new SenderSearchBy(),
            ]))
            ->withSearchBy($searchBy)
            ->withSearchPhrase($searchPhrase);

        $filter = (new SenderFilter())
            ->withSearchForm($searchForm);

        $paginator = $this->senderRepo->getFullPaginator($filter)
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->viewRenderer->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $senderId = $request->getAttribute('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('view', compact('sender'));
    }

    /**
     * @param Request $request
     * @param SenderForm $senderForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function create(Request $request, SenderForm $senderForm, UrlGenerator $urlGenerator): Response
    {
        $submitted = $request->getMethod() === Method::POST;

        $senderForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        if ($submitted) {
            $senderForm->loadFromServerRequest($request);

            if (($sender = $senderForm->save()) !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/sender/sender/view', ['id' => $sender->getId()]));
            }
        }

        return $this->viewRenderer->render('create', compact('senderForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param SenderForm $senderForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function edit(Request $request, SenderForm $senderForm, UrlGenerator $urlGenerator): Response
    {
        $senderId = $request->getAttribute('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $senderForm
            ->withSender($sender)
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $senderForm->loadFromServerRequest($request);

            if ($senderForm->save() !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/sender/sender/view', ['id' => $sender->getId()]));
            }
        }

        return $this->viewRenderer->render('edit', compact('sender', 'senderForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param SenderCrudService $senderCrudService
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(Request $request, SenderCrudService $senderCrudService, UrlGenerator $urlGenerator): Response
    {
        $senderId = $request->getAttribute('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $senderCrudService->delete($sender);

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $urlGenerator->generate('/sender/sender/index'));
    }
}
