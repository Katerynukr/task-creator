<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Title field can not be empty!")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "The title is too short. Minimum length is {{ limit }} characters",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Type(
     *     type="alpha",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Priority field can not be empty!")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "The priority is too short. Minimum length is {{ limit }} characters",
     *      maxMessage = "The priority cannot be longer than {{ limit }} characters"
     * )
     */
    private $priority;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Description field can not be empty!")
     */
    private $note;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     */
    private $status_id;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function setStatusId(int $status_id): self
    {
        $this->status_id = $status_id;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }
}
