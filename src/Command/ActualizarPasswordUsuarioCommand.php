<?php

namespace App\Command;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:actualizar-password',
    description: 'Actualiza la contraseña de un usuario existente.',
)]
class ActualizarPasswordUsuarioCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email del usuario')
            ->addArgument('newPassword', InputArgument::REQUIRED, 'Nueva contraseña');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $newPassword = $input->getArgument('newPassword');

        $usuario = $this->em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (!$usuario) {
            $io->error("No se encontró un usuario con ese email.");
            return Command::FAILURE;
        }

        $hashed = $this->passwordHasher->hashPassword($usuario, $newPassword);
        $usuario->setPassword($hashed);
        $this->em->flush();

        $io->success("Contraseña actualizada correctamente para $email.");
        return Command::SUCCESS;
    }
}
