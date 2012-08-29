<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityTool
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeQualityTool
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string $path_to_tool
     *
     * @ORM\Column(name="path_to_tool", type="string", length=255)
     */
    private $path_to_tool;

    /**
     * @var string $command
     *
     * @ORM\Column(name="command", type="string", length=255)
     */
    private $command;

    /**
     * @var string $format
     *
     * @ORM\Column(name="format", type="string", length=20)
     */
    private $format;


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
     * Set name
     *
     * @param string $name
     * @return CodeQualityTool
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path_to_tool
     *
     * @param string $pathToTool
     * @return CodeQualityTool
     */
    public function setPathToTool($pathToTool)
    {
        $this->path_to_tool = $pathToTool;

        return $this;
    }

    /**
     * Get path_to_tool
     *
     * @return string
     */
    public function getPathToTool()
    {
        return $this->path_to_tool;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return CodeQualityTool
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return CodeQualityTool
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    public function processFile($diff_code_file, $original_code_file) {

    }
}
