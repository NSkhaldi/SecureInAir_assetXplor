<?php
        
namespace SeeItAll\assetXploreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;



/**
 * Level0
 *
 * @ORM\Table(name="Level0")
 * @ORM\Entity(repositoryClass="SeeItAll\assetXploreBundle\Repository\Level0Repository")
 * @ORM\HasLifecycleCallbacks
 */




 
class Level0
{


    private $uniqid ;

        
    
    

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
     * @ORM\Column(name="level0_name", type="string", length=255,  nullable=true)
     */
    private $level0Name;

 

    /**
     * @var string
     *
     * @ORM\Column(name="img_path", type="string", length=255, nullable=true)
     */
    private $img_path;

    /**
     * @var string
     *
     * @ORM\Column(name="DISPLAY", type="string", length=255, nullable=true)
     */
    private $display;
    
        /**
     * @var string
     *
     * @ORM\Column(name="thumb_path", type="string", length=255, nullable=true)
     */
    private $thumb_path;

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
    *  @var string
    * @ORM\Column(name="map_path", type="string", length=400, nullable=true)
    */
    private $map_path;
    

                /**
    *  @var string
    * @ORM\Column(name="360_url", type="string", length=400, nullable=true)
    */
    private $url;


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
     * @return level0
     */
    public function setIdAsset($idAsset)
    {
        $this->id_asset =$idAsset;

        return $this;
    }

    /**
     * Set level0Name
     *
     * @param string $level0Name
     *
     * @return level0
     */
    public function setlevel0Name($level0Name)
    {
        $this->level0Name =$level0Name;

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
     * @return level0
     */
    public function setDataLoc($DataLoc)
    {
        $this->data_loc =$DataLoc;
        return $this;
    }

    /**
     * Get level0Name
     *
     * @return string
     */
    public function getlevel0Name()
    {
        return $this->level0Name;
    }

    /**
     * Set level0Pdf
     *
     * @param string $level0Pdf
     *
     * @return level0
     */
    public function setlevel0Pdf($level0Pdf)
    {
        $this->level0Pdf = $level0Pdf;

        return $this;
    }

    /**
     * Get level0Pdf
     *
     * @return string
     */
    public function getlevel0Pdf()
    {
        return $this->level0Pdf;
    }

    /**
     * Set level0Image
     *
     * @param string $level0Image
     *
     * @return level0
     */
    public function setlevel0Image($level0Image)
    {
        $this->level0Image = $level0Image;

        return $this;
    }

    /**
     * Get level0Image
     *
     * @return string
     */
    public function getlevel0Image()
    {
        return $this->level0Image;
    }


      /**
     * SetNote
     *
     * @param string $Note
     *
     * @return level0
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
     * @return level0
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
 private $file_thumb;
 
// THis variables will store the files bedore suppression
  private $thumb_img;
  private $img;


    public function getFile()
  {
    return $this->file;
  }

  // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;

    // On vérifie si on avait déjà un fichier pour cette entité
    if (null !== $this->level0Name) {
      // On sauvegarde l'extension du fichier pour le supprimer plus tard
      $this->tempFilename = $this->level0Name;


      // On réinitialise les valeurs des attributs url et alt
      $this->level0Image = null;
      $this->img_path = null;
      $this->thumb_path = null;
    }
  }





public function getUniqiD(){
    return $this->uniqid;
}

public function setUniqiD()
{
    return $this->uniqid= uniqid();
}


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
            
              //$this->level0Name = date('Y-m-d H:i:s');
            $this->setUniqId();
            $this->level0Name = $this->file->getClientOriginalName();
            $this->img_path = $this->getUploadDir().$this->getUniqId();
            $this->thumb_path=  $this->getThumbUploadDir().$this->getUniqId().'_thumb';
             
  }


 


  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */

 public function upload()
    {
      // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
      if (null === $this->file) {
        return ;
      }
        
        //Create and upoad the thumbnail
        $this->generateThumbnail($this->file, 300, 150, $quality = 75);

        //upload the full size image
        $this->file->move(
            $this->getUploadRootDir().$this->getUploadDir(), // Le répertoire de destination
            $this->uniqid // Le nom du fichier à créer, ici « id.extension »
           );

        $this->fileUploaded( $this->getUploadRootDir().$this->getUploadDir().$this->uniqid);

           
 
    }

    public function fileUploaded($file)
{
    if(is_uploaded_file($file))
    {
    echo ("$file is uploaded via HTTP POST");
    }
  else
    {
    echo ("$file is not uploaded via HTTP POST");
    }
}


   /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    $this->thumb_img = $this->getUploadRootDir().$this->thumb_path;
   // $this->img = $this->getUploadRootDir().$this->getUploadDir().$this->getImgPath;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {
    // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    if (file_exists($this->thumb_img)) {
      // On supprime le fichier
      unlink( $this->thumb_img);
    }
  }

  
    public function getUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'img/';
    }
    public function getMapsUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'maps/';
    }

    public function getThumbUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'optim/';
    }
  
    public function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/';
       
    }
    

    public function generateThumbnail($img, $width, $height, $quality = 90)
{
    if (is_file($img)) {
        $imagick = new \Imagick(realpath($img));
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality($quality);
        $imagick->thumbnailImage($width, $height, false, false);
        //$filename_no_ext = reset(explode('.', $img));
        $this->thumb_file= $this->getUploadRootDir().$this->getThumbUploadDir().$this->getUniqid().'_thumb';
        if (file_put_contents( $this->thumb_file , $imagick) === false) {
            throw new Exception("Could not put contents.");
        }
        return true;
    }
    else {
        throw new Exception("No valid image provided with {$img}.");
    } 
    $this->fileUploaded( $this->thumb_file); 
}


    /**
     * Set imgPath.
     *
     * @param string|null $imgPath
     *
     * @return Level0
     */
    public function setImgPath($imgPath = null)
    {
        $this->img_path = $imgPath;

        return $this;
    }

    /**
     * Get imgPath.
     *
     * @return string|null
     */
    public function getImgPath()
    {
        return $this->img_path;
    }

    /**
     * Set thumbPath.
     *
     * @param string|null $thumbPath
     *
     * @return Level0
     */
    public function setThumbPath($thumbPath = null)
    {
        $this->thumb_path = $thumbPath;

        return $this;
    }

    /**
     * Get thumbPath.
     *
     * @return string|null
     */
    public function getThumbPath()
    {
        return $this->thumb_path;
    }

    /**
     * Set mapPath.
     *
     * @param string|null $mapPath
     *
     * @return Level0
     */
    public function setMapPath($mapPath = null)
    {
        $this->map_path = $mapPath;

        return $this;
    }

    /**
     * Get mapPath.
     *
     * @return string|null
     */
    public function getMapPath()
    {
        return $this->map_path;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return Level0
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set display.
     *
     * @param string|null $display
     *
     * @return Level0
     */
    public function setDisplay($display = null)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display.
     *
     * @return string|null
     */
    public function getDisplay()
    {
        return $this->display;
    }
}
