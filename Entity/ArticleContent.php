<?php
/*
 * This file is part of the Kazetenn Pages Bundle
 *
 * (c) Gwilherm-Alan Turpin (elvandar.ysalys@protonmail.com) 2022.
 *
 * For more informations about the license and copyright, please view the LICENSE file at the root of the project.
 */

namespace Kazetenn\Pages\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kazetenn\Articles\Entity\Article;
use Kazetenn\Articles\Repository\ArticleContentRepository;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=ArticleContentRepository::class)
 */
class ArticleContent
{
    const ROW_TEMPLATE     = '@Kazetenn/articles/_block_content_display.twig';
    const HORIZONTAL_ALIGN = 'horizontal';
    const VERTICAL_ALIGN   = 'vertical';

    use TimestampableEntity;
//    use BlameableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private ?UuidV4 $id;

    /**
     * @var Article|null
     * @ORM\ManyToOne(targetEntity="Kazetenn\Entity\Article", inversedBy="pageContents")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private ?Article $page;

    /**
     * @var ArticleContent|null
     * @ORM\ManyToOne(targetEntity="Kazetenn\Entity\ArticleContent", inversedBy="childrens")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private ?ArticleContent $parent;

    /**
     * @var ArticleContent[]|null
     * @ORM\OneToMany(targetEntity="Kazetenn\Entity\ArticleContent", mappedBy="parent")
     * @ORM\JoinColumn(name="children_id", referencedColumnName="id")
     */
    private $childrens;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $template = self::ROW_TEMPLATE;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $blocOrder;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $align = self::VERTICAL_ALIGN;

    public function __construct()
    {
        if (null === $this->id) {
            $this->id = Uuid::v4();
        }
        if (null === $this->template) {
            $this->template = self::ROW_TEMPLATE;
        }

        $this->childrens = new ArrayCollection();
    }

    public function addChildren(ArticleContent $pageContent): self
    {
        if (!$this->childrens->contains($pageContent)) {
            $this->childrens[] = $pageContent;
            $pageContent->setParent($this);
        }

        return $this;
    }

    public function removeChildren(ArticleContent $pageContent): self
    {
        if ($this->childrens->removeElement($pageContent)) {
            if ($pageContent->getPage() === $this) {
                $pageContent->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return ?UuidV4
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
     * @return Article|null
     */
    public function getPage(): ?Article
    {
        return $this->page;
    }

    /**
     * @param Article $page
     */
    public function setPage(Article $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getBlocOrder(): int
    {
        return $this->blocOrder;
    }

    /**
     * @param int $blocOrder
     */
    public function setBlocOrder(int $blocOrder): void
    {
        $this->blocOrder = $blocOrder;
    }

    /**
     * @return ArticleContent|null
     */
    public function getParent(): ?ArticleContent
    {
        return $this->parent;
    }

    /**
     * @param ArticleContent|null $parent
     */
    public function setParent(?ArticleContent $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getAlign(): string
    {
        return $this->align;
    }

    /**
     * @param string $align
     */
    public function setAlign(string $align): void
    {
        $this->align = $align;
    }

    /**
     * @return ArticleContent[]|null
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @return ArticleContent[]
     */
    public function getChildrensOrdered(): array
    {
        /** @var ArticleContent[] $data */
        $data = $this->childrens;

        $return = [];
        foreach ($data as $datum) {
            $return[$datum->getBlocOrder()] = $datum;
        }
        return $return;
    }

    /**
     * @param ArticleContent|null $childrens
     */
    public function setChildrens(?ArticleContent $childrens): void
    {
        $this->childrens = $childrens;
    }
}
