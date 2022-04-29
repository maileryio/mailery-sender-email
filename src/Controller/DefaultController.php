<?php

namespace Mailery\Sender\Email\Controller;

use Mailery\Widget\Search\Form\SearchForm;
use Mailery\Widget\Search\Model\SearchByList;
use Mailery\Sender\Search\SenderSearchBy;
use Mailery\Sender\Filter\SenderFilter;
use Mailery\Sender\Repository\SenderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Brand\BrandLocatorInterface;
use Mailery\Sender\Email\Form\SenderForm;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Mailery\Sender\Email\Service\SenderCrudService;
use Mailery\Sender\Email\Service\SenderVerifyService;
use Yiisoft\Validator\ValidatorInterface;
use Mailery\Sender\Email\ValueObject\SenderValueObject;
use Mailery\Sender\Model\SenderTypeList;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Router\CurrentRoute;

class DefaultController
{
    private const PAGINATION_INDEX = 10;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param SenderRepository $senderRepo
     * @param SenderCrudService $senderCrudService
     * @param SenderVerifyService $senderVerifyService
     * @param BrandLocatorInterface $brandLocator
     */
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ResponseFactory $responseFactory,
        private UrlGenerator $urlGenerator,
        private SenderRepository $senderRepo,
        private SenderCrudService $senderCrudService,
        private SenderVerifyService $senderVerifyService,
        BrandLocatorInterface $brandLocator
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');

        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        $this->senderCrudService = $senderCrudService->withBrand($brandLocator->getBrand());
    }

    /**
     * @param Request $request
     * @param SenderTypeList $senderTypeList
     * @return Response
     */
    public function index(Request $request, SenderTypeList $senderTypeList): Response
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

        return $this->viewRenderer->render('index', compact('searchForm', 'paginator', 'senderTypeList'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @return Response
     */
    public function view(CurrentRoute $currentRoute): Response
    {
        $senderId = $currentRoute->getArgument('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        return $this->viewRenderer->render('view', compact('sender'));
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SenderForm $form
     * @return Response
     */
    public function create(Request $request, ValidatorInterface $validator, SenderForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($request->getMethod() === Method::POST)
            && $form->load($body)
            && isset($body['submit-sender-email-form'])
            && $validator->validate($form)->isValid()
        ) {
            $valueObject = SenderValueObject::fromForm($form);
            $sender = $this->senderCrudService->create($valueObject);

            if (!$this->senderVerifyService->verify($sender)) {
                $this->senderVerifyService->sendVerificationEmail($sender);
            }

            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/default/index'));
        }

        if ($form->getChannel() === null) {
            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/default/create'));
        }

        return $this->viewRenderer->render('create', compact('form'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param SenderForm $form
     * @return Response
     */
    public function edit(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, SenderForm $form): Response
    {
        $body = $request->getParsedBody();
        $senderId = $currentRoute->getArgument('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($sender);

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = SenderValueObject::fromForm($form);
            $this->senderCrudService->update($sender, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('edit', compact('form', 'sender'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(CurrentRoute $currentRoute, UrlGenerator $urlGenerator): Response
    {
        $senderId = $currentRoute->getArgument('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $this->senderCrudService->delete($sender);

        return $this->responseFactory
            ->createResponse(Status::SEE_OTHER)
            ->withHeader(Header::LOCATION, $urlGenerator->generate('/sender/default/index'));
    }

}
