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
                ['processRedirectResponse', 1],
                ['processResponse', 0],
            ],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function processRedirectResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($response instanceof RedirectResponse
            && $this->isFrujaxRequest($request)
            && $request->headers->has('Frujax-Intercept-Redirect')
        ) {
            $frujaxResponse = new FrujaxRedirectResponse($response->getTargetUrl(), $response->headers->all());
            $frujaxResponse->setProtocolVersion($response->getProtocolVersion());
            $frujaxResponse->setCharset($response->getCharset());

            $event->setResponse($frujaxResponse);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function processResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($event->isMasterRequest() && $this->isFrujaxRequest($request)) {
            $response->headers->add(['Frujax-Request-Url' => $request->getRequestUri()]);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->headers->addCacheControlDirective('no-cache', true);
            $response->headers->addCacheControlDirective('no-store', true);
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
