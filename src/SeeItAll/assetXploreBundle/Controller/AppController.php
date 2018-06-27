<?php



namespace SeeItAll\assetXploreBundle\Controller;

use SeeItAll\assetXploreBundle\Objects\image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SeeItAll\assetXploreBundle\Entity\building;
use SeeItAll\assetXploreBundle\Entity\room;
use SeeItAll\assetXploreBundle\Entity\item;
use SeeItAll\assetXploreBundle\Entity\document;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


use SeeItAll\assetXploreBundle\Form\buildingType;
use SeeItAll\assetXploreBundle\Form\titleType;
use SeeItAll\assetXploreBundle\Form\roomType;
use SeeItAll\assetXploreBundle\Form\itemType;
use SeeItAll\assetXploreBundle\Form\buildingNameType;
use SeeItAll\assetXploreBundle\Form\roomNameType;
use SeeItAll\assetXploreBundle\Form\itemNameType;
use SeeItAll\assetXploreBundle\Form\saveImageType;
use SeeItAll\assetXploreBundle\Form\saveDocType;
use SeeItAll\assetXploreBundle\Form\buildingLocType;



use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class AppController extends Controller
{
    public function indexAction()
    {
        $content = $this->get('templating')->render('SeeItAllassetXploreBundle:App:login.html.twig');
    
        return new Response($content);
    }

    public function coreAction($id_location,$id_building)
    {
        //sql
        $query="SELECT * from building";
        $query_res=$db->query($query);

        return $this->redirectToRoute('see_it_allasset_xplore_homepage');


       // $content = $this->get('templating')->render('SeeItAllassetXploreBundle:App:building.html.twig');
        //return new Response($content);
    }






    public function locationsAction(Request $request)
    {

    /*        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux auteurs.');
          } */  
      

      // EMPTY OBJECTS INSTANCIATION
      $building = new building();
      $image= new image();
  
      // GET THE FORMS
      $form = $this->get('form.factory')->create(buildingType::class, $building);
      $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
      $form_buildingName = $this->get('form.factory')->create(buildingNameType::class, $building);
      $form_buildingLoc = $this->get('form.factory')->create(buildingLocType::class, $building);
      

      $em = $this->getDoctrine()->getManager(); //GET THE ENTITY MANAGER (It's responsible for saving objects to, and fetching objects from, the database.)
      $listbuildings = $em->getRepository('SeeItAllassetXploreBundle:building')->findAll(); //GET the REPOSITORY and fetch objects (You can think of a repository as a PHP class whose only job is to help you fetch entities of a certain class.)
      $buildings_number = count($listbuildings);
      
  
      // BY DEFAULT POST IS THE METHOS USED BY FORMS
      if ($request->isMethod('POST')) {

        $form->handleRequest($request);// it takes the POST’ed data from the previous request, processes it, and runs any validation (checks integrity of expected versus received data). it only does this for POST requests
        //Form here the building object is hydrated by the form

        //FORM 1: adding buildings
        if ($form->isSubmitted() && $form->isValid()) { //CHECK whether this was submitted and whether it is valid 
         
          
          $em = $this->getDoctrine()->getManager();
          $em->persist($building); 
          $em->flush();
         // $request->getSession()->getFlashBag()->add('notice', 'Building bien enregistrée.');
          $listbuildings = $em->getRepository('SeeItAllassetXploreBundle:building')->findAll();
  
 
            
          //url redirection (solves reupload when refresh)
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_locations')); 
        }


        //FORM 2 :saving edited image        
        $form_saveImage->handleRequest($request);

         
         //This form doesn't hydrate directly the building object( which is totally possible), but instead he fill an image object (Objects/image) 
        if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {

            $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
            $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param

            define('UPLOAD_DIR', 'uploads/'); // define the upload path
         
            
          //Here we extract the header from the raw 64base image data
	        $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
	        $img = str_replace(' ', '+', $img);
	        $data = base64_decode($img);
            
            $filename=date('Y-m-d H:i:s');
            $file = UPLOAD_DIR.$filename; // We give the file a unique name (converted timestamp)
	          $success = file_put_contents($file,  $data); //store the image data in a file
            print $success ? $file : 'Unable to save the file.';

            //Building hydration
            $building->setBuildingName($filename);
            $building->setBuildingImage($file);
            $building->setNote($image->getNote());
            $building->setIdAsset($image->getAssetId());
            $building->setContractNumber($image->getContractNumber());

            //storing the image in the db
            $em = $this->getDoctrine()->getManager();
            $em->persist($building); 
            $em->flush();

          //url redirection (solves reupload when refresh)
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_locations')); 

        }





      } 

    
    
      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
      return $this->render('SeeItAllassetXploreBundle:App:location.html.twig', array(
        'form' => $form->createView(),
        'form_saveImage' => $form_saveImage->createView(),
        'name_form' => $form_buildingName->createView(),
        'form_buildingLoc' => $form_buildingLoc->createView(),
         'buildings' => $listbuildings, 'buildings_number' => $buildings_number ));
    }

    public function Loclevel_0Action($building_id,Request $request)
    {
        $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
        $loc= $raw_data->get('pin-data');
       

        $em = $this->getDoctrine()->getManager();
        $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
         $building->setDataLoc($loc);
         $em->persist($building);
         $em->flush();
         // url redirection
           return $this->redirect($this->generateUrl('see_it_allasset_xplor_locations')); 
    }

    public function docbuildingsAction($building_id,Request $request)
    {
      
     // EMPTY OBJECTS INSTANCIATION
      $building = new building();
      $document= new document();
      
      $em = $this->getDoctrine()->getManager();
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
      $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('building' => $building));

      // GET FORMS
      $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

      if (null === $document) {
        throw new NotFoundHttpException("the document for the building with the id ".$building_id." do not exist");
      }

        if ($request->isMethod('POST')) {

        //FORM 1: adding buildings  docs        
         $form_saveDoc->handleRequest($request);

         if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

            $document->setBuilding($building);
            $em->persist($document);
            $em->flush();
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_buildings_docs', array(
                'building_id' => $building->getId())));
         }
        }

        return $this->render('SeeItAllassetXploreBundle:App:docs_buildings.html.twig', array(
            'form_saveDoc' => $form_saveDoc->createView(),
            'docs' => $listdocuments,'building' => $building, 'building_id' => $building_id, ));

    }

    public function gridbuildingsAction($building_id,Request $request)
    {

        $building = new building();

        $em = $this->getDoctrine()->getManager();
        $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);

        if (null === $building) {
            throw new NotFoundHttpException("the document for the building with the id ".$building_id." do not exist");
          }
            $str = $building->getDataGrid();
            $myArr= array();

            for ($i = 0; $i < strlen($str); $i++){
                 
 
                    array_push( $myArr , array( "cell" => $i , "object_id" => $building->getId(), "selected" => ($building->getDataGrid()[$i]==1)?true:false) );

  
            }

            

            $response = new Response(json_encode($myArr));
            $response->headers->set('Content-Type', 'application/json');

            

            return $response;


    }




    public function buildingsAction($building_id,Request $request)
    {
      // EMPTY OBJECTS INSTANCIATION
      $building = new building();
      $room = new room();
      $image= new image();

       //GET the rooms associated with the building
       $em = $this->getDoctrine()->getManager();
       $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
       $listrooms = $em->getRepository('SeeItAllassetXploreBundle:room')->findBy(array('building' => $building));
       $rooms_number = count($listrooms);
   
      // GET FORMS
      $form = $this->get('form.factory')->create(roomType::class, $room);
      $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
      $form_buildingName = $this->get('form.factory')->create(buildingNameType::class, $building);
      $form_buildingLoc = $this->get('form.factory')->create(buildingLocType::class, $building);
     
    
   
  
  
      if ($request->isMethod('POST')) {

        //FORM 1: adding rooms  
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
          // On enregistre notre objet $advert dans la base de données, par exemple
          $room->setBuilding($building);
          $em->persist($room);
          $em->flush();

         //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_buildings', array(
            'building_id' => $building->getId())));       
        }
    

        
        //FORM 2: changing the building's name
        $form_buildingName->handleRequest($request);
       
        if ($form_buildingName->isSubmitted() && $form_buildingName->isValid()) {
   
          $em = $this->getDoctrine()->getManager();
          $em->persist($building);
          $em->flush();
          // url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_buildings', array(
            'building_id' => $building->getId() ))); 
         }


         //FORM 3: saving edited room        
         $form_saveImage->handleRequest($request);

         //This form doesn't hydrate directly the room object( which is totally possible), but instead he fill an image object (Objects/image) 
         if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {
 
           $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
           $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
 
           define('UPLOAD_DIR', 'uploads/'); // define the upload path
        
           
         //Here we extract the header from the raw 64base image data
         $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
         $img = str_replace(' ', '+', $img);
         $data = base64_decode($img);
           
           $filename=date('Y-m-d H:i:s');
           $file = UPLOAD_DIR.$filename; // We give the file a unique name (converted timestamp)
           $success = file_put_contents($file,  $data); //store the image data in a file
           print $success ? $file : 'Unable to save the file.';
 
           //room hydration
           $room->setroomName($filename);
           $room->setroomImage($file);
           $room->setNote($image->getNote());
           $room->setIdAsset($image->getAssetId());
           $room->setContractNumber($image->getContractNumber());
 
           //storing the image in the db
           $em = $this->getDoctrine()->getManager();
           $room->setBuilding($building);// link the edited room to a building
           $em->persist($room); 
           $em->flush();
 
          //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_buildings', array(
            'building_id' => $building->getId()))); 
 
       } 
       
         
        //FORM 4: changing asset location
        $form_buildingLoc->handleRequest($request);
       
        if ($form_buildingLoc->isSubmitted() && $form_buildingLoc->isValid()) {
   
          $em = $this->getDoctrine()->getManager();
          $em->persist($building);
          $em->flush();
          // url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_buildings', array(
            'building_id' => $building->getId() ))); 
         }



        }
      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
      return $this->render('SeeItAllassetXploreBundle:App:building.html.twig', array(
        'form' => $form->createView(),
        'form_saveImage' => $form_saveImage->createView(),
        'name_form' => $form_buildingName->createView(),
        'form_buildingLoc' => $form_buildingLoc->createView(),
        'building' => $building, 'rooms' => $listrooms,'rooms_number' => $rooms_number ));
      

      //}
    }

    public function removeBuildingAction($building_id,Request $request)
    {
      
      //When you query for a particular type of object, you always use what's known as its "repository". 
      $building = new building();
      $em = $this->getDoctrine()->getManager(); 
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->findOneBy(['id' => $building_id]);

      if (null === $building) {
        throw new NotFoundHttpException("the building with the id ".$building_id." do not exist");
      }

      $em->remove($building);
      $em->flush();
      
    return $this->redirectToRoute('see_it_allasset_xplor_locations');
      
    }


    public function pdfBuildingsAction($asset_id,Request $request)
    {

      return $this->render('SeeItAllassetXploreBundle:App:pdfs.html.twig', array('id' => $asset_id) );

      //, array( 'building' => $building, 'pdfs' => $listpdfs,'rooms_number' => $rooms_number

    }


    public function removeBuildingPdfAction($document_id, $building_id,Request $request)
    {
      
      
      $document =new document();
      $em = $this->getDoctrine()->getManager(); 
     
      $document= $em->getRepository('SeeItAllassetXploreBundle:document')->find($document_id);
      if (null === $document) {
        throw new NotFoundHttpException("the building with the id ".$document_id." do not exist");
      }

      $em->remove($document);
      $em->flush();
      
    return $this->redirectToRoute('see_it_allasset_xplor_buildings_docs',  array('building_id' => $building_id));
      
    }






















    
    public function removeRoomAction($building_id, $room_id,Request $request)
    {
      
      //When you query for a particular type of object, you always use what's known as its "repository". 
      $room = new room();
      $em = $this->getDoctrine()->getManager(); 
      $room= $em->getRepository('SeeItAllassetXploreBundle:room')->findOneBy(['id' => $room_id]);

      if (null === $room) {
        throw new NotFoundHttpException("the room with the id ".$room_id." do not exist");
      }

      $em->remove($room);
      $em->flush();
      
    return $this->redirectToRoute('see_it_allasset_xplor_buildings', array('building_id' => $building_id));
      
    }







    public function docroomsAction($building_id,$room_id,Request $request)
    {
      
     // EMPTY OBJECTS INSTANCIATION
      $building= new building();
      $room = new room();
      $document= new document();
      
      $em = $this->getDoctrine()->getManager();
      $room= $em->getRepository('SeeItAllassetXploreBundle:room')->find($room_id);
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
      $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('room' => $room));

      // GET FORMS
      $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

      if (null === $document) {
        throw new NotFoundHttpException("the document for the room with the id ".$room_id." do not exist");
      }

        if ($request->isMethod('POST')) {

        //FORM 1: adding buildings  docs        
         $form_saveDoc->handleRequest($request);

         if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

            $document->setRoom($room);
            $em->persist($document);
            $em->flush();
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_rooms_docs', array(
                'building_id' => $building_id ,'room_id' =>$room_id  )));
         }
        }

        return $this->render('SeeItAllassetXploreBundle:App:docs_rooms.html.twig', array(
            'form_saveDoc' => $form_saveDoc->createView(),
            'docs' => $listdocuments,'building' => $building, 'room' => $room ));

    }













    public function roomsAction($building_id, $room_id,Request $request)
    {

      // EMPTY OBJECTS INSTANCIATION
      $building = new building();
      $room = new room();
      $image= new image();
      $item = new item();

      //GET the items associated with the room
      $em = $this->getDoctrine()->getManager();
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
      $room= $em->getRepository('SeeItAllassetXploreBundle:room')->find($room_id);
      $listitems = $em->getRepository('SeeItAllassetXploreBundle:item')->findBy(array('room' => $room));
      $items_number = count($listitems);
   
      // GET FORMS
      $form = $this->get('form.factory')->create(itemType::class, $item);
      $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
      $form_roomName= $this->get('form.factory')->create(roomNameType::class, $room);


      
  
      
      if ($request->isMethod('POST')) {

        //FORM 1: adding rooms
        $form->handleRequest($request);
  
  
        if ($form->isSubmitted() && $form->isValid()) { 
          // On enregistre notre objet $advert dans la base de données, par exemple
          $item->setRoom($room);
          $em = $this->getDoctrine()->getManager();
          $em->persist($item);
          $em->flush();
  
          //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_rooms', array(
            'building_id' => $building->getId(), 'room_id' => $room->getId())));      
        }

        //FORM 2: changing the room's name
        $form_roomName->handleRequest($request);
       
        if ($form_roomName->isSubmitted() && $form_roomName->isValid()) {

          
          $em = $this->getDoctrine()->getManager();
          $em->persist($room);
          $em->flush();

          $listrooms = $em->getRepository('SeeItAllassetXploreBundle:item')->findBy(array('room' => $room));
  
          //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_rooms', array(
            'building_id' => $building->getId(), 'room_id' => $room->getId()))); 
         }


         //FORM 3: saving edited item        
         $form_saveImage->handleRequest($request);

         //This form doesn't hydrate directly the item object( which is totally possible), but instead he fill an image object (Objects/image) 
         if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {
 
           $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
           $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
 
           define('UPLOAD_DIR', 'uploads/'); // define the upload path
        
           
         //Here we extract the header from the raw 64base image data
         $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
         $img = str_replace(' ', '+', $img);
         $data = base64_decode($img);
           
           $filename=date('Y-m-d H:i:s');
           $file = UPLOAD_DIR.$filename; // We give the file a unique name (converted timestamp)
           $success = file_put_contents($file,  $data); //store the image data in a file
           print $success ? $file : 'Unable to save the file.';
 
           //item hydration
           $item->setitemName($filename);
           $item->setitemImage($file);
           $item->setNote($image->getNote());
           $item->setIdAsset($image->getAssetId());
           $item->setContractNumber($image->getContractNumber());
 
           //storing the image in the db
           $em = $this->getDoctrine()->getManager();
           $item->setRoom($room);// link the edited item to a room
           $em->persist($item); 
           $em->flush();
 
          //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_rooms', array(
            'building_id' => $building->getId(), 'room_id' => $room->getId()))); 
 
       }  

      }
  
      // twig template rendering
      return $this->render('SeeItAllassetXploreBundle:App:room.html.twig', array(
        'form' => $form->createView(),
        'form_saveImage' => $form_saveImage->createView(),
        'name_form' => $form_roomName->createView(),'room' => $room,
        'building' => $building, 'room' => $room, 'items' => $listitems, 'items_number' => $items_number  )); 
    }



    public function removeRoomPdfAction($document_id, $building_id, $room_id, Request $request)
    {
      
      //When you query for a particular type of object, you always use what's known as its "repository". 
    
     
      $document =new document();
      
      $em = $this->getDoctrine()->getManager(); 
      $document= $em->getRepository('SeeItAllassetXploreBundle:document')->find($document_id);
     
      

      if (null === $document) {
        throw new NotFoundHttpException("the document with the id ".$document_id." do not exist");
      }

      $em->remove($document);
      $em->flush();
      
    return $this->redirectToRoute('see_it_allasset_xplor_rooms_docs',  array('building_id' => $building_id , 'room_id' => $room_id));
    
    }



    public function removeitemAction($building_id, $room_id,$item_id,Request $request)
    {
      
      //When you query for a particular type of object, you always use what's known as its "repository". 
      $item = new item ();
      $em = $this->getDoctrine()->getManager(); 
      $item= $em->getRepository('SeeItAllassetXploreBundle:item')->findOneBy(['id' => $item_id]);

      if (null === $item) {
        throw new NotFoundHttpException("the item with the id ".$item_id." do not exist");
      }

      $em->remove($item);
      $em->flush();
      
      //url redirection
      return $this->redirectToRoute('see_it_allasset_xplor_rooms', array('building_id' => $building_id, 'room_id' => $room_id));
      
    }


    public function docitemsAction($building_id,$room_id,$item_id,Request $request)
    {
      
     // EMPTY OBJECTS INSTANCIATION
      $building= new building();
      $room = new room();
      $item = new item();
      $document= new document();
      
      $em = $this->getDoctrine()->getManager();
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
      $room= $em->getRepository('SeeItAllassetXploreBundle:room')->find($room_id);
      $item= $em->getRepository('SeeItAllassetXploreBundle:item')->find($item_id);
      $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('item' => $item));

      // GET FORMS
      $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

      if (null === $document) {
        throw new NotFoundHttpException("the document for the room with the id ".$item_id." do not exist");
      }

        if ($request->isMethod('POST')) {

        //FORM 1: adding buildings  docs        
         $form_saveDoc->handleRequest($request);

         if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

            $document->setItem($item);
            $em->persist($document);
            $em->flush();
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_items_docs', array(
                'building_id' => $building_id ,'room_id' =>$room_id, 'item_id' =>$item_id  )));
         }
        }

        return $this->render('SeeItAllassetXploreBundle:App:docs_items.html.twig', array(
            'form_saveDoc' => $form_saveDoc->createView(),
            'docs' => $listdocuments,'building' => $building, 'room' => $room, 'item' => $item ));

    }

    public function itemsAction($building_id, $room_id, $item_id, Request $request)
    {

      // EMPTY OBJECTS INSTANCIATION
      $building = new building();
      $room = new room();
      $item = new item();
      $image= new image();

      $em = $this->getDoctrine()->getManager();
      $building= $em->getRepository('SeeItAllassetXploreBundle:building')->find($building_id);
      $room= $em->getRepository('SeeItAllassetXploreBundle:room')->find($room_id);
      $item= $em->getRepository('SeeItAllassetXploreBundle:item')->find($item_id);
   
      // GET FORMS
      $form = $this->get('form.factory')->create(itemType::class, $item);
      $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
      $form_itemName= $this->get('form.factory')->create(itemNameType::class, $item);




  
      // Si la requête est en POST
      if ($request->isMethod('POST')) {

    /*   //FORM 1: adding rooms
       $form->handleRequest($request);
  
  
       if ($form->isSubmitted() && $form->isValid()) { 
         // On enregistre notre objet $advert dans la base de données, par exemple
         $item->setRoom($room);
         $em = $this->getDoctrine()->getManager();
         $em->persist($item);
         $em->flush();
 
         //$request->getSession()->getFlashBag()->add('notice', 'room bien enregistrée.');

         $listitems = $em->getRepository('SeeItAllassetXploreBundle:item')->findBy(array('room' => $room));
 
         // On redirige vers la page de visualisation de l'annonce nouvellement créée
         return $this->render('SeeItAllassetXploreBundle:App:room.html.twig', array(
           'form' => $form->createView(),'building' => $building, 'room' => $room, 'items' => $listitems, 'items_number' => $items_number  ));        
       } */

       //FORM 2: changing the item's name
       $form_itemName->handleRequest($request);
      
       if ($form_itemName->isSubmitted() && $form_itemName->isValid()) {

         
         $em = $this->getDoctrine()->getManager();
         $em->persist($item);
         $em->flush();

         
          //url redirection
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_items', array(
            'building_id' => $building->getId(), 'room_id' => $room->getId(), 'item_id' => $item->getId() ))); 

         // On redirige vers la page de visualisation de l'annonce nouvellement créée
         return $this->render('SeeItAllassetXploreBundle:App:item.html.twig', array(
           'form' => $form->createView(),
           'name_form' => $form_itemName->createView(),
           'item' => $item,'building' => $building,'room' => $room 
           )); 
       } 

          /*
        //FORM 3: saving edited item        
        $form_saveImage->handleRequest($request);

        //This form doesn't hydrate directly the item object( which is totally possible), but instead he fill an image object (Objects/image) 
        if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {

          $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
          $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param

          define('UPLOAD_DIR', 'uploads/'); // define the upload path
       
          
        //Here we extract the header from the raw 64base image data
        $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
          
          $file = UPLOAD_DIR.date('Y-m-d H:i:s'); // We give the file a unique name (converted timestamp)
          $success = file_put_contents($file,  $data); //store the image data in a file
          print $success ? $file : 'Unable to save the file.';

          //item hydration
          $item->setitemName(uniqid());
          $item->setitemImage($file);
          $item->setNote($image->getNote());
          $item->setIdAsset($image->getAssetId());
          $item->setContractNumber($image->getContractNumber());

          //storing the image in the db
          $em = $this->getDoctrine()->getManager();
          $em->persist($item); 
          $em->flush();

          return $this->render('SeeItAllassetXploreBundle:App:room.html.twig', array(
              'form' => $form->createView(),'form_saveImage' => $form_saveImage->createView(),'name_form' => $form_itemName->createView(), 
              'items' => $listitems, 'items_number' => $items_number));

       }  */

      }
  
      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
      return $this->render('SeeItAllassetXploreBundle:App:item.html.twig', array(
        'form' => $form->createView(),
        'name_form' => $form_itemName->createView(),
        'item' => $item,'building' => $building,'room' => $room  ));
    }


    public function removeItemPdfAction($document_id, $building_id, $room_id, $item_id, Request $request)
    {
      
      //When you query for a particular type of object, you always use what's known as its "repository". 
    
     
      $document =new document();
      
      $em = $this->getDoctrine()->getManager(); 
      $document= $em->getRepository('SeeItAllassetXploreBundle:document')->find($document_id);
     
      

      if (null === $document) {
        throw new NotFoundHttpException("the document with the id ".$document_id." do not exist");
      }

      $em->remove($document);
      $em->flush();
      
    return $this->redirectToRoute('see_it_allasset_xplor_items_docs',  array('building_id' => $building_id , 'room_id' => $room_id , 'item_id' => $item_id));
    
    }





    

}