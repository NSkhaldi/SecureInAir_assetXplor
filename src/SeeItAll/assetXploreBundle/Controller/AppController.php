<?php



namespace SeeItAll\assetXploreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use SeeItAll\assetXploreBundle\Entity\Level0;
use SeeItAll\assetXploreBundle\Entity\Level1;
use SeeItAll\assetXploreBundle\Entity\Level2;
use SeeItAll\assetXploreBundle\Entity\Level3;
use SeeItAll\assetXploreBundle\Entity\Level4;
use SeeItAll\assetXploreBundle\Entity\Level5;
use SeeItAll\assetXploreBundle\Entity\Level6;

use SeeItAll\assetXploreBundle\Entity\Document;
use SeeItAll\assetXploreBundle\Objects\image; //FOR new edited images
use SeeItAll\assetXploreBundle\Entity\Map;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use SeeItAll\assetXploreBundle\Form\Level0Type;
use SeeItAll\assetXploreBundle\Form\Level1Type;
use SeeItAll\assetXploreBundle\Form\Level2Type;
use SeeItAll\assetXploreBundle\Form\Level3Type;
use SeeItAll\assetXploreBundle\Form\Level4Type;
use SeeItAll\assetXploreBundle\Form\Level5Type;
use SeeItAll\assetXploreBundle\Form\Level6Type;


use SeeItAll\assetXploreBundle\Form\titleType;



use SeeItAll\assetXploreBundle\Form\Level0NameType;
use SeeItAll\assetXploreBundle\Form\Level1NameType;
use SeeItAll\assetXploreBundle\Form\Level2NameType;
use SeeItAll\assetXploreBundle\Form\Level3NameType;
use SeeItAll\assetXploreBundle\Form\Level4NameType;
use SeeItAll\assetXploreBundle\Form\Level5NameType;
use SeeItAll\assetXploreBundle\Form\Level6NameType;


use SeeItAll\assetXploreBundle\Form\Level0UrlType;
use SeeItAll\assetXploreBundle\Form\Level1UrlType;
use SeeItAll\assetXploreBundle\Form\Level2UrlType;
use SeeItAll\assetXploreBundle\Form\Level3UrlType;
use SeeItAll\assetXploreBundle\Form\Level4UrlType;
use SeeItAll\assetXploreBundle\Form\Level5UrlType;
use SeeItAll\assetXploreBundle\Form\Level6UrlType;

use SeeItAll\assetXploreBundle\Form\saveImageType;
use SeeItAll\assetXploreBundle\Form\saveDocType;
use SeeItAll\assetXploreBundle\Form\saveMapType;

use SeeItAll\assetXploreBundle\Form\Level0LocType;
use SeeItAll\assetXploreBundle\Form\Level1LocType;
use SeeItAll\assetXploreBundle\Form\Level2LocType;
use SeeItAll\assetXploreBundle\Form\Level3LocType;
use SeeItAll\assetXploreBundle\Form\Level4LocType;
use SeeItAll\assetXploreBundle\Form\Level5LocType;
use SeeItAll\assetXploreBundle\Form\Level6LocType;





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




public function level0Action(Request $request)
{

      // EMPTY OBJECTS INSTANCIATION
      $level0 = new Level0();
      $image= new image();
      $map= new Map();


      // GET THE FORMS
      $form_addImage = $this->get('form.factory')->create(Level0Type::class, $level0);
      $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
      $form_level0Name = $this->get('form.factory')->create(Level0NameType::class, $level0);
      $form_level0Loc = $this->get('form.factory')->create(Level0LocType::class, $level0);
      $form_level0Url = $this->get('form.factory')->create(Level0UrlType::class, $level0);
      
      $em = $this->getDoctrine()->getManager(); //GET THE ENTITY MANAGER (It's responsible for saving objects to, and fetching objects from, the database.)
      $list_level0Assets = $em->getRepository('SeeItAllassetXploreBundle:Level0')->findAll(); //GET the REPOSITORY and fetch objects (You can think of a repository as a PHP class whose only job is to help you fetch entities of a certain class.)
      $level0_assetsNumber = count($list_level0Assets );


         // BY DEFAULT 'POST' IS THE METHOS USED BY FORMS
     if ($request->isMethod('POST')) {

        $form_addImage->handleRequest($request);// it takes the POSTed data from the previous request, processes it, and runs any validation (checks integrity of expected versus received data). it only does this for POST requests
        //Form here the building object is hydrated by the form

        //FORM 1: adding level0 assets
        if ($form_addImage->isSubmitted() && $form_addImage->isValid()) { //CHECK whether this was submitted and whether it is valid 
    
          $em->persist($level0); 
          $em->flush();
         // $request->getSession()->getFlashBag()->add('notice', 'Building bien enregistrée.');
          //$listbuildings = $em->getRepository('SeeItAllassetXploreBundle:building')->findAll();

          //url redirection (solves reupload when refresh)
          return $this->redirect($this->generateUrl('see_it_allasset_xplor_level0')); 
        }


        //FORM 2 :saving edited image        
        $form_saveImage->handleRequest($request);

        //This form doesn't hydrate directly the building object( which is totally possible), but instead he fill an image object (Objects/image) 
            if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) 
            {

                $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
                $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
                
            // $level0=  saveEditedImage($level0, $edited_image)

                define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
                define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
            
                
            //Here we extract the header from the raw 64base image data
                $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                
                $filename=date('Y-m-d H:i:s');
                $filename_thumb = date('Y-m-d H:i:s');
                $file_path = UPLOAD_DIR_STD.$filename;
                $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
                // We give the file a unique name (converted timestamp)
                $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
                print $success ? $file_path : 'Unable to save the file.';

                //Level's asset hydration
                $level0->setLevel0Name($filename);
                $level0->setImgPath($file_path);
                $level0->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
                $level0->setUniqId(); 
                $level0->setThumbPath($file_thumb_path); //stores the thumnail    
                $level0->setNote($image->getNote());
                $level0->setIdAsset($image->getAssetId());
                $level0->setContractNumber($image->getContractNumber());
                $level0->setDisplay(1);

                //storing the image in the db
                $em = $this->getDoctrine()->getManager();
                $em->persist($level0); 
                $em->flush();

            //url redirection (solves reupload when refresh)
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_level0')); 
            }

          
      } 

        return $this->render('SeeItAllassetXploreBundle:App:level0.html.twig', array(
      'form' => $form_addImage->createView(),
      'form_saveImage' => $form_saveImage->createView(),
      'name_form' => $form_level0Name ->createView(),
      'form_level0Loc' => $form_level0Loc->createView(),
      'form_level0Url' => $form_level0Url->createView(),
      'level0_assets' => $list_level0Assets, 'asset_number' => $level0_assetsNumber ));
}

public function removeLevel0Action($level0_id,Request $request)
{
    
    //When you query for a particular type of object, you always use what's known as its "repository". 
    $level0 = new Level0();
    $em = $this->getDoctrine()->getManager(); 
    $level0= $em->getRepository('SeeItAllassetXploreBundle:Level0')->findOneBy(['id' => $level0_id]);

    if (null === $level0) {
      throw new NotFoundHttpException("the level0 with the id ".$level0_id." do not exist");
    }

    $em->remove($level0);
    $em->flush();
    
     return $this->redirectToRoute('see_it_allasset_xplor_level0');
    
}
 
