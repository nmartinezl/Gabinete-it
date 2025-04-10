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
    name: 'app:crear-usuario',
    description: 'Crea un nuevo usuario desde consola con email, contraseña y rol.',
)]
class CrearUsuarioCommand extends Command
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
            ->addArgument('email', InputArgument::REQUIRED, 'El email del nuevo usuario.')
            ->addArgument('password', InputArgument::REQUIRED, 'La contraseña del nuevo usuario.')
            ->addArgument('rol', InputArgument::OPTIONAL, 'Rol del usuario (ROLE_ADMIN, ROLE_USER, etc.)', 'ROLE_USER');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $rol = $input->getArgument('rol');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('El email proporcionado no es válido.');
            return Command::FAILURE;
        }

        $usuarioExistente = $this->em->getRepository(Usuario::class)->findOneBy(['email' => $email]);
        if ($usuarioExistente) {
            $io->error('Ya existe un usuario con ese email.');
            return Command::FAILURE;
        }

        $usuario = new Usuario();
        $usuario->setEmail($email);
        $usuario->setRoles([$rol]);
        $usuario->setPassword($this->passwordHasher->hashPassword($usuario, $password));

        $this->em->persist($usuario);
        $this->em->flush();

        $io->success(sprintf('Usuario %s creado correctamente con rol %s.', $email, $rol));

        return Command::SUCCESS;
    }
}
