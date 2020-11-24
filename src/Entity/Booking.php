<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="BookingId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $personsNumber;

    /**
     * @ORM\OneToOne(targetEntity=Comment::class, mappedBy="booking", cascade={"persist", "remove"})
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPersonsNumber(): ?int
    {
        return $this->personsNumber;
    }

    public function setPersonsNumber(int $personsNumber): self
    {
        $this->personsNumber = $personsNumber;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        // set (or unset) the owning side of the relation if necessary
        $newBooking = null === $comment ? null : $this;
        if ($comment->getBooking() !== $newBooking) {
            $comment->setBooking($newBooking);
        }

        return $this;
    }
}