public function doclevel0Action($level0_id,Request $request)
{
  
 // EMPTY OBJECTS INSTANCIATION
  $level0 = new Level0();
  $document= new Document();
  
  $em = $this->getDoctrine()->getManager();
  $level0= $em->getRepository('SeeItAllassetXploreBundle:level0')->find($level0_id);
  $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('level0' => $level0));

  // GET FORMS
  $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

  if (null === $document) {
    throw new NotFoundHttpException("the document for the level0 with the id ".$level0_id." do not exist");
  }

    if ($request->isMethod('POST')) {

    //FORM 1: adding level0s  docs        
     $form_saveDoc->handleRequest($request);

     if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

        $document->setlevel0($level0);
        $em->persist($document);
        $em->flush();
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level0_docs', array(
            'level0_id' => $level0->getId())));
     }
    }

    return $this->render('SeeItAllassetXploreBundle:App:docs_level0.html.twig', array(
        'form_saveDoc' => $form_saveDoc->createView(),
        'docs' => $listdocuments,'level0' => $level0, 'level0_id' => $level0_id, ));

}

public function removeLevel0DocAction($document_id, $level0_id,Request $request)
{
  
  
  $document =new Document();
  $em = $this->getDoctrine()->getManager(); 
 
  $document= $em->getRepository('SeeItAllassetXploreBundle:document')->find($document_id);
  if (null === $document) {
    throw new NotFoundHttpException("the building with the id ".$document_id." do not exist");
  }

  $em->remove($document);
  $em->flush();
  
  return $this->redirectToRoute('see_it_allasset_xplor_level0_docs',  array('level0_id' => $level0_id));
  
}

public function urlLevel0Action($level0_id, Request $request)
{  
    $level0 = new Level0();
    $em = $this->getDoctrine()->getManager(); 
    $level0= $em->getRepository('SeeItAllassetXploreBundle:Level0')->findOneBy(['id' => $level0_id]);
    $form_level0Url = $this->get('form.factory')->create(Level0UrlType::class, $level0);
    

    $form_level0Url->handleRequest($request);   
    if ($form_level0Url->isSubmitted() && $form_level0Url->isValid()) {
    $em->persist($level0);
    $em->flush();
    // url redirection
    return $this->redirect($this->generateUrl('see_it_allasset_xplor_level0')); 
    }
}

public function loclevel0Action($level0_id,Request $request) 
{

  $level0 = new Level0();
     
     $em = $this->getDoctrine()->getManager();
     $level0= $em->getRepository('SeeItAllassetXploreBundle:Level0')->findOneBy(['id' => $level0_id]);


     $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
     $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param

     $level0->SetDataLoc($loc_data);
     $em->persist($level0); 
     $em->flush();
     return $this->redirect($this->generateUrl('see_it_allasset_xplor_level0')); 


}








public function level1Action($level0_id,Request $request)
{

  /*        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
        // Sinon on déclenche une exception « Accès interdit »
        throw new AccessDeniedException('Accès limité aux auteurs.');
      } */  
  

  // EMPTY OBJECTS INSTANCIATION
  $level0= new Level0();
  $level1 = new Level1();
  $image= new image();
  
  
  

  //GET the level1 assets associated with the level0
    $em = $this->getDoctrine()->getManager();
    $level0= $em->getRepository('SeeItAllassetXploreBundle:level0')->find($level0_id);
    $list_level1Assets = $em->getRepository('SeeItAllassetXploreBundle:Level1')->findBy(array('level0' => $level0));
    $level1_assetsNumber = count($list_level1Assets);
   

  // GET THE FORMS
  $form = $this->get('form.factory')->create(level1Type::class, $level1);
  $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
  $form_level0Name = $this->get('form.factory')->create(Level0NameType::class, $level0);
  $form_level1Loc = $this->get('form.factory')->create(Level1LocType::class, $level1);
  $form_level1Url = $this->get('form.factory')->create(Level1UrlType::class, $level1);
  $data= array();
  $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                  ->add('save',  SubmitType::class) //This form is not attached to a class
                                                  ->getForm();
      

  $em = $this->getDoctrine()->getManager(); //GET THE ENTITY MANAGER (It's responsible for saving objects to, and fetching objects from, the database.)
  $listlevel1s = $em->getRepository('SeeItAllassetXploreBundle:level1')->findAll(); //GET the REPOSITORY and fetch objects (You can think of a repository as a PHP class whose only job is to help you fetch entities of a certain class.)
  $level1s_number = count($listlevel1s);
  
  

  // BY DEFAULT POST IS THE METHOS USED BY FORMS
  if ($request->isMethod('POST')) {

    $form->handleRequest($request);// it takes the POST’ed data from the previous request, processes it, and runs any validation (checks integrity of expected versus received data). it only does this for POST requests
    //Form here the level1 object is hydrated by the form

    //FORM 1: adding level1s
    if ($form->isSubmitted() && $form->isValid()) { //CHECK whether this was submitted and whether it is valid 

      $level1->setLevel0($level0);
      $em->persist($level1); 
      $em->flush();

      //url redirection (solves reupload when refresh)
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1',  array(
        'level0_id' => $level0->getId())));
    }



    $form_saveMap->handleRequest($request);
    //FORM 2: adding maps
    if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
          
      $data = $form_saveMap->getData();
      $uniqid= uniqid();
      $data["file"]->move(
          $level0->getUploadRootDir().$level0->getMapsUploadDir(), // Le répertoire de destination
          $uniqid // Le nom du fichier à créer, ici « id.extension »
         );

      $level0->setMapPath($level0->getMapsUploadDir().$uniqid);
      $em->persist($level0); 
      $em->flush(); 
      

      //url redirection (solves reupload when refresh)
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1',  array(
        'level0_id' => $level0->getId()))); 
    }


    
       //FORM 3: changing the level0's name
       $form_level0Name->handleRequest($request);
     
       if ($form_level0Name->isSubmitted() && $form_level0Name->isValid()) {
  
         $em = $this->getDoctrine()->getManager();
         $em->persist($level0);
         $em->flush();
         // url redirection
         return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1', array(
           'level0_id' => $level0->getId() ))); 
        }


    //FORM4 :saving edited image        
    $form_saveImage->handleRequest($request);

     
     //This form doesn't hydrate directly the level1 object( which is totally possible), but instead he fill an image object (Objects/image) 
    if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {

        $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
        $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param

        define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
        define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
     
        
      //Here we extract the header from the raw 64base image data
        $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        
        $filename=date('Y-m-d H:i:s');
        $filename_thumb = date('Y-m-d H:i:s');
        $file_path = UPLOAD_DIR_STD.$filename;
        $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
         // We give the file a unique name (converted timestamp)
          $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
        print $success ? $file_path : 'Unable to save the file.';

        //Level's asset hydration
        $level1->setLevel1Name($filename);
        $level1->setImgPath($file_path);
        $level1->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
        $level1->setUniqId(); 
        $level1->setThumbPath($file_thumb_path); //stores the thumnail    
        $level1->setNote($image->getNote());
        $level1->setIdAsset($image->getAssetId());
        $level1->setContractNumber($image->getContractNumber());
        $level1->setDisplay(1);

        //storing the image in the db
        $level1->setLevel0($level0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($level1); 
        $em->flush();

      //url redirection (solves reupload when refresh)
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1',  array(
          'level0_id' => $level0->getId())));

    }
  } 

  // À ce stade, le formulaire n'est pas valide car :
  // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
  // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
  return $this->render('SeeItAllassetXploreBundle:App:level1.html.twig', array(
    'form' => $form->createView(),
    'form_saveImage' => $form_saveImage->createView(),
    'form_saveMap' => $form_saveMap->createView(),
    'name_form' => $form_level0Name->createView(),
    'form_level1Loc' => $form_level1Loc->createView(),
    'form_level1Url' => $form_level1Url->createView(),
    'level0_id' => $level0_id, 'level0' => $level0,
     'level1' => $level1, 'level1_assets' => $list_level1Assets, 'asset_number' => $level1_assetsNumber   ));
}

