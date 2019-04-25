<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity({"name", "productBrand"})
 */
class Product
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
     * @Assert\Length(min=3, max=50)
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var ProductBrand
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="ProductBrand", inversedBy="product")
     */
    private $productBrand;

    /**
     * @var ProductCategory
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="product")
     */
    private $productCategory;

    /**
     * @var CreditProduct
     *
     * @ORM\OneToMany(targetEntity="CreditProduct", mappedBy="product")
     */
    private $creditProduct;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creditProduct = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getCode() {
        return 'PRD_'.str_pad($this->getId(), 5, '0', STR_PAD_LEFT);
    }

    public function getFullProductName() {
        return $this->getCode().': '.$this->name.'  ('.$this->getProductBrand()->getName().')';
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set productBrand
     *
     * @param \AppBundle\Entity\ProductBrand $productBrand
     *
     * @return Product
     */
    public function setProductBrand(\AppBundle\Entity\ProductBrand $productBrand = null)
    {
        $this->productBrand = $productBrand;

        return $this;
    }

    /**
     * Get productBrand
     *
     * @return \AppBundle\Entity\ProductBrand
     */
    public function getProductBrand()
    {
        return $this->productBrand;
    }

    /**
     * Set productCategory
     *
     * @param \AppBundle\Entity\ProductCategory $productCategory
     *
     * @return Product
     */
    public function setProductCategory(\AppBundle\Entity\ProductCategory $productCategory = null)
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * Get productCategory
     *
     * @return \AppBundle\Entity\ProductCategory
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }

    /**
     * Add creditProduct
     *
     * @param \AppBundle\Entity\CreditProduct $creditProduct
     *
     * @return Product
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
}
