<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;

class PageController extends Controller
{
    public function about(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('pages/about', [
            'title' => 'Acerca de Nosotros'
        ]);
    }

    public function contact(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('pages/contact', [
            'title' => 'Contacto'
        ]);
    }

    public function terms(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('pages/terms', [
            'title' => 'Términos y Condiciones'
        ]);
    }

    public function privacy(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('pages/privacy', [
            'title' => 'Privacidad'
        ]);
    }
}