public function removelevel1Action($level0_id, $level1_id,Request $request)
{
  
  //When you query for a particular type of object, you always use what's known as its "repository". 
  $level1 = new Level1();
  $em = $this->getDoctrine()->getManager(); 
  $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->findOneBy(['id' => $level1_id]);

  if (null === $level1) {
    throw new NotFoundHttpException("the level1 with the id ".$level1_id." do not exist");
  }

  $em->remove($level1);
  $em->flush();
  
 return $this->redirectToRoute('see_it_allasset_xplor_level1', array('level0_id' => $level0_id ));
  
}

public function doclevel1Action($level0_id, $level1_id,Request $request)
{
  
 // EMPTY OBJECTS INSTANCIATION
  $level1 = new Level1();
  $document= new Document();
  
  $em = $this->getDoctrine()->getManager();
  $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id);
  $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:Document')->findBy(array('level1' => $level1));

  // GET FORMS
  $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

  if (null === $document) {
    throw new NotFoundHttpException("the document for the level1 with the id ".$level1_id." do not exist");
  }

    if ($request->isMethod('POST')) {

    //FORM 1: adding level1s  docs        
     $form_saveDoc->handleRequest($request);

     if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

        $document->setLevel1($level1);
        $em->persist($document);
        $em->flush();
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1_docs', array(
            'level0_id' => $level0_id,  'level1_id' => $level1->getId())));
     }
    }

    return $this->render('SeeItAllassetXploreBundle:App:docs_level1.html.twig', array(
        'form_saveDoc' => $form_saveDoc->createView(),
        'docs' => $listdocuments,'level1' => $level1, 'level0_id' => $level0_id, 'level1_id' => $level1_id));

}

public function removeLevel1DocAction($document_id, $level0_id, $level1_id,Request $request)
{
  
  
  $document =new Document();
  $em = $this->getDoctrine()->getManager(); 
 
  $document= $em->getRepository('SeeItAllassetXploreBundle:document')->find($document_id);
  if (null === $document) {
    throw new NotFoundHttpException("the building with the id ".$document_id." do not exist");
  }

  $em->remove($document);
  $em->flush();
  
  return $this->redirectToRoute('see_it_allasset_xplor_level1_docs',  array('level0_id' => $level0_id,  'level1_id' => $level1_id));
  
}

public function urlLevel1Action($level0_id,$level1_id, Request $request)
{  
   $level1 = new Level1();
   $em = $this->getDoctrine()->getManager(); 
   $level0= $em->getRepository('SeeItAllassetXploreBundle:Level1')->findOneBy(['id' => $level1_id]);
   $form_level1Url = $this->get('form.factory')->create(Level1UrlType::class, $level0);
   

   $form_level1Url->handleRequest($request);   
   if ($form_level1Url->isSubmitted() && $form_level1Url->isValid()) {
   $em->persist($level1);
   $em->flush();
   // url redirection
   return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1', array(
    'level0_id' => $level0_id))); 
   }
}

public function loclevel1Action($level0_id, $level1_id, Request $request)
{

        $level1 = new Level1();
           
           $em = $this->getDoctrine()->getManager();
           $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->findOneBy(['id' => $level1_id]);
    
    
           $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
           $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
           $level1->SetDataLoc($loc_data);
           $em->persist($level1); 
           $em->flush();
         //url redirection
         return $this->redirect($this->generateUrl('see_it_allasset_xplor_level1',  array(
            'level0_id' => $level0_id)));
    
}








public function level2Action($level0_id,$level1_id,Request $request)
{
  // EMPTY OBJECTS INSTANCIATION
  $level0 = new Level0();
  $level1 = new Level1();
  $level2 = new Level2();
  $image= new image();

   //GET the level2s associated with the level1
   $em = $this->getDoctrine()->getManager();
   $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id); 
   $list_level2Assets = $em->getRepository('SeeItAllassetXploreBundle:Level2')->findBy(array('level1' => $level1));
   $level1_assetsNumber = count($list_level2Assets);

  // GET FORMS
  $form = $this->get('form.factory')->create(Level2Type::class, $level2);
  $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
  $form_level1Name = $this->get('form.factory')->create(Level1NameType::class, $level1);
  $form_level2Loc = $this->get('form.factory')->create(Level2LocType::class, $level2);
  $form_level2Url = $this->get('form.factory')->create(Level2UrlType::class, $level2);
  $data= array();
  $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                  ->add('save',  SubmitType::class) //This form is not attached to a class
                                                  ->getForm();

  if ($request->isMethod('POST')) {

    //FORM 1: adding level2s  
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {   
      // On enregistre notre objet $advert dans la base de données, par exemple
      $level2->setlevel1($level1);
      $em->persist($level2);
      $em->flush();

     //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1->getId())));       
    }

    $form_saveMap->handleRequest($request);
    //FORM 2: adding maps
    if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
          
      $data = $form_saveMap->getData();
      $uniqid= uniqid();
      $data["file"]->move(
          $level1->getUploadRootDir().$level1->getMapsUploadDir(), // Le répertoire de destination
          $uniqid // Le nom du fichier à créer, ici « id.extension »
         );

      $level1->setMapPath($level1->getMapsUploadDir().$uniqid);
      $em->persist($level1); 
      $em->flush(); 
      

      //url redirection (solves reupload when refresh)
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1->getId())));  
    }
    
    //FORM 2: changing the level1's name
    $form_level1Name->handleRequest($request);
   
    if ($form_level1Name->isSubmitted() && $form_level1Name->isValid()) {

      $em = $this->getDoctrine()->getManager();
      $em->persist($level1);
      $em->flush();
     //url redirection
     return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1->getId())));  
     }


     //FORM 3: saving edited level2        
     $form_saveImage->handleRequest($request);

     //This form doesn't hydrate directly the level2 object( which is totally possible), but instead he fill an image object (Objects/image) 
     if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {

       $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
       $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param


       define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
       define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
    
       
     //Here we extract the header from the raw 64base image data
       $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
       $img = str_replace(' ', '+', $img);
       $data = base64_decode($img);
       
       $filename=date('Y-m-d H:i:s');
       $filename_thumb = date('Y-m-d H:i:s');
       $file_path = UPLOAD_DIR_STD.$filename;
       $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
        // We give the file a unique name (converted timestamp)
         $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
       print $success ? $file_path : 'Unable to save the file.';

       //Level's asset hydration
       $level2->setLevel2Name($filename);
       $level2->setImgPath($file_path);
       $level2->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
       $level2->setUniqId(); 
       $level2->setThumbPath($file_thumb_path); //stores the thumnail    
       $level2->setNote($image->getNote());
       $level2->setIdAsset($image->getAssetId());
       $level2->setContractNumber($image->getContractNumber());
       $level2->setDisplay(1);
       //storing the image in the db
       $level2->setLevel1($level1);
       $em = $this->getDoctrine()->getManager();
       $em->persist($level2); 
       $em->flush();

     //url redirection
     return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1->getId())));  

   } 
   
    }
  // À ce stade, le formulaire n'est pas valide car :
  // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
  // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
  return $this->render('SeeItAllassetXploreBundle:App:level2.html.twig', array(
    'form' => $form->createView(),
    'form_saveImage' => $form_saveImage->createView(),
    'form_saveMap' => $form_saveMap->createView(),
    'form_level2Url' => $form_level2Url->createView(),
    'name_form' => $form_level1Name->createView(),
    'form_level2Loc' => $form_level2Loc->createView(),
    'level0_id'=>$level0_id, 'level1_id'=>$level1_id  ,'level1' => $level1, 'level2_assets' => $list_level2Assets,'asset_number' => $level1_assetsNumber ));
  

  //}
}

