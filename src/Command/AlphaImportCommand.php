<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Meal;
use App\Entity\Theme;
use App\Repository\CategoryRepository;
use App\Repository\IngredientRepository;
use App\Repository\MealRepository;
use App\Service\CategoryManager;
use App\Service\IngredientManager;
use App\Service\MealManager;
use App\Service\MealService;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\IOFactory as PDF;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AlphaImportCommand extends Command
{
    protected static $defaultName = 'alpha:import';
    private ManagerRegistry $doctrine;
    private CategoryManager $categManager;
    private CategoryRepository $categRepo;
    private IngredientManager $ingrManager;
    private IngredientRepository $ingrRepo;
    private MealManager $mealManager;
    private MealRepository $mealRepo;
    private MealService $mealService;
    private array $themes;

    public function __construct(
        ManagerRegistry $doctrine,
        CategoryManager $categManager,
        IngredientManager $ingrManager,
        MealManager $mealManager,
        MealService $mealService
    ) {
        $this->doctrine = $doctrine;

        $this->categManager = $categManager;
        $this->categRepo = $this->doctrine->getRepository(Category::class);

        $this->ingrManager = $ingrManager;
        $this->ingrRepo = $this->doctrine->getRepository(Ingredient::class);

        $this->mealManager = $mealManager;
        $this->mealRepo = $this->doctrine->getRepository(Meal::class);
        $this->mealService = $mealService;

        $themeRepo = $this->doctrine->getRepository(Theme::class);
        $tmpThemes = $themeRepo->findForCloset();

        $this->themes = [];
        foreach ($tmpThemes as $theme) {
            $this->themes[$theme->getName()] = $theme;
        }

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Alpha Import',
            '=====================',
        ]);

        $spreadsheet = PDF::load('./var/data/import/alpha-list.xlsx');
        $rows = $spreadsheet->getSheetByName('INGREDIENTS & ARTICLES')->toArray(null, null, true, true, true);

        $output->writeln([
            'Ingrédients',
            '*********************',
        ]);

        $ingredients = [];

        foreach ($rows as $i => $row) {
            $output->write($i.' ');
            if (0 === $i) {
                continue;
            }

            $output->write($row['A'].' ');
            $ingr = $this->ingrRepo->findOneBy(['name' => $row['A']]);

            if (null === $ingr) {
                $ingr = new Ingredient();
                $ingr->setName($row['A']);
                $ingr->setCategory($this->getCategory($row['B']));
                $ingr->setSeasonality($this->getSeasonalities($row));
                $this->ingrManager->save($ingr);
                $output->writeln(' -> created !');
            }

            $output->writeln('');

            $ingredients[mb_strtolower($ingr->getName(), 'utf-8')] = $ingr;
        }

        $spreadsheet = PDF::load('./var/data/import/alpha-list.xlsx');
        $rows = $spreadsheet->getSheetByName('Liste repas')->toArray(null, null, true, true, true);

        $output->writeln([
            'Plats',
            '*********************',
        ]);

        $ingrCols = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];

        foreach ($rows as $i => $row) {
            if (1 >= $i) {
                continue;
            }

            if (null === $row['B']) {
                break;
            }

            $output->write($i.' ');
            $output->writeln($row['B'].' ');

            $meal = $this->mealRepo->findOneBy(['name' => $row['B']]);

            if (null === $meal) {
                $meal = new Meal();
                $meal->setName($row['B']);
                $meal->setPreparation($this->getPreparation($row['D']));
                $this->setThemes($row, $meal);

                foreach ($ingrCols as $cols) {
                    $ingrName = $row[$cols];

                    if (null === $ingrName) {
                        break;
                    }

                    $ingrName = mb_strtolower($ingrName, 'utf-8');
                    if (!array_key_exists($ingrName, $ingredients)) {
                        $output->writeln('x Ingrédient non trouvé : '.$ingrName);
                        continue;
                    }

                    $meal->addIngredient($ingredients[$ingrName]);
                    $output->writeln('v Ingrédient ajouté : '.$ingrName);
                }

                $this->mealService->setToken($meal);
                $this->mealManager->save($meal);
                $output->writeln('-> created !');
            }

            $output->writeln('');
        }

        return Command::SUCCESS;
    }

    private function getSeasonalities($row): array
    {
        $seasons = [];

        if (null !== $row['D']) {
            $seasons[] = Ingredient::SEASONALITY_WINTER;
        }

        if (null !== $row['E']) {
            $seasons[] = Ingredient::SEASONALITY_SPRING;
        }

        if (null !== $row['F']) {
            $seasons[] = Ingredient::SEASONALITY_SUMMER;
        }

        if (null !== $row['G']) {
            $seasons[] = Ingredient::SEASONALITY_AUTUMN;
        }

        return $seasons;
    }

    private function getCategory(string $name): Category
    {
        $categ = $this->categRepo->findOneBy(['name' => $name]);

        if (null === $categ) {
            $categ = new Category();
            $categ->setName($name);
            $this->categManager->save($categ);
        }

        return $categ;
    }

    private function getPreparation(?string $label): int
    {
        switch ($label) {
            case 'EXPRESS (- 5 min)':
                return Meal::PREP_EXPRESS;
            case 'RAPIDE (5 à 15 min)':
                return Meal::PREP_FAST;
            case 'NORMAL (15 à 30 min)':
                return Meal::PREP_BASIC;
            case 'LONG (+ de 30 min)':
                return Meal::PREP_LONG;
        }

        return Meal::PREP_EXPRESS;
    }

    private function setThemes(array $row, Meal $meal): Meal
    {
        $map = [
            'AE' => 'Légumes',
            'AF' => 'Féculents',
            'AG' => 'Viande blanche',
            'AH' => 'Viande rouge',
            'AI' => 'Poisson / Crustacés',
            'AJ' => 'Snack / Pizza',
            'AK' => 'Festif / Exeptionnel',
        ];

        foreach ($map as $col => $name) {
            if (null !== $row[$col] && array_key_exists($name, $this->themes)) {
                $meal->addTheme($this->themes[$name]);
            }
        }

        return $meal;
    }
}
