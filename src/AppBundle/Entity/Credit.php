<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Credit
 *
 * @ORM\Table(name="credit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreditRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Credit
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
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="date")
     */
    private $deadline;

    /**
     * @var string
     *
     * @Assert\Length(min=0, max=500)
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Client
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="credit")
     */
    private $client;

    /**
     * @var CreditProduct
     *
     * @ORM\OneToMany(targetEntity="CreditProduct", mappedBy="credit", cascade={"all"})
     */
    private $creditProduct;

    /**
     * @var Payment
     *
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="credit")
     */
    private $payment;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creditProduct = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payment = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getCode() {
        return 'CRD_'.str_pad($this->getId(), 5, '0', STR_PAD_LEFT);
    }

    public function getAmount(){
        $total = 0;

        /** @var CreditProduct $item */
        foreach ($this->creditProduct as $item){
            $total += $item->getTotalAmount();
        }

        return $total;
    }

    public function getAmountUnit(){
        return number_format($this->getAmount(), 2).' Bs.';
    }

    public function getTotalPaid(){
        $total = 0;

        /** @var Payment $item */
        foreach ($this->payment as $item){
            $total += $item->getAmount();
        }

        return $total;
    }

    public function getTotalToPay(){

        return $this->getAmount() - $this->getTotalPaid();
    }

    public function getTotalToPayExcludeId($id = null){
        $total = 0;

        /** @var Payment $item */
        foreach ($this->payment as $item){
            if ($item->getId() === $id) {
                continue;
            }

            $total += $item->getAmount();
        }

        return $this->getAmount() - $total;
    }

    public function isDelayedPayment(){
        /** @var Payment $payment */
        foreach ($this->payment as $payment) {
            if ($payment->getDate() > $this->deadline) {
                return true;
            }
        }

        return false;
    }

    public function isPaid()
    {
        if ($this->getTotalPaid() == $this->getAmount()) {
            return true;
        }

        return false;
    }

    public function isExpired()
    {
        if ($this->isPaid()) {
            return false;
        }

        if (new \DateTime() > $this->deadline) {
            return true;
        }

        return false;
    }


    public function getStatus()
    {
        $status = '';
        $amount = $this->getAmount();
        $totalPaid = $this->getTotalPaid();
        $expired = $this->isDelayedPayment();

        if (0 === $this->getCreditProduct()->count() && 0 === $this->getPayment()->count()) {
            $status = '<span class="label label-warning" title="Credito sin asignar productos">Incompleto</span>';

        } elseif ($totalPaid > $amount) {
            $status = '<span class="label label-danger" title="El monto pagado es superior al monto acreditado">Error</span>';
        } elseif ($totalPaid == $amount) {
            $status = '<span class="label label-success" title="Credito pagado">Pagado</span>';
            if ($expired) {
                $status .= '<span class="label label-warning" title="Se ha registrado mora durante el credito"><i class="fa fa-warning"></i></span>';
            }
        } elseif ($totalPaid < $amount) {
            if ($expired || new \DateTime() > $this->deadline) {
                $status = '<span class="label label-danger" title="Saldo vencido de '.number_format($amount - $totalPaid, 2).' Bs">Vencido</span>';
            } else {
                $status = '<span class="label label-warning" title="Saldo perdiente de '.number_format($amount - $totalPaid, 2).' Bs">Pendiente</span>';
            }
        }

        return $status;
    }

    public function getProgress()
    {
        $percentage = 0;

        if (0 < $this->getAmount()) {
            $percentage = ($this->getTotalPaid() * 100) / $this->getAmount();
        }

        $progress = '<div class="progress" title="Pagado: '.number_format($this->getTotalPaid(), 2).' Bs / '.number_format($this->getAmount(), 2).' Bs">
                <div class="progress-bar progress-bar-green" role="progressbar" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percentage.'%">
                  <span class="sr-only">'.$percentage.'% Completado</span>
                </div>
              </div>';

        return $progress;
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
     * @return Credit
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
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Credit
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Credit
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Credit
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add creditProduct
     *
     * @param \AppBundle\Entity\CreditProduct $creditProduct
     *
     * @return Credit
     */
    public function addCreditProduct(\AppBundle\Entity\CreditProduct $creditProduct)
    {
        $this->creditProduct[] = $creditProduct;

        return $this;
    }

    /**
     * Remove creditProduct
     *
     * @param \AppBundle\Entity\CreditProduct $creditProduct
     */
    public function removeCreditProduct(\AppBundle\Entity\CreditProduct $creditProduct)
    {
        $this->creditProduct->removeElement($creditProduct);
    }

    /**
     * Get creditProduct
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditProduct()
    {
        return $this->creditProduct;
    }

    /**
     * Add payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return Credit
     */
    public function addPayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payment[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \AppBundle\Entity\Payment $payment
     */
    public function removePayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payment->removeElement($payment);
    }

    /**
     * Get payment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