public function removeLevel2Action($level0_id, $level1_id, $level2_id,Request $request)
{
  
  //When you query for a particular type of object, you always use what's known as its "repository". 
  $level2 = new Level2();
  $em = $this->getDoctrine()->getManager(); 
  $level2= $em->getRepository('SeeItAllassetXploreBundle:Level2')->findOneBy(['id' => $level2_id]);

  if (null === $level2) {
    throw new NotFoundHttpException("the level2 with the id ".$level2_id." do not exist");
  }

  $em->remove($level2);
  $em->flush();
  
     //url redirection
     return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1_id))); 
  
}

public function doclevel2Action($level0_id,$level1_id,$level2_id,Request $request)
{
  
 // EMPTY OBJECTS INSTANCIATION
  $level1= new Level1();
  $level2 = new Level2();
  $document= new Document();
  
  $em = $this->getDoctrine()->getManager();
  $level2= $em->getRepository('SeeItAllassetXploreBundle:level2')->find($level2_id);
  $level1= $em->getRepository('SeeItAllassetXploreBundle:level1')->find($level1_id);
  $listdocuments = $em->getRepository('SeeItAllassetXploreBundle:Document')->findBy(array('level2' => $level2));

  // GET FORMS
  $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

  if (null === $document) {
    throw new NotFoundHttpException("the document for the level2 with the id ".$level2_id." do not exist");
  }

    if ($request->isMethod('POST')) {

    //FORM 1: adding level1s  docs        
     $form_saveDoc->handleRequest($request);

     if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

        $document->setlevel2($level2);
        $em->persist($document);
        $em->flush();
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2_docs', array('level0_id' => $level0_id,
            'level1_id' => $level1_id ,'level2_id' =>$level2_id  )));
     }
    }

    return $this->render('SeeItAllassetXploreBundle:App:docs_level2.html.twig', array(
        'form_saveDoc' => $form_saveDoc->createView(),
        'level0_id' => $level0_id,
        'docs' => $listdocuments,'level1' => $level1, 'level2' => $level2 ));

}

public function removelevel2DocAction($document_id, $level0_id ,$level1_id, $level2_id, Request $request)
{
  
  //When you query for a particular type of object, you always use what's known as its "repository". 

 
  $document =new Document();
  
  $em = $this->getDoctrine()->getManager(); 
  $document= $em->getRepository('SeeItAllassetXploreBundle:Document')->find($document_id);
 
  

  if (null === $document) {
    throw new NotFoundHttpException("the document with the id ".$document_id." do not exist");
  }

  $em->remove($document);
  $em->flush();
  
 return $this->redirectToRoute('see_it_allasset_xplor_level2_docs',  array('level0_id' => $level0_id, 'level1_id' => $level1_id , 'level2_id' => $level2_id));

}

public function urlLevel2Action($level0_id,$level1_id,$level2_id, Request $request)
{  
   $level2 = new Level2();
   $em = $this->getDoctrine()->getManager(); 
   $level2= $em->getRepository('SeeItAllassetXploreBundle:Level2')->findOneBy(['id' => $level2_id]);
   $form_level2Url = $this->get('form.factory')->create(Level2UrlType::class, $level2);

   $form_level2Url->handleRequest($request);   
   if ($form_level2Url->isSubmitted() && $form_level2Url->isValid()) {
   $em->persist($level2);
   $em->flush();
   // url redirection
   return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
    'level0_id' => $level0_id,'level1_id' => $level1_id))); 
   }
}

public function loclevel2Action($level0_id, $level1_id, $level2_id, Request $request) 
{

    $level2 = new Level2();
       
       $em = $this->getDoctrine()->getManager();
       $level2= $em->getRepository('SeeItAllassetXploreBundle:Level2')->findOneBy(['id' => $level2_id]);


       $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
       $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param

       $level2->SetDataLoc($loc_data);
       $em->persist($level2); 
       $em->flush();
     //url redirection
     return $this->redirect($this->generateUrl('see_it_allasset_xplor_level2', array(
        'level0_id' => $level0_id ,'level1_id' => $level1_id  )));  

}







public function level3Action($level0_id, $level1_id, $level2_id,Request $request)
{

  // EMPTY OBJECTS INSTANCIATION
  $level1 = new Level1();
  $level2 = new Level2();
  $level3 = new Level3();
  $image= new image();
  
  

  //GET the level3s associated with the level2
  $em = $this->getDoctrine()->getManager();
  $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id);
  $level2= $em->getRepository('SeeItAllassetXploreBundle:Level2')->find($level2_id);
  $list_level3Assets = $em->getRepository('SeeItAllassetXploreBundle:Level3')->findBy(array('level2' => $level2));
  $level2_assetsNumber = count($list_level3Assets);

  // GET FORMS
  $form = $this->get('form.factory')->create(level3Type::class, $level3);
  $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
  $form_level2Name= $this->get('form.factory')->create(level2NameType::class, $level2);
  $form_level3Url = $this->get('form.factory')->create(Level3UrlType::class, $level3);
  $data= array();
  $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                  ->add('save',  SubmitType::class) //This form is not attached to a class
                                                  ->getForm();


  

  
  if ($request->isMethod('POST')) {

    //FORM 1: adding level2s
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) { 
      // On enregistre notre objet $advert dans la base de données, par exemple
      $level3->setlevel2($level2);
      $em = $this->getDoctrine()->getManager();
      $em->persist($level3);
      $em->flush();

      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id)));     
    }

    //FORM 2: changing the level2's name
    $form_level2Name->handleRequest($request);
   
    if ($form_level2Name->isSubmitted() && $form_level2Name->isValid()) {

      
      $em = $this->getDoctrine()->getManager();
      $em->persist($level2);
      $em->flush();

      $listlevel2s = $em->getRepository('SeeItAllassetXploreBundle:level3')->findBy(array('level2' => $level2));

      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id))); 
     }

     $form_saveMap->handleRequest($request);
     //FORM 3: adding maps
     if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
           
       $data = $form_saveMap->getData();
       $uniqid= uniqid();
       $data["file"]->move(
           $level2->getUploadRootDir().$level2->getMapsUploadDir(), // Le répertoire de destination
           $uniqid // Le nom du fichier à créer, ici « id.extension »
          );

       $level2->setMapPath($level2->getMapsUploadDir().$uniqid);
       $em->persist($level2); 
       $em->flush(); 
       

       //url redirection (solves reupload when refresh)
       return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id)));   
     }
     

     //FORM 4: saving edited level3        
     $form_saveImage->handleRequest($request);

     //This form doesn't hydrate directly the level3 object( which is totally possible), but instead he fill an image object (Objects/image) 
     if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {

       $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
       $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param

       define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
       define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
    
       
     //Here we extract the header from the raw 64base image data
       $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
       $img = str_replace(' ', '+', $img);
       $data = base64_decode($img);
       
       $filename=date('Y-m-d H:i:s');
       $filename_thumb = date('Y-m-d H:i:s');
       $file_path = UPLOAD_DIR_STD.$filename;
       $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
        // We give the file a unique name (converted timestamp)
         $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
       print $success ? $file_path : 'Unable to save the file.';

       //Level's asset hydration
       $level3->setLevel3Name($filename);
       $level3->setImgPath($file_path);
       $level3->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
       $level3->setUniqId(); 
       $level3->setThumbPath($file_thumb_path); //stores the thumnail    
       $level3->setNote($image->getNote());
       $level3->setIdAsset($image->getAssetId());
       $level3->setContractNumber($image->getContractNumber());
       $level3->setDisplay(1);

       //storing the image in the db
       $level3->setLevel2($level2);
       $em = $this->getDoctrine()->getManager();
       $em->persist($level3); 
       $em->flush();

      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id))); 

   }  

  }

  // twig template rendering
  return $this->render('SeeItAllassetXploreBundle:App:level3.html.twig', array(
    'form' => $form->createView(),
    'form_saveImage' => $form_saveImage->createView(),
    'form_saveMap' => $form_saveMap->createView(),
    'form_level3Url' => $form_level3Url->createView(),
    'name_form' => $form_level2Name->createView(),
    'level2' => $level2, 'level0_id' => $level0_id, 'level1_id' => $level1_id,'level2_id' => $level2_id,
    'level2' => $level2, 'level3_assets' => $list_level3Assets,'asset_number' , 'asset_number' => $level2_assetsNumber  )); 
} 

