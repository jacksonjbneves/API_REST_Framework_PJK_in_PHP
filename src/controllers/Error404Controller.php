<?php

namespace controllers;

class Error404Controller {
    
    public function index() {               
       echo json_encode(['error' => 'Algo deu errado, rota não existe']);
    }
}