<?php

namespace App\Controller;

use App\Entity\Contacto;
use App\Entity\Provincia;
use App\Repository\ContactoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ContactoFormType as ContactoType;

final class ContactoController extends AbstractController
{
    private $contactos = [

        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],

        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],

        3 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],

        4 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],

        5 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]

    ];     


    #[Route('/', name: 'inicio')]

    public function inicio(ManagerRegistry $doctrine): Response

    {

        $repositorio = $doctrine->getRepository(Contacto::class);

        $contactos = $repositorio->findAll();
        
        //Mostramos la plantilla pasándole los contactos

        return $this->render("inicio.html.twig", ["contactos" => $contactos]);

    }


    #[Route('/contacto/nuevo', name: 'nuevo')]
    
    public function nuevo(ManagerRegistry $doctrine, Request $request) {
        $contacto = new Contacto();
        $formulario = $this->createForm(ContactoType::class, $contacto);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $contacto = $formulario->getData();
            
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contacto);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);
        }
        return $this->render('nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/contacto/insertarConProvincia', name: 'insertar_con_provincia_contacto')]

    public function insertarConProvincia(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $provincia = new Provincia();

        $provincia->setNombre("Alicante");
        $contacto = new Contacto();

        $contacto->setNombre("Inserción de prueba con provincia");
        $contacto->setTelefono("900220022");
        $contacto->setEmail("insercion.de.prueba.provincia@contacto.es");
        $contacto->setProvincia($provincia);

        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }

    #[Route('/contacto/insertarSinProvincia', name: 'insertar_sin_provincia_contacto')]

    public function insertarSinProvincia(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);

        $provincia = $repositorio->findOneBy(["nombre" => "Alicante"]);

        $contacto = new Contacto();

        $contacto->setNombre("Inserción de prueba sin provincia");
        $contacto->setTelefono("900220022");
        $contacto->setEmail("insercion.de.prueba.sin.provincia@contacto.es");
        $contacto->setProvincia($provincia);

        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }
    
    #[Route('/contacto/{codigo}', name: 'ficha_contacto', defaults: ['codigo' => 1])]
    
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }

    
    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]

    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        //Filtramos aquellos que contengan dicho texto en el nombre
        /** @var \App\Repository\ContactoRepository $repositorio */
        $repositorio = $doctrine->getRepository(Contacto::class);

        $contactos = $repositorio->findByName($texto);

        return $this->render('lista_contactos.html.twig', [
            'contactos' => $contactos
        ]);
    }


    #[Route('/contacto/editar/{codigo}', name: 'editar', requirements:["codigo"=>"\d+"])]

    public function editar(ManagerRegistry $doctrine, Request $request, int $codigo) {
        $repositorio = $doctrine->getRepository(Contacto::class);
        //En este caso, los datos los obtenemos del repositorio de contactos
        $contacto = $repositorio->find($codigo);
        if ($contacto){
            $formulario = $this->createForm(ContactoType::class, $contacto);

            $formulario->handleRequest($request);  

            if ($formulario->isSubmitted() && $formulario->isValid()) {
            //Esta parte es igual que en la ruta para insertar
                $contacto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);
            }
            return $this->render('nuevo.html.twig', array(
                'formulario' => $formulario->createView()
            ));
        }else{
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => NULL
            ]);
        }   
    }


    #[Route('/contacto/update/{id}/{nombre}', name: 'modificar_contacto')]

    public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
        if ($contacto){
            $contacto->setNombre($nombre);
            try
            {
                $entityManager->flush();
                return $this->render('ficha_contacto.html.twig', [
                    'contacto' => $contacto
                ]);
            } catch (\Exception $e) {
                return new Response("Error insertando objetos");
            }
        }else
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => null
        ]);
    }

 
    #[Route('/contacto/delete/{id}', name: 'eliminar_contacto')]

    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($id);
        if ($contacto){
            try
            {
                $entityManager->remove($contacto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            } catch (\Exception $e) {
                return new Response("Error eliminado objeto");
            }
        }else
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => null
        ]);
    }
}
