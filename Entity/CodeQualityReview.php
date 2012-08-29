<?php

namespace Hostnet\HostnetCodeQualityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hostnet\HostnetCodeQualityBundle\Entity\CodeQualityReview
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CodeQualityReview
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
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var string $file_name
     *
     * @ORM\Column(name="file_name", type="string", length=50)
     */
    private $file_name;

    /**
     * @var double $total_grade
     *
     * @ORM\Column(name="total_grade", type="double")
     */
    private $total_grade;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $created_at;


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
     * Set user_id
     *
     * @param integer $userId
     * @return CodeQualityReview
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     * @return CodeQualityReview
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set total_grade
     *
     * @param double $totalGrade
     * @return CodeQualityReview
     */
    public function setTotalGrade($totalGrade)
    {
        $this->total_grade = $totalGrade;

        return $this;
    }

    /**
     * Get total_grade
     *
     * @return double
     */
    public function getTotalGrade()
    {
        return $this->total_grade;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return CodeQualityReview
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
