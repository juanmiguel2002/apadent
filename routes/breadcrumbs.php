<?php

use App\Models\Carpeta;
use App\Models\Clinica;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard Admin
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.clinica'));
});

// Mi unidad (archivos)
Breadcrumbs::for('admin.archivos', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Mi Unidad', route('admin.archivos'));
});

// Clinica
Breadcrumbs::for('admin.carpeta.view', function (BreadcrumbTrail $trail, Carpeta $carpeta) {
    if ($carpeta->carpeta_id) {
        $parent = Carpeta::find($carpeta->carpeta_id);
        if ($parent) {
            $trail->parent('admin.carpeta.view', $parent);
        }
    }else {
        if(!$carpeta->clinica_id) {
            $trail->push($carpeta->nombre, route('admin.archivos.view', $carpeta));
        }
    }

    $trail->push($carpeta->nombre, route('admin.archivos.view', $carpeta->id));
});