public function removeLevel3Action($level0_id, $level1_id, $level2_id, $level3_id, Request $request)
{
  
  //When you query for a particular type of object, you always use what's known as its "repository". 
  $level3 = new Level3();
  $em = $this->getDoctrine()->getManager(); 
  $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->findOneBy(['id' => $level3_id]);

  if (null === $level3) {
    throw new NotFoundHttpException("the level3 with the id ".$level3_id." do not exist");
  }

  $em->remove($level3);
  $em->flush();
  
      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id))); 
  
}

public function doclevel3Action($level0_id,$level1_id,$level2_id,$level3_id,Request $request)
{
  
 // EMPTY OBJECTS INSTANCIATION
  $level1= new Level1();
  $level2 = new Level2();
  $level3 = new Level3();
  $document= new document();
  
  $em = $this->getDoctrine()->getManager();
  $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id);
  $level2= $em->getRepository('SeeItAllassetXploreBundle:Level2')->find($level2_id);
  $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->find($level3_id);
  $list_documents = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('level3' => $level3));

  // GET FORMS
  $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

  if (null === $document) {
    throw new NotFoundHttpException("the document for the level2 with the id ".$level3_id." do not exist");
  }

    if ($request->isMethod('POST')) {

    //FORM 1: adding level1s  docs        
     $form_saveDoc->handleRequest($request);

     if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

        $document->setLevel3($level3);
        $em->persist($document);
        $em->flush();
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3_docs', array(
            'level0_id' => $level0_id, 'level1_id' => $level1_id ,'level2_id' =>$level2_id, 'level3_id' =>$level3_id  )));
     }
    }

    return $this->render('SeeItAllassetXploreBundle:App:docs_level3.html.twig', array(
        'form_saveDoc' => $form_saveDoc->createView(),'level0_id' => $level0_id, 'level1_id' => $level1_id,
        'docs' => $list_documents, 'level2' => $level2, 'level3' => $level3 ));

}

public function removeLevel3DocAction($document_id, $level0_id, $level1_id, $level2_id, $level3_id, Request $request)
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
  
 return $this->redirectToRoute('see_it_allasset_xplor_level3_docs',  array('level0_id' => $level0_id , 'level1_id' => $level1_id , 'level2_id' => $level2_id , 'level3_id' => $level3_id));

}

public function urlLevel3Action($level0_id,$level1_id,$level2_id,$level3_id, Request $request)
{  
   $level3 = new Level3();
   $em = $this->getDoctrine()->getManager(); 
   $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->findOneBy(['id' => $level3_id]);
   $form_level3Url = $this->get('form.factory')->create(Level3UrlType::class, $level3);
   

   $form_level3Url->handleRequest($request);   
   if ($form_level3Url->isSubmitted() && $form_level3Url->isValid()) {
   $em->persist($level3);
   $em->flush();
   // url redirection
   return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
    'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id))); 
   }
}

public function loclevel3Action($level0_id, $level1_id, $level2_id, $level3_id, Request $request)
{

    $level3 = new Level3();
       
       $em = $this->getDoctrine()->getManager();
       $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->findOneBy(['id' => $level3_id]);


       $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
       $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param

       $level3->SetDataLoc($loc_data);
       $em->persist($level3); 
       $em->flush();
      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level3', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id, 'level2_id' => $level2_id))); 

}











public function level4Action($level0_id, $level1_id, $level2_id,$level3_id,Request $request)
{

         // EMPTY OBJECTS INSTANCIATION
         $level3 = new Level3();
         $level4 = new Level4();
         $image= new image();
         
         
   
         //GET the level4 associated with the level3
         $em = $this->getDoctrine()->getManager();
         $level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id);
         $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->find($level3_id);
         $list_level4Assets = $em->getRepository('SeeItAllassetXploreBundle:Level4')->findBy(array('level3' => $level3));
         $level4_assetsNumber = count($list_level4Assets);
      
         // GET FORMS
         $form = $this->get('form.factory')->create(level4Type::class, $level4);
         $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
         $form_level3Name= $this->get('form.factory')->create(level3NameType::class, $level3);
         $form_level4Url = $this->get('form.factory')->create(Level4UrlType::class, $level4);
         $data= array();
         $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                         ->add('save',  SubmitType::class) //This form is not attached to a class
                                                         ->getForm();
   
   
         
     
         
         if ($request->isMethod('POST')) {
   
           //FORM 1: adding level4 assets
           $form->handleRequest($request);
     
     
           if ($form->isSubmitted() && $form->isValid()) { 
             // On enregistre notre objet $advert dans la base de données, par exemple
             $level4->setLevel3($level3);
             $em = $this->getDoctrine()->getManager();
             $em->persist($level4);
             $em->flush();
     
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
               'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id)));     
           }
   
           $form_saveMap->handleRequest($request);
           //FORM 2: adding maps
           if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
                 
             $data = $form_saveMap->getData();
             $uniqid= uniqid();
             $data["file"]->move(
                 $level3->getUploadRootDir().$level3->getMapsUploadDir(), // Le répertoire de destination
                 $uniqid // Le nom du fichier à créer, ici « id.extension »
                );
     
             $level3->setMapPath($level3->getMapsUploadDir().$uniqid);
             $em->persist($level3); 
             $em->flush(); 
             
     
             //url redirection (solves reupload when refresh)
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id)));     
           }
        

           //FORM 3: changing the level3's name
           $form_level3Name->handleRequest($request);
          
           if ($form_level3Name->isSubmitted() && $form_level3Name->isValid()) {
   
             
             $em = $this->getDoctrine()->getManager();
             $em->persist($level3);
             $em->flush();
   
             $listlevel3s = $em->getRepository('SeeItAllassetXploreBundle:level4')->findBy(array('level3' => $level3));
     
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
               'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id))); 
            }
   
   
            //FORM 4: saving edited level4        
            $form_saveImage->handleRequest($request);
   
            //This form doesn't hydrate directly the level4 object( which is totally possible), but instead he fill an image object (Objects/image) 
            if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {
    
              $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
              $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
              define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
              define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
           
              
            //Here we extract the header from the raw 64base image data
              $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
              $img = str_replace(' ', '+', $img);
              $data = base64_decode($img);
              
              $filename=date('Y-m-d H:i:s');
              $filename_thumb = date('Y-m-d H:i:s');
              $file_path = UPLOAD_DIR_STD.$filename;
              $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
               // We give the file a unique name (converted timestamp)
                $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
              print $success ? $file_path : 'Unable to save the file.';
    
              //Level's asset hydration
              $level4->setLevel4Name($filename);
              $level4->setImgPath($file_path);
              $level4->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
              $level4->setUniqId(); 
              $level4->setThumbPath($file_thumb_path); //stores the thumnail    
              $level4->setNote($image->getNote());
              $level4->setIdAsset($image->getAssetId());
              $level4->setContractNumber($image->getContractNumber());
              $level4->setDisplay(1);
    
              //storing the image in the db
              $level4->setLevel3($level3);
              $em = $this->getDoctrine()->getManager();
              $em->persist($level4); 
              $em->flush();
    
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id))); 
    
          }  
   
         }
     
         // twig template rendering
         return $this->render('SeeItAllassetXploreBundle:App:level4.html.twig', array(
           'form' => $form->createView(),
           'form_saveMap' => $form_saveMap->createView(),
           'form_saveImage' => $form_saveImage->createView(),
           'form_level4Url' => $form_level4Url->createView(),
           'name_form' => $form_level3Name->createView(),'level3' => $level3, 'level0_id' => $level0_id, 'level1_id' => $level1_id,'level2_id' => $level2_id,
           'level3_id' => $level3_id, 'level4_assets' => $list_level4Assets,'asset_number' , 'asset_number' => $level4_assetsNumber  )); 

}

