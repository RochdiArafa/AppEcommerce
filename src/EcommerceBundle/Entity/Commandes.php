<?php

namespace EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle;

/**
 * Commandes
 *
 * @ORM\Table(name="commandes")
 * @ORM\Entity(repositoryClass="EcommerceBundle\Repository\CommandesRepository")
 */
class Commandes
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
     * @ORM\Column(name="datecom", type="date")
     */
    private $datecom;

    /**
     * @var bool
     *
     * @ORM\Column(name="paye", type="boolean")
     */
    private $paye;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id" , onDelete="CASCADE")
     */
    private $User;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set paye
     *
     * @param boolean $paye
     *
     * @return Commandes
     */
    public function setPaye($paye)
    {
        $this->paye = $paye;

        return $this;
    }

    /**
     * Get paye
     *
     * @return bool
     */
    public function getPaye()
    {
        return $this->paye;
    }

    /**
     * Set datecom
     *
     * @param \DateTime $datecom
     *
     * @return Commandes
     */
    public function setDatecom($datecom)
    {
        $this->datecom = $datecom;

        return $this;
    }

    /**
     * Get datecom
     *
     * @return \DateTime
     */
    public function getDatecom()
    {
        return $this->datecom;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Commandes
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->User = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }
}
