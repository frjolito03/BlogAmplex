<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * users1
 *
 * @ORM\Table(name="users1")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\users1Repository")
 */
class users1
{

    /**
     * @ORM\OneToMany(targetEntity="post", mappedBy="users1")
     */
    private $postuser;

    public function __construct()
    {
        $this->postuser = new ArrayCollection();
    }




    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="string", unique=true)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=25, unique=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=25, unique=false)
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="redDate", type="datetimetz")
     */
    private $redDate;


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
     * Set userid
     *
     * @param integer $userid
     *
     * @return users1
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return users1
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return users1
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
     * Set password
     *
     * @param string $password
     *
     * @return users1
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }





    /**
     * Set role
     *
     * @param string $role
     *
     * @return users1
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }




    /**
     * Set redDate
     *
     * @param \DateTime $redDate
     *
     * @return users1
     */
    public function setRedDate($redDate)
    {
        $this->redDate = $redDate;

        return $this;
    }

    /**
     * Get redDate
     *
     * @return \DateTime
     */
    public function getRedDate()
    {
        return $this->redDate;
    }









}

