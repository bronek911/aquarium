<?php

namespace waterZooBundle\Model;

use waterZooBundle\Entity\Tank;

interface ItemInterface
{

    /**
     * @return int
     */
    public function getId();

    public function addToTank(Tank $tank);

    public function removeFromTank();




}