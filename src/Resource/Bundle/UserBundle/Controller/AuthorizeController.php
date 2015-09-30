<?php
namespace Resource\Bundle\UserBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;
use Resource\Bundle\UserBundle\Form\Model\Authorize;
use Resource\Bundle\UserBundle\Document\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeController extends Controller
{
    protected $sess;
    const HTTP_FORM_CONTENT_TYPE_APPLICATION = 0;
    const HTTP_FORM_CONTENT_TYPE_MULTIPART = 1;
    const HTTP_METHOD_POST="POST";

    public function authorizeAction(Request $request)
    {

        if (!$request->get('client_id')) {
        throw new NotFoundHttpException("Client id parameter {$request->get('client_id')} is missing.");
        }

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->findClientByPublicId($request->get('client_id'));

        if (!($client instanceof Client)) {
        throw new NotFoundHttpException("Client {$request->get('client_id')} is not found.");
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $this->sess = new Session();



        $this->sess->set('client_id',$request->get('client_id'));
        $this->sess->set('redirect_uri',$request->get('redirect_uri'));


        if ($this->get('request')->request->get('formAuth'))
        {
            var_dump($user);
        }

        elseif (!$user || $user == 'anon.'){


            return $this->redirect($this->generateUrl('resource_oauth_server_auth_login'));

        }



    $form = $this->container->get('resource_oauth_server.authorize.form');
    $formHandler = $this->container->get('resource_oauth_server.authorize.form_handler');

    $authorize = new Authorize();

    if (($response = $formHandler->process($authorize)) !== false) {
    return $response;
    }

    return $this->container->get('templating')->renderResponse('ResourceUserBundle:Authorize:authorize.html.twig', array(
    'form' => $form->createView(),
    'client' => $client,
    ));
    }


}



?>