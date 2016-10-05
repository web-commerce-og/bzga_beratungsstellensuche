<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model\ValueObject;


class ImageLink
{

    /**
     * @var string
     */
    private $externalUrl;

    /**
     * ImageLink constructor.
     * @param string $externalUrl
     */
    public function __construct($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }


}