<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity("ci")
 */
class Client
{
    use LogTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=9, max=10)
     *
     * @ORM\Column(name="ci", type="string", length=15, unique=true)
     */
    private $ci;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\Length(min=5, max=50)
     *
     * @ORM\Column(name="address", type="string", length=50, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @Assert\Email
     * @Assert\Length(min=10, max=50)
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\Length(min=15, max=15)
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min=0.01, max=99999999.99)
     *
     * @ORM\Column(name="creditLimit", type="decimal", precision=10, scale=2)
     */
    private $creditLimit;

    /**
     * @var string
     *
     * @Assert\Length(min=0, max=500)
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Credit
     *
     * @ORM\OneToMany(targetEntity="Credit", mappedBy="client")
     */
    private $credit;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->credit = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getFullName()
    {
        return $this->name.' '.$this->lastName;
    }

    public function getStatus()
    {

        $pendingCredits = 0;
        $expiredCredits = 0;
        $issues = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            if ($credit->isPaid()) {

                if ($credit->isDelayedPayment()) {
                    $issues++;
                }

                continue;
            }


            if (new \DateTime() > $credit->getDeadline()) {
                $expiredCredits++;

                continue;
            }

            $pendingCredits++;
        }

        if (0 !== $expiredCredits) {
            $status = '<span class="label label-danger">En mora</span>';
        } elseif (0 !== $pendingCredits) {
            $status = '<span class="label label-warning">Insolvente</span>';
        } else {
            $status = '<span class="label label-success">Solvente</span>';
        }

        if (0 !== $issues) {
            $status .= '<span class="label label-warning" title="Se han registrado insidencias"><i class="fa fa-warning"></i></span>';
        }

        return $status;
    }

    public function getFlash()
    {
        $flash = '';

        if (0 < $this->countExpiredCredit()) {
            $flash .= '<div class="alert alert-danger alert-dismissible" role="alert"><i class="icon fa fa-ban"></i> El cliente posee créditos vencidos</div>';
        } elseif (0 < $this->countPendingCredit()) {
            $flash .= '<div class="alert alert-warning alert-dismissible" role="alert"><i class="icon fa fa-warning"></i> El cliente posee créditos pendientes</div>';
        }

        if ($this->totalToPaid() < $this->getCreditLimit() && $this->totalToPaid() >= ($this->getCreditLimit() - ($this->getCreditLimit() / 4)) ) {
            $flash .= '<div class="alert alert-info alert-dismissible" role="alert"><i class="icon fa fa-info"></i> El cliente se encuentra cerca de su límite de crédito</div>';
        } elseif ($this->totalToPaid() == $this->getCreditLimit()) {
            $flash .= '<div class="alert alert-warning alert-dismissible" role="alert"><i class="icon fa fa-warning"></i> El cliente  ha alcanzado su límite de crédito</div>';
        } elseif ($this->totalToPaid() > $this->getCreditLimit()) {
            $flash .= '<div class="alert alert-warning alert-dismissible" role="alert"><i class="icon fa fa-warning"></i> El cliente ha superado su límite de credito</div>';
        }

        return $flash;
    }

    public function countTotalCredit()
    {
        return $this->credit->count();
    }

    public function countPaidCredit()
    {
        $total = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            if ($credit->isPaid()) {
                $total++;
            }
        }

        return $total;
    }

    public function countPendingCredit() {
        $total = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            if ($credit->isPending()) {
                $total++;
            }
        }

        return $total;
    }

    public function countExpiredCredit() {
        $total = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            if ($credit->isExpired()) {
                $total++;
            }
        }

        return $total;
    }

    public function totalPaid() {
        $total = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            $total = $credit->getTotalPaid();
        }

        return $total;
    }

    public function totalToPaid() {
        $total = 0;

        /** @var Credit $credit */
        foreach ($this->credit as $credit) {
            $total = $credit->getTotalToPay();
        }

        return $total;
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
     * Set ci
     *
     * @param string $ci
     *
     * @return Client
     */
    public function setCi($ci)
    {
        $this->ci = $ci;

        return $this;
    }

    /**
     * Get ci
     *
     * @return string
     */
    public function getCi()
    {
        return $this->ci;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Client
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
     * @return Client
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
     * Set address
     *
     * @param string $address
     *
     * @return Client
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set creditLimit
     *
     * @param string $creditLimit
     *
     * @return Client
     */
    public function setCreditLimit($creditLimit)
    {
        $this->creditLimit = $creditLimit;

        return $this;
    }

    /**
     * Get creditLimit
     *
     * @return string
     */
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Client
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Client
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Add credit
     *
     * @param \AppBundle\Entity\Credit $credit
     *
     * @return Client
     */
    public function addCredit(\AppBundle\Entity\Credit $credit)
    {
        $this->credit[] = $credit;

        return $this;
    }

    /**
     * Remove credit
     *
     * @param \AppBundle\Entity\Credit $credit
     */
    public function removeCredit(\AppBundle\Entity\Credit $credit)
    {
        $this->credit->removeElement($credit);
    }

    /**
     * Get credit
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCredit()
    {
        return $this->credit;
    }
}
