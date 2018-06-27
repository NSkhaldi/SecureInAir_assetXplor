<?php

namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * room
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\roomRepository")
 * @ORM\HasLifecycleCallbacks
 */
class room
{

 /**
   * @ORM\ManyToOne(targetEntity="SeeItAll\assetXploreBundle\Entity\building")
   * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")  
   */
//@ORM\JoinColumn(nullable=false) prohibits the creation of rooms without a building
    private $building;



    private $tempFilename;
    private $uniqid;
    private $file;


 

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="room_image", type="string", length=255, nullable=true)
     */
    private $roomImage;

    /**
     * @var string
     *
     * @ORM\Column(name="room_name", type="string", length=255, nullable=true)
     */
    private $roomName;

         /**
     * @var int
     *
     * @ORM\Column(name="contract_number", type="integer", nullable=true )
     */
    private $contract_number;



    /**
    *  @var string
    * @ORM\Column(name="note", type="string", length=50, nullable=true)
    */
    private $note;


    /**
     * @var int
     *
     * @ORM\Column(name="id_asset", type="integer", nullable=true )
     */
    private $id_asset;

        /**
     * @var string
     *
     * @ORM\Column(name="data_loc", type="string", length=255, nullable=true)
     */
    private $data_loc;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get asset_id
     *
     * @return int
     */
    public function getIdasset()
    {
        return $this->id_asset;
    }

        /**
     * Set asset_id
     *
     * @param string $id_asset
     *
     * @return room
     */
    public function setIdAsset($id_asset)
    {
        $this->id_asset = $id_asset;

        return $this;
    }
  

    /**
     * Set roomImage
     *
     * @param string $roomImage
     *
     * @return room
     */
    public function setRoomImage($roomImage)
    {
        $this->roomImage = $roomImage;

        return $this;
    }

    /**
     * Get roomImage
     *
     * @return string
     */
    public function getRoomImage()
    {
        return $this->roomImage;
    }

    /**
     * Set roomName
     *
     * @param string $roomName
     *
     * @return room
     */
    public function setRoomName($roomName)
    {
        $this->roomName = $roomName;

        return $this;
    }

    /**
     * Get roomName
     *
     * @return string
     */
    public function getRoomName()
    {
        return $this->roomName;
    }

    /**
     * Set roomPdf
     *
     * @param string $roomPdf
     *
     * @return room
     */
    public function setRoomPdf($roomPdf)
    {
        $this->roomPdf = $roomPdf;

        return $this;
    }

    /**
     * Get roomPdf
     *
     * @return string
     */
    public function getRoomPdf()
    {
        return $this->roomPdf;
    }


      /**
     * SetNote
     *
     * @param string $Note
     *
     * @return building
     */
    public function setNote($Note)
    {
        $this->note =$Note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }


         /**
     * SetContract_number
     *
     * @param string $Contract_number
     *
     * @return building
     */
    public function setContractNumber($Contract_number)
    {
        $this->contract_number =$Contract_number;

        return $this->contract_number;
    }

    /**
     * Get Contract_number
     *
     * @return string
     */
    public function getContractNumber()
    {
        return $this->contract_number;
    }



    public function setBuilding(building $building)
  {
    $this->building = $building;

    return $this;       
  }

  public function getBuilding()
  {
    return $this->building;
  }



    public function getFile()
  {
    return $this->file;
  }

  // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    // On vérifie si on avait déjà un fichier pour cette entité
    if (null !== $this->roomImage) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->roomImage;

      // On réinitialise les valeurs des attributs url et alt
      $this->roomImage = null;
      $this->roomName = null;
    }
  }



  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function preUpload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    if (null === $this->file) {
      return;
    }
    $this->uniqid = uniqid();
    // Le nom du fichier est son id, on doit juste stocker également son extension
    // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « url »
    $this->roomImage = $this->getUploadDir().'/'.$this->uniqid;
    
    // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
    $this->roomName = $this->file->getClientOriginalName();



  }

  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */

 public function upload()
    {
      // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
      if (null === $this->file) {
        return;
      }
        
         // Si on avait un ancien fichier, on le supprime
    if (null !== $this->tempFilename) {
      $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
      if (file_exists($oldFile)) {
        unlink($oldFile);
      }
    }

        // On déplace le fichier envoyé dans le répertoire de notre choix
    $this->file->move(
      $this->getUploadRootDir(), // Le répertoire de destination
      $this->uniqid // Le nom du fichier à créer, ici « id.extension »
    );

 
    }


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    //$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->roomImage;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->tempFilename)) {
      // On supprime le fichier
      unlink($this->tempFilename);
    }
  }

  
    public function getUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'uploads/rooms';
    }
  
    protected function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
       
    }





  



  



    /**
     * Set dataLoc
     *
     * @param string $dataLoc
     *
     * @return room
     */
    public function setDataLoc($dataLoc)
    {
        $this->data_loc = $dataLoc;

        return $this;
    }

    /**
     * Get dataLoc
     *
     * @return string
     */
    public function getDataLoc()
    {
        return $this->data_loc;
    }
}