<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    use LogTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=50)
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(min=10, max=50)
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     *
     * @ORM\Column(name="lastName", type="string", length=30, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\File(maxSize = "4096k", mimeTypes = {"image/png", "image/jpeg"})
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var AccessHistory
     *
     * @ORM\OneToMany(targetEntity="AccessHistory", mappedBy="user")
     */
    private $accessHistory;

    /**
     * @var OperationHistory
     *
     * @ORM\OneToMany(targetEntity="OperationHistory", mappedBy="user")
     */
    private $operationHistory;


    public function __construct()
    {
        parent::__construct();

        $this->accessHistory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->operationHistory = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getFullName(){
        return $this->name.' '.$this->lastName;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return User
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Add accessHistory
     *
     * @param \AppBundle\Entity\AccessHistory $accessHistory
     *
     * @return User
     */
    public function addAccessHistory(\AppBundle\Entity\AccessHistory $accessHistory)
    {
        $this->accessHistory[] = $accessHistory;

        return $this;
    }

    /**
     * Remove accessHistory
     *
     * @param \AppBundle\Entity\AccessHistory $accessHistory
     */
    public function removeAccessHistory(\AppBundle\Entity\AccessHistory $accessHistory)
    {
        $this->accessHistory->removeElement($accessHistory);
    }

    /**
     * Get accessHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccessHistory()
    {
        return $this->accessHistory;
    }

    /**
     * Add operationHistory
     *
     * @param \AppBundle\Entity\OperationHistory $operationHistory
     *
     * @return User
     */
    public function addOperationHistory(\AppBundle\Entity\OperationHistory $operationHistory)
    {
        $this->operationHistory[] = $operationHistory;

        return $this;
    }

    /**
     * Remove operationHistory
     *
     * @param \AppBundle\Entity\OperationHistory $operationHistory
     */
    public function removeOperationHistory(\AppBundle\Entity\OperationHistory $operationHistory)
    {
        $this->operationHistory->removeElement($operationHistory);
    }

    /**
     * Get operationHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperationHistory()
    {
        return $this->operationHistory;
    }
}
