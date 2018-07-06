<?php
        
namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Google\Cloud\Storage\StorageClient;


# Your Google Cloud Platform project ID
//$projectId = 'assetxplor';

# Instantiates a client
//$storage = new StorageClient([
   // 'projectId' => $projectId
//]);

/**
 * Level1
 *
 * @ORM\Table(name="Level1")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\Level1Repository")
 * @ORM\HasLifecycleCallbacks
 */
class Level1
{

  /**
   * @ORM\ManyToOne(targetEntity="SeeItAll\assetXploreBundle\Entity\Level0" )
   * @ORM\JoinColumn(nullable=true, onDelete="SET NULL" )  
   */
//@ORM\JoinColumn(nullable=true) prohibits the creation of rooms without a level1
    private $level0;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_asset", type="integer", nullable=true)
     */
    private $id_asset;

      /**
     * @var int
     *
     * @ORM\Column(name="contract_number", type="integer", nullable=true )
     */
    private $contract_number;


    /**
     * @var string
     *
     * @ORM\Column(name="level1_name", type="string", length=255,  nullable=true)
     */
    private $level1Name;

 

    /**
     * @var string
     *
     * @ORM\Column(name="level1_image", type="string", length=255, nullable=true)
     */
    private $level1Image;

    /**
    *  @var string
    * @ORM\Column(name="note", type="string", length=50, nullable=true)
    */
    private $note;


        /**
    *  @var string
    * @ORM\Column(name="data_loc", type="string", length=400, nullable=true)
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
    public function getIdAsset()
    {
        return $this->id_asset;
    }

    /**
     * Set id_asset
     *
     * @param string $idAsset
     *
     * @return level1
     */
    public function setIdAsset($idAsset)
    {
        $this->id_asset =$idAsset;

        return $this;
    }

    /**
     * Set level1Name
     *
     * @param string $Level1Name
     *
     * @return Level1
     */
    public function setLevel1Name($Level1Name)
    {
        $this->level1Name =$Level1Name;

        return $this;
    }
    /**
     * Get data_loc
     *
     * @return string
     */
    public function getDataLoc()
    {
        return $this->data_loc;
    }

     /**
     * Set data_loc
     *
     * @param string $Dataloc
     *
     * @return level1
     */
    public function setDataLoc($DataLoc)
    {
        $this->data_loc =$DataLoc;
        return $this;
    }

    /**
     * Get level1Name
     *
     * @return string
     */
    public function getLevel1Name()
    {
        return $this->level1Name;
    }

    /**
     * Set level1Pdf
     *
     * @param string $blevel1Pdf
     *
     * @return level1
     */
    public function setLevel1Pdf($level1Pdf)
    {
        $this->level1Pdf = $level1Pdf;

        return $this;
    }

    /**
     * Get level1Pdf
     *
     * @return string
     */
    public function getNoteLevel1Pdf()
    {
        return $this->blevel1Pdf;
    }

    /**
     * Set blevel1Image
     *
     * @param string $blevel1Image
     *
     * @return blevel1
     */
    public function setBlevel1Image($blevel1Image)
    {
        $this->blevel1Image = $blevel1Image;

        return $this;
    }

    /**
     * Get level1Image
     *
     * @return string
     */
    public function getLevel1Image()
    {
        return $this->level1Image;
    }


      /**
     * SetNote
     *
     * @param string $Note
     *
     * @return level1
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
     * @return level1
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






 private $file;

// On ajoute cet attribut pour y stocker le nom du fichier temporairement
  private $tempFilename;


    public function getFile()
  {
    return $this->file;
  }

  // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    // On vérifie si on avait déjà un fichier pour cette entité
    if (null !== $this->level1Name) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->level1Name;


      // On réinitialise les valeurs des attributs url et alt
      $this->level1Image = null;
      $this->level1Name = null;
    }
  }


private $uniqid;


  /**
   * @ORM\PreUpdate()
   *  @ORM\PrePersist()    
   * 
   */
  public function preUpload()
  {
    // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    if (null === $this->file) {
      return;
    }
            $this->uniqid = uniqid();
              //$this->level1Name = date('Y-m-d H:i:s');
            $this->level1Name = $this->file->getClientOriginalName();
             $this->level1Image = $this->getUploadDir().'/'.$this->uniqid;
             
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
      //$storage = new StorageClient();  

      //move_uploaded_file($this->file, "gs://assetxplor/".$this->level1Name);
 // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
         $this->getUploadRootDir(), // Le répertoire de destination
             $this->uniqid // Le nom du fichier à créer, ici « id.extension »
            );
      
     // move_uploaded_file($this->file, "gs://${my_bucket}/{$this->level1Name}");
 
    }


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->level1Name;
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
      return 'uploads';
    }
  
    protected function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
       
    }
    




    /**
     * Set level1Image.
     *
     * @param string|null $level1Image
     *
     * @return Level1
     */
    public function setLevel1Image($level1Image = null)
    {
        $this->level1Image = $level1Image;

        return $this;
    }

    /**
     * Set level0.
     *
     * @param \SeeItAll\assetXploreBundle\Entity\Level0|null $level0
     *
     * @return Level1
     */
    public function setLevel0(\SeeItAll\assetXploreBundle\Entity\Level0 $level0 = null)
    {
        $this->level0 = $level0;

        return $this;
    }

    /**
     * Get level0.
     *
     * @return \SeeItAll\assetXploreBundle\Entity\Level0|null
     */
    public function getLevel0()
    {
        return $this->level0;
    }
}
