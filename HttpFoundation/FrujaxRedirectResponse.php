<?php

namespace Ruvents\FrujaxBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class FrujaxRedirectResponse extends Response
{
    /**
     * @var string
     */
    private $targetUrl;

    /**
     * {@inheritdoc}
     */
    public function __construct($url, $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);
        $this->setTargetUrl($url);
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
