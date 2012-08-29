<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeBlock
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeBlock
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $begin_line
     *
     * @ORM\Column(name="begin_line", type="string", length=8)
     */
    private $begin_line;

    /**
     * @var string $end_line
     *
     * @ORM\Column(name="end_line", type="string", length=8)
     */
    private $end_line;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string")
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
