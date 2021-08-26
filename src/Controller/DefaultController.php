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

class DefaultController
{
    private const PAGINATION_INDEX = 10;

    /**
     * @var ViewRenderer
     */
    private ViewRenderer $viewRenderer;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var UrlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @var SenderRepository
     */
    private SenderRepository $senderRepo;

    /**
     * @var SenderCrudService
     */
    private SenderCrudService $senderCrudService;

    /**
     * @var SenderVerifyService
     */
    private SenderVerifyService $senderVerifyService;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param BrandLocatorInterface $brandLocator
     * @param UrlGenerator $urlGenerator
     * @param SenderRepository $senderRepo
     * @param SenderCrudService $senderCrudService
     * @param SenderVerifyService $senderVerifyService
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        BrandLocatorInterface $brandLocator,
        UrlGenerator $urlGenerator,
        SenderRepository $senderRepo,
        SenderCrudService $senderCrudService,
        SenderVerifyService $senderVerifyService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
        $this->senderCrudService = $senderCrudService->withBrand($brandLocator->getBrand());
        $this->senderVerifyService = $senderVerifyService;
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
     * @param ValidatorInterface $validator
     * @param SenderForm $form
     * @return Response
     */
    public function create(Request $request, ValidatorInterface $validator, SenderForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)) {
            $valueObject = SenderValueObject::fromForm($form);
            $sender = $this->senderCrudService->create($valueObject);

            $this->senderVerifyService->verify($sender);

            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/default/index'));
        }

        return $this->viewRenderer->render('create', compact('form'));
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param SenderForm $form
     * @return Response
     */
    public function edit(Request $request, ValidatorInterface $validator, FlashInterface $flash, SenderForm $form): Response
    {
        $body = $request->getParsedBody();
        $senderId = $request->getAttribute('id');
        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $form = $form->withEntity($sender);

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)) {
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
     * @param Request $request
     * @param FlashInterface $flash
     * @return Response
     */
    public function verify(Request $request, FlashInterface $flash): Response
    {
        $senderId = $request->getAttribute('id');
        $verificationToken = $request->getAttribute('token');

        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        if ($sender->isActive()) {
            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/default/index'));
        }

        $result = $this->senderVerifyService
            ->withVerificationToken($verificationToken)
            ->verify($sender);

        if (!$result) {
            return $this->responseFactory->createResponse(404);
        }

        $flash->add(
            'success',
            [
                'body' => 'Email have been verified!',
            ],
            true
        );

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/email/view', ['id' => $sender->getId()]));
    }
}
