<?php

require_once __DIR__.'/vendor/autoload.php'; // Chargez l'autoloader de Composer

use Symfony\Component\Finder\Finder;

// Initialisez le Finder pour trouver tous les fichiers de contrôleur Symfony
$finder = new Finder();
$finder->files()->in(__DIR__.'/src/Controller')->name('*.php');

$documentation = [];

// Parcourez les fichiers de contrôleur Symfony
foreach ($finder as $file) {
    $controllerClass = 'App\\Controller\\' . $file->getBasename('.php');
    
    // Utilisez le nom du contrôleur comme catégorie de route
    $category = str_replace('Controller', '', $file->getBasename('.php'));
    
    // Utilisez la réflexion pour analyser le contrôleur
    $reflectionClass = new ReflectionClass($controllerClass);
    
    // Parcourez les méthodes du contrôleur
    foreach ($reflectionClass->getMethods() as $method) {
        $annotations = $method->getAttributes();
        
        foreach ($annotations as $annotation) {
            // Vérifiez si l'annotation est de type Route
            if ($annotation->getName() === 'Symfony\Component\Routing\Annotation\Route') {
                // Obtenez l'instance de l'annotation Route
                $route = $annotation->newInstance();
                // Récupérez les propriétés de l'annotation Route
                $path = $route->getPath();
                $name = $route->getName();
                $methods = $route->getMethods();
                
                // Récupérez les options de la route
                $options = $route->getOptions();
                $description = isset($options['description route']) ? $options['description route'] : '';
                $body = isset($options['body']) ? json_encode($options['body'], JSON_PRETTY_PRINT) : '';

                // Ajoutez les informations extraites à la documentation
                $documentation[$category][] = [
                    'path' => $path,
                    'name' => $name,
                    'methods' => $methods,
                    'description' => $description,
                    'body' => $body,
                    // Ajoutez d'autres informations pertinentes au besoin
                ];
            }
        }
    }
}

// Générez le contenu HTML
$htmlContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>API Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        h1 {
            text-align: center;
        }
        h2 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 10px;
            position: relative;
        }
        li:hover {
            background-color: #f5f5f5;
        }
        strong {
            font-weight: bold;
        }
        .method-square {
            position: absolute;
            left: -90px;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 80px;
            border: 1px solid #000;
        }
        .get { background-color: green; }
        .post { background-color: blue; }
        .put { background-color: yellow; }
        .patch { background-color: orange; }
        .delete { background-color: red; }
        .code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f5f5f5;
            padding: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>API Documentation</h1>
HTML;

foreach ($documentation as $category => $routes) {
    $htmlContent .= '<h2>' . $category . '</h2>';
    $htmlContent .= '<ul>';
    foreach ($routes as $route) {
        $htmlContent .= '<li>';
        // Ajoutez les carrés de couleur selon la méthode de la route
        foreach ($route['methods'] as $method) {
            $htmlContent .= '<div class="method-square ' . strtolower($method) . '"></div>';
        }
        $htmlContent .= '<strong>' . implode(', ', $route['methods']) . '</strong> ' . $route['name'] . '<br>';
        $htmlContent .= '<strong>Path:</strong> ' . $route['path'] . '<br>';
        $htmlContent .= '<strong>Description:</strong> ' . $route['description'] . '<br>';
        // Afficher le corps de la requête dans un style de code si ce n'est pas une méthode GET
        if (!empty($route['body']) && !in_array('GET', $route['methods'])) {
            $htmlContent .= '<strong>Body:</strong><pre class="code">' . htmlentities($route['body']) . '</pre>';
        }
        // Ajoutez d'autres informations pertinentes au besoin
        $htmlContent .= '</li>';
    }
    $htmlContent .= '</ul>';
}

$htmlContent .= <<<HTML
</body>
</html>
HTML;

// Sauvegardez le contenu HTML dans un fichier
file_put_contents(__DIR__.'/api_docs.html', $htmlContent);

echo "Documentation générée avec succès !\n";
