<?php

namespace Aliocza\SortableUiBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SortableUiAdminController extends CRUDController {

    /**
     * @Method({"POST"})
     */
    public function dragAction(Request $request) {
        $translator = $this->get('translator');

        if (!$this->admin->isGranted('EDIT')) {
            $this->addFlash(
                'sonata_flash_error',
                $translator->trans('flash_error_no_rights_update_position')
            );

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }
        

        
        if ($request->isMethod('POST')) {

            $positionService = $this->get('aliocza_sortable_ui.position');


            $entity = $this->admin->getClass();
            
            
            $setter = sprintf('set%s', ucfirst($positionService->getPositionFieldByEntity($entity)));
            
            
            $dataPositionList = $request->request->get('data');
            
            $updateDb = $positionService->setPosition($entity, $setter, $dataPositionList);
        
            if($updateDb === true){
                return new Response($translator->trans('flash_success_post_update_position'));
            }else{
                return new Response($translator->trans('flash_error_post_update_position'));
            }
        }
        return new Response($translator->trans('flash_error_no_post_update_position'));
    }

}
