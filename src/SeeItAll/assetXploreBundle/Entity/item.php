<?php

namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\itemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class item
{

      /**
   * @ORM\ManyToOne(targetEntity="SeeItAll\assetXploreBundle\Entity\room")
   * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")  
      */
//@ORM\JoinColumn(nullable=false) an item without a room makes no


      private $room;
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
     * @ORM\Column(name="item_image", type="string", length=255, nullable=true)
     */
    private $ItemImage;

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
     * @ORM\Column(name="item_name", type="string", length=255, nullable=true)
     */
    private $ItemName;




          /**
     * @var int
     *
     * @ORM\Column(name="contract_number", type="integer", nullable=true )
     */
    private $contract_number;


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
     * Set id_asset
     *
     * @param string $Id_asset
     *
     * @return item
     */
    public function setIdAsset($Id_asset)
    {
        $this->id_asset = $Id_asset;

        return $this;
    }
    
    /**
     * Set itemImage
     *
     * @param string $itemImage
     *
     * @return item
     */
    public function setItemImage($itemImage)
    {
        $this->ItemImage = $itemImage;

        return $this;
    }

    /**
     * Get itemImage
     *
     * @return string
     */
    public function getItemImage()
    {
        return $this->ItemImage;
    }

    /**
     * Set itemName
     *
     * @param string $itemName
     *
     * @return item
     */
    public function setItemName($itemName)
    {
        $this->ItemName = $itemName;

        return $this;
    }

    /**
     * Get itemName
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->ItemName;
    }

            /**
    *  @var string
    * @ORM\Column(name="data_loc", type="string", length=400, nullable=true)
    */
    private $data_loc;
    


    /**
     * Set itemPdf
     *
     * @param string $itemPdf
     *
     * @return item
     */
    public function setItemPdf($itemPdf)
    {
        $this->ItemPdf = $ItemPdf;

        return $this;
    }

    /**
     * Get itemPdf
     *
     * @return string
     */
    public function getItemPdf()
    {
        return $this->ItemPdf;
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

          /**
     * SetNote
     *
     * @param string $Note
     *
     * @return item
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



    public function setRoom(room $room)
  {
    $this->room = $room;

    return $this;       
  }

  public function getRoom()
  {
    return $this->room;
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
    if (null !== $this->ItemImage) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->ItemImage;

      // On réinitialise les valeurs des attributs url et alt
      $this->ItemImage = null;
      $this->ItemName = null;
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
    $this->ItemImage = $this->getUploadDir().'/'.$this->uniqid;
    
    // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
    $this->ItemName = $this->file->getClientOriginalName();



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
      return 'uploads/items';
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
     * @return item
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