public function removeLevel4Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id, Request $request)
{
      //When you query for a particular type of object, you always use what's known as its "repository". 
      $level4 = new Level4();
      $em = $this->getDoctrine()->getManager(); 
      $level4= $em->getRepository('SeeItAllassetXploreBundle:Level4')->findOneBy(['id' => $level4_id]);

      if (null === $level4) {
        throw new NotFoundHttpException("the level4 with the id ".$level4_id." do not exist");
      }

      $em->remove($level4);
      $em->flush();
      
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id))); 
}

public function doclevel4Action($level0_id,$level1_id,$level2_id,$level3_id,$level4_id,Request $request)
{

        // EMPTY OBJECTS INSTANCIATION
      
      $level3 = new Level3();
      $level4 = new Level4();
      $document= new document();
      
      $em = $this->getDoctrine()->getManager();
      $level3= $em->getRepository('SeeItAllassetXploreBundle:Level3')->find($level3_id);
      $level4= $em->getRepository('SeeItAllassetXploreBundle:Level4')->find($level4_id);
      $list_documents = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('level4' => $level4));

      // GET FORMS
      $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

      if (null === $document) {
        throw new NotFoundHttpException("the document for the level3 with the id ".$level4_id." do not exist");
      }

        if ($request->isMethod('POST')) {

        //FORM 1: adding level1s  docs        
         $form_saveDoc->handleRequest($request);

         if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

            $document->setLevel4($level4);
            $em->persist($document);
            $em->flush();
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4_docs', array(
                'level0_id' => $level0_id, 'level1_id' => $level1_id, 'level2_id' => $level2_id  ,'level3_id' =>$level3_id, 'level4_id' =>$level4_id  )));
         }
        }

        return $this->render('SeeItAllassetXploreBundle:App:docs_level4.html.twig', array(
            'form_saveDoc' => $form_saveDoc->createView(),'level0_id' => $level0_id, 'level1_id' => $level1_id,  'level2_id' => $level2_id,
            'docs' => $list_documents, 'level3' => $level3, 'level4' => $level4 ));
}

public function removeLevel4DocAction($document_id, $level0_id, $level1_id, $level2_id, $level3_id, $level4_id, Request $request)
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
      
     return $this->redirectToRoute('see_it_allasset_xplor_level3_docs',  array('level0_id' => $level0_id , 'level1_id' => $level1_id , 'level2_id' => $level2_id , 'level3_id' => $level3_id,  'level4_id' => $level4_id));
    
}
    
public function loclevel4Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id, Request $request)
{

        $level4 = new Level4();
           
           $em = $this->getDoctrine()->getManager();
           $level4= $em->getRepository('SeeItAllassetXploreBundle:Level4')->findOneBy(['id' => $level4_id]);
    
    
           $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
           $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
           $level4->SetDataLoc($loc_data);
           $em->persist($level4); 
           $em->flush();
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id))); 
    
}







public function level5Action($level0_id, $level1_id, $level2_id,$level3_id, $level4_id,Request $request)
{

         // EMPTY OBJECTS INSTANCIATION
         $level4 = new Level4();
         $level5 = new Level5();
         $image= new image();
         
         
   
         //GET the level5 associated with the level4
         $em = $this->getDoctrine()->getManager();
         //$level1= $em->getRepository('SeeItAllassetXploreBundle:Level1')->find($level1_id);
         $level4= $em->getRepository('SeeItAllassetXploreBundle:Level4')->find($level4_id);
         $list_level5Assets = $em->getRepository('SeeItAllassetXploreBundle:Level5')->findBy(array('level4' => $level4));
         $level5_assetsNumber = count($list_level5Assets);
      
         // GET FORMS
         $form = $this->get('form.factory')->create(Level5Type::class, $level5);
         $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
         $form_level4Name= $this->get('form.factory')->create(Level4NameType::class, $level4);
         $form_level5Url = $this->get('form.factory')->create(Level5UrlType::class, $level5);
         $data= array();
         $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                         ->add('save',  SubmitType::class) //This form is not attached to a class
                                                         ->getForm();
   
   
         
     
         
         if ($request->isMethod('POST')) {
   
           //FORM 1: adding level5 assets
           $form->handleRequest($request);
     
     
           if ($form->isSubmitted() && $form->isValid()) { 
             // On enregistre notre objet $advert dans la base de données, par exemple
             $level5->setLevel4($level4);
             $em = $this->getDoctrine()->getManager();
             $em->persist($level5);
             $em->flush();
     
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
               'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id)));     
           }
   
           $form_saveMap->handleRequest($request);
           //FORM 2: adding maps
           if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
                 
             $data = $form_saveMap->getData();
             $uniqid= uniqid();
             $data["file"]->move(
                 $level4->getUploadRootDir().$level4->getMapsUploadDir(), // Le répertoire de destination
                 $uniqid // Le nom du fichier à créer, ici « id.extension »
                );
     
             $level4->setMapPath($level4->getMapsUploadDir().$uniqid);
             $em->persist($level4); 
             $em->flush();
             
     
             //url redirection (solves reupload when refresh)
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id))); 

            }
        

           //FORM 3: changing the level3's name
           $form_level4Name->handleRequest($request);
          
           if ($form_level4Name->isSubmitted() && $form_level4Name->isValid()) {
   
             
             $em = $this->getDoctrine()->getManager();
             $em->persist($level4);
             $em->flush();
   
             $listlevel4s = $em->getRepository('SeeItAllassetXploreBundle:Level4')->findBy(array('level4' => $level4));
     
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
               'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id))); 
            }
   
   
            //FORM 4: saving edited level5        
            $form_saveImage->handleRequest($request);
   
            //This form doesn't hydrate directly the level4 object( which is totally possible), but instead he fill an image object (Objects/image) 
            if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {
    
              $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
              $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
              define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
              define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
           
              
            //Here we extract the header from the raw 64base image data
              $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
              $img = str_replace(' ', '+', $img);
              $data = base64_decode($img);
              
              $filename=date('Y-m-d H:i:s');
              $filename_thumb = date('Y-m-d H:i:s');
              $file_path = UPLOAD_DIR_STD.$filename;
              $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
               // We give the file a unique name (converted timestamp)
                $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
              print $success ? $file_path : 'Unable to save the file.';
    
              //Level's asset hydration
              $level5->setLevel5Name($filename);
              $level5->setImgPath($file_path);
              $level5->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
              $level5->setUniqId(); 
              $level5->setThumbPath($file_thumb_path); //stores the thumnail    
              $level5->setNote($image->getNote());
              $level5->setIdAsset($image->getAssetId());
              $level5->setContractNumber($image->getContractNumber());
              $level5->setDisplay(1);
    
              //storing the image in the db
              $level5->setLevel4($level4);
              $em = $this->getDoctrine()->getManager();
              $em->persist($level5); 
              $em->flush();
    
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id))); 
    
          }  
   
         }
     
         // twig template rendering
         return $this->render('SeeItAllassetXploreBundle:App:level5.html.twig', array(
           'form' => $form->createView(),
           'form_saveMap' => $form_saveMap->createView(),
           'form_saveImage' => $form_saveImage->createView(),
           'form_level5Url' => $form_level5Url->createView(),
           'name_form' => $form_level4Name->createView(),'level4' => $level4, 'level0_id' => $level0_id, 'level1_id' => $level1_id,'level2_id' => $level2_id,
           'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_assets' => $list_level5Assets,'asset_number' , 'asset_number' => $level5_assetsNumber  )); 

}

