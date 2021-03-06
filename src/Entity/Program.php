<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\ProgramOutput;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *     "get"={"path"="/programs/{id}"},
 *      "put"={"path"="/programs/{id}"},
 *      "delete"={"path"="/programs/{id}"},
 *      "patch"={"path"="/programs/{id}"}
 *     },
 *     collectionOperations={
 *      "post"={"path"="/programs"},
 *      "get"={"path"="/programs"}
 *     },
 *     output=ProgramOutput::class,
 *     normalizationContext={"groups"={"program:read"}},
 *     denormalizationContext={"groups"={"program:write"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"name": "exact"})
 * @ORM\Entity(repositoryClass="App\Repository\ProgramRepository")
 */
class Program
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "author:read", "level:read", "program:write", "program:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"author:read", "level:read", "program:write", "program:read", "programLang:read", "programLang:write"})
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Framework", mappedBy="program")
     * @Groups({"program:read"})
     * @ApiProperty(push=true)
     */
    private $frameworks;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TopicProgrammingLanguage", mappedBy="programmingLanguage", cascade={"persist", "remove"})
     * @Groups ({"programLang:write"})
     */
    private $topic;

    public function __construct()
    {
        $this->frameworks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Framework[]
     */
    public function getFrameworks(): Collection
    {
        return $this->frameworks;
    }

    public function addFramework(Framework $framework): self
    {
        if (!$this->frameworks->contains($framework)) {
            $this->frameworks[] = $framework;
            $framework->setProgram($this);
        }

        return $this;
    }

    public function removeFramework(Framework $framework): self
    {
        if ($this->frameworks->contains($framework)) {
            $this->frameworks->removeElement($framework);
            // set the owning side to null (unless already changed)
            if ($framework->getProgram() === $this) {
                $framework->setProgram(null);
            }
        }

        return $this;
    }

    public function getTopic(): ?TopicProgrammingLanguage
    {
        return $this->topic;
    }

    public function setTopic(TopicProgrammingLanguage $topic): self
    {
        $this->topic = $topic;

        // set the owning side of the relation if necessary
        if ($topic->getProgrammingLanguage() !== $this) {
            $topic->setProgrammingLanguage($this);
        }

        return $this;
    }
}
