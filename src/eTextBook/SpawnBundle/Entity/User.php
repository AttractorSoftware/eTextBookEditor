<?php

namespace eTextBook\SpawnBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseUser {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**= phenomenon =

= background analysis =

= implementation recommendation =
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
