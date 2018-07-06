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
 * Level2
 *
 * @ORM\Table(name="Level2")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\Level2Repository")
 * @ORM\HasLifecycleCallbacks
 */
class Level2
{

  /**
   * @ORM\ManyToOne(targetEntity="SeeItAll\assetXploreBundle\Entity\Level1" )
   * @ORM\JoinColumn(nullable=true, onDelete="SET NULL" )  
   */
//@ORM\JoinColumn(nullable=true) prohibits the creation of rooms without a level2
    private $level1;


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
     * @ORM\Column(name="level2_name", type="string", length=255,  nullable=true)
     */
    private $level2Name;

 

    /**
     * @var string
     *
     * @ORM\Column(name="level2_image", type="string", length=255, nullable=true)
     */
    private $level2Image;

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
    if (null !== $this->level2Name) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->level2Name;


      // On réinitialise les valeurs des attributs url et alt
      $this->level2Image = null;
      $this->level2Name = null;
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
              //$this->level2Name = date('Y-m-d H:i:s');
            $this->level2Name = $this->file->getClientOriginalName();
             $this->level2Image = $this->getUploadDir().'/'.$this->uniqid;
             
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

      //move_uploaded_file($this->file, "gs://assetxplor/".$this->level2Name);
 // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
         $this->getUploadRootDir(), // Le répertoire de destination
             $this->uniqid // Le nom du fichier à créer, ici « id.extension »
            );
      
     // move_uploaded_file($this->file, "gs://${my_bucket}/{$this->level2Name}");
 
    }


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->tempFilename = $this->getUploadRootDir().'/'.$this->level2Name;
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idAsset.
     *
     * @param int|null $idAsset
     *
     * @return Level2
     */
    public function setIdAsset($idAsset = null)
    {
        $this->id_asset = $idAsset;

        return $this;
    }

    /**
     * Get idAsset.
     *
     * @return int|null
     */
    public function getIdAsset()
    {
        return $this->id_asset;
    }

    /**
     * Set contractNumber.
     *
     * @param int|null $contractNumber
     *
     * @return Level2
     */
    public function setContractNumber($contractNumber = null)
    {
        $this->contract_number = $contractNumber;

        return $this;
    }

    /**
     * Get contractNumber.
     *
     * @return int|null
     */
    public function getContractNumber()
    {
        return $this->contract_number;
    }

    /**
     * Set level2Name.
     *
     * @param string|null $level2Name
     *
     * @return Level2
     */
    public function setLevel2Name($level2Name = null)
    {
        $this->level2Name = $level2Name;

        return $this;
    }

    /**
     * Get level2Name.
     *
     * @return string|null
     */
    public function getLevel2Name()
    {
        return $this->level2Name;
    }

    /**
     * Set level2Image.
     *
     * @param string|null $level2Image
     *
     * @return Level2
     */
    public function setLevel2Image($level2Image = null)
    {
        $this->level2Image = $level2Image;

        return $this;
    }

    /**
     * Get level2Image.
     *
     * @return string|null
     */
    public function getLevel2Image()
    {
        return $this->level2Image;
    }

    /**
     * Set note.
     *
     * @param string|null $note
     *
     * @return Level2
     */
    public function setNote($note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return string|null
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set dataLoc.
     *
     * @param string|null $dataLoc
     *
     * @return Level2
     */
    public function setDataLoc($dataLoc = null)
    {
        $this->data_loc = $dataLoc;

        return $this;
    }

    /**
     * Get dataLoc.
     *
     * @return string|null
     */
    public function getDataLoc()
    {
        return $this->data_loc;
    }

    /**
     * Set level1.
     *
     * @param \SeeItAll\assetXploreBundle\Entity\Level1|null $level1
     *
     * @return Level2
     */
    public function setLevel1(\SeeItAll\assetXploreBundle\Entity\Level1 $level1 = null)
    {
        $this->level1 = $level1;

        return $this;
    }

    /**
     * Get level1.
     *
     * @return \SeeItAll\assetXploreBundle\Entity\Level1|null
     */
    public function getLevel1()
    {
        return $this->level1;
    }
}
