<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Contrato;
use dlaser\AdminBundle\Form\ContratoType;

class ContratoController extends Controller
{
    
    public function newAction($id)
    {
        $entity = new Contrato();
        $form   = $this->createForm(new ContratoType(), $entity);
    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($id);
        
        if (!$cliente) {
            throw $this->createNotFoundException('El cliente solicitado no existe.');
        }
        
        $entity  = new Contrato();
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ContratoType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
            
            $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findBy(array('cliente' => $cliente->getId(), 'sede' => $entity->getSede()->getId()));
            
            if($contrato){
                $this->get('session')->setFlash('info', 'Ya existe un contrato con la sede seleccionada.');
            
                return $this->render('AdminBundle:Contrato:new.html.twig', array(
                        'entity' => $entity,
                        'id'    => $id,
                        'form'   => $form->createView()
                ));
            }
            
            $entity->setCliente($cliente);            
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('info', 'El contrato ha sido creado éxitosamente.');
    
    
            return $this->redirect($this->generateUrl('contrato_show', array("id" => $entity->getId())));
    
        }
    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        
        $actividades = $em->getRepository('ParametrizarBundle:Actividad')->findBy(array('contrato' => $id));
    
        return $this->render('AdminBundle:Contrato:show.html.twig', array(
                'contrato'    => $contrato,
                'actividades'    => $actividades,
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
    
        $editForm = $this->createForm(new ContratoType(), $contrato);
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe');
        }
    
        $editForm   = $this->createForm(new ContratoType(), $contrato);
    
        $request = $this->getRequest();
    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($contrato);
            $em->flush();
    
            $this->get('session')->setFlash('info', 'La información del contrato ha sido modificada éxitosamente.');
    
            return $this->redirect($this->generateUrl('contrato_edit', array('id' => $id)));
        }
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }

}