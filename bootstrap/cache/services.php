<?php return array (
  'providers' => 
  array (
    0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    1 => 'Fruitcake\\Cors\\CorsServiceProvider',
    2 => 'Laravel\\Tinker\\TinkerServiceProvider',
    3 => 'Carbon\\Laravel\\ServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    1 => 'Fruitcake\\Cors\\CorsServiceProvider',
    2 => 'Carbon\\Laravel\\ServiceProvider',
  ),
  'deferred' => 
  array (
    'command.tinker' => 'Laravel\\Tinker\\TinkerServiceProvider',
  ),
  'when' => 
  array (
    'Laravel\\Tinker\\TinkerServiceProvider' => 
    array (
    ),
  ),
);