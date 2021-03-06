<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
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
     * @var \DateTime
     *
     * @Assert\NotBlank
     * @Assert\Date
     * @Assert\Range(min="2000-01-01", max="2050-01-01")
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min=0.01, max=99999999.99)
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

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
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Credit", inversedBy="payment")
     */
    private $credit;

    /**
     * @var PaymentMethod
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="PaymentMethod", inversedBy="payment")
     */
    private $paymentMethod;


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getDate() && $this->getCredit()->getDate() > $this->getDate()) {
            $context
                ->buildViolation('La fecha del pago no puede ser menor a la fecha del credito.')
                ->atPath('date')
                ->addViolation()
            ;

            $context
                ->buildViolation('Fecha minima del pago: '.$this->getCredit()->getDate()->format('Y/m/d'))
                ->atPath('date')
                ->addViolation()
            ;
        }

        if ($this->credit->getTotalToPayExcludeId($this->getId()) < $this->getAmount()) {
            $context
                ->buildViolation('La sumatoria de los pagos no puede ser mayor al monto del credito.')
                ->atPath('amount')
                ->addViolation()
            ;
            $context
                ->buildViolation('Monto máximo del pago: '.number_format($this->getCredit()->getTotalToPayExcludeId($this->id), 2).' Bs.')
                ->atPath('amount')
                ->addViolation()
            ;
        }
    }

    public function getCode() {
        return 'PG_'.str_pad($this->getId(), 5, '0', STR_PAD_LEFT);
    }

    public function getAmountUnit() {
        return number_format($this->getAmount(), 2).' Bs.';
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
     * @return Payment
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
     * Set amount
     *
     * @param string $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Payment
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
     * @return Payment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set credit
     *
     * @param \AppBundle\Entity\Credit $credit
     *
     * @return Payment
     */
    public function setCredit(\AppBundle\Entity\Credit $credit = null)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return \AppBundle\Entity\Credit
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set paymentMethod
     *
     * @param \AppBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return Payment
     */
    public function setPaymentMethod(\AppBundle\Entity\PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \AppBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
