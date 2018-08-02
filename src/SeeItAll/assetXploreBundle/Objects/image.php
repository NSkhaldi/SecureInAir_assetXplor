<?php

namespace SeeItAll\assetXploreBundle\Objects;



class image
{

    private $raw_data;
    private $note;
    private $asset_id;
    private $contract_number;



    public function setNote($Note)
    {
        $this->note = $Note;

        return $this; 
    }


    public function setRawData($Raw_data)
  {
    $this->raw_data = $Raw_data;

    return $this;       
  }

  public function setAssetId($Asset_Id)
  {
    $this->asset_id = $Asset_Id;

    return $this;       
  }

  public function setContractNumber($Contract_number)
  {
    $this->contract_number = $Contract_number;

    return $this;       
  }

  public function getAssetId()
  {
    return $this->asset_id;
  }

  public function getRawData()
  {
    return $this->raw_data;
  }

  public function getNote()
  {
    return $this->note;
  }



    public function getContractNumber()
  {
    return $this->contract_number;
  }

 



  



  


}




