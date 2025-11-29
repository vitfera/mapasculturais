<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventFavorite
 *
 * @ORM\Table(name="event_favorite")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class EventFavorite extends \MapasCulturais\Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="event_favorite_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent", fetch="LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $agent;

    /**
     * @var \MapasCulturais\Entities\Event
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Event", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $event;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    public function __construct() {
        $this->createTimestamp = new \DateTime;
        parent::__construct();
    }

    function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'agent' => $this->agent->simplify('id,name,singleUrl'),
            'event' => $this->event->simplify('id,name,singleUrl,shortDescription,files.avatar'),
            'createTimestamp' => $this->createTimestamp
        ];
    }

    protected function canUserCreate($user){
        if($user->is('guest')){
            return false;
        }
        
        return $this->agent->userId === $user->id;
    }

    protected function canUserRemove($user){
        if($user->is('guest')){
            return false;
        }
        
        return $this->agent->userId === $user->id || $user->is('admin');
    }

    protected function canUserModify($user){
        return false; // Favoritos não são editáveis, apenas criados ou removidos
    }

    protected function canUserView($user){
        return $this->agent->canUser('view', $user);
    }

    //============================================================= //
    // The following lines ara used by MapasCulturais hook system.
    // Please do not change them.
    // ============================================================ //

    /** @ORM\PrePersist */
    public function prePersist($args = null){ parent::prePersist($args); }
    /** @ORM\PostPersist */
    public function postPersist($args = null){ parent::postPersist($args); }

    /** @ORM\PreRemove */
    public function preRemove($args = null){ parent::preRemove($args); }
    /** @ORM\PostRemove */
    public function postRemove($args = null){ parent::postRemove($args); }

    /** @ORM\PreUpdate */
    public function preUpdate($args = null){ parent::preUpdate($args); }
    /** @ORM\PostUpdate */
    public function postUpdate($args = null){ parent::postUpdate($args); }
}
