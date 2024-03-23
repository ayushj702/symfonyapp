<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Category;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column(type: 'string')]
    private string $name;

    // #[ORM\Column(type: 'text')]
    // private string $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $price;

    //check cascade
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductVariation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $variations;

    #[ORM\ManyToOne(targetEntity: Inventory::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $inventory;

    //remove quantity
    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    public function __construct()
    {
        $this->variations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // public function getDescription(): string
    // {
    //     return $this->description;
    // }

    // public function setDescription(string $description): self
    // {
    //     $this->description = $description;
    //     return $this;
    // }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): self
    {
        $this->inventory = $inventory;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getVariations(): Collection
    {
        return $this->variations;
    }

    public function addVariation(ProductVariation $variation): self
    {
        if (!$this->variations->contains($variation)) {
            $this->variations[] = $variation;
            $variation->setProduct($this);
        }
        return $this;
    }

    public function removeVariation(ProductVariation $variation): self
    {
        if ($this->variations->removeElement($variation)) {
            if ($variation->getProduct() === $this) {
                $variation->setProduct(null);
            }
        }
        return $this;
    }

    public function getTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->variations as $variation) {
            $totalQuantity += $variation->getQuantity();
        }
        return $totalQuantity;
    }
}
