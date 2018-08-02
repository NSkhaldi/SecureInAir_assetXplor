<?php

namespace SeeItAll\assetXploreBundle\Entity;

/**
 * doc
 */
class doc
{
    /**
     * @var string
     */
    private $PdfName;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \SeeItAll\assetXploreBundle\Entity\building
     */
    private $building;

    /**
     * @var \SeeItAll\assetXploreBundle\Entity\room
     */
    private $room;

    /**
     * @var \SeeItAll\assetXploreBundle\Entity\item
     */
    private $item;


    /**
     * Set pdfName
     *
     * @param string $pdfName
     *
     * @return doc
     */
    public function setPdfName($pdfName)
    {
        $this->PdfName = $pdfName;

        return $this;
    }

    /**
     * Get pdfName
     *
     * @return string
     */
    public function getPdfName()
    {
        return $this->PdfName;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set building
     *
     * @param \SeeItAll\assetXploreBundle\Entity\building $building
     *
     * @return doc
     */
    public function setBuilding(\SeeItAll\assetXploreBundle\Entity\building $building = null)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return \SeeItAll\assetXploreBundle\Entity\building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set room
     *
     * @param \SeeItAll\assetXploreBundle\Entity\room $room
     *
     * @return doc
     */
    public function setRoom(\SeeItAll\assetXploreBundle\Entity\room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \SeeItAll\assetXploreBundle\Entity\room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set item
     *
     * @param \SeeItAll\assetXploreBundle\Entity\item $item
     *
     * @return doc
     */
    public function setItem(\SeeItAll\assetXploreBundle\Entity\item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \SeeItAll\assetXploreBundle\Entity\item
     */
    public function getItem()
    {
        return $this->item;
    }
}

