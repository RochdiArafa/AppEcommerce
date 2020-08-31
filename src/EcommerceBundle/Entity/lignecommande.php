<?php

namespace EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * lignecommande
 *
 * @ORM\Table(name="lignecommande")
 * @ORM\Entity(repositoryClass="EcommerceBundle\Repository\lignecommandeRepository")
 */
class lignecommande
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="EcommerceBundle\Entity\Commandes", inversedBy="lignecommande")
     * @ORM\JoinColumn(name="Commandes_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $Commandes;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="EcommerceBundle\Entity\Produit", inversedBy="lignecommande")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $Produit;


    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;


    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return lignecommande
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set commandes
     *
     * @param \EcommerceBundle\Entity\Commandes $commandes
     *
     * @return lignecommande
     */
    public function setCommandes(\EcommerceBundle\Entity\Commandes $commandes)
    {
        $this->Commandes = $commandes;

        return $this;
    }

    /**
     * Get commandes
     *
     * @return \EcommerceBundle\Entity\Commandes
     */
    public function getCommandes()
    {
        return $this->Commandes;
    }

    /**
     * Set produit
     *
     * @param \EcommerceBundle\Entity\Produit $produit
     *
     * @return lignecommande
     */
    public function setProduit(\EcommerceBundle\Entity\Produit $produit)
    {
        $this->Produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return \EcommerceBundle\Entity\Produit
     */
    public function getProduit()
    {
        return $this->Produit;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return Produit
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Produit
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
