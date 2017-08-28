<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 8/26/2017
 * Time: 06:11 PM
 */

namespace waterZooBundle\Entity;


use waterZooBundle\Model\ItemInterface;
use Doctrine\ORM\Mapping as ORM;

abstract class ItemBase implements ItemInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="parentId", type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Tank")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     */
    protected $parentId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    protected function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }


    public function removeFromTank(){
        $this->setParentId(null);
    }

    public function addToTank(Tank $tank){
        $this->setParentId($tank->getId());
    }



}