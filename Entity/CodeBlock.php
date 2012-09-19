<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

class CodeBlock
{
    /**
     * @var string $begin_line
     */
    private $begin_line;

    /**
     * @var string $end_line
     */
    private $end_line;

    /**
     * @var string $code
     */
    private $code;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set begin_line
     *
     * @param string $beginLine
     * @return CodeBlock
     */
    public function setBeginLine($beginLine)
    {
        $this->begin_line = $beginLine;

        return $this;
    }

    /**
     * Get begin_line
     *
     * @return string
     */
    public function getBeginLine()
    {
        return $this->begin_line;
    }

    /**
     * Set end_line
     *
     * @param string $endLine
     * @return CodeBlock
     */
    public function setEndLine($endLine)
    {
        $this->end_line = $endLine;

        return $this;
    }

    /**
     * Get end_line
     *
     * @return string
     */
    public function getEndLine()
    {
        return $this->end_line;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return CodeBlock
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
