<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un mail valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * Comparaison du champ ci-dessous avec le champ hash
     * 
     * @Assert\EqualTo(propertyPath="hash",message="Les deux mots de passe ne correspondent pas")
     * @var [type]
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Votre numéro de téléphone doit comporter 10 chiffres",
     *      maxMessage = "Votre numéro de téléphone doit comporter 10 chiffres",
     *      allowEmptyString = false)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Length(
     *      min = 5,
     *      max = 5,
     *      minMessage = "Votre code postal doit comporter exactement 5 chiffres",
     *      maxMessage = "Votre code postal doit comporter exactement 5 chiffres",
     *      allowEmptyString = false)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\Length(
     *      max = 40,
     *      maxMessage = "Ce champ 'ville' ne peut pas dépasser 40 caractères",
     *      allowEmptyString = false)
     */
    private $city;

        /**
     * Creation d'une fonction pour permettre d'initialiser le slug (avant la persistance et la maj)
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug()
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname.' '.$this->lastname);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        $roles = $this->userRoles->map(function($role)
        {
            return $role->getTitle();
        })->toArray();

        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {

    }
}
