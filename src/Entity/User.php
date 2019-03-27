<?php

namespace App\Entity;

use DateTime;
use Serializable;
use App\Entity\Role;
use DateTimeInterface;
use Cocur\Slugify\Slugify;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Another user already has this email thank you for editing"
 * )
 * @Vich\Uploadable
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "The first name must not be empty"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(
     *      message = "The last name must not be empty"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "Please enter a valid email")
     * 
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="picture")
     * @Assert\File(
    *     maxSize = "5M",
    *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
    *     maxSizeMessage = "The maxmimum allowed file size is 1MB.",
    *     mimeTypesMessage = "Only the filetypes image are allowed."
    * )
     * @var File
     */
    private $pictureFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @Assert\EqualTo(
     *      propertyPath = "hash",
     *      message = "The two passwords are not identical."
     * )
     */
    private $passwordConfirm;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="user", orphanRemoval=true)
     */
    private $quotes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActif;


    public function __construct()
    {
        $this->updatedAt = new DateTime();
        $this->isActif = true;
    }



    public function getId(): ? int
    {
        return $this->id;
    }

    public function getFirstName(): ? string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ? string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ? string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture(): ? string
    {
        return $this->picture;
    }

    public function setPicture(? string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getHash(): ? string
    {
        return $this->hash;
    }

    public function setHash($hash): self
    {
        if ($hash  !== null) {
            $this->hash = $hash;
        } else {
            return $this;
        }
        return $this;
    }

    public function getPasswordConfirm(): ? string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getSlug(): ? string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     */
    public function initialiseSlug()
    {
        if (empty($this->slug)) {
            $slug = new Slugify();
            $this->slug = $slug->slugify($this->firstName . ' ' . $this->lastName);
        }
    }


    /*
     *Les fonctions implementées par l'interface userInterface
     */

    public function getRoles()
    {
        $roles = $this->userRoles->map(function ($role) {
            return $role->getTitle();
        })->toArray();

        $roles[] = "ROLE_USER";

        return $roles;
    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function getSalt()
    { }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    { }

    public function getFullName()
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {

        //if (!$this->userRoles->contains($userRole)) {
        $this->userRoles[] = $userRole;
        $userRole->addUser($this);
        //}

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }



    /**
     * @return Collection|Quote[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
            $quote->setUser($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getUser() === $this) {
                $quote->setUser(null);
            }
        }

        return $this;
    }

    public function getGender(): ? string
    {
        return $this->gender;
    }

    public function setGender(? string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function __toString()
    {
        return $this->getEmail();
    }


    public function setPictureFile(File $image = null)
    {
        $this->pictureFile = $image;

        if ($image) {
            $this->updatedAt = new DateTime();
        }
    }

    public function getPictureFile()
    {
        return $this->pictureFile;
    }

    public function getUpdatedAt(): ? \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $UpdateAt): self
    {
        $this->updatedAt = $UpdateAt;

        return $this;
    }

    public function getPathImageUser()
    {
        //return  $this->getParameter('app.path.user_images') . '/' . $this->getPicture();
        return '/uploads/images/users' . '/' . $this->getPicture();
    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->firstName,
            $this->lastName,
            $this->gender,
            $this->picture,
            $this->updatedAt,
            $this->email,
            $this->hash,
            //$this->pictureFile,
            //$this->picture,
            //$this->userRoles,
            //$this->quotes
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->firstName,
            $this->lastName,
            $this->gender,
            $this->picture,
            $this->updatedAt,
            $this->email,
            $this->hash,
            //$this->pictureFile,
            //$this->picture,
            //$this->userRoles,
            //$this->quotes
        ) =  unserialize($serialized);
    }

    public function getIsActif(): ? bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }
}