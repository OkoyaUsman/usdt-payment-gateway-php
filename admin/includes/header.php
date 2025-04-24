<?php
require_once '../config.php';

if (!isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    redirect(BASE_URL . '/admin/login');
}

if (!isset($pageTitle)) {
    $pageTitle = 'Admin Dashboard';
}

// Get current page name
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
        .mobile-menu {
            display: none;
        }
        @media (max-width: 768px) {
            .mobile-menu {
                display: block;
            }
            .desktop-menu {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="<?php echo BASE_URL; ?>/admin/" class="text-xl font-bold text-indigo-600"><?php echo SITE_NAME; ?></a>
                        </div>
                        <!-- Desktop Menu -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8 desktop-menu">
                            <a href="<?php echo BASE_URL; ?>/admin/" class="<?php echo $currentPage === 'dashboard' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/transactions" class="<?php echo $currentPage === 'transactions' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Transactions
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/settings" class="<?php echo $currentPage === 'settings' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Settings
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <div class="mobile-menu">
                            <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                        <div class="ml-3 relative">
                            <div>
                                <button type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <i class="fas fa-user-circle text-gray-400 text-2xl"></i>
                                </button>
                            </div>
                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="<?php echo BASE_URL; ?>/admin/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="sm:hidden mobile-menu hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="<?php echo BASE_URL; ?>/admin/" class="<?php echo $currentPage === 'dashboard' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/transactions" class="<?php echo $currentPage === 'transactions' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Transactions
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/settings" class="<?php echo $currentPage === 'settings' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                        Settings
                    </a>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Mobile Menu Script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mobileMenuButton = document.querySelector('.mobile-menu-button');
                    const mobileMenu = document.getElementById('mobile-menu');
                    const userMenuButton = document.getElementById('user-menu-button');
                    const userMenu = document.querySelector('[role="menu"]');
                    
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                    
                    userMenuButton.addEventListener('click', function() {
                        userMenu.classList.toggle('hidden');
                    });
                    
                    // Close menus when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                            mobileMenu.classList.add('hidden');
                        }
                        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                            userMenu.classList.add('hidden');
                        }
                    });
                });
            </script>
        