<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\comment;
use AppBundle\Entity\mensaje;
use AppBundle\Entity\post;
use AppBundle\Entity\categoria;
use AppBundle\Form\postType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\users1;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class HomeController extends Controller
{


    /**
     * @Route("/", name="homepage")
     */
    public function homeAction(Request $request)
    {
        $popular = $this->popular();
        $popular2 = $this->popular2();
        $editores = $this->editores();
        $recientes = $this->recientes();
        $recientes2 = $this->recientes2();
        $feacture = $this->feacture();


        return $this->render('base.html.twig', array(

            'allpost' => $recientes, 'popular' => $popular,

            'reciente2' => $recientes2, 'popular2' => $popular2,

            'editores' => $editores, 'feacture' => $feacture));

    }

    private function recientes()
    {
        $em = $this->getDoctrine()->getManager();
        $recientes = $em->getRepository('AppBundle:post')->findBy(array('seccion' => '1'), array('fechaPublicacion' => 'DESC'), 2);
        return $recientes;
    }

    private function recientes2()
    {
        $em = $this->getDoctrine()->getManager();
        $recientes = $em->getRepository('AppBundle:post')->findBy(array('seccion' => '1'), array('fechaPublicacion' => 'ASC'), 1);
        return $recientes;
    }

    private function popular()
    {
        $em = $this->getDoctrine()->getManager();
        $popular = $em->getRepository('AppBundle:post')->findBy(array('seccion' => '2'), array('fechaPublicacion' => 'DESC'), 4);
        return $popular;
    }

    private function popular2()
    {
        $em = $this->getDoctrine()->getManager();
        $popular = $em->getRepository('AppBundle:post')->findBy(array('seccion' => '2'), array('fechaPublicacion' => 'ASC'), 3);
        return $popular;
    }

    private function editores()
    {
        $em = $this->getDoctrine()->getManager();
        $editores = $em->getRepository('AppBundle:post')->findBy(array('seccion' => '3'), array('fechaPublicacion' => 'DESC'), 4);
        return $editores;
    }

    private function feacture()
    {
        $em = $this->getDoctrine()->getManager();
        $feacture = $em->getRepository('AppBundle:post')->findOneBySeccion(4, array('fechaPublicacion' => 'DESC'));
        return $feacture;
    }


    private function top()
    {

        $em = $this->getDoctrine()->getManager();


        $query2 = $em->createQuery(
            'SELECT u, COUNT (u.postcomment)  FROM AppBundle:comment u WHERE u.postcomment = 1 ORDER BY u.date DESC ');
        $top = $query2->getResult();
        return $top;

    }


    /**
     * @Route("/top", name="top")
     */
    public function topAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $top = $this->top();
        dump($top);
        exit();

        return $this->render('default/vistas/top.html.twig', array('top' => $top));
    }


    /**
     * @Route("/printig/{id}", name="printig")
     */
    public function printigAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:post')->find($id);
        $comment = $em->getRepository('AppBundle:comment')->findByPostcomment($id);
        //$comment = $em->getRepository('AppBundle:comment')->findBy(array('post_id'=>$id ));

        $popular = $this->popular();
        $editores = $this->editores();
        $recientes = $this->recientes();
        if ($request->getMethod() == 'POST') {

            $email = $request->get("email");
            $texto = $request->get("texto");
            $status = ('true');
            $postcomment = $id;

            $comentario = new comment();
            $comentario->setComment($texto);
            $comentario->setEmail($email);
            $comentario->setDate(new \DateTime("now"));
            $comentario->setStatus($status);

            $postcomment = $em->getRepository('AppBundle:post')->find($id);
            $comentario->setPostcomment($postcomment);
            $em->persist($comentario);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', '¡Genial! Tu Comentario ha sido enviado Muy pronto nos pondremos en contacto contigo.');

            return $this->redirectToRoute('printig',$id);

        }

        return $this->render('default/vistas/priting.html.twig', array('post' => $post,'recientes' => $recientes,'popular' => $popular,'editores' => $editores,  'comentario' => $comment));


    }

    /**
     * @Route("/nuevopost", name="nuevopost")
     */
    public function postAction(Request $request)
        //un query
    {

        //formulario de registro de Post
        $session = $request->getSession();
        $id = $this->get('session')->get('id');
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {

                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
                //$mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


                //me traigo el usuario que tiene la seccion activa
                $post = new post();
                $user = new users1();
                $cat= new categoria();


                //  $post->getCategoria()->add($categoria);
                //aqui creo y defino los campos del formulario para enviar a la vista
                $form = $this->createFormBuilder($post)
                    ->add('titulo', TextType::class)
                    ->add('seccion', ChoiceType ::class, array('choices' => array(
                        'Noticias Recientes' => 1,
                        'Populares' => 2,
                        'Seleccion de Editores' => 3,
                        'FEATURED NEWS' => 4,
                    ),))


                    ->add('contenido', TextareaType::class)
                    ->add('imagen', FileType::class)
                    ->add('save', SubmitType::class, array('label' => 'save'))->getForm();
                //un query
                // $c = $this->getDoctrine()->getRepository('AppBundle\Entity\categoria');
                // $categorias = $c->findAll();
                //fin de query


                $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()) {
//en esta parte seleciono cada campo que cree en mi formulario

                    // $categoria = $request->get("categoria");

                    // Recogemos el fichero
                    $file = $form['imagen']->getData();
                    $titulo = $form['titulo']->getData();
                    $contenido = $form['contenido']->getData();
                    $seccion = $form['seccion']->getData();
                    $cate= $form['categoria']->getData();

// Sacamos la extensión del fichero
                    $ext = $file->guessExtension();

// Le ponemos un nombre al fichero
                    $file_name = time() . "." . $ext;

// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
                    $file->move("uploads", $file_name);


// Establecemos el nombre de fichero en el atributo de la entidad
                    $post->setImagen($file_name);
                    $post->setSeccion($seccion);
                    $post->setTitulo($titulo);
                    $post->setContenido($contenido);

                    $post->setFechaPublicacion(new \DateTime("now"));

                    // tells Doctrine you want to (eventually) save the Product (no queries yet)

                    $postuser = $em->getRepository('AppBundle:users1')->find($id);
                    $post->setPostuser($postuser);
                    $post->cat->add($cat);
                    $cat->post->add($post);
                    $em->persist($post);

                    // actually executes the queries (i.e. the INSERT query)
                    $em->flush();

                    return $this->redirectToRoute('test');
                }
            }
        } else {


            return $this->render('default/vistas/permierror.html.twig');


        }


        return $this->render('default/vistas/post/newpost.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios, 'form' => $form->createView()));

    }


    /**
     * * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {
                $id = $this->get('session')->get('id');
                $em = $this->getDoctrine()->getManager();

                $repository = $this->getDoctrine()->getRepository('AppBundle:post');
                $q = $repository->findBy(
                    array('user_id' => $id),
                    array('fechaPublicacion' => 'DESC')
                );


                $dql = "SELECT u FROM AppBundle:post u WHERE u.user_id = '$id' ORDER BY u.fechaPublicacion DESC ";
                $post = $em->createQuery($dql);
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate($post, $request->query->getInt('page', 1), 15);








//seccion para los mensajes
                //  $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                return $this->render('default/vistas/test.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios, 'listpost' => $q ,'pagination'=>$pagination));

            }
        }


    }


    /**
     * * @Route("/listpost", name="listpost")
     */
    public function listAction(Request $request)

    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {
                $id = $this->get('session')->get('id');


                $repository = $this->getDoctrine()->getRepository('AppBundle:post');
                $q = $repository->findBy(
                    array('user_id' => $id),
                    array('fechaPublicacion' => 'DESC')
                );


                return $this->render('default/vistas/ListarPost.html.twig', array('listpost' => $q));

            }
        }

        return $this->render('default/vistas/ListarPost.html.twig', array('listpost' => $q));

    }


    /**
     * * @Route("/todopost", name="todopost")
     */


    public function allpostAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {
                $id = $this->get('session')->get('id');
                $em = $this->getDoctrine()->getManager();

                $dql = "SELECT u FROM AppBundle:post u ";
                $post = $em->createQuery($dql);
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate($post, $request->query->getInt('page', 1), 10

                );

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


                $deleformAjax = $this->createCustomForm(':POST_ID', 'DELETE', 'borrar');

                return $this->render('default/vistas/post/todopost.html.twig', array('post' => $post, 'pagination' => $pagination, 'delete_form_ajax' => $deleformAjax->createView(), 'mensajes' => $mensajes, 'comentarios' => $comentarios

                ));
            }
        }

    }


    /**
     * * @Route("/editar/{id}", name="editar")
     */
    public function editAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {
                $sesionid = $this->get('session')->get('id');
                $c = $this->getDoctrine()->getRepository('AppBundle\Entity\categoria');
                $categorias = $c->findAll();

                $post = $this->getDoctrine()
                    ->getRepository('AppBundle:post')
                    ->find($id);

                $form = $this->createEditForm($post);
            }
        }

        return $this->render('default/vistas/editar.html.twig', array('categorias' => $categorias, 'post' => $post, 'form' => $form->createView()));
    }


    //parte de actualizar

    private function createEditForm(post $post)
    {
        // Note the change of the first parameter of createForm
        $form = $this->createForm(postType::class, $post, array(
            'action' => $this->generateUrl('update', array('id' => $post->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }


    /**
     * * @Route("/update/{id}", name="update")
     */


    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:post')->find($id);
        $c = $this->getDoctrine()->getRepository('AppBundle\Entity\categoria');
        $categorias = $c->findAll();

        if (!$post) {
            echo "Post no encontrado";
            die();

        }

        $form = $this->createEditForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $titulo = $form['titulo']->getData();
            $file = $form['imagen']->getData();
            $contenido = $form['contenido']->getData();
            $seccion = $request->get('seccion');
            $imagen = $request->get('foto');

            if ($file == null or empty($file)) {
                $file_name = $imagen;

            } else {

                $file = $form['imagen']->getData();
// Sacamos la extensión del fichero
                $ext = $file->guessExtension();

// Le ponemos un nombre al fichero
                $file_name = time() . "." . $ext;

// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
                $file->move("uploads", $file_name);

            }

            $post->setImagen($file_name);
            $post->setSeccion($seccion);
            $post->setContenido($contenido);
            $post->setTitulo($titulo);

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se han guardado los cambios.');
            return $this->redirect($this->generateUrl('newedit', array('id' => $post->getId())));
        }

        return $this->render('default/vistas/error.html.twig', array('categorias' => $categorias, 'form' => $form->createView(), 'post' => $post));


    }


    //parte de borrar

    private function creatDeleteform($post)
    {
        return $this->createFormBuilder()->setAction($this->generateUrl('borrar', array('id' => $post->getId())))->setMethod('POST')->getForm();
    }


    /**
     * * @Route("/prueba/{id}", name="prueba")
     */

    public function pruebaAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            //$comment=$em->getRepository('AppBundle:comment')->findBy(array('postcomment'=>$id)); // ATest is my entitity class
            //   $data = $em->getRepository('AppBundle:post')->find($id); // ATest is my entitity class


            $query2 = $em->createQuery(
                'DELETE AppBundle:comment u 
               WHERE u.postcomment = :postid')
                ->setParameter("postid", $id);
            $query2->execute();


            $query = $em->createQuery(
                'DELETE AppBundle:post u 
               WHERE u.id = :postid')
                ->setParameter("postid", $id);
            $query->execute();


            $removed = 1;
            $message = 'se ha eliminado el post';


            $response = new JsonResponse();
            $response->setData(array('removed' => $removed, 'message' => $message), 200);
            return $response;
        }

        return $this->render('default/vistas/404.html.twig');
    }


    /**
     * * @Route("/borrar/{id}", name="borrar")
     */


    public function borrarAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:post')->find($id);
        if (!$post) {
            echo ' no se ha encontrado el post que desea borrar';
            die();
        }
        //$form= $this->creatDeleteform($post);
        $form = $this->createCustomForm($post->getId(), 'DELETE', 'borrar');
        $form->handleRequest($request);
        $em->remove($post);
        $em->flush();
        return $this->redirect($this->generateUrl('test'));


    }


    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()->setAction($this->generateUrl($route, array('id' => $id)))->setMethod($method)->getForm();
    }


    /**
     * * @Route("/ver/{id}", name="ver")
     */


    public function verAction(Request $request, $id)
    {

        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->has("role") == "admin") or ($session->has("role") == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
                // $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));
                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                $post = $this->getDoctrine()->getRepository('AppBundle:post')->find($id);

                $deleteform = $this->createCustomForm($post->getId(), 'DELETE', 'borrar');


                return $this->render('default/vistas/post/view.html.twig', array('post' => $post, 'delete_form' => $deleteform->createView(), 'mensajes' => $mensajes, 'comentarios' => $comentarios));
            }
        }

    }


    /**
     * * @Route("acerca", name="acerca")
     */
    public function acercaAction(Request $request)
    {
        $allpost = $this->todopost();
        return $this->render('default/vistas/acerca.html.twig', array('allpost' => $allpost));
    }

    /**
     * * @Route("contactanos", name="contactanos")
     */
    public function contactaAction(Request $request)
    {

        if ($request->getMethod() == 'POST') {

            $email = 'carlossosa200@gmail.com';
            $nombre = $request->get("nombre");
            $texto = $request->get("texto");
            $asunto = $request->get("texto");
            $status = ('true');


            $mensaje = new mensaje();
            $mensaje->setTexto($texto);
            $mensaje->setAsunto($asunto);
            $mensaje->setNombre($nombre);
            $mensaje->setEmail($email);
            $mensaje->setFecha(new \DateTime("now"));
            $mensaje->setStatus($status);
            $em = $this->getDoctrine()->getManager();
            $em->persist($mensaje);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', '¡Genial! Tu Mensaje ha sido enviado Muy pronto nos pondremos en contacto contigo.');

            return $this->redirect($this->generateUrl('contactanos'));
        }

        return $this->render('default/vistas/contactanos.html.twig');
    }


    /**
     * * @Route("/priting/{id}", name="priting")
     */
    public function singleAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:post')->find($id);
        if ($post == null or empty($post)) {

            return $this->render('default/vistas/404.html.twig');

        }
        return $this->render('default/vistas/priting.html.twig', array('post' => $post));
    }


    /**
     * * @Route("/error", name="error")
     */
    public function errorAction(Request $request, $id)
    {
        return $this->render('default/vistas/404.html.twig');
    }

    /**
     * * @Route("/notificacion", name="notificacion")
     */

    public function notiAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
            //$msj = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));
            $email = $session->get('email');
            $msj = $this->mensaje($email, $em);
            $mensajes = count($msj);
