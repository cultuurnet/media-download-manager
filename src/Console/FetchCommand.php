<?php

namespace CultuurNet\MediaDownloadManager\Console;

use CultuurNet\MediaDownloadManager\Parser\ParserInterface;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * WatchCommand constructor.
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('mediadownloader')
            ->setDescription('Start the importer by watching the folder.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parser->start();
    }
}
