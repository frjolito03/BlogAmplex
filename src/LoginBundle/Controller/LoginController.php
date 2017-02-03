<?php

namespace LoginBundle\Controller;

use  Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\users1;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends Controller
{

    /**
     * @Route("/register", name="register")
     */
    public function newuserAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and  ($session->get('role') == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
                //  $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));


                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);


//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


//formulario de registro de usuario
                $user = new users1();

//aqui creo y defino los campos del formulario para enviar a la vista
                $form = $this->createFormBuilder($user)
                    ->add('userid', TextType::class)
                    ->add('username', TextType::class)
                    ->add('email', EmailType::class)
                    ->add('password', PasswordType::class)
                    ->add('role', ChoiceType::class, array(
                        'choices' => array(
                            'admin' => 'admin',
                            'superadmin' => 'superadmin',
                        ),
                    ))
                    ->add('save', SubmitType::class, array('label' => 'save'))->getForm();


                $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()) {

                    $id = $form['userid']->getData();
                    $name = $form['username']->getData();
                    $email = $form['email']->getData();
                    $pass = $form['password']->getData();
                    $role = $form['role']->getData();
//en esta parte seleciono cada campo que cree en mi formulario

                    $emailexist = $this->getDoctrine()->getRepository('AppBundle\Entity\users1')->findOneBy(array("email" => $email));
                    if ($emailexist == true) {

                        $request->getSession()->getFlashBag()->add('error', '¡Error! Haz algunos cambios antes de volver a enviar el formulario Ya hay Un Usuario Reistrado con ese correo.');

                        return $this->redirectToRoute('register');
                    } else {



                        $user->setUserid($id);
                        $user->setUsername($name);
                        $user->setEmail($email);
                        $user->setPassword($pass);
                        $user->setRole($role);
                        $user->setRedDate(new \DateTime("now"));


                        $em = $this->getDoctrine()->getManager();

                        // tells Doctrine you want to (eventually) save the Product (no queries yet)
                        $em->persist($user);

                        // actually executes the queries (i.e. the INSERT query)
                        $em->flush();
                        $request->getSession()->getFlashBag()->add('EXITO', ' Un Usuario Reistrado .');

                        return $this->redirectToRoute('register');
                    }

                }
            }
            else{$request->getSession()->getFlashBag()->add('error3', ' No Tiene Los permiso Para Ingresar aqui .');
                return $this->render('default/vistas/permierror.html.twig');
            }

        }


        return $this->render('default/vistas/usuarios/admin/registro.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios, 'form' => $form->createView()));

    }

    /**
     * @Route("/permierror", name="permierror")
     */
    public function permiAction(Request $request)
    { $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->get('role') == "admin")) {
                return $this->render('default/vistas/permierror.html.twig');
            }}
        return $this->render('default/vistas/permierror.html.twig');
    }



    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        return $this->render('admin.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */

    public function LoginAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $email = $request->get("email");
            $pass = $request->get("pass");
            $user = $this->getDoctrine()->getRepository('AppBundle\Entity\users1')->findOneBy(array("email" => $email, "password" => $pass));


        }

        if ($user == true) {
            $session = new Session();
            $session = $request->getSession();
            $session->set("id", $user->getId());
            $session->set("nombre", $user->getUserid());
            $session->set("role", $user->getRole());
            $session->set("email", $user->getEmail());

            $session->start();
            return $this->redirect($this->generateUrl('index'));

        } else {

            $request->getSession()->getFlashBag()->add('error', '¡Error! Haz algunos cambios antes de volver a enviar el formulario Usuario o Contraseña incorrecta.');

        }
        return $this->render('admin.html.twig');

    }

    private function mensaje($email, $em)
    {


        $dql = "SELECT u FROM AppBundle:mensaje u WHERE u.email='$email' AND u.status ='true' ORDER BY u.fecha DESC ";
        $query = $em->createQuery($dql);
        $mensajes = $query->getResult();

        return $mensajes;
    }





    /**
     * @Route("/index", name="index")
     */
    public function getContainer(Request $request)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->get('role') == "admin") or ($session->get('role') == "superadmin")) {
                $em = $this->getDoctrine()->getManager();
//seccion para los mensajes
                //  $mensajes = $em->getRepository('AppBundle:mensaje')->findBy(array('status' => 'true'), array('fecha' => 'DESC'));


                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);


