<?php

namespace AppBundle\Entity;

use AppBundle\Utils\HistoryResolver;
use Doctrine\ORM\Mapping as ORM;

/**
 * OperationHistory
 *
 * @ORM\Table(name="operation_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationHistoryRepository")
 */
class OperationHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="operationType", type="integer")
     */
    private $operationType;

    /**
     * @var int
     *
     * @ORM\Column(name="targetEntity", type="integer")
     */
    private $targetEntity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="operationHistory")
     */
    private $user;


    public function getDecodedOperationType(){
        return HistoryResolver::decodeOperationType($this->operationType);
    }

    public function getDecodedTargetEntity(){
        return HistoryResolver::decodeTargetEntity($this->targetEntity);
    }

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return OperationHistory
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set operationType
     *
     * @param integer $operationType
     *
     * @return OperationHistory
     */
    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;

        return $this;
    }

    /**
     * Get operationType
     *
     * @return integer
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * Set targetEntity
     *
     * @param integer $targetEntity
     *
     * @return OperationHistory
     */
    public function setTargetEntity($targetEntity)
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    /**
     * Get targetEntity
     *
     * @return integer
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return OperationHistory
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