//seccion para los alertas (comentarios)
            $alt = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));
            $alert = count($alt);

            $response = new JsonResponse();
            $response->setData(array('msj' => $mensajes, 'alert' => $alert, 'comentarios' => $alt), 200);
            return $response;
        }


        return $this->render('default/vistas/404.html.twig');

    }

    /**
     * * @Route("/updatestatusmensaje/{id}", name="updatestatusmensaje")
     */

    public function notiUpdateAjax(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $data = $em->getRepository('AppBundle:mensaje')->find($id); // ATest is my entitity class
            $data->setStatus('false');
            $choise = 1;
            $em->persist($data);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('choise' => $choise));
            return $response;
        }

        return $this->render('default/vistas/404.html.twig');
    }


    /**
     * * @Route("/updatestatuscomentario/{id}", name="updatestatuscomentario")
     */

    public function notiUpdatConmmentAjax(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $data = $em->getRepository('AppBundle:comment')->find($id); // ATest is my entitity class
            $data->setStatus('false');
            $choise = 1;
            $em->persist($data);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('choise' => $choise));
            return $response;
        }

        return $this->render('default/vistas/404.html.twig');
    }


    private function createquery($repositori)
    {

        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository($repositori);
        return $response;

    }


    /**
     * * @Route("/vercomment/{id}", name="vercomment")
     */

    public function vernoti(Request $request, $id)
    {


        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
                //   $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                $comentario2 = $em->getRepository('AppBundle:comment')->find($id);
                //$post=$em->getRepository('AppBundle:comment')->findBy( array(),array('date' => 'DESC'));
                $post_id = $comentario2->getPostcomment()->getId();

                $comentario = $em->getRepository('AppBundle:comment')->findBy(array('postcomment' => $post_id), array('date' => 'DESC'));
                $post = $em->getRepository('AppBundle:post')->find($post_id);


                // $post = $em->getRepository('AppBundle:post')->findOneByPostcomment( array('post_id'=>$id ));
                // dump($post);exit();

                if ($request->getMethod() == 'POST') {

                    $email = 'AmplexCorp@amplexcorp.com';
                    $texto = $request->get("texto");
                    $status = ('true');
                    if (empty($file) or $file == NULL) {

                        $file_name = $request->get("imagen");

                    } else {

                        $file_name = $request->get("imagen");
// Sacamos la extensión del fichero
                        $ext = $file->guessExtension();

// Le ponemos un nombre al fichero
                        $file_name = time() . "." . $ext;

// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
                        $file->move("uploads", $file_name);


                    }


                    $comment = new comment();
                    $comment->setComment($texto);
                    $comment->setImagen($file_name);
                    $comment->setEmail($email);
                    $comment->setDate(new \DateTime("now"));
                    $comment->setStatus($status);
                    $comment->setPostcomment($postuser);
                    $em->persist($comment);
                    // actually executes the queries (i.e. the INSERT query)
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', '¡Genial! Tu Post Ha sido Guardado.');

                    return $this->redirect($this->generateUrl('vercomment/', $id));
                }


            } else {
                return $this->render('default/vistas/permierror.html.twig');
            }
        }
        return $this->render('default/vistas/vercomment.html.twig', array('post' => $post, 'mensajes' => $mensajes, 'comentarios' => $comentarios, 'comentario' => $comentario, 'comentario2' => $comentario2));

    }


    /**
     * * @Route("/newadmin", name="newadmin")
     */
    public function newAction()
    {
        return $this->render('newadmin.html.twig');
    }

    /**
     * * @Route("/verpostuser/{id}", name="verpostuser")
     */
    public function veruserAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "superadmin")) {

                $em = $this->getDoctrine()->getManager();
                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));
                $usuario = $em->getRepository('AppBundle:users1')->find($id);


                $post = $em->getRepository('AppBundle:post')->findBy(array('postuser' => $id), array('fechaPublicacion' => 'DESC'));


                return $this->render('default/vistas/usuarios/admin/verpostuser.html.twig', array('listpost' => $post, 'mensajes' => $mensajes, 'comentarios' => $comentarios, 'user' => $usuario));

            } else {
                return $this->render('default/vistas/permierror.html.twig');
            }

        }

    }


    /**
     * * @Route("/veremail", name="veremail")
     */
    public function veremailAction(Request $request)

    {
        //preguntar a la jefaza sobre si solo debo ver mis correo y o tengo q verlos todos
        $session = $request->getSession();
        $email = $session->get('email');
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {

                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes


                // $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('email' => $email), array('fecha' => 'DESC'));

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


                $dql = "SELECT u FROM AppBundle:mensaje u WHERE u. email = '$email' ORDER BY u.fecha DESC ";
                $post = $em->createQuery($dql);
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate($post, $request->query->getInt('page', 1), 15);

                $mensaje = $em->getRepository('AppBundle:mensaje')->findAll();
                $total = count($mensaje);


            } else {
                return $this->render('default/vistas/permierror.html.twig');
            }
        }

        return $this->render('default/vistas/correo/VerEmaill.html.twig', array('total' => $total, 'mensajes' => $mensajes, 'comentarios' => $comentarios, 'pagination' => $pagination));
    }


    /**
     * * @Route("/versend", name="versend")
     */
    public function versendlAction(Request $request)

    {
        //preguntar a la jefaza sobre si solo debo ver mis correo y o tengo q verlos todos
        $session = $request->getSession();
        $email = $session->get('email');
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {

                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes


                // $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('email' => $email), array('fecha' => 'DESC'));

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


                $dql = "SELECT u FROM AppBundle:mensaje u WHERE u.nombre = '$email' ORDER BY u.fecha DESC ";
                $post = $em->createQuery($dql);
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate($post, $request->query->getInt('page', 1), 15);

                $mensaje = $em->getRepository('AppBundle:mensaje')->findAll();
                $total = count($mensaje);


            } else {
                return $this->render('default/vistas/permierror.html.twig');
            }
        }

        return $this->render('default/vistas/correo/emailenviados.htm.twig', array('total' => $total, 'mensajes' => $mensajes, 'comentarios' => $comentarios, 'pagination' => $pagination));
    }


    private function mensaje($email, $em)
    {


        $dql = "SELECT u FROM AppBundle:mensaje u WHERE u.email='$email' AND u.status ='true' ORDER BY u.fecha DESC ";
        $query = $em->createQuery($dql);
        $mensajes = $query->getResult();

        return $mensajes;
    }


    /**
     * * @Route("/leermail/{id}", name="leermail")
     */
    public function leermailAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {

                $em = $this->getDoctrine()->getManager();
                $correo = $em->getRepository('AppBundle:mensaje')->find($id);
//seccion para los mensajes
                // $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));


                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));
            } else {
                return $this->render('default/vistas/permierror.html.twig');
            }
        }
        return $this->render('default/vistas/correo/leer-mensaje.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios, 'correo' => $correo));
    }


    /**
     * * @Route("/busqueda/{param}", name="busqueda")
     */

    public function busquedaAjax(Request $request, $param)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $dql_query = $em->createQuery("
    SELECT o FROM AppBundle:users1 o
    WHERE 
      o.email = '%$param%'
");
            $user = $dql_query->getResult();
            $response = new JsonResponse();
            $response->setData(array('user' => $user), 200);
            return $response;
        }

        return $this->render('default/vistas/404.html.twig');
    }


    /**
     * * @Route("/newemail", name="newemail")
     */
    public function newemailAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
                // $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));


                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                if ($request->getMethod() == 'POST') {

                    $correo = $request->get("email");
                    $de = $email;
                    $texto = $request->get("texto");
                    $asunto = $request->get("asunto");
                    $file = $request->get("imagen");
                    $status = ('true');

                    if (empty($file)) {
                        $file_name = null;
                    } else {

// Sacamos la extensión del fichero
                        $ext = $file->guessExtension();

// Le ponemos un nombre al fichero
                        $file_name = time() . "." . $ext;

// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
                        $file->move("uploads", $file_name);
                    }


                    $mensaje = new mensaje();
                    $mensaje->setTexto($texto);
                    $mensaje->setAsunto($asunto);
                    $mensaje->setNombre($de);
                    $mensaje->setImagen($file_name);
                    $mensaje->setEmail($correo);
                    $mensaje->setFecha(new \DateTime("now"));
                    $mensaje->setStatus($status);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($mensaje);
                    // actually executes the queries (i.e. the INSERT query)
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', '¡Genial! Tu Mensaje ha sido enviado Muy pronto nos pondremos en contacto contigo.');

                    return $this->redirect($this->generateUrl('newemail'));
                }
            }
        } else {
            return $this->render('default/vistas/permierror.html.twig');
        }


        return $this->render('default/vistas/correo/new_message.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios));


    }


    /**
     * * @Route("/newedit/{id}", name="newedit")
     */
    public function neweditAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->get("id")) and ($session->get("role") == "admin") or ($session->get("role") == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
                $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                $sesionid = $this->get('session')->get('id');
                $c = $this->getDoctrine()->getRepository('AppBundle\Entity\categoria');
                $categorias = $c->findAll();

                $post = $this->getDoctrine()
                    ->getRepository('AppBundle:post')
                    ->find($id);

                $form = $this->createEditForm($post);
                $form->handleRequest($request);


                if ($request->getMethod() == 'POST') {
                    $titulo = $request->get("titulo");
                    $contenido = $request->get("texto");

                    $file = $form['imagen']->getData();

                    if (empty($file) or $file == NULL) {

                        $file_name = $post->getImagen();

                    } else {

                        $file = $form['imagen']->getData();
// Sacamos la extensión del fichero
                        $ext = $file->guessExtension();

// Le ponemos un nombre al fichero
                        $file_name = time() . "." . $ext;

// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
                        $file->move("uploads", $file_name);


                    }


                    $post->setImagen($file_name);

                    $post->setTitulo($titulo);
                    $post->setContenido($contenido);
                    $post = $em->getRepository('AppBundle:post')->find($id);
                    // $p=$postuser-> getId();
                    $a = $post->getPostuser()->getId();
                    //$em->persist($upost);


                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        'Se han guardado los cambios.');
                    return $this->redirect($this->generateUrl('newedit', array('id' => $post->getId())));
                }
            }
        } else {
            return $this->render('default/vistas/permierror.html.twig');
        }


        return $this->render('default/vistas/post/editpost.html.twig', array('categorias' => $categorias, 'mensajes' => $mensajes, 'comentarios' => $comentarios, 'form' => $form->createView(), 'post' => $post));


    }


}



