<?php

namespace AppBundle\Utils;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

trait LogTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(LifecycleEventArgs $event)
    {
        $this->createdAt = new \DateTime();
    }
}
