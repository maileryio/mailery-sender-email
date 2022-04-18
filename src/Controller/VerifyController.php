<?php

namespace Mailery\Sender\Email\Controller;

use Mailery\Sender\Repository\SenderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\Brand\BrandLocatorInterface;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Mailery\Sender\Email\Service\SenderVerifyService;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Router\CurrentRoute;

class VerifyController
{
    /**
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param SenderRepository $senderRepo
     * @param SenderVerifyService $senderVerifyService
     * @param BrandLocatorInterface $brandLocator
     */
    public function __construct(
        private ResponseFactory $responseFactory,
        private UrlGenerator $urlGenerator,
        private SenderRepository $senderRepo,
        private SenderVerifyService $senderVerifyService,
        BrandLocatorInterface $brandLocator
    ) {
        $this->senderRepo = $senderRepo->withBrand($brandLocator->getBrand());
    }

    /**
     * @param CurrentRoute $currentRoute
     * @param FlashInterface $flash
     * @return Response
     */
    public function index(CurrentRoute $currentRoute, FlashInterface $flash): Response
    {
        $senderId = $currentRoute->getArgument('id');
        $verificationToken = $currentRoute->getArgument('token');

        if (empty($senderId) || ($sender = $this->senderRepo->findByPK($senderId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        if ($sender->getStatus()->isActive()) {
            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/sender/default/index'));
        }

        $result = $this->senderVerifyService
            ->withVerificationToken($verificationToken)
            ->verify($sender);

        if (!$result) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
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
