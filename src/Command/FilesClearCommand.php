<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FilesClearCommand extends Command
{
    protected static $defaultName = 'files:clear';
    protected static $defaultDescription = '/!\ Dev command /!\ Clear all files in public/dl directory !';

    private string $rootPath;

    public function __construct(string $rootPath)
    {

        parent::__construct();
        $this->rootPath = $rootPath;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (false === $this->removeAll()) {
            $io->error('No file were found');
        return Command::FAILURE;
        }

        $io->success('[Success] All files have been deleted ');
        return Command::SUCCESS;      
    }

    public function removeAll(): ?bool
    {
        $dirFiles = glob($this->rootPath . "/*", GLOB_BRACE);

        if (false === $dirFiles || count($dirFiles) === 0) {
            return false;
        }

        foreach ($dirFiles as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . "/*", GLOB_BRACE);
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir($dir);
            }
        }
        return true;
    }
}
