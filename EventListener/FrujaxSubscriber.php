<?php

namespace Ruvents\FrujaxBundle\EventListener;

use Ruvents\FrujaxBundle\HttpFoundation\FrujaxRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FrujaxSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['processRedirect', 1],
                ['addRequestInfo', 0],
            ],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function processRedirect(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($response instanceof RedirectResponse
            && $this->isFrujaxRequest($request)
            && $request->headers->has('Frujax-Intercept-Redirect')
        ) {
            $newResponse = new FrujaxRedirectResponse($response->getTargetUrl());
            $event->setResponse($newResponse);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function addRequestInfo(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($event->isMasterRequest() && $this->isFrujaxRequest($request)) {
            $event->getResponse()->headers->add([
                'Frujax-Request-Url' => $request->getRequestUri(),
            ]);
        }
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isFrujaxRequest(Request $request)
    {
        return $request->isXmlHttpRequest() && $request->headers->get('Frujax');
    }
}
