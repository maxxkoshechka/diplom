<?php

namespace App\Controller;

use App\Entity\Dialogs;
use App\Entity\DialogUsers;
use App\Entity\Messages;
use App\Entity\User;
use App\Form\DialogType;
use App\Form\MessageType;
use App\Service\CryptographyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dialogs")
 */
class DialogsController extends AbstractController
{
    /**
     * @Route("/", name="dialogs")
     */
    public function index(): Response
    {
        $user = $this->getUser()->getId();
        $dialogs = $this->getDoctrine()->getRepository(Dialogs::class)->findAllByUser($user);

        return $this->render('dialogs/index.html.twig', [
            'dialogs' => $dialogs,
        ]);
    }

    /**
     * @Route("/add", name="add_dialog")
     */
    public function addDialog(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $dialog = new Dialogs();
        $form = $this->createForm(DialogType::class, $dialog, [
            'action' => $this->generateUrl( 'add_dialog'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()){
            $crypt = new CryptographyService();
            $dialog->setCryptoKey($crypt->getMatrix());
            $em->persist($dialog);
            $em->flush();
            $dialogUserOne = new DialogUsers();
            $dialogUserOne->setDialog($dialog);
            $dialogUserOne->setUser($this->getUser());
            $dialogUserTwo = new DialogUsers();
            $dialogUserTwo->setDialog($dialog);
            $dialogUserTwo->setUser($form->get('user')->getData());
            $em->persist($dialogUserOne);
            $em->persist($dialogUserTwo);
            $em->flush();

            return $this->redirectToRoute('dialogs');
        }

        return $this->render('dialogs/dialogAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajax/dialogs/users", name="ajax_users_list")
     */
    public function ajaxUserList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        $list = $repository->findAllLike($request->get('q'));
        $result = [];
        foreach ($list as $item){
            $result[] = [
                'id' => $item['id'],
                'text' => $item['email'],
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/{id}", name="dialog")
     */
    public function dialog(Request $request, $id): Response
    {
        $message = new Messages();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl( 'dialog', ['id' => $id]),
            'method' => 'POST',
        ]);
        $user = $this->getUser();
        $dialog = $em->getRepository(Dialogs::class)->find($id);
        $crypt = new CryptographyService();
        $keyMatrix = $dialog->getCryptoKey();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mess = $crypt->encryptMessage($request->get('message')['message'], $keyMatrix);

            $message->setMessage($mess);
            $message->setDialog($dialog);
            $message->setUser($user);
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('dialog', ['id' => $id]);
        }

        $messList = $this->getDoctrine()->getRepository(Messages::class)->findBy(['dialog' => $id]);
        /*var_dump($messList);
        die();*/
        $list = [];
        for ($i = 0; $i < count($messList); $i++){
            $list[$i]['message'] = implode($crypt->decodeMessage($messList[$i]->getMessage(), $keyMatrix));
            $list[$i]['id'] = $messList[$i]->getId();
            $list[$i]['user'] = $messList[$i]->getUser();
        }
        $interlocutor = $em->getRepository(DialogUsers::class)->getInterlocutor($dialog, $user);
        if (empty($interlocutor)){
            $interlocutor = 'Пользователем, который удалил этот диалог';
        }else
            $interlocutor = $interlocutor[0]->getUser()->getEmail();

        return $this->render('dialogs/dialog.html.twig', [
            'form' => $form->createView(),
            'mess' => $list,
            'dialog_id' => $id,
            'interlocutor' => $interlocutor,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="dialog_delete")
     */
    public function deleteDialog($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $dialogUser = $em->getRepository(DialogUsers::class)->findBy(['user' => $user, 'dialog' => $id]);
        $del = $em->getRepository(Dialogs::class)->find($id)->removeDialogUser($dialogUser[0]);
        $em->persist($del);
        $em->flush();
        return $this->redirectToRoute('dialogs');
    }

    /**
     * @Route("/delete_message/{dialog_id}/{id}", name="message_delete")
     */
    public function deleteMessage($dialog_id, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository(Messages::class)->findBy(['id' => $id]);
        /*var_dump($message);
        die();*/
        $del = $em->getRepository(Dialogs::class)->find($dialog_id)->removeMessage($message[0]);
        $em->persist($del);
        $em->flush();
        return $this->redirectToRoute("dialog", ['id' => $dialog_id]);
    }

}