public function removeLevel5Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id,$level5_id, Request $request)
{
    //When you query for a particular type of object, you always use what's known as its "repository". 
    $level5 = new Level4();
    $em = $this->getDoctrine()->getManager(); 
    $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->findOneBy(['id' => $level5_id]);

    if (null === $level5) {
      throw new NotFoundHttpException("the level5 with the id ".$level5_id." do not exist");
    }

    $em->remove($level5);
    $em->flush();
    
           //url redirection
           return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
              'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id))); 
}

public function doclevel5Action($level0_id,$level1_id,$level2_id,$level3_id,$level4_id,$level5_id,Request $request)
{

        // EMPTY OBJECTS INSTANCIATION
      
      $level4 = new Level4();
      $level5 = new Level5();
      $document= new document();
      
      $em = $this->getDoctrine()->getManager();
      $level4= $em->getRepository('SeeItAllassetXploreBundle:Level4')->find($level4_id);
      $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->find($level5_id);
      $list_documents = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('level5' => $level5));

      // GET FORMS
      $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);

      if (null === $document) {
        throw new NotFoundHttpException("the document for the level5 with the id ".$level5_id." do not exist");
      }

        if ($request->isMethod('POST')) {

        //FORM 1: adding level1s  docs        
         $form_saveDoc->handleRequest($request);

         if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {

            $document->setLevel5($level5);
            $em->persist($document);
            $em->flush();
            return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5_docs', array(
                'level0_id' => $level0_id, 'level1_id' => $level1_id, 'level2_id' => $level2_id  ,'level3_id' =>$level3_id, 'level4_id' =>$level4_id, 'level5_id' =>$level5_id  )));
         }
        }

        return $this->render('SeeItAllassetXploreBundle:App:docs_level5.html.twig', array(
            'form_saveDoc' => $form_saveDoc->createView(),'level0_id' => $level0_id, 'level1_id' => $level1_id,  'level2_id' => $level2_id,  'level3_id' => $level3_id,
            'docs' => $list_documents, 'level4' => $level4, 'level5' => $level5 ));
}

public function removeLevel5DocAction($document_id, $level0_id, $level1_id, $level2_id, $level3_id, $level4_id,$level5_id, Request $request)
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
        
       return $this->redirectToRoute('see_it_allasset_xplor_level5_docs',  array('level0_id' => $level0_id , 'level1_id' => $level1_id , 'level2_id' => $level2_id , 'level3_id' => $level3_id,  'level4_id' => $level4_id,  'level5_id' => $level5_id));
      
}

public function urlLevel5Action($level0_id,$level1_id,$level2_id,$level3_id,$level4_id,$level5_id, Request $request)
{  
       $level5 = new Level5();
       $em = $this->getDoctrine()->getManager(); 
       $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->findOneBy(['id' => $level5_id]);
       $form_level5Url = $this->get('form.factory')->create(Level5UrlType::class, $level5);
       
    
       $form_level5Url->handleRequest($request);   
       if ($form_level5Url->isSubmitted() && $form_level5Url->isValid()) {
       $em->persist($level5);
       $em->flush();
       // url redirection
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id)));  
       }
}    

public function loclevel5Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id, $level5_id, Request $request)
{

    $level5 = new Level5();
           
    $em = $this->getDoctrine()->getManager();
    $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->findOneBy(['id' => $level5_id]);


    $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
    $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param

    $level5->SetDataLoc($loc_data);
    $em->persist($level5); 
    $em->flush();
      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level5', array(
         'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id))); 


}










public function level6Action($level0_id, $level1_id, $level2_id,$level3_id,$level4_id, $level5_id, Request $request)
{

         // EMPTY OBJECTS INSTANCIATION
         $level5 = new Level5();
         $level6 = new Level6();
         $image= new image();
         
         
   
         //GET the level6 associated with the level5
         $em = $this->getDoctrine()->getManager();
         $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->find($level5_id);
         $list_level6Assets = $em->getRepository('SeeItAllassetXploreBundle:Level6')->findBy(array('level5' => $level5));
         $level6_assetsNumber = count($list_level6Assets);
      
         // GET FORMS
         $form = $this->get('form.factory')->create(level6Type::class, $level6);
         $form_saveImage = $this->get('form.factory')->create(saveImageType::class, $image);
         $form_level5Name= $this->get('form.factory')->create(level5NameType::class, $level5);
         $form_level6Url = $this->get('form.factory')->create(Level6UrlType::class, $level6);
         $data= array();
         $form_saveMap  = $this->createFormBuilder($data)->add('file',   FileType::class)
                                                         ->add('save',  SubmitType::class) //This form is not attached to a class
                                                         ->getForm();
   
   
         
     
         
         if ($request->isMethod('POST')) {
   
           //FORM 1: adding level6 assets
           $form->handleRequest($request);
     
     
           if ($form->isSubmitted() && $form->isValid()) { 
             // On enregistre notre objet $advert dans la base de données, par exemple
             $level6->setLevel5($level5);
             $em = $this->getDoctrine()->getManager();
             $em->persist($level6);
             $em->flush();
     
             //url redirection (solves reupload when refresh)
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id))); 
           }
   
           $form_saveMap->handleRequest($request);
           //FORM 2: adding maps
           if ($form_saveMap->isSubmitted() &&  $form_saveMap->isValid()) { //CHECK whether this was submitted and whether it is valid 
                 
             $data = $form_saveMap->getData();
             $uniqid= uniqid();
             $data["file"]->move(
                 $level5->getUploadRootDir().$level5->getMapsUploadDir(), // Le répertoire de destination
                 $uniqid // Le nom du fichier à créer, ici « id.extension »
                );
     
             $level5->setMapPath($level5->getMapsUploadDir().$uniqid);
             $em->persist($level5); 
             $em->flush(); 
             
     
             //url redirection (solves reupload when refresh)
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id)));     
           }
        

           //FORM 3: changing the level5's name
           $form_level5Name->handleRequest($request);
          
           if ($form_level5Name->isSubmitted() && $form_level5Name->isValid()) {
   
             
             $em = $this->getDoctrine()->getManager();
             $em->persist($level5);
             $em->flush();
   
             $listlevel5s = $em->getRepository('SeeItAllassetXploreBundle:level6')->findBy(array('level5' => $level5));
     
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level4', array(
               'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id))); 
            }
   
   
            //FORM 4: saving edited level6        
            $form_saveImage->handleRequest($request);
   
            //This form doesn't hydrate directly the level6 object( which is totally possible), but instead he fill an image object (Objects/image) 
            if ($form_saveImage->isSubmitted() && $form_saveImage->isValid()) {
    
              $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
              $edited_image= $raw_data->get('save-image-input-image');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
              define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
              define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
           
              
            //Here we extract the header from the raw 64base image data
              $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
              $img = str_replace(' ', '+', $img);
              $data = base64_decode($img);
              
              $filename=date('Y-m-d H:i:s');
              $filename_thumb = date('Y-m-d H:i:s');
              $file_path = UPLOAD_DIR_STD.$filename;
              $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
               // We give the file a unique name (converted timestamp)
                $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
              print $success ? $file_path : 'Unable to save the file.';
    
              //Level's asset hydration
              $level6->setLevel6Name($filename);
              $level6->setImgPath($file_path);
              $level6->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
              $level6->setUniqId(); 
              $level6->setThumbPath($file_thumb_path); //stores the thumnail    
              $level6->setNote($image->getNote());
              $level6->setIdAsset($image->getAssetId());
              $level6->setContractNumber($image->getContractNumber());
              $level6->setDisplay(1);
    
              //storing the image in the db
              $level6->setLevel5($level5);
              $em = $this->getDoctrine()->getManager();
              $em->persist($level6); 
              $em->flush();
    
             //url redirection
             return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
                'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id)));
    
          }  
   
         }
     
         // twig template rendering
         return $this->render('SeeItAllassetXploreBundle:App:level6.html.twig', array(
            'form' => $form->createView(),
            'form_saveMap' => $form_saveMap->createView(),
            'form_saveImage' => $form_saveImage->createView(),
            'form_level6Url' => $form_level6Url->createView(),
            'name_form' => $form_level5Name->createView(),'level5' => $level5, 'level0_id' => $level0_id, 'level1_id' => $level1_id,'level2_id' => $level2_id,
            'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id, 'level6_assets' => $list_level6Assets,'asset_number' , 'asset_number' => $level6_assetsNumber  ));
}

public function removeLevel6Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id,$level5_id, $level6_id, Request $request)
{
     
    //When you query for a particular type of object, you always use what's known as its "repository". 
    $level6 = new Level5();
    $em = $this->getDoctrine()->getManager(); 
    $level6= $em->getRepository('SeeItAllassetXploreBundle:Level6')->findOneBy(['id' => $level6_id]);

    if (null === $level6) {
      throw new NotFoundHttpException("the level6 with the id ".$level6_id." do not exist");
    }

    $em->remove($level6);
    $em->flush();
    
           //url redirection
           return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
              'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id))); 
}

