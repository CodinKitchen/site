<?php

namespace App\Controller\Admin;

use App\Entity\Assessment;
use App\Entity\DynamicForm;
use App\Entity\Meeting;
use App\Entity\ScheduleRule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('build/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Manage schedule', 'fa fa-calendar', ScheduleRule::class);
        yield MenuItem::linkToCrud('Meetings', 'fa fa-calendar', Meeting::class);
        yield MenuItem::linkToCrud('Forms', 'fa fa-calendar', DynamicForm::class);
        yield MenuItem::linkToCrud('Assessment', 'fa fa-calendar', Assessment::class);
    }
}
