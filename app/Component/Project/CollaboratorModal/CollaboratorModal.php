<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\CollaboratorModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\User;
use App\Database\Manager\ProjectManager;
use App\Database\Manager\UserManager;
use App\Database\Repository\ProjectRepository;
use App\Database\Repository\UserRepository;
use App\Mailer\User\UserMailer;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class CollaboratorModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var ProjectRepository
     */
    protected ProjectRepository $projectRepository;

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var ProjectManager
     */
    protected ProjectManager $projectManager;

    /**
     * @var UserManager
     */
    protected UserManager $userManager;

    /**
     * @var UserMailer
     */
    protected UserMailer $userMailer;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * @var int|null
     * @persistent
     */
    public ?int $id = NULL;

    /**
     * @var string|null
     * @persistent
     */
    public ?string $secretKey = NULL;

    /**
     * CollaboratorModal constructor.
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param ProjectManager $projectManager
     * @param UserManager $userManager
     * @param UserMailer $userMailer
     * @param User $activeUser
     */
    public function __construct(
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        ProjectManager $projectManager,
        UserManager $userManager,
        UserMailer $userMailer,
        User $activeUser
    ) {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->projectManager = $projectManager;
        $this->userManager = $userManager;
        $this->userMailer = $userMailer;
        $this->activeUser = $activeUser;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function render() : void
    {
        $project = NULL;

        if ($this->id && $this->secretKey) {
            $project = $this->projectRepository->findOneByKeyAndIdWithUsers($this->id, $this->secretKey);
        }

        $this->template->render(__DIR__ . '/templates/collaborator-modal.latte', [
            'project' => $project,
            'users' => $project ? $project->getUsers() : []
        ]);
    }

    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form;

        $form->addText('email')
            ->setRequired('E-mail uživatele je povinný.')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu.');

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $user = $this->userRepository->findOneByEmail($values->email);
            $project = $this->projectRepository->findOneByKeyAndIdWithUsers($this->id, $this->secretKey);

            $this->projectManager->beginTransaction();
            $newPassword = NULL;

            try
            {
                if (!$user) {
                    $newPassword = Random::generate(6);
                    $user = $this->userManager->create($values->email, $newPassword);
                }

                $userHasProject = $project->getUsers()->contains($user);

                if (!$userHasProject)
                {
                    $this->projectManager->appendUser($project, $user);
                    $this->userMailer->addedProjectUser($project, $user, $newPassword);
                    $this->notification(Notification::SUCCESS, 'Projekt', "Uživatel {$user->getEmail()}, byl úspěšně přiřazen k tomuto projektu.", 4000, FALSE);

                } else {
                    $this->notification(Notification::WARNING, 'Projekt', "Uživatel {$user->getEmail()}, je k tomuto projektu již přiřazen.", 0, FALSE);
                }

                $this->projectManager->commit();

                // Redraw control
                $form->reset();
                $this->redrawControl('form');
                $this->redrawControl('users');

                // Redraw projectSummary
                $this->presenter->getComponent('projectSummary')->redrawControl('summary');

            }
            catch (\Exception $exception)
            {
                $this->projectManager->rollback();
                throw $exception;
            }
        };

        return $form;
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id, string $secretKey) : void
    {
        $this->id = $id;
        $this->secretKey = $secretKey;
        $this->toggleModal($this->getName(), 'show');
        $this->redrawControl('users');
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @param int $userId
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     */
    public function handleRemove(int $id, string $secretKey, int $userId) : void
    {
        // Find & Remove collaborator
        $project = $this->projectRepository->findOneByKeyAndIdWithUsers($id, $secretKey);
        $user = $this->userRepository->findOneById($userId);
        $this->projectManager->removeUser($project, $user);
        $this->userMailer->removedProjectUser($project, $user);

        // Notification & redraw users
        $this->notification(Notification::SUCCESS, 'Projekt', "Správce projektu '{$user->getEmail()}' byl odebrán.", 4000, FALSE);
        $this->redrawControl('users');

        // Redraw projectSummary
        $this->presenter->getComponent('projectSummary')->redrawControl('summary');
    }
}