public function doclevel6Action($level0_id,$level1_id,$level2_id,$level3_id,$level4_id,$level5_id, $level6_id,Request $request)
{


        // EMPTY OBJECTS INSTANCIATION
      
        $level5 = new Level5();
        $level6 = new Level6();
        $document= new document();
        
        $em = $this->getDoctrine()->getManager();
        $level5= $em->getRepository('SeeItAllassetXploreBundle:Level5')->find($level5_id);
        $level6= $em->getRepository('SeeItAllassetXploreBundle:Level6')->find($level6_id);
        $list_documents = $em->getRepository('SeeItAllassetXploreBundle:document')->findBy(array('level6' => $level6));
  
        // GET FORMS
        $form_saveDoc = $this->get('form.factory')->create(saveDocType::class, $document);
  
        if (null === $document) {
          throw new NotFoundHttpException("the document for the level6 with the id ".$level6_id." do not exist");
        }
  
          if ($request->isMethod('POST')) {
  
          //FORM 1: adding level1s  docs        
           $form_saveDoc->handleRequest($request);
  
           if ($form_saveDoc->isSubmitted() && $form_saveDoc->isValid()) {
  
              $document->setLevel6($level6);
              $em->persist($document);
              $em->flush();
              return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6_docs', array(
                  'level0_id' => $level0_id, 'level1_id' => $level1_id, 'level2_id' => $level2_id  ,'level3_id' =>$level3_id, 'level5_id' =>$level5_id, 'level5_id' =>$level5_id  )));
           }
          }
  
          return $this->render('SeeItAllassetXploreBundle:App:docs_level6.html.twig', array(
              'form_saveDoc' => $form_saveDoc->createView(),'level0_id' => $level0_id, 'level1_id' => $level1_id,  'level2_id' => $level2_id, 'level3_id' => $level3_id,
              'docs' => $list_documents, 'level4_id' => $level4_id, 'level5' => $level5, 'level6' => $level6 ));
}

public function removeLevel6DocAction($document_id, $level0_id, $level1_id, $level2_id, $level3_id, $level4_id,$level5_id,$level6_id, Request $request)
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
    
   return $this->redirectToRoute('see_it_allasset_xplor_level6_docs',  array('level0_id' => $level0_id , 'level1_id' => $level1_id , 'level2_id' => $level2_id , 'level3_id' => $level3_id,  'level4_id' => $level4_id,  'level5_id' => $level5_id,  'level6_id' => $level6_id));
  
}

public function urlLevel6Action($level0_id,$level1_id,$level2_id,$level3_id,$level4_id,$level5_id,$level6_id, Request $request)
{  
       $level6 = new Level6();
       $em = $this->getDoctrine()->getManager(); 
       $level6= $em->getRepository('SeeItAllassetXploreBundle:Level6')->findOneBy(['id' => $level5_id]);
       $form_level6Url = $this->get('form.factory')->create(Level6UrlType::class, $level6);
       
    
       $form_level6Url->handleRequest($request);   
       if ($form_level6Url->isSubmitted() && $form_level6Url->isValid()) {
       $em->persist($level6);
       $em->flush();
       // url redirection
        return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
        'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id)));  
       }
} 

public function loclevel6Action($level0_id, $level1_id, $level2_id, $level3_id, $level4_id, $level5_id, $level6_id, Request $request)
{

    $level6 = new Level6();
       
    $em = $this->getDoctrine()->getManager();
    $level6= $em->getRepository('SeeItAllassetXploreBundle:Level6')->findOneBy(['id' => $level6_id]);
    
    
    $raw_data= $this->get('request_stack')->getCurrentRequest();  //take all the content of the resuest
    $loc_data= $raw_data->get('pin-data');   //extract the raw base64 image data from the hidden input by giving it's name as param
    
    $level6->SetDataLoc($loc_data);
    $em->persist($level6); 
    $em->flush();
      //url redirection
      return $this->redirect($this->generateUrl('see_it_allasset_xplor_level6', array(
         'level0_id' => $level0_id,'level1_id' => $level1_id,'level2_id' => $level2_id, 'level3_id' => $level3_id, 'level4_id' => $level4_id, 'level5_id' => $level5_id))); 
    
}















/*


public function saveEditedImage ($level){

    define('UPLOAD_DIR_STD', 'uploads/'); // define the upload path for standard size
    define('UPLOAD_DIR_THUMB', 'optim/');  // define the upload path for thumb size
 
    
  //Here we extract the header from the raw 64base image data
  $img = str_replace('data:image/jpeg;base64,', '', $edited_image);
  $img = str_replace(' ', '+', $img);
  $data = base64_decode($img);
    
    $filename=date('Y-m-d H:i:s');
    $filename_thumb = date('Y-m-d H:i:s');
    $file_path = UPLOAD_DIR_STD.$filename;
    $file_thumb_path = UPLOAD_DIR_THUMB.$filename;
     // We give the file a unique name (converted timestamp)
    $success = file_put_contents($file_path,  $data) && file_put_contents($file_thumb_path,  $data); //store the satandrd image data in a file
    print $success ? $file_path : 'Unable to save the file.';

    //Level's asset hydration
    $level->setLevel0Name($filename);
    $level->setImgPath($file_path);
    $level->generateThumbnail($file_thumb_path, 300, 150, $quality = 75);
    $level->setUniqId(); 
    $level->setThumbPath($file_thumb_path); //stores the thumnail    
    $level->setNote($image->getNote());
    $level->setIdAsset($image->getAssetId());
    $level->setContractNumber($image->getContractNumber());

    //storing the image in the db
    $em = $this->getDoctrine()->getManager();
    $em->persist($level0); 
    $em->flush();

return $level;

    $level1->setLevel0($level0);
}


*/







    

    

}