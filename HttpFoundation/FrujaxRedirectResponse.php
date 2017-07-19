<?php

namespace Ruvents\FrujaxBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class FrujaxRedirectResponse extends Response
{
    /**
     * @var string
     */
    private $targetUrl;

    /**
     * {@inheritdoc}
     *
     * @param string $url
     */
    public function __construct($url, $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);
        $this->setTargetUrl($url);
    }

    /**
     * @param RedirectResponse $redirect
     *
     * @return static
     */
    public static function fromRedirectResponse(RedirectResponse $redirect)
    {
        $frujax = new static($redirect->getTargetUrl());

        $frujax->setProtocolVersion($redirect->getProtocolVersion());
        $frujax->setCharset($redirect->getCharset());
        $frujax->headers = clone $redirect->headers;
        $frujax->headers->remove('Location');

        // we must set url again after replacing the headers
        $frujax->setTargetUrl($redirect->getTargetUrl());

        return $frujax;
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * @param string $url
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setTargetUrl($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;
        $this->headers->set('Frujax-Redirect-Url', $url);

        return $this;
    }
}
