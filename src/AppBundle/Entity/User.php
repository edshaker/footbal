<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User  implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="password", type="string", length=255, unique=true)
     */
    private $password;
    private $password2;
    /**
     * @Assert\True(message = "The password and confirmation password do not match")
     */
    public function isPasswordEqualToConfirmationPassword()
    {
        return ($this->password === $this->password2);
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var string
     * @Assert\Email()
     * @Assert\NotBlank()
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $city;


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
     * Set username
     *
     * @param string $username
     * @return User
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
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Set password and encrypt it for saving in db
     *
     * @param string $password
     * @return User
     */
    public function setPasswordAndEncrypt($password)
    {
        $this->password = sha1($password);

        return $this;
    }
    /**
     * is password correct
     *
     * @param string $password
     * @return User
     */
    public function isPasswordCorrect($password)
    {
        return sha1($password)==$this->password;
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
    public function getPassword2()
    {
        return $this->password2;
    }
    public function setPassword2($password)
    {
        $this->password2 = $password;

        return $this;
    }
    /**
    * add role
    *
    * @return User
    */
    public function addRole($role)
    {
        if(!in_array($role,explode(',',$this->role)))
            $this->role.=','.$role;
        return $this;
    }
    /**
     * remove role
     *
     * @return User
     */
    public function removeRole($role)
    {
        $roles=explode(',',$this->role);
        if(in_array($role,$roles))
        {
            $this->role = '';
            foreach ($roles as $one) {
                if($one!=$role)
                    $this->role.=','.$one;
            }
        };
        return $this;
    }
    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return explode(',',$this->role);
    }
    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
    /**
     * Get Salt
     *
     * @return string
     */
    public function getSalt()
    {
        return null;
    }
    /**
     * erase Credentials
     *
     * @return string
     */
    public function eraseCredentials()
    {
        return true;
    }
}
