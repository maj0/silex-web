<?php


namespace MyApp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;

class ContactController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];
        $factory->match('/', 'MyApp\Controller\ContactController::home')->bind('contact_home');
        return $factory;
    }

    protected static function access($req, $app, $id = 0, $oid = 0)
    {
        $username = $app['security.token_storage']->getToken()->getUser();
        $access = array(
            'error'         => $app['security.last_error']($req),
            'last_username' => $app['session']->get('_security.last_username'),
            'user_type' => 'User','user_add' => '/user/add','hide_add_user' => '',
            'organisation_ID_readonly' => '',
            'last_organisation_ID' => '',
            'active' => 'contact',
        );
        
        if (empty($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = '/user';
        }
        $access['_SERVER'] = $_SERVER;
        return $access;
    }

    public function home(Request $req, Application $app)
    {
        require_once __DIR__ . '/../Helpers/sendmail.php';
        $form = $app['form.factory']->createBuilder(Type\FormType::class)
               ->add('name', Type\TextType::class)
               ->add('surname', Type\TextType::class)
               ->add('email', Type\EmailType::class)
               ->add('message', Type\TextareaType::class)
               ->getForm() ;

        if ($req->isMethod('POST')) {
            //echo "request:<pre>",print_r($_REQUEST,1),"</pre><br/>\n";
            //echo "email:",$req->get('form')['email'],"<br\>\n";
            $form->bind($req);
            if ($form->isValid()) {
                $myform = $req->get('form');
                $subject = 'Silex Application Contact';
                $from = $myform['email'];
                $to = array('feedback@mifon.tk');
                //$app['repository.contact']->save($user);
                /*$message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array($from))
                    ->setTo($to)
                    ->setBody($myform['message']);
                //echo "message:<pre>",print_r($message,1),"</pre><br/>\n";
                $ret = $app['mailer']->send($message);*/
                $ret = send_mail($app, $subject, $myform['message'], $to, "$from");
				if($ret)
				{
					$message = "Hello {$myform['name']}, your message from {$myform['email']} has been sent.";
					$app['session']->getFlashBag()->add('success', $message);
				} else {
					$message = "Hello {$myform['name']}, your message from {$myform['email']} has not been sent.";
					$app['session']->getFlashBag()->add('error', $message);
				}
            } else {
                $message = 'The contact '.print_r($form, 1).' is not valid';
                $app['session']->getFlashBag()->add('warning', $message);
            }
        }
        $access = self::access($req, $app);
        $access['form'] = $form->createView();
        return $app['twig']->render('contact.html.twig', $app['app.access'] = $access);
    }
}
