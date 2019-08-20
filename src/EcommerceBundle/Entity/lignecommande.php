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
}
