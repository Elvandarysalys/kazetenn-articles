<?php
/*
 * This file is part of the Kazetenn Pages Bundle
 *
 * (c) Gwilherm-Alan Turpin (elvandar.ysalys@protonmail.com) 2022.
 *
 * For more informations about the license and copyright, please view the LICENSE file at the root of the project.
 */

namespace Kazetenn\Articles\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kazetenn\Articles\Repository\ArticleRepository;
use Kazetenn\Pages\Entity\ArticleContent;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    const PAGE_TEMPLATE = '@Kazetenn/article/_block_content_display.twig'; // todo: do something about this

    use TimestampableEntity;
//    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private ?UuidV4 $id;

    /**
     * @var Article[]
     * @ORM\OneToMany(targetEntity="Kazetenn\Entity\Article", mappedBy="parent")
     */
    private $children;

    /**
     * @var Article|null
     * @ORM\ManyToOne(targetEntity="Kazetenn\Entity\Article", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private Article $parent;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @var ArticleContent[]
     * @ORM\OneToMany(targetEntity="Kazetenn\Entity\ArticleContent", mappedBy="article")
     * @ORM\OrderBy({"blocOrder" = "asc"})
     */
    private $pageContents;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private ?string $template;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->pageContents = new ArrayCollection();

        if (null === $this->id) {
            $this->id = Uuid::v4();
        }

        if (null === $this->template){
            $this->template = self::PAGE_TEMPLATE;
        }
    }

    /**
     * @return Collection|ArticleContent[]
     */
    public function getPageContents(): Collection
    {
        return $this->pageContents;
    }

    /**
     * @return ArticleContent[]
     */
    public function getPageContentsOrdered(): array
    {
        $data = $this->pageContents;

        $return = [];
        foreach ($data as $datum){
            if($datum->getParent() === null) {
                $return[$datum->getBlocOrder()][] = $datum;
            }
        }
        return $return;
    }

    public function addPageContent(ArticleContent $pageContent): self
    {
        if (!$this->pageContents->contains($pageContent)) {
            $this->pageContents[] = $pageContent;
            $pageContent->setPage($this);
        }

        return $this;
    }

    public function removePageContent(ArticleContent $pageContent): self
    {
        if ($this->pageContents->removeElement($pageContent)) {
            // set the owning side to null (unless already changed)
            if ($pageContent->getPage() === $this) {
                $pageContent->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return UuidV4|null
     */
    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @return Article|null
     */
    public function getParent(): ?Article
    {
        return $this->parent;
    }

    /**
     * @param Article|null $parent
     */
    public function setParent(?Article $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     */
    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }
}
