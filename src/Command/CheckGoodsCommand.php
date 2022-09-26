<?php

namespace App\Command;

use App\Repository\GoodsRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckGoodsCommand extends Command
{
    private $goodsRepository;
    private $addTr;
    private $userRepository;

    protected static $defaultName = 'app:goods:check';

    public function __construct(GoodsRepository $goodsRepository ,TransactionRepository $addTr, UserRepository $userRepository  )
    {
        $this->goodsRepository = $goodsRepository;
        $this->addTr = $addTr;
        $this->userRepository = $userRepository;
        parent::__construct();
    }



    protected function configure()
    {
        $this
            ->setDescription('Check active goods for placement period')
            ->addOption('go', null, InputOption::VALUE_NONE, 'go')
        ;
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        if ($input->getOption('go')) {
            $io->note('Dry mode enabled');

            $count =  $this->goodsRepository->countOldGoods() ;
        } else {
            $count =  $this->goodsRepository->countOldGoods() ;
            // Находим Активные товары с датой таой завершения < = new \DateTime()

            $goods = $this->goodsRepository->getOldGoods();

            foreach ( $goods as $good) {
                //Ищем актуальную(последнюю) транзакцию по товару
                $lastTransaction = $this->addTr->findOneBy([
                    'good_id' => $good->getId(),
                    'status' => '1'
                ]);
                //если нашли
                if (!is_null($lastTransaction)) {
                    $BillUser = $lastTransaction->getUserId()->getBalance();

                    // устанавливаем владельцем товара пользователя из транзакции
                    $good->setUser($lastTransaction->getUserId())->setStatus('1');

                    // вычитаем сумму платежа из реального баланса
                    $lastTransaction->getUserId()->setBalance($BillUser - $lastTransaction->getPay());
                    $this->addTr->userRepository->add($lastTransaction->getUserId(), true);
                }else{
                    $good->setStatus('2');
                }
                //завершаем торги по товару
                $good->setStatus('2');
                $this->goodsRepository->add($good, true);
            }
        }

        $io->success(sprintf('  "%d" ', $count));
        return 0;
    }

}