//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));

                //return $this->render('adminbase.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios));
                return $this->render('default/vistas/usuarios/admin/indexadmin.html.twig', array('mensajes' => $mensajes, 'comentarios' => $comentarios));
            }

            else {

                return $this->render('default/vistas/permierror.html.twig');

            }
        }
        return $this->redirect($this->generateUrl('homepage'));
    }


    /**
     * @Route("/listuser", name="listuser")
     */
    public function listuserAction(Request $request)
    {
        $session = $request->getSession();
        $role=$session->get('role');


        if ($session->start() == true) {
            if (($session->has("id")) and ($session->get('role') == "superadmin")) {

                $id = $this->get('session')->get('id');
                $em = $this->getDoctrine()->getManager();

                $dql = "SELECT u FROM AppBundle:users1 u ";
                $post = $em->createQuery($dql);
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate($post, $request->query->getInt('page', 1), 10
                );

                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);
//seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));


            }else{
                return $this->render('default/vistas/permierror.html.twig');
                }
        }


        return $this->render('default/vistas/usuarios/admin/listuser.html.twig', array('pagination' => $pagination, 'mensajes' => $mensajes, 'comentarios' => $comentarios));
    }


    /**
     * @Route("/edituser/{id}", name="edituser")
     */
    public function editusertAction(Request $request, $id)
    {
        $session = $request->getSession();
        if ($session->start() == true) {
            if (($session->has("id")) and ($session->get('role') == "admin") or ($session->get('role') == "superadmin")) {

                $em = $this->getDoctrine()->getManager();


                $email = $session->get('email');
                $mensajes = $this->mensaje($email, $em);

                //seccion para los alertas (comentarios)
                $comentarios = $em->getRepository('AppBundle:comment')->findBy(array('status' => 'true'), array('date' => 'DESC'));
                $user = $em->getRepository('AppBundle:users1')->find($id);


                if ($request->getMethod() == 'POST') {

                    $email = $request->get("email");
                    $nombre = $request->get("nombre");
                    $pass = $request->get("pass");

                    //$postcomment=$id;
                    $fecha = $user->getRedDate();

                    if ($request->get("role") == null or empty($request->get("role"))) {

                        $role = 'admin';

                    } else {

                        $role = $request->get("role");

                    }
                    $user->setPassword($pass);
                    $user->setEmail($email);
                    $user->setRole($role);
                    $user->setUsername($nombre);

                    $user->setRedDate($fecha);

                    //$postcomment=$em->getRepository('AppBundle:post')->find($id);
                    //$comentario-> setPostcomment($postcomment);
                    $em->persist($user);
                    // actually executes the queries (i.e. the INSERT query)
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', '¡Genial! Tu Datos han sido Actualizado.');

                    return $this->redirect($this->generateUrl('edituser', array('id' => $user->getId())));
                }

            }
            else{return $this->render('default/vistas/permierror.html.twig');}
        }

        return $this->render('default/vistas/usuarios/admin/edituser.html.twig', array('user' => $user, 'mensajes' => $mensajes, 'comentarios' => $comentarios));

    }


    /**
     * * @Route("/deleteuser/{id}", name="deleteuser")
     */

    public function deleteuserAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            //$comment=$em->getRepository('AppBundle:comment')->findBy(array('postcomment'=>$id)); // ATest is my entitity class
            //   $data = $em->getRepository('AppBundle:post')->find($id); // ATest is my entitity class


            $query2 = $em->createQuery(
                'DELETE AppBundle:users1 u 
               WHERE u.id = :userid')
                ->setParameter("userid", $id);
            $query2->execute();


            $removed = 1;
            $message = 'se ha eliminado el usario';


            $response = new JsonResponse();
            $response->setData(array('removed' => $removed, 'message' => $message), 200);
            return $response;
        }

        return $this->render('default/vistas/404.html.twig');
    }


    /**
     * @Route("/logaout", name="logaout")
     */
    public function logaoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->Clear();

        return $this->redirect($this->generateUrl('admin'));
    }


}
