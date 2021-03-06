<?php

namespace AppBundle\Entity;

use AppBundle\Utils\MeasurementUnit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CreditProduct
 *
 * @ORM\Table(name="credit_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreditProductRepository")
 */
class CreditProduct
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
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min=0.01, max=99999999.99)
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min=1, max=5)
     *
     * @ORM\Column(name="measurement_unit", type="integer")
     */
    private $measurementUnit;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min=0.01, max=99999999.99)
     *
     * @ORM\Column(name="quantity", type="decimal", precision=10, scale=2)
     */
    private $quantity;

    /**
     * @var Product
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="creditProduct")
     */
    private $product;

    /**
     * @var Credit
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Credit", inversedBy="creditProduct")
     */
    private $credit;


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getCredit()->getAmount() < $this->getCredit()->getTotalPaid()) {
            $context
                ->buildViolation('La sumatoriria del costo total de los productos no puede ser menor al monto total pagado.')
                ->addViolation()
            ;
        }
    }

    public function getQuantityUnit() {
        $unit = '';

        switch ($this->getMeasurementUnit()) {
            case 1:
                if (1 === $this->getQuantity()) {
                    $unit = '<span title="Unidad">unidad</span>';
                } else {
                    $unit = '<span title="Unidades">unidades</span>';
                }
                break;
            case 2:
                $unit = '<span title="Kilogramo">kg</span>';
                break;
            case 3:
                $unit = '<span title="Gramo">g</span>';
                break;
            case 4:
                $unit = '<span title="Litro">l</span>';
                break;
            case 5:
                $unit = '<span title="Mililitro">ml</span>';
                break;
        }
        return $this->getQuantity().' '.$unit;
    }

    public function getAmountUnit() {
        $unit = '';

        switch ($this->getMeasurementUnit()) {
            case 1:
                $unit = '<span title="Unidad">unidad</span>';
                break;
            case 2:
                $unit = '<span title="Kilogramo">kg</span>';
                break;
            case 3:
                $unit = '<span title="Kilogramo">kg</span>';
                break;
            case 4:
                $unit = '<span title="Litro">l</span>';
                break;
            case 5:
                $unit = '<span title="Litro">l</span>';
                break;
        }
        return number_format($this->getAmount(), 2).' <span title="Bolivares">Bs.</span>/'.$unit;
    }

    public function getTotalAmountUnit(){

        return number_format($this->getTotalAmount(), 2).' Bs.';
    }

    public function getTotalAmount(){
        $total = $this->getAmount() * MeasurementUnit::resolve($this->quantity, $this->measurementUnit);

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
     * Set amount
     *
     * @param string $amount
     *
     * @return CreditProduct
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
     * Set measurementUnit
     *
     * @param integer $measurementUnit
     *
     * @return CreditProduct
     */
    public function setMeasurementUnit($measurementUnit)
    {
        $this->measurementUnit = $measurementUnit;

        return $this;
    }

    /**
     * Get measurementUnit
     *
     * @return integer
     */
    public function getMeasurementUnit()
    {
        return $this->measurementUnit;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     *
     * @return CreditProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return CreditProduct
     */
    public function setProduct(\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set credit
     *
     * @param \AppBundle\Entity\Credit $credit
     *
     * @return CreditProduct
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
}
